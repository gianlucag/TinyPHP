<?php

interface AuthUserInterface {
    public function Login($username, $password); // returns token on success, false on failure/denied
    public function GetUserId($username); // returns the user id
    public function GetUserInfo($id); // returns the user object
}

interface AuthSessionInterface {
    public function AddSession($id, $token);
    public function DeleteSessions($id); // if id is not specified, delete all session of current user
    public function DeleteSession($token);
    public function GetSessionId($token); // returns the session id
    public function TruncateSessions(); // delete all sessions of all users
}

class Auth
{
    private static $cookieName = null;
    private static $authMode = null;
    private static $authUserPlugin = null;
    private static $authSessionPlugins = [];
    private static $loggedUserId = null;
    private static $sessionPluginName = null;

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
  
    public static function SetSessionPlugin($pluginName)
    {
        self::$sessionPluginName = $pluginName;
    }

    public static function AddSessionPlugin($pluginName, $authSessionPlugin)
    {
        self::$authSessionPlugins[$pluginName] = $authSessionPlugin;
    }

    public static function Init($config, $authUserPlugin)
    {
        self::$authUserPlugin = $authUserPlugin;
        self::$authMode = $config->method == "xauth" ? "xauth" : "cookie";
        self::$cookieName = isset($config->cookieName) ? $config->cookieName : null;

        $currentToken = Auth::GetCurrentToken();

        if($currentToken)
        {
            self::$loggedUserId = self::$authSessionPlugins[self::$sessionPluginName]->GetSessionId($currentToken);
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

            self::$authSessionPlugins[self::$sessionPluginName]->AddSession($userId, $token);
            self::$loggedUserId = $userId;
            return $token;
        }

        return false;
    }

    public static function LogoutThisSession()
    {  
        self::$authSessionPlugins[self::$sessionPluginName]->DeleteSession(Auth::GetCurrentToken());
    }

    public static function LogoutAllSessions($userId = null)
    {  
        self::$authSessionPlugins[self::$sessionPluginName]->DeleteSessions($userId ? $userId : self::$loggedUserId);
    }

    public static function IsLogged()
    {
        return self::$loggedUserId !== null;
    }

    public static function GetLoggedUserInfo()
    {  
        return self::$authUserPlugin->GetUserInfo(self::$loggedUserId);
    }

    public static function SetNewPassword($password)
    {  
        return self::$authUserPlugin->SetNewPassword(self::$loggedUserId, $password);
    }

    public static function TruncateSessions()
    {  
        self::$authSessionPlugins[self::$sessionPluginName]->TruncateSessions();
    }
}

?>