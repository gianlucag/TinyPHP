<?php

class QRCodeGenerator
{
    public static function GetPng($data)
    {
        QRcode::png($data, false, QR_ECLEVEL_L, 10);
    }

    public static function GetImage($data)
    {
        return QRcode::image($data, false, QR_ECLEVEL_L, 10);
    }
}

?>