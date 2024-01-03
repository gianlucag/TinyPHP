<?php

class Logger
{
    private static $dir = null; 

    public static function Init($dir)
    {
        self::$dir = $dir;
    }

    public static function Write($sink, $msg)
    {
        $logline = "\n".date("[Y-m-d H:i:s]")." [".$sink."] ".json_encode($msg);
        $logfile = self::$dir."/".date("Ymd").".log";
        if (!is_dir(self::$dir))
        {
            mkdir(self::$dir, 0777, true);
        }
        file_put_contents($logfile, $logline, FILE_APPEND);
    }
}

?>