<?php

class Config
{
	private static $data = null;
	private static $error = null;

	public static function Init($path, $errorCallback = null)
	{
		$file = file_get_contents($path);
		self::$data = json_decode($file);
		self::$error = $errorCallback;
	}

	public static function GetField($field)
	{
		if(isset(self::$data->$field))
		{
			return self::$data->$field;
		}
		else
		{
			if(self::$error) call_user_func(self::$error, "Cannot find config parameter '".$field."'");
		}
	}
}

?>