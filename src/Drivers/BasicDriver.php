<?php
/**
 * This file is part of Ares.
 * Copyright (c) 2015 Václav Vrbka (http://aurielle.cz)
 */

namespace Grifart\Ares\Drivers;

use Grifart\Ares;
use Kdyby\Curl;
use Nette;


/**
 * Data fetching layer via the Basic service provided by ARES.
 * @see http://wwwinfo.mfcr.cz/ares/ares_xml_basic.html.cz
 * @see http://wwwinfo.mfcr.cz/ares/ares_xml_basic.html.en
 */
class BasicDriver extends Nette\Object implements IDriver
{
	/** URL for API requests */
	const URL = 'http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_bas.cgi';

	/** @var Curl\CurlSender */
	private $curlSender;


	public function __construct()
	{
		$this->curlSender = new Curl\CurlSender();
	}

	/**
	 * Fetches data of one subject identified by his IN from the ARES database.
	 *
	 * @see http://wwwinfo.mfcr.cz/ares/xml_doc/schemas/ares/ares_answer_basic/v_1.0.3/ares_answer_basic_v_1.0.3.xsd
	 * @see http://wwwinfo.mfcr.cz/ares/xml_doc/schemas/ares/ares_datatypes/v_1.0.3/ares_datatypes_v_1.0.3.xsd
	 * @see http://wwwinfo.mfcr.cz/ares/xml_doc/schemas/uvis_datatypes/v_1.0.3/uvis_datatypes_v_1.0.3.xsd
	 *
	 * @param string $in
	 * @param bool $includeExpired
	 * @return Ares\Subject
	 * @throws Ares\FailedRequestException
	 * @throws Ares\UnknownSubjectException
	 * @throws Ares\XmlParsingException
	 */
	public function fetch($in, $includeExpired = FALSE)
	{
		try {
			$request = new Curl\Request($this->buildUrl($in, $includeExpired));
			$response = $this->curlSender->send($request);
		} catch (\Exception $e) {
			throw new Ares\FailedRequestException('Querying ARES database failed: ' . $e->getMessage(), $e->getCode(), $e);
		}

		$xml = $this->parseXml($response->getResponse());
		$ns = $xml->getDocNamespaces();
		$info = $xml->children($ns['are'])->children($ns['D']);

		// Subject wasn't found, parse the error code and description
		if ((string) $info->VH->K === '2') {
			$code = (string) $info->E->EK;
			$message = (string) $info->E->ET;

			throw new Ares\UnknownSubjectException("Subject wasn't found.", $code, new Ares\NoResultException($message, $code));
		}

		// Parse infos
		$subject = $info->VBAS;
		$street = (string) $subject->AD->UC;
		if (is_numeric($street)) {
			$street = $subject->AA->NCO . ' ' . $street;
		}

		if (isset($subject->AA->CO)) {
			$street .= '/' . $subject->AA->CO;
		}

		return new Ares\Subject([
			'identificationNumber' => $subject->ICO,
			'vatIdentificationNumber' => $subject->DIC ?: NULL,
			'vatPayer' => !empty($subject->DIC),
			'name' => $subject->OF,
			'city' => $subject->AA->N,
			'street' => $street,
			'zipCode' => $subject->AA->PSC,
			'person' => ((int) $subject->PF->KPF) <= 108,
			'createdAt' => new \DateTime((string) $subject->DV, new \DateTimeZone('Europe/Prague')), // timezone definition is intentional
		]);
	}

	/**
	 * Builds request URL.
	 * @see http://wwwinfo.mfcr.cz/ares/ares_xml_basic.html.cz for more information about possible query string params
	 * @param string $in Subject identification number
	 * @param bool $includeExpired Search among expired subjects as well
	 * @return Nette\Http\Url
	 */
	private function buildUrl($in, $includeExpired)
	{
		$url = new Nette\Http\Url(self::URL);
		$url->appendQuery([
			'ico' => $in,
			'aktivni' => $includeExpired ? 'false' : 'true',
		]);

		return $url;
	}

	/**
	 * Helper function for parsing XML and handling errors.
	 * @author Tomáš Jacík <http://forum.nette.org/cs/23705-navrh-na-xml-parser-pro-nette-utils>
	 * @param string $response
	 * @return \SimpleXMLElement
	 * @throws Ares\XmlParsingException
	 */
	private function parseXml($response)
	{
		$previous = libxml_use_internal_errors(TRUE);
		$xml = @simplexml_load_string($response); // intentionally @ - errors are handled separately

		if (!$xml) {
			$errors = libxml_get_errors();
			libxml_clear_errors();
			libxml_use_internal_errors($previous);

			throw new Ares\XmlParsingException($errors);
		}

		libxml_use_internal_errors($previous);
		return $xml;
	}
}