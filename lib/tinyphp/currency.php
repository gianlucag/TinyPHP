<?php

class Currency
{
    private static $format = null;
    private static $decimalDigits = null;

    public static function Init($format, $decimalDigits)
    {
        self::$format = $format;
        self::$decimalDigits = $decimalDigits;
    }

    public static function Format($value, $options = null)
    {
        $value = (int)$value;
        $f = $value % (10 ** self::$decimalDigits);
        $i = ($value - $f) / (10 ** self::$decimalDigits);

        $out = isset($options["format"]) ? $options["format"] : self::$format;
        $fractDigits = isset($options["fractDigits"]) ? $options["fractDigits"] : self::$decimalDigits;

        $out = str_replace("#i#", $i, $out);
        $out = str_replace("#f#", substr(sprintf("%0".self::$decimalDigits."d", $f), 0, $fractDigits), $out);
        return $out;
    }

    public static function Parse($input)
    {
        $filtered = preg_replace('/[^\d,\.]/', '', $input);
        if (strpos($filtered, ',') !== false && strpos($filtered, '.') !== false) {
            return false;
        }
        $uniform = str_replace(',', '.', $filtered);
        if (!is_numeric($uniform)) {
            return false;
        }
        $numeric = (float)$uniform;
        $cents = round($numeric * (10 ** self::$decimalDigits));
        return (int)$cents;
    }
}

?>