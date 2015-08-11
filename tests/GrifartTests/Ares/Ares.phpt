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
	public function testValidateIdentificationNumber_givenIllegalType_thenThrowsException()
	{
		Assert::exception(function() {
			Grifart\Ares\Ares::validateIdentificationNumber(NULL);
		}, Nette\InvalidArgumentException::class);

		Assert::exception(function() {
			Grifart\Ares\Ares::validateIdentificationNumber(FALSE);
		}, Nette\InvalidArgumentException::class);

		Assert::exception(function() {
			Grifart\Ares\Ares::validateIdentificationNumber(216224);
		}, Nette\InvalidArgumentException::class);

		Assert::exception(function() {
			Grifart\Ares\Ares::validateIdentificationNumber(1.0);
		}, Nette\InvalidArgumentException::class);

		Assert::exception(function() {
			Grifart\Ares\Ares::validateIdentificationNumber([]);
		}, Nette\InvalidArgumentException::class);

		Assert::exception(function() {
			Grifart\Ares\Ares::validateIdentificationNumber(new \stdClass());
		}, Nette\InvalidArgumentException::class);
	}

	public function testValidateIdentificationNumber_givenCorrectNumber_thenReturnsTrue()
	{
		Assert::true(Grifart\Ares\Ares::validateIdentificationNumber('216224'));
		Assert::true(Grifart\Ares\Ares::validateIdentificationNumber('00216224'));
		Assert::true(Grifart\Ares\Ares::validateIdentificationNumber('25596641'));
		Assert::true(Grifart\Ares\Ares::validateIdentificationNumber('69663963'));
		Assert::true(Grifart\Ares\Ares::validateIdentificationNumber('25501186'));
		Assert::true(Grifart\Ares\Ares::validateIdentificationNumber('1'));
		Assert::true(Grifart\Ares\Ares::validateIdentificationNumber('00000001'));
		Assert::true(Grifart\Ares\Ares::validateIdentificationNumber('99999994'));

		Assert::true(Grifart\Ares\Ares::validateIdentificationNumber('    25501186
		')); // whitespace doesn't matter
	}

	public function testValidateIdentificationNumber_givenIncorrectNumber_thenReturnsFalse()
	{
		Assert::false(Grifart\Ares\Ares::validateIdentificationNumber('12345678'));
		Assert::false(Grifart\Ares\Ares::validateIdentificationNumber('87654321'));
		Assert::false(Grifart\Ares\Ares::validateIdentificationNumber('99999995'));
		Assert::false(Grifart\Ares\Ares::validateIdentificationNumber('99999999'));
		Assert::false(Grifart\Ares\Ares::validateIdentificationNumber('1234567890'));
		Assert::false(Grifart\Ares\Ares::validateIdentificationNumber('foo bar'));
	}
}

\run(new AresTest());
