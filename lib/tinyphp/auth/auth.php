<?php

interface AuthUserInterface {
    public function Verify($username, $password); // returns token on success, false on failure/denied
    public function GetUserId($username); // returns the user id
    public function GetUserInfo($id); // returns the user object
    public function SetNewPassword($id, $password); // set a new password
    public function GetToken2fa($username); // get the 2FA token
    public function SetToken2fa($id, $token); // set the 2FA token
    public function SetAccountId($id, $accountid); // set the account id
}

interface AuthSessionInterface {
    public function AddSession($id, $token, $created);
    public function DeleteSessions($id); // if id is not specified, delete all sessions of current user
    public function DeleteSession($token);
    public function GetSessionId($token); // returns the session id
    public function TruncateSessions(); // delete all sessions of all users
    public function DeleteExpiredSessions($createdBefore); // delete sessions created before the given timestamp
}

class Auth
{
    private static $cookieName = null;
    private static $authMode = null;
    private static $expirationHours = null;
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
        self::$expirationHours = $config->expirationHours;
        self::$cookieName = isset($config->cookieName) ? $config->cookieName : null;

        if(self::$expirationHours)
        {
            $t = new DateTime();
            $t->modify("-".self::$expirationHours." hours");
            $createdBefore = $t->format("YmdHis");

            self::$authSessionPlugins[self::$sessionPluginName]->DeleteExpiredSessions($createdBefore);
        }

        $currentToken = Auth::GetCurrentToken();

        if($currentToken)
        {
            $userId = self::$authSessionPlugins[self::$sessionPluginName]->GetSessionId($currentToken);
            
            if(self::$authUserPlugin->GetUserInfo($userId)) // if user exists
            {
                self::$loggedUserId = $userId;
            }
        }
    }

    public static function Verify($username, $password)
    {  
        return self::$authUserPlugin->Verify($username, $password);
    }

    public static function GetToken2fa($username)
    {  
        return self::$authUserPlugin->GetToken2fa($username);
    }

    public static function Login($username)
    {  
        $userId = self::$authUserPlugin->GetUserId($username);   

        $token = bin2hex(random_bytes(16));

        if(!Auth::IsXAuthMode()) 
        {
            $expiry = time() + 300 * 24 * 60 * 60;
            setcookie(self::$cookieName, $token, [
                'expires' => $expiry,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }

        $t = new DateTime();
        $created = $t->format("YmdHis");

        self::$authSessionPlugins[self::$sessionPluginName]->AddSession($userId, $token, $created);
        self::$loggedUserId = $userId;
        return $token;
    }

    public static function LogoutThisSession()
    {  
        self::$authSessionPlugins[self::$sessionPluginName]->DeleteSession(Auth::GetCurrentToken());
        self::$loggedUserId = null;
    }

    public static function LogoutAllSessions($userId = null)
    {  
        self::$authSessionPlugins[self::$sessionPluginName]->DeleteSessions($userId ? $userId : self::$loggedUserId);
        self::$loggedUserId = null;
    }

    public static function IsLogged()
    {
        return self::$loggedUserId !== null;
    }

    public static function GetLoggedUserInfo()
    {  
        return self::$authUserPlugin->GetUserInfo(self::$loggedUserId);
    }

    public static function GetUserInfo($username)
    {  
        $userid = self::$authUserPlugin->GetUserId($username);
        return self::$authUserPlugin->GetUserInfo($userid);
    }

    public static function SetNewPassword($userid, $password)
    {  
        return self::$authUserPlugin->SetNewPassword($userid, $password);
    }

    public static function SetUserAccountId($userid, $accountid)
    {  
        return self::$authUserPlugin->SetAccountId($userid, $accountid);
    }

    public static function SetToken2fa($token)
    {  
        return self::$authUserPlugin->SetToken2fa(self::$loggedUserId, $token);
    }

    public static function TruncateSessions()
    {  
        self::$authSessionPlugins[self::$sessionPluginName]->TruncateSessions();
    }
}

?>