<?php

class API
{
	private static $routes = null;

	private static function AddHeaders()
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, x-auth-token");
	}

	public static function Error($statusCode = 500, $errorData = null)
	{
		ob_end_clean();
		self::AddHeaders();
		http_response_code($statusCode);
		if ($errorData)
			echo $errorData;
		die();
	}

	public static function Ok($data = null)
	{
		ob_end_clean();
		self::AddHeaders();
	
		$json = json_encode($data, JSON_BIGINT_AS_STRING);
	
		if ($json === false) {
			http_response_code(500);
			echo json_last_error_msg();
			die();
		}
	
		echo $json;
		die();
	}

	public static function Post()
	{
		$post = json_decode(file_get_contents('php://input'), false, 16);
		if (!$post === null)
			self::Error(500, "Could not decode POST data");
		return $post;
	}

	public static function Get()
	{
		return (object) $_GET;
	}
}

?>