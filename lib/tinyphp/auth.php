<?php

class Auth
{
    public static $cookieName = null;
    public static $authMode = null;
    public static $passwordCheckFunction = null;
    public static $passwordHashFunction = null;

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

    public static function Init($authMode, $cookieName = null, $passwordCheckFunction = null, $passwordHashFunction = null)
    {
        self::$authMode = $authMode == "xauth" ? "xauth" : "cookie";
        self::$cookieName = $cookieName;
        self::$passwordCheckFunction = $passwordCheckFunction;
        self::$passwordHashFunction = $passwordHashFunction;
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

    public static function ComputeHashPassword($password)
    {
        if(self::$passwordHashFunction)
        {
            $res = call_user_func(self::$passwordHashFunction, $password);
        }
        else
        {
            $res = password_hash($password, PASSWORD_DEFAULT);
        }

        return $res;
    }

    public static function Login($password, $storedPassword)
    {  
        if(self::$passwordCheckFunction)
        {
            $res = call_user_func(self::$passwordCheckFunction, $password, $storedPassword);
        }
        else
        {
            $res = password_verify($password, $storedPassword);
        }

        if($res)
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