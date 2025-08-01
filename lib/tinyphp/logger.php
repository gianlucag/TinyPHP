<?php

class Logger
{
    private static $configs = null; 

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

    public static function Init($configs)
    {
        self::$configs = $configs;
    }

    public static function Write($sink, $msg)
    {
        if(self::$configs === null)
        {
            throw new Exception("Logger not initialized");
        }

        $logConfig = null;
        foreach (self::$configs as $config)
        {
            if ($config->sink === $sink)
            {
                $logConfig = $config;
                break;
            }
        }

        if($logConfig === null)
        {
            throw new Exception("Sink ".$sink." not found");
        }

        $path = $logConfig->path;
        $rotateDaily = $logConfig->rotateDaily;

        $filename = $rotateDaily ? date("Ymd") . ".log" : "log.log";
        $logfile = $path . "/" . $filename;

        $logline = "\n".date("[Y-m-d H:i:s]")." [".self::GetClientIPAddress()."] [".$sink."] ".$msg;

        if (!is_dir($path))
        {
            mkdir($path, 0777, true);
        }
        file_put_contents($logfile, $logline, FILE_APPEND);
    }
}

?>