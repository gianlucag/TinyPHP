<?php
session_start();
error_reporting(E_ALL);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, x-auth-token");

define("TINYPHP_ROOT", __DIR__);

ob_start();

class TinyPHPmodule
{
    const CONFIG = 'CONFIG';
    const API = 'API';
    const DB = 'DB';
    const AUTH = 'AUTH';
    const LOGGER = 'LOGGER';
    const CRYPT = 'CRYPT';
    const DICTIONARY = 'DICTIONARY';
    const DOWNLOAD = 'DOWNLOAD';
    const UPLOAD = 'UPLOAD';
    const DATE = 'DATE';
    const INPUT = 'INPUT';
    const OUTPUT = 'OUTPUT';
    const CURRENCY = 'CURRENCY';
    const MAIL = 'MAIL';
    const SPREADSHEET = 'SPREADSHEET';
    const CAPTCHA = 'CAPTCHA';
    const PAYMENT = 'PAYMENT';
    const QRCODE = 'QRCODE';
    const TOTP = 'TOTP';
}

class TinyPHP
{
    private static $routes = null;
    private static $root = null;
    private static $page404 = null;
    private static $page500 = null;
    private static $pageMaintenance = null;
    private static $maintenanceAllowedIPAddress = null;
    private static $routeParams = null;

    public static function EnableModule($module, $libraryFolderPath = null)
    {
        switch($module)
        {
            case TinyPHPmodule::CONFIG:
                require_once("tinyphp/config.php");
                break;
            case TinyPHPmodule::API:
                require_once("tinyphp/api.php");
                break;
            case TinyPHPmodule::DB:
                require_once("tinyphp/db.php");
                break;
            case TinyPHPmodule::AUTH:
                require_once("tinyphp/auth/auth.php");
                require_once("tinyphp/auth/authPluginDbSession.php");
                require_once("tinyphp/auth/authPluginDbUser.php");
                break;
            case TinyPHPmodule::LOGGER:
                require_once("tinyphp/logger.php");
                break;
            case TinyPHPmodule::CRYPT:
                require_once("tinyphp/crypt.php");
                break;
            case TinyPHPmodule::DICTIONARY:
                require_once("tinyphp/dictionary.php");
                break;
            case TinyPHPmodule::DOWNLOAD:
                require_once("tinyphp/download.php");
                break;
            case TinyPHPmodule::UPLOAD:
                require_once("tinyphp/upload.php");
                break;
            case TinyPHPmodule::DATE:
                require_once("tinyphp/date.php");
                break;
            case TinyPHPmodule::INPUT:
                require_once("tinyphp/input.php");
                break;
            case TinyPHPmodule::OUTPUT:
                require_once("tinyphp/output.php");
                break;
            case TinyPHPmodule::CURRENCY:
                require_once("tinyphp/currency.php");
                break;
            case TinyPHPmodule::MAIL:
                require_once($libraryFolderPath."/src/Exception.php");
                require_once($libraryFolderPath."/src/PHPMailer.php");
                require_once($libraryFolderPath."/src/SMTP.php");
                require_once("tinyphp/mail.php");
                break;
            case TinyPHPmodule::SPREADSHEET:
                require_once($libraryFolderPath."/autoload.php");
                require_once("tinyphp/spreadsheet.php");
                break;
            case TinyPHPmodule::CAPTCHA:
                require_once("tinyphp/captcha.php");
                break;
            case TinyPHPmodule::PAYMENT:
                require_once($libraryFolderPath."/init.php");
                require_once("tinyphp/stripe.php");
                break;
            case TinyPHPmodule::QRCODE:
                require_once($libraryFolderPath."/phpqrcode.php");
                require_once("tinyphp/qrcodegenerator.php");
                break;
            case TinyPHPmodule::TOTP:
                require_once("tinyphp/totp.php");
                break;
            default:
                break;
        }
    }

    private static function GetClientIPAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }

        return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
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

    private static function Match($routes, $path)
    {
        foreach ($routes as $route => $destination)
        {
            $pattern = self::ConvertDynamicParamsToRegex($route);

            if (preg_match($pattern, $path, $matches))
            {
                array_shift($matches);

                $paramNames = self::ExtractParamNames($route);
                $params = array_combine($paramNames, $matches);

                return (object)[
                    'destination' => $destination,
                    'params' => $params
                ];
            }
        }
        return null;
    }

    private static function ConvertDynamicParamsToRegex($route)
    {
        $escapedRoute = preg_quote($route, '/');
        $pattern = '/^' . preg_replace('/\\\:([a-zA-Z0-9_]+)/', '([^\/]+)', $escapedRoute) . '$/';
        return $pattern;
    }

    private static function ExtractParamNames($route) {
        preg_match_all('/:(\w+)/', $route, $matches);
        return $matches[1];
    }

    public static function Run()
    {
        if($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET')
        {
            http_response_code(200);
            exit;
        }

        $requestUri = $_SERVER['REQUEST_URI'];
        $path = self::GetPathFromRequestUri($requestUri);

        if(self::$maintenanceAllowedIPAddress && self::$maintenanceAllowedIPAddress != self::GetClientIPAddress())
        {
            self::RunMaintenance();
        }
        else
        {
            $matchedRoute = self::Match(self::$routes, $path);

            if ($matchedRoute)
            {
                self::$routeParams = $matchedRoute->params;
                $page = $matchedRoute->destination;
    
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

    public static function GetRouteParam($paramName)
    {
        return isset(self::$routeParams[$paramName]) ? self::$routeParams[$paramName] : null;
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

    public static function Register500($controller)
    {
        self::$page500 = $controller;
    }

    public static function RegisterMaintenance($controller)
    {
        self::$pageMaintenance = $controller;
    }

    public static function EnableMaintenance($allowedIPAddress)
    {
        self::$maintenanceAllowedIPAddress = $allowedIPAddress;
    }

    public static function ThrowError500($msg = null)
    {
        if (self::$page500 && file_exists(self::$page500))
        {
            include_once(self::$page500);
            die();
        }
    }
}

?>
