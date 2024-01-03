<?php

class Auth
{
    public static $cookieName = null;
    public static $authMode = null;

    private static function IsXAuthMode()
    {
        return self::$authMode == "xauth";
    }

    private static function GetTokenFromCookie()
    {
        if(isset($_COOKIE[self::$cookieName]))
        {
            return $_COOKIE[self::$cookieName];
        }
        return null;
    }

    private static function GetTokenFromXAuth()
    {
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);
        if(isset($headers['x-auth-token']))
        {
            return $headers['x-auth-token'];
        }
        return null;
    }

    public static function Init($authMode, $cookieName = null)
    {
        self::$authMode = $authMode == "xauth" ? "xauth" : "cookie";
        self::$cookieName = $cookieName;
    }
    
    public static function GetCurrentToken()
    {
        if(Auth::IsXAuthMode())
        {
            $token = Auth::GetTokenFromXAuth();
        }
        else
        {
            $token = Auth::GetTokenFromCookie();
        }

        return $token;
    }

    public static function Login($password, $storedPassword)
    {  
        if(password_verify($password, $storedPassword))
        {
            $token = bin2hex(random_bytes(16));

            if(!Auth::IsXAuthMode()) 
            {
                $expiry = time() + 300 * 24 * 60 * 60;
                setcookie(self::$cookieName, $token, $expiry);
            }
    
            return $token;
        }
        else
        {
            return false;
        }
    }

    public static function GetHashedPassword($password)
    { 
        return password_hash($password, PASSWORD_DEFAULT);
    }
}

?>