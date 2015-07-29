<?php
/**
 * This file is part of Ares.
 * Copyright (c) 2015 GRIFART spol. s r.o. (https://grifart.cz)
 */

namespace Grifart\Ares;

use Nette;


class Subject extends Nette\Object
{
	/** @var string */
	private $identificationNumber;

	/** @var string|null */
	private $vatIdentificationNumber;

	/** @var bool */
	private $vatPayer;

	/** @var string */
	private $name;

	/** @var string */
	private $city;

	/** @var string */
	private $street;

	/** @var int */
	private $zipCode;

	/** @var bool */
	private $person;

	/** @var \DateTime */
	private $createdAt;

	/** @var array */
	private static $requiredData = [
		'identificationNumber',
		'vatIdentificationNumber',
		'vatPayer',
		'name',
		'city',
		'street',
		'zipCode',
		'person',
		'createdAt',
	];


	public function __construct(array $data)
	{
		$required = self::$requiredData;
		foreach ($data as $key => $value) {
			if (($k = array_search($key, $required, TRUE)) === FALSE) {
				throw new InvalidPayloadException("Extra key '$key' detected in payload.");
			}

			$this->$key = $value;
			unset($required->$k);
		}

		if (count($required) > 0) {
			throw new InvalidPayloadException('Some required keys are missing from payload: ' . implode(', ', $required));
		}
	}


	/**
	 * @return string
	 */
	public function getIdentificationNumber()
	{
		return $this->identificationNumber;
	}

	/**
	 * @return null|string
	 */
	public function getVatIdentificationNumber()
	{
		return $this->vatIdentificationNumber;
	}

	/**
	 * @return boolean
	 */
	public function isVatPayer()
	{
		return $this->vatPayer;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getCity()
	{
		return $this->city;
	}

	/**
	 * @return string
	 */
	public function getStreet()
	{
		return $this->street;
	}

	/**
	 * @return int
	 */
	public function getZipCode()
	{
		return $this->zipCode;
	}

	/**
	 * @return boolean
	 */
	public function isPerson()
	{
		return $this->person;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}
}