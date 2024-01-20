<?php

class Input
{
    public static function Clean($input)
    {
        return htmlspecialchars(trim($input));
    }
}

?>