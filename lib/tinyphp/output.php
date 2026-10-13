<?php

class Output
{
    public static function e($text)
    {
		return htmlspecialchars((string)$text, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
    }
}

?>
