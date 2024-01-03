<?php
session_start();
error_reporting(E_ALL);

include_once("tinyphp/config-1.0.0.php");
include_once("tinyphp/api-2.0.0.php");
include_once("tinyphp/db-2.2.0.php");
include_once("tinyphp/auth-6.0.0.php");
include_once("tinyphp/dbauth-1.0.0/dbauth.php");
include_once("tinyphp/logger-2.1.0.php");
include_once("tinyphp/crypt-1.1.0.php");
include_once("tinyphp/dictionary-1.0.0.php");

class TinyPHP
{
    private static $routes = null;
    private static $root = null;
    private static $page404 = null;

    private static function GetPathFromRequestUri($requestUri)
    {
        $parsedUrl = parse_url($requestUri);
        return isset($parsedUrl['path']) ? $parsedUrl['path'] : '/';
    }

    private static function Run404()
    {
        if (file_exists(self::$page404))
        {
            include_once(self::$page404);
        }
    }

    public static function Run()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $path = self::GetPathFromRequestUri($requestUri);

        if (isset(self::$routes[$path]))
        {
            $page = self::$routes[$path];

            if (file_exists($page))
            {
                include_once($page);
            }
            else
            {
                self::Run404();
            }
        }
        else
        {
            self::Run404();
        }
    }

    public static function RegisterRoute($path, $controller)
    {
        self::$routes[self::$root.$path] = $controller;
    }

    public static function RegisterRoot($rootPath)
    {
        self::$root = $rootPath;
    }

    public static function Register404($controller)
    {
        self::$page404 = $controller;
    }
}

?>
