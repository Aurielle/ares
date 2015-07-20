<?php

namespace h4kuna\Ares;

use h4kuna\CUrl,
	Nette;

/**
 * @author Milan Matějček
 * @author Václav Vrbka
 */
class Get extends Nette\Object implements IRequest
{
	const URL = 'http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_bas.cgi';

	/** @var Data */
	protected $data;


	public function __construct(Data $data = NULL)
	{
		if ($data === NULL) {
			$data = new Data;
		}

		$this->data = $data;
	}

	/**
	 * @param string|int $inn Subject identification number
	 * @param bool $includeExpired Whether to include details about old/expired subjects, otherwise throws exception
	 * @return Data
	 * @throws IdentificationNumberNotFoundException
	 */
	public function loadData($inn = NULL, $includeExpired = FALSE)
	{
		if ($inn === NULL) {
			return $this->data;
		}

		return $this->loadXML($inn, $includeExpired);
	}

	/**
	 * @param string $inn
	 * @param bool $includeExpired
	 * @return string
	 */
	private function buildUrl($inn, $includeExpired)
	{
		// See http://wwwinfo.mfcr.cz/ares/ares_xml_basic.html.cz about all available options
		$options = [
			'ico' => $inn,
			'aktivni' => $includeExpired ? 'false' : 'true',
		];

		return self::URL . '?' . http_build_query($options);
	}

	/**
	 * Load XML and fill Data object
	 * @param string|int $inn
	 * @param bool $includeExpired
	 * @return Data
	 * @throws IdentificationNumberNotFoundException
	 */
	private function loadXML($inn, $includeExpired = FALSE)
	{
		$this->clean();

		// Subject identification numbers are by definition exactly 8 digits long
		// Older INs are padded from left with zeroes
		$IN = intval($inn);
		$IN = str_pad((string) $IN, 8, '0', STR_PAD_LEFT);

		$url = $this->buildUrl($IN, $includeExpired);

		$xmlSource = CUrl\CurlBuilder::download($url);
		$xml = @simplexml_load_string($xmlSource);
		if (!$xml) {
			throw new IdentificationNumberNotFoundException;
		}

		$ns = $xml->getDocNamespaces();
		$xmlEl = $xml->children($ns['are'])->children($ns['D'])->VBAS;

		// When IN is not found, ARES returns 200 OK and error description in D:E tag
		// So this is a simplified detection, if no D:VBAS->ICO is present, consider the request invalid
		if (!isset($xmlEl->ICO)) {
			throw new IdentificationNumberNotFoundException;
		}

		$street = strval($xmlEl->AD->UC);
		if (is_numeric($street)) {
			$street = $xmlEl->AA->NCO . ' ' . $street;
		}

		if (isset($xmlEl->AA->CO)) {
			$street .= '/' . $xmlEl->AA->CO;
		}

		$this->data->setIN($xmlEl->ICO)
				->setVatIN($xmlEl->DIC)
				->setCity($xmlEl->AA->N)
				->setCompany($xmlEl->OF)
				->setStreet($street)
				->setZip($xmlEl->AA->PSC)
				->setPerson($xmlEl->PF->KPF)
				->setCreated($xmlEl->DV);

		if (isset($xmlEl->ROR)) {
			$this->data->setActive($xmlEl->ROR->SOR->SSU)
					->setFileNumber($xmlEl->ROR->SZ->OV)
					->setCourt($xmlEl->ROR->SZ->SD->T);
		}

		return $this->data;
	}

	/**
	 * Clear data.
	 * @return self
	 */
	public function clean()
	{
		$this->data->clean();
		return $this;
	}
}
