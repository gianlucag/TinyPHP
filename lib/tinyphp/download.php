<?php

class Download
{
    public static function Start($filename, $mimeType, $contentBinary)
    {
        ob_end_clean();
        header('Content-Type: '.$mimeType);
        header('Content-Disposition: attachment; filename="'. urlencode($filename).'"');
        echo $contentBinary;
        die();
    }
}

?>