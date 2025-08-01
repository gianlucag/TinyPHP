<?php

class Date
{
    public static function Now()
    {
        return date("Y-m-d H:i:s");
    }

    public static function Format($timestamp, $outFormat)
    {
        $d = DateTime::createFromFormat("Y-m-d H:i:s", $timestamp);
        return $d->format($outFormat);
    }

    public static function FormatYMD($timestamp)
    {
        return self::Format($timestamp, "Y-m-d");
    }

    public static function FormatYMDHis($timestamp)
    {
        return self::Format($timestamp, "Y-m-d H:i:s");
    }

    public static function Parse($timestamp, $inFormat)
    {
        $d = DateTime::createFromFormat($inFormat, $timestamp);
        return $d->format("Y-m-d H:i:s");
    }

    public static function ParseYMD($timestamp)
    {
        return self::Parse($timestamp, "Y-m-d");
    }

    public static function ParseYMDHis($timestamp)
    {
        return self::Parse($timestamp, "Y-m-d H:i:s");
    }

    public static function AddHours($timestamp, $hours) {
        $d = new DateTime($timestamp);
        $d->modify("+$hours hours");
        return $d->format("Y-m-d H:i:s");
    }

    public static function SubHours($timestamp, $hours) {
        $d = new DateTime($timestamp);
        $d->modify("-$hours hours");
        return $d->format("Y-m-d H:i:s");
    }
    
    public static function AddDays($timestamp, $days) {
        $d = new DateTime($timestamp);
        $d->modify("+$days days");
        return $d->format("Y-m-d H:i:s");
    }

    public static function SubDays($timestamp, $days) {
        $d = new DateTime($timestamp);
        $d->modify("-$days days");
        return $d->format("Y-m-d H:i:s");
    }

    public static function AddMonths($timestamp, $howmany) {
        $d = new DateTime($timestamp);
        $d->modify("+$howmany months");
        return $d->format("Y-m-d H:i:s");
    }

    public static function SubMonths($timestamp, $howmany) {
        $d = new DateTime($timestamp);
        $d->modify("-$howmany months");
        return $d->format("Y-m-d H:i:s");
    }

    public static function AddYears($timestamp, $howmany) {
        $d = new DateTime($timestamp);
        $d->modify("+$howmany years");
        return $d->format("Y-m-d H:i:s");
    }

    public static function SubYears($timestamp, $howmany) {
        $d = new DateTime($timestamp);
        $d->modify("-$howmany years");
        return $d->format("Y-m-d H:i:s");
    }

    public static function DiffDays($timestamp1, $timestamp2) {
        $d1 = new DateTime($timestamp1);
        $d2 = new DateTime($timestamp2);
        $diff = $d1->diff($d2);
        return $diff->days;
    }
}

?>