<?php

/**
 * Test: Grifart\Ares\Ares
 * 
 * @testCase
 * @author Václav Vrbka <vaclav.vrbka@grifart.cz>
 * @package Grifart\Ares
 */
 
namespace GrifartTests\Ares;

use Grifart;
use Nette;
use Tester;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @author Václav Vrbka <vaclav.vrbka@grifart.cz>
 */
class AresTest extends Tester\TestCase
{
	public function testValidateIdentificationNumber_givenInvalidInput_thenReturnsFalse()
	{
		Assert::false(Grifart\Ares\Ares::validateIdentificationNumber(NULL));
		Assert::false(Grifart\Ares\Ares::validateIdentificationNumber(1));
		Assert::false(Grifart\Ares\Ares::validateIdentificationNumber('1'));
	}

	public function testValidateIdentificationNumber_givenCorrectNumber_whenLacksRequiredLength_thenReturnsFalse()
	{
		Assert::false(Grifart\Ares\Ares::validateIdentificationNumber(216224));
		Assert::false(Grifart\Ares\Ares::validateIdentificationNumber('216224'));
	}

	public function testValidateIdentificationNumber_givenCorrectNumber_thenReturnsTrue()
	{
		Assert::true(Grifart\Ares\Ares::validateIdentificationNumber('00216224')); // INs with leading zeroes - strings only!
		Assert::true(Grifart\Ares\Ares::validateIdentificationNumber(25596641));
		Assert::true(Grifart\Ares\Ares::validateIdentificationNumber('25596641'));
		Assert::true(Grifart\Ares\Ares::validateIdentificationNumber(69663963));
		Assert::true(Grifart\Ares\Ares::validateIdentificationNumber(25501186));
		Assert::true(Grifart\Ares\Ares::validateIdentificationNumber('25501186'));
	}
}

\run(new AresTest());