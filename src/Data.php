<?php

namespace h4kuna\Ares;

use ArrayAccess,
	Countable,
	DateTime,
	DateTimeZone,
	Iterator,
	Nette;

/**
 * @author Milan Matějček
 * @author Václav Vrbka
 */
class Data extends Nette\Object implements ArrayAccess, Iterator, Countable
{
	private $data = [];


	public function setActive($s)
	{
		return $this->set('active', strval($s) === 'Aktivní');
	}

	public function setCity($s)
	{
		return $this->set('city', $s);
	}

	public function setCompany($s)
	{
		return $this->set('company', $s);
	}

	public function setCourt($s)
	{
		return $this->set('court', $s);
	}

	public function setCreated($s)
	{
		return $this->set('created', new DateTime($s, new DateTimeZone('Europe/Prague')));
	}

	public function setFileNumber($s)
	{
		return $this->set('file_number', $s);
	}

	public function setPerson($s)
	{
		return $this->set('person', strval($s) <= '108');
	}

	public function setStreet($s)
	{
		return $this->set('street', $s);
	}

	public function setIN($s)
	{
		return $this->set('in', $s);
	}

	public function setVatIN($s)
	{
		$this->set('vat_in', !empty($s) ? $s : NULL);
		return $this->set('vat_payer', !empty($s));
	}

	public function setZip($s)
	{
		return $this->set('zip', $s);
	}

	private function setFileNumberAndCourt()
	{
		if (!isset($this->data['court_all']) && array_key_exists('file_number', $this->data) && array_key_exists('court', $this->data)) {
			$this->data['court_all'] = $this->data['file_number'] . ', ' . $this->data['court'];
		}
	}

	private function set($key, $val)
	{
		if ($val instanceof DateTime) {
			$this->data[$key] = $val->format(DateTime::ISO8601);
		} elseif (is_bool($val) || is_null($val)) {
			$this->data[$key] = $val;
		} else {
			$this->data[$key] = strval($val);
		}
		return $this;
	}

	/**
	 * Prepare for another load from ares
	 * @return self
	 */
	public function clean()
	{
		$this->data = [];
		return $this;
	}

	/**
	 * Copy data
	 *
	 * @param array $map
	 * @return array
	 */
	public function toArray(array $map = [])
	{
		$this->setFileNumberAndCourt();
		if (!$map) {
			return $this->data;
		}
		$out = [];
		foreach ($map as $k => $v) {
			if ($this->offsetExists($k)) {
				if (!$v) {
					$v = $k;
				}
				$out[$v] = $this->data[$k];
			}
		}
		return $out;
	}

	public function __toString()
	{
		return json_encode($this->toArray());
	}

	/**
	 * ARRAY-ACCESS INTERFACE **************************************************
	 * *************************************************************************
	 */

	/**
	 *
	 * @param string $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->data);
	}

	/**
	 * Return value from array
	 *
	 * @param string $offset
	 * @return string
	 * @throws AresException
	 */
	public function offsetGet($offset)
	{
		if ($this->offsetExists($offset)) {
			return $this->data[$offset];
		}

		throw new UndefinedOffsetException('Undefined offset: ' . $offset);
	}

	/**
	 * @param string $offset
	 * @param string $value
	 * @return string
	 */
	public function offsetSet($offset, $value)
	{
		return $this->data[$offset] = $value;
	}

	/**
	 * Remove value from array
	 *
	 * @param string $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->data[$offset]);
	}

	/**
	 * ITERATOR INTERFACE ******************************************************
	 * *************************************************************************
	 */

	/**
	 * Current value
	 *
	 * @return mixed
	 */
	public function current()
	{
		return current($this->data);
	}

	/**
	 * Current key
	 *
	 * @return string
	 */
	public function key()
	{
		return key($this->data);
	}

	/**
	 * Next value
	 *
	 * @return string
	 */
	public function next()
	{
		return next($this->data);
	}

	/** @return void */
	public function rewind()
	{
		reset($this->data);
	}

	/** @return bool */
	public function valid()
	{
		return array_key_exists($this->key(), $this->data);
	}

	/**
	 * COUNTABLE INTERFACE *****************************************************
	 * *************************************************************************
	 */

	/** @return int */
	public function count()
	{
		return count($this->data);
	}
}
