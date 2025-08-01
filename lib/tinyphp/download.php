<?php

class Download
{
    public static function Start($filename, $mimeType, $contentBinary, $inline = false)
    {
        ob_end_clean();
        header('Content-Type: '.$mimeType);
        $disposition = $inline ? 'inline' : 'attachment';
        header('Content-Disposition: ' . $disposition . '; filename="'. urlencode($filename).'"');
        echo $contentBinary;
        die();
    }
}

?>