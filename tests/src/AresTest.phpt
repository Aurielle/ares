<?php

namespace Aurielle\Ares;

use Tester\Assert,
	Tests;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Milan Matějček
 * @TestCase
 */
class AresTest extends \Tester\TestCase
{
	/** @var Ares */
	private $ares;

	protected function setUp()
	{
		$this->ares = new Ares;
	}

	public function testFreelancer()
	{
		$in = '87744473';
		/* @var $data Data */
		$data = (string) $this->ares->loadData($in);
		Assert::same(Tests\Utils::getContent($in), $data);
	}

	public function testMerchant()
	{
		$in = '27082440';
		/* @var $data Data */
		$data = (string) $this->ares->loadData($in);
		Assert::same(Tests\Utils::getContent($in), $data);
	}

	public function testInvalidIdentificationNumber()
	{
		$in = '12345678';
		Assert::exception(function() use ($in) {
			$this->ares->loadData($in);
		}, IdentificationNumberNotFoundException::class);
	}

	public function testExpiredIdentificationNumberError()
	{
		$in = '25596641';
		Assert::exception(function() use ($in) {
			$this->ares->loadData($in);
		}, IdentificationNumberNotFoundException::class);
	}

	public function testExpiredIdentificationNumberSuccess()
	{
		$in = '25596641';
		/* @var $data Data */
		$data = (string) $this->ares->loadData($in, TRUE);
		Assert::same(Tests\Utils::getContent($in), $data);
	}
}

$test = new AresTest;
$test->run();
