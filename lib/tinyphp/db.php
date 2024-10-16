<?php

class Db
{
	private static $ctx = null;
	private static $config = null;
	private static $error = null;

    private function __construct() {}
    private function __clone() {}

    public static function Init($config, $errorCallback = null)
    {
    	self::$config = $config;
       	self::$error = $errorCallback;
    }

	private static function GetInstance()
	{
		if(self::$ctx == NULL)
		{
			$connectionStr = "mysql:";
			$connectionStr .= "host=".self::$config->host.";";
			$connectionStr .= "dbname=".self::$config->name.";";
			self::$ctx = new PDO($connectionStr, self::$config->user, self::$config->pass);
			self::$ctx->exec("SET CHARACTER SET utf8mb4");
    		self::$ctx->exec("SET NAMES utf8mb4");
			self::$ctx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			self::$ctx->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			self::$ctx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		
		return self::$ctx;
	}

	public static function Query($query, $data)
	{
		try
		{
			$db = Db::GetInstance();
			$stmt = $db->prepare($query);
			$stmt->execute($data);
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $res;
		}
		catch (Exception $e)
		{
			$msg = [
				"query" => $query,
				"error" =>  $e->getMessage()
			];

			if(self::$error) call_user_func(self::$error, $msg);
		}
	}
}

?>