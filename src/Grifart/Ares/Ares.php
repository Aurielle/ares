<?php
/**
 * This file is part of Ares.
 * Copyright (c) 2015 GRIFART spol. s r.o. (https://grifart.cz)
 */

namespace Grifart\Ares;

use Nette;


/**
 * @author Milan Matějček <milan.matejcek@gmail.com>
 * @author Václav Vrbka <vaclav.vrbka@grifart.cz>
 */
class Ares extends Nette\Object
{
	/** @var Drivers\IDriver */
	private $driver;


	public function __construct(Drivers\IDriver $driver)
	{
		$this->driver = $driver;
	}


	/**
	 * Validates an identification number.
	 *
	 * Identification number is exactly eight digits long, for algorithm explanation please visit the URL below.
	 * @see http://phpfashion.com/jak-overit-platne-ic-a-rodne-cislo
	 * @author David Grudl
	 *
	 * @param string $in Identification number to validate
	 * @return bool
	 */
	public static function validateIdentificationNumber($in)
	{
		if (!is_string($in)) {
			// disallow all other types except strings
			$type = gettype($in);
			throw new Nette\InvalidArgumentException(
				is_int($in) ? 'Please pass the identification number as a string, integers can cause problems.'
					: "Identification number can't be of type $type."
			);
		}

		// be liberal in what you receive
		$in = preg_replace('#\s+#', '', $in);

		// pad with zeroes from the left if length < 8
		if (Nette\Utils\Strings::length($in) < 8) {
			$in = str_pad($in, 8, '0', STR_PAD_LEFT);
		}

		// is exactly 8 digits?
		if (!preg_match('#^\d{8}$#', $in)) {
			return FALSE;
		}

		// checksum
		$a = 0;
		for ($i = 0; $i < 7; $i++) {
			$a += $in[$i] * (8 - $i);
		}

		$a = $a % 11;
		if ($a === 0) {
			$c = 1;
		} elseif ($a === 1) {
			$c = 0;
		} else {
			$c = 11 - $a;
		}

		return (int) $in[7] === $c;
	}

	/**
	 * Fetches details about an identification number.
	 *
	 * @param string $in
	 * @return Subject
	 * @throws ValidationException
	 */
	public function findDetails($in)
	{
		$in = (string) $in;

		// pad with zeroes from the left if length < 8
		if (Nette\Utils\Strings::length($in) < 8) {
			$in = str_pad($in, 8, '0', STR_PAD_LEFT);
		}

		if (!self::validateIdentificationNumber($in)) {
			throw new ValidationException('This identification number does not meet schematic requirements and therefore is invalid.');
		}

		return $this->driver->fetch($in);
	}
}
