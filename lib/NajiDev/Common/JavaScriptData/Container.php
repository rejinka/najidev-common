<?php

namespace NajiDev\Common\JavaScriptData;

use
	\NajiDev\Common\Exception\ElementNotFoundException,
	\NajiDev\Common\Exception\InvalidArgumentException,
	\NajiDev\Common\Exception\UnsupportedException,
	\NajiDev\Common\Helper\ArrayHelper
;


class Container
{
	protected $data = array();

	/**
	 * Sets the value, if the does not exists yet
	 *
	 * @param $key string
	 * @param $value mixed
	 * @throws InvalidArgumentException
	 * @throws UnsupportedException
	 * @return void
	 */
	public function add($key, $value)
	{
		try
		{
			$this->getRecursive($key, $this->data);
		}
		catch (ElementNotFoundException $e)
		{
			$this->set($key, $value);
		}
	}

	/**
	 * Sets the value, not regarding whether the key does exist yet or it does not
	 *
	 * @param $key string
	 * @param $value mixed
	 * @throws InvalidArgumentException
	 * @throws UnsupportedException
	 * @return void
	 */
	public function set($key, $value)
	{
		if (!is_string($key))
			throw new InvalidArgumentException('First parameter has to be a string');

		if (!$this->isTypeSupported(gettype($value)))
			throw new UnsupportedException('First parameter has to be an int, a double, a bool or a string');

		try
		{
			// try to find the key, and set the value if this succeeds
			$this->getRecursive($key, $this->data);
			$this->data = $this->setRecursive($key, $value);
		}
		catch (ElementNotFoundException $e)
		{
			$this->data = array_merge_recursive($this->data, $this->setRecursive($key, $value));
		}
	}


	/**
	 * Returns the value of $key, if $key exists. Otherwise $defaultValue will be returned
	 *
	 * @param $key string
	 * @param $defaultValue
	 * @return mixed
	 */
	public function get($key, $defaultValue = null)
	{
		try
		{
			return $this->getRecursive($key, $this->data);
		}
		catch (ElementNotFoundException $e)
		{
			return $defaultValue;
		}
	}

	/**
	 * Removes the element with $key. The method does just nothing, if the key does not exist
	 *
	 * @param $key
	 * @return mixed
	 */
	public function remove($key)
	{
		try
		{
			$this->getRecursive($key, $this->data);

			$data = $this->removeRecursive($key, $this->data);
			$this->data = ArrayHelper::array_filter_recursive($data, function ($value)
			{
				return !empty($value);
			});
		}
		catch (ElementNotFoundException $e) { }
	}

	/**
	 * @return string json encoded string
	 */
	public function getTransformedData()
	{
		return json_encode($this->data);
	}

	/**
	 * Returns all stored data
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	private function removeRecursive($key, $data)
	{
		$key = explode('.', $key);

		if (1 === count($key))
		{
			unset($data[$key[0]]);
			return $data;
		}

		$first = array_shift($key);
		$data[$first] = $this->removeRecursive(implode('.', $key), $data[$first]);

		return $data;
	}

	private function setRecursive($key, $value)
	{
		$key = explode('.', $key);
		$data = array();

		if (1 === count($key))
			$data[$key[0]] = $value;
		else
		{
			$first = array_shift($key);
			$data[$first] = $this->setRecursive(implode('.', $key), $value);
		}

		return $data;
	}

	private function getRecursive($key, $data)
	{
		$key = explode('.', $key);

		if (1 === count($key))
		{
			if (!array_key_exists($key[0], $data))
				throw new ElementNotFoundException();

			return $data[$key[0]];
		}

		$first = array_shift($key);
		if (!array_key_exists($first, $data))
			throw new ElementNotFoundException;

		return $this->getRecursive(implode('.', $key), $data[$first]);
	}

	protected function isTypeSupported($type)
	{
		if ('string' === $type)
			return true;

		if ('boolean' === $type)
			return true;

		if ('integer' === $type)
			return true;

		if ('double' === $type)
			return true;

		return false;
	}
}
