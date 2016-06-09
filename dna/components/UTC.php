<?php

/**
 * General utility class for methods that help enforce UTC time handling
 */
class UTC
{

	/**
	 * Converts the time string treating it as a UTC time string, returning the corresponding UTC unix timestamp.
	 * @param type $my_time_string
	 * @return null
	 * @throws Exception
	 */
	static public function gmstrtotime($my_time_string)
	{
		if (empty($my_time_string))
			return null;
		$previous_def_tz = date_default_timezone_get();
		if (empty($previous_def_tz))
			throw new Exception("No default timezone is set");
		date_default_timezone_set("UTC");
		$time = strtotime($my_time_string . " UTC");
		date_default_timezone_set($previous_def_tz);
		return $time;
	}

	/**
	 * Returns the current UTC unix timestamp
	 * @return type
	 */
	static public function gmtime()
	{
		return (int) gmdate('U');
	}

}
