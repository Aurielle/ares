<?php

namespace h4kuna\Ares;

use Nette;

/**
 * @author Milan Matějček <milan.matejcek@gmail.com>
 * @author Václav Vrbka
 */
class Ares extends Nette\Object
{

	/** @var IRequest */
	private $request;

	public function __construct(IRequest $request = NULL)
	{
		if ($request === NULL) {
			$request = new Get();
		}
		$this->request = $request;
	}

	/**
	 * Load fresh data.
	 * @param int|string $inn Identification number
	 * @param bool $includeExpired Whether to include details about old/expired subjects
	 * @return Data
	 */
	public function loadData($inn, $includeExpired = FALSE)
	{
		$this->request->clean();
		return $this->request->loadData($inn, $includeExpired);
	}

	/**
	 * Get temporary data.
	 * @return Data
	 */
	public function getData()
	{
		return $this->request->loadData();
	}

}
