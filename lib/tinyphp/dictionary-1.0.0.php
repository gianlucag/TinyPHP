<?php

class Dictionary
{
    private static $dictionaries = [];
    private static $lang = null; 

    public static function Translate($tag)
    {
        $dictionary = self::$dictionaries[self::$lang];
        return isset($dictionary->$tag) ? $dictionary->$tag : "???";
    }

    public static function SetLanguage($lang)
    {
        self::$lang = $lang;
    }

    public static function Add($lang, $jsonFile)
    {
        $dictionary = json_decode(file_get_contents($jsonFile));
        self::$dictionaries[$lang] = $dictionary;
    }
}

function Txt($tag)
{
    echo Dictionary::Translate($tag);
}

?>