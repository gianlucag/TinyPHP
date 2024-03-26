<?php

/*
    Config section:

    "dictionary": {
        "default_language": "it",
        "languages": [
            {
                "lang": "it",
                "file": "dictionaries/it.json"
            },
            {
                "lang": "en",
                "file": "dictionaries/en.json"
            }
        ]
    }
*/

class Dictionary
{
    private static $dictionaries = [];
    private static $lang = null; 
    private static $defaultLang = null; 

    public static function Init($config)
    {
        self::$defaultLang = $config->default_language;

        for($l = 0; $l < count($config->languages); $l++)
        {
            $language = $config->languages[$l];
            $dictionary = json_decode(file_get_contents($language->file));
            self::$dictionaries[$language->lang] = $dictionary;
        }
    }

    public static function Add($lang, $jsonFile)
    {

    }
    
    public static function Translate($tag, $values = [])
    {
        $dictionary = self::$dictionaries[self::$lang];
        $txt = isset($dictionary->$tag) ? $dictionary->$tag : "???";
        $txt = str_replace('%', '%s', $txt);
        return $values == [] ? $txt : vsprintf($txt, $values);
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
            $lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : self::$defaultLang;
            $lang = array_key_exists($lang, self::$dictionaries) ? $lang : self::$defaultLang;
            self::$lang = $lang;
        }
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