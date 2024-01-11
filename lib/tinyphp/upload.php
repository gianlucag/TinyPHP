<?php

class Upload
{
    public static function IsFileUploaded()
    {
        return isset($_FILES["file"]) && !empty($_FILES["file"]["name"]);
    }

    public static function GetFileSize()
    {
        return $filesize = $_FILES['file']['size'];
    }

    public static function GetFileExtension()
    {
        $filename = $_FILES["file"]["name"];
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    public static function GetUploadedFile()
    {
        return $_FILES['file']['tmp_name'];
    }
}

?>