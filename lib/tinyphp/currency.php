<?php

class Currency
{
    private static $format = null;

    public static function Init($format)
    {
        self::$format = $format;
    }

    public static function Show($value, $showCents = true)
    {
        $f = $value % 100;
        $m = ($value - $f) / 100;

        $out = self::$format;
        
        if($showCents == false)
        {
            $pos = strpos($out, '#');
            $out = substr($out, 0, $pos + 1);
        }

        $out = preg_replace('/#/', $m, $out, 1);
        $out = preg_replace('/#/', sprintf('%02d', $f), $out, 1);
        return $out;
    }
}

?>