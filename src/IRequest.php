<?php

namespace Aurielle\Ares;

/**
 * @author Milan Matějček
 * @author Václav Vrbka
 */
interface IRequest {

	/**
	 * Load data from ares.
	 * @param string $in Identification Number
	 * @param bool $includeExpired Whether to include details about old/expired subjects
	 * @return Data
	 */
    public function loadData($in = NULL, $includeExpired = FALSE);

    /**
     * Clean last request.
     * @void
     */
    public function clean();
}

