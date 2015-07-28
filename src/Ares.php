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

	/** @var Cache */
	private $cache;


	public function __construct(Drivers\IDriver $driver, Cache $cache)
	{
		$this->driver = $driver;
		$this->cache = $cache;
	}


	/**
	 * Validates an identification number.
	 *
	 * Identification number is exactly eight digits long, for algorithm explanation please visit the URL below.
	 * @see http://phpfashion.com/jak-overit-platne-ic-a-rodne-cislo
	 * @author David Grudl
	 *
	 * @param string|int $in Identification number to validate
	 * @return bool
	 */
	public static function validateIdentificationNumber($in)
	{
		// be liberal in what you receive
		$in = preg_replace('#\s+#', '', $in);

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
	 * @param string|int $in
	 * @param bool $includeExpired
	 * @return Subject
	 * @throws ValidationException
	 */
	public function findDetails($in, $includeExpired = FALSE)
	{
		$in = (string) $in;
		if (!self::validateIdentificationNumber($in)) {
			throw new ValidationException('This identification number does not meet schematic requirements and therefore is invalid.');
		}

		$data = $this->cache->get($in);
		if ($data === NULL) {
			/** @var Subject $data */
			$data = $this->driver->fetch($in, $includeExpired);
			return $this->cache->save($in, $data);
		}

		return $data;
	}
}
