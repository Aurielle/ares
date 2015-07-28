<?php
/**
 * This file is part of Ares.
 * Copyright (c) 2015 Václav Vrbka (http://aurielle.cz)
 */

namespace Grifart\Ares\Drivers;

use Grifart\Ares;


interface IDriver
{
	/**
	 * Fetches data of one subject identified by his IN from the ARES database.
	 * @param string $in
	 * @param bool $includeExpired
	 * @return Ares\Subject
	 */
	function fetch($in, $includeExpired = FALSE);
}