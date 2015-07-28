<?php
/**
 * This file is part of Ares.
 * Copyright (c) 2015 GRIFART spol. s r.o. (https://grifart.cz)
 */

namespace Grifart\Ares\DI;

use Grifart\Ares;
use Nette;


class AresExtension extends Nette\DI\CompilerExtension
{
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('basicDriver'))
			->setClass(Ares\Drivers\BasicDriver::class)
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('cachingDriver'))
			->setClass(Ares\Drivers\CachingDriver::class, [
				$this->prefix('@basicDriver'),
				'@Nette\Caching\IStorage',
			])
			->setAutowired(FALSE);

		$builder->addDefinition($this->prefix('ares'))
			->setClass(Ares\Ares::class);
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		if ($builder->findByType('Nette\Caching\IStorage')) {
			$builder->getDefinition($this->prefix('ares'))
				->setArguments([$this->prefix('@cachingDriver')]);
		} else {
			$builder->getDefinition($this->prefix('ares'))
				->setArguments([$this->prefix('@basicDriver')]);
		}
	}
}