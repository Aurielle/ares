<?php
/**
 * This file is part of Ares.
 * Copyright (c) 2015 Václav Vrbka (http://aurielle.cz)
 */

namespace Grifart\Ares;

use Nette;
use Nette\Caching;


/**
 * Caching layer for Ares. Do not use standalone.
 */
class Cache extends Nette\Object
{
	const CACHE_IDENTIFIER = 'Ares';

	/** @var int|string */
	private $expiration;

	/** @var Nette\Caching\Cache */
	private $netteCache;


	public function __construct(Caching\IStorage $cacheStorage, $expiration)
	{
		if (is_string($expiration) && strtotime($expiration) === FALSE) {
			throw new Nette\InvalidArgumentException("Can't parse expiration time string.");
		}

		$this->netteCache = new Caching\Cache($cacheStorage, self::CACHE_IDENTIFIER);
		$this->expiration = $expiration;
	}

	public function get($in)
	{
		return $this->netteCache->load($in);
	}

	public function save($in, Subject $data)
	{
		return $this->netteCache->save($in, $data, [
			Caching\Cache::EXPIRE => $this->expiration,
			Caching\Cache::TAGS => [self::CACHE_IDENTIFIER],
		]);
	}

	public function clean()
	{
		$this->netteCache->clean([
			Caching\Cache::TAGS => self::CACHE_IDENTIFIER
		]);
	}
}