<?php

interface AuthUserInterface {
    public function Login($username, $password); // returns token on success, false on failure/denied
    public function GetUserId($username); // returns the user id
    public function GetUserInfo($id); // returns the user object
}

interface AuthSessionInterface {
    public function AddSession($id, $token);
    public function DeleteSessions($id);
    public function DeleteSession($token);
    public function GetSessionId($token); // returns the session id
}

class Auth
{
    private static $cookieName = null;
    private static $authMode = null;
    private static $authUserPlugin = null;
    private static $authSessionPlugin = null;
    private static $loggedUserId = null;

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

    private static function GetCurrentToken()
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

    public static function Init($config, &$authSessionPlugin, &$authUserPlugin = null)
    {
        self::$authUserPlugin = $authUserPlugin;
        self::$authSessionPlugin = $authSessionPlugin;
        self::$authMode = $config->method == "xauth" ? "xauth" : "cookie";
        self::$cookieName = isset($config->cookieName) ? $config->cookieName : null;
        
        $currentToken = Auth::GetCurrentToken();

        if($currentToken)
        {
            self::$loggedUserId = self::$authSessionPlugin->GetSessionId($currentToken);
        }
    }


    public static function Login($username, $password)
    {  
        $res = self::$authUserPlugin->Login($username, $password);

        if($res)
        {
            $token = bin2hex(random_bytes(16));

            if(!Auth::IsXAuthMode()) 
            {
                $expiry = time() + 300 * 24 * 60 * 60;
                setcookie(self::$cookieName, $token, $expiry);
            }
    
            $userId = self::$authUserPlugin->GetUserId($username);            
            self::$authSessionPlugin->AddSession($userId, $token);
            return $token;
        }

        return false;
    }

    public static function LogoutThisSession()
    {  
        self::$authSessionPlugin->DeleteSession(Auth::GetCurrentToken());
    }

    public static function LogoutAllSessions()
    {  
        self::$authSessionPlugin->DeleteSessions(self::$loggedUserId);
    }

    public static function IsLogged()
    {
        return self::$loggedUserId !== null;
    }

    public static function GetLoggedUserInfo()
    {  
        return self::$authUserPlugin->GetUserInfo(self::$loggedUserId);
    }
}

?>