<?php
/**
 * This file is part of Ares.
 * Copyright (c) 2015 GRIFART spol. s r.o. (https://grifart.cz)
 */

namespace Grifart\Ares\Drivers;

use Grifart\Ares;


interface IDriver
{
	/**
	 * Fetches data of one subject identified by his IN from the ARES database.
	 * @param string $in
	 * @return Ares\Subject
	 */
	function fetch($in);
}