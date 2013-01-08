<?php

namespace NajiDev\Common\Helper;

use \NajiDev\Common\Exception\InvalidArgumentException;


class StringHelper
{
	/**
	 * Strip a string from the end of a string
	 *
	 * @param string $str      the input string
	 * @param string $remove   string to remove
	 *
	 * @throws \NajiDev\Common\Exception\InvalidArgumentException
	 * @return string the modified string
	 */
	public static function trimStringRight($str, $remove)
	{
		if (!is_string($str))
			throw new InvalidArgumentException('$str has to be a string');

		if (!is_string($remove))
			throw new InvalidArgumentException('$remove has to be a string');

		$len    = strlen($remove);
		$offset = strlen($str) - $len;

		while(0 < $offset && strpos($str, $remove, $offset) === $offset)
		{
			$str    = substr($str, 0, $offset);
			$offset = strlen($str) - $len;
		}

		return $str;
	}
}