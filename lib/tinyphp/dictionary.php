<?php

class Dictionary
{
    private static $dictionaries = [];
    private static $lang = null; 
    private static $defaultLang = null; 

    public static function Init($defaultLang)
    {
        self::$defaultLang = $defaultLang;
    }

    public static function Translate($tag, $values = [])
    {
        $dictionary = self::$dictionaries[self::$lang];
        $txt = isset($dictionary->$tag) ? $dictionary->$tag : "???";
        $txt = str_replace('%', '%s', $txt);
        return vsprintf($txt, $values);
    }

    public static function GetLanguage()
    {
        return self::$lang;
    }

    public static function SetLanguage($lang = null)
    {
        if($lang)
        {
            self::$lang = $lang;
        }
        else
        {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            $lang = array_key_exists($lang, self::$dictionaries) ? $lang : self::$defaultLang;
            self::$lang = $lang;
        }
    }

    public static function Add($lang, $jsonFile)
    {
        $dictionary = json_decode(file_get_contents($jsonFile));
        self::$dictionaries[$lang] = $dictionary;
    }
}

function txt($tag, $values = [])
{
    return Dictionary::Translate($tag, $values);
}

function etxt($tag, $values = [])
{
    echo Dictionary::Translate($tag, $values);
}

?>