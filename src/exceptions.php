<?php
/**
 * This file is part of Ares.
 * Copyright (c) 2015 GRIFART spol. s r.o. (https://grifart.cz)
 */

namespace Grifart\Ares;



abstract class AresException extends \Exception
{
}

class ValidationException extends AresException
{
}

class InvalidPayloadException extends AresException
{
}

class FailedRequestException extends AresException
{
}

class NoResultException extends AresException
{
}

class UnknownSubjectException extends AresException
{
}

class XmlParsingException extends AresException
{
	/** @var array */
	private $errors;

	public function __construct(array $errors)
	{
		$this->errors = $errors;
		parent::__construct('Error parsing XML string, last error: ' . end($errors));
	}

	/**
	 * Allows to retrieve all XML encountered parsing errors
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}
}