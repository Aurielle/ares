<?php
/**
 * This file is part of Ares.
 * Copyright (c) 2015 GRIFART spol. s r.o. (https://grifart.cz)
 */

namespace Grifart\Ares\Drivers;

use Grifart\Ares;
use Nette;
use Nette\Caching;


/**
 * Proxy over another IDriver, caches results from ARES for a given time.
 */
class CachingDriver extends Nette\Object implements IDriver
{
	/** Cache namespace and tag */
	const CACHE_IDENTIFIER = 'Ares';

	/** Cache expiration, in seconds (1 day) */
	const DEFAULT_EXPIRATION = 86400;

	/** @var IDriver */
	private $destinationDriver;

	/** @var Nette\Caching\Cache */
	private $cache;

	/** @var int|string */
	private $expiration;


	public function __construct(IDriver $destinationDriver, Caching\IStorage $cacheStorage, $expiration = self::DEFAULT_EXPIRATION)
	{
		if ($destinationDriver instanceof static) {
			throw new Nette\InvalidArgumentException("Can't proxy another caching driver.");
		}

		$this->destinationDriver = $destinationDriver;
		$this->cache = new Caching\Cache($cacheStorage, self::CACHE_IDENTIFIER);
		$this->expiration = Nette\Utils\DateTime::from($expiration);
	}


	/**
	 * Fetches data of one subject identified by his IN from the ARES database.
	 * @param string $in
	 * @return Ares\Subject
	 */
	public function fetch($in)
	{
		return $this->cache->load($in, function(&$dependencies) use ($in) {
			$dependencies[Caching\Cache::EXPIRATION] = $this->expiration;
			$dependencies[Caching\Cache::TAGS] = [self::CACHE_IDENTIFIER];

			return $this->destinationDriver->fetch($in);
		});
	}

	/**
	 * Purges result cache.
	 * @return void
	 */
	public function clean()
	{
		$this->cache->clean([
			Caching\Cache::TAGS => self::CACHE_IDENTIFIER,
		]);
	}
}