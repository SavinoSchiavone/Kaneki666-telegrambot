<?php


/**
* PHP class
*/
class phpfunc
{
	
	//See with what the string is starting
	public function startsWith($string, $array, $end)
	{
		if (is_int($end)) {
			if (in_array(substr($string, 0, $end), $array)) {
				return true;
			} else {
				return false;
			}
		} else {
			$error = "END ERROR";
			return $error;
		}
	}

}


?>