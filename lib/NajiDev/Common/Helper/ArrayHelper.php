<?php

namespace NajiDev\Common\Helper;


class ArrayHelper
{
	/**
	 * Iterates over each value in the input array passing them to the callback function (recursively). If the callback
	 * function returns true, the current value from input is returned into the result array. Array keys are preserved.
	 *
	 * @param array $input
	 * @param callable $callback
	 * @return array
	 */
	public static function array_filter_recursive(array $input, $callback = null)
	{
		$result = array();

		foreach ($input as $key => $value)
		{
			if (is_array($value))
				$value = self::array_filter_recursive($value, $callback);

			if (call_user_func($callback, $value))
				$result[$key] = $value;
		}

		return $result;
	}
}