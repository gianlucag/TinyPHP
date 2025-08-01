<?php

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
    
    public static function Translate($tag, $values = [])
    {
        $dictionary = isset(self::$dictionaries[self::$lang]) ? self::$dictionaries[self::$lang] : null;
        $txt = isset($dictionary->$tag) ? $dictionary->$tag : "{{".$tag."}}";
        $txt = str_replace('%', '%s', $txt);
        return $values == [] ? $txt : vsprintf($txt, $values);
    }

    public static function GetLanguage()
    {
        return self::$lang;
    }

    public static function DetectLanguage()
    {
        return isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
    }

    public static function SetLanguage($lang)
    {
        self::$lang = array_key_exists($lang, self::$dictionaries) ? $lang : self::$defaultLang;
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