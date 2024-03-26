<?php
session_start();
error_reporting(E_ALL);
header('Cache-Control: no cache');

define("TINYPHP_ROOT", __DIR__);

// enabled modules
include_once("tinyphp/config.php");
include_once("tinyphp/api.php");
include_once("tinyphp/db.php");
include_once("tinyphp/auth.php");
include_once("tinyphp/dbauth/dbauth.php");
include_once("tinyphp/logger.php");
include_once("tinyphp/crypt.php");
include_once("tinyphp/dictionary.php");
include_once("tinyphp/download.php");
include_once("tinyphp/upload.php");
include_once("tinyphp/date.php");
include_once("tinyphp/input.php");
include_once("tinyphp/currency.php");
//include_once("tinyphp/mail/mail.php");
//include_once("tinyphp/spreadsheet/spreadsheet.php");
//include_once("tinyphp/captcha.php");
//include_once("tinyphp/stripe/stripe.php");
//include_once("tinyphp/qrcodegenerator.php");

class TinyPHP
{
    private static $routes = null;
    private static $root = null;
    private static $page404 = null;
    private static $pageMaintenance = null;
    private static $maintenanceAllowedIPAddress = null;
    
    private static function GetClientIPAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    private static function GetPathFromRequestUri($requestUri)
    {
        $parsedUrl = parse_url($requestUri);
        return isset($parsedUrl['path']) ? $parsedUrl['path'] : '/';
    }

    private static function Run404()
    {
        if (self::$page404 && file_exists(self::$page404))
        {
            include_once(self::$page404);
        }
    }

    private static function RunMaintenance()
    {
        if (self::$pageMaintenance && file_exists(self::$pageMaintenance))
        {
            include_once(self::$pageMaintenance);
        }
    }

    public static function Run()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $path = self::GetPathFromRequestUri($requestUri);

        if(self::$maintenanceAllowedIPAddress && self::$maintenanceAllowedIPAddress != self::GetClientIPAddress())
        {
            self::RunMaintenance();
        }
        else
        {
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

    public static function RegisterMaintenance($controller)
    {
        self::$pageMaintenance = $controller;
    }

    public static function EnableMaintenance($allowedIPAddress)
    {
        self::$maintenanceAllowedIPAddress = $allowedIPAddress;
    }
}

?>
