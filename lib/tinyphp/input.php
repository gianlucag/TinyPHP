<?php

class Input
{
    public static function Clean($input)
    {
        if (is_string($input)) {
            $input = trim($input);
            return $input === '' ? null : $input;
        }

        return $input;
    }

    public static function IsEmailValid($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

?>