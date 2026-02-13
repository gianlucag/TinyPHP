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

	public static function BuildSchema()
    {
		try {
			$schema = file_get_contents(self::$config->schema);
			$queries = array_filter(array_map('trim', explode(";", $schema)));
			foreach ($queries as $query) {
				if (!empty($query)) {
					self::Query($query, null);
				}
			}
		} catch (Exception $e) {
			if (self::$error) {
				call_user_func(self::$error, ["error" => $e->getMessage()]);
			} else {
				throw $e;
			}
		}
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
			self::$ctx->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			self::$ctx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		
		return self::$ctx;
	}

	public static function Query($query, $data)
	{
		$query = str_replace("{{prefix}}", self::$config->tablePrefix, $query);

		try
		{
			$db = Db::GetInstance();
			$stmt = $db->prepare($query);
			$stmt->execute($data);
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e)
		{
			if ($e->getCode() === '23000' &&  stripos($query, "DELETE") === 0) return false;

			$msg = [
				"query" => $query,
				"data" => $data,
				"error" =>  $e->getMessage()
			];

			if(self::$error) call_user_func(self::$error, $msg);

			if (self::GetInstance()->inTransaction())
			{
				throw $e;
			}
			
			return false;
		}
	}

	public static function Begin()
	{
		self::GetInstance()->beginTransaction();
	}
	
	public static function Commit()
	{
		$pdo = self::GetInstance();
		
		if ($pdo->inTransaction())
		{
			$pdo->commit();
		}
	}
	
	public static function Rollback()
	{
		$pdo = self::GetInstance();

		if ($pdo->inTransaction())
		{
			$pdo->rollBack();
		}
	}
}

?>