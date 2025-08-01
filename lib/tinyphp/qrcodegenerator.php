<?php

class QRCodeGenerator
{
    public static function GetPng($data)
    {
        QRcode::png($data, false, QR_ECLEVEL_L, 10);
    }

    public static function GetImage($data)
    {
        ob_start();
        QRcode::png($data, null, QR_ECLEVEL_L, 10);
        $imageData = ob_get_clean();
        return 'data:image/png;base64,' . base64_encode($imageData);
    }
}

?>