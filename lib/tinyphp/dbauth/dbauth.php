<?php

class DbAuth
{
    private static $config = null;
    private static $loggedUser = null;

    public static function Init($config)
    {
        self::$config = $config;

        $authToken = Auth::GetCurrentToken();
        $query = "SELECT * FROM ".self::$config->sessionTableName." WHERE ".self::$config->sessionTokenFieldName." = ?";
        $res = Db::Query($query, array($authToken));
        if(count($res) == 1)
        {
            $userid = $res[0][self::$config->sessionUserIdFieldName];
            $query = "SELECT * FROM ".self::$config->userTableName." WHERE ".self::$config->userIdFieldName." = ?";
            $res = Db::Query($query, array($userid));
            if(count($res) == 1)
            {
                self::$loggedUser = $res[0];
            }    
        }
    }

    public static function IsLogged()
    {
        return self::$loggedUser !== null;
    }

    public static function GetLoggedUser()
    {
        return (object)self::$loggedUser;
    }

    public static function Login($username, $password)
    {
        $query = "SELECT * FROM ".self::$config->userTableName." WHERE ".self::$config->userUsernameFieldName." = ?";
        $res = Db::Query($query, array($username));
        if(count($res) != 1) return false;

        $userid = $res[0][self::$config->userIdFieldName];
        $storedPassword = $res[0][self::$config->userPasswordFieldName];

        $authToken = Auth::Login($password, $storedPassword);

        if($authToken)
        {
            $query = "INSERT INTO ".self::$config->sessionTableName." (".self::$config->sessionUserIdFieldName.", ".self::$config->sessionTokenFieldName.") VALUES (?, ?);";
            Db::Query($query, array($userid, $authToken));
            self::$loggedUser = $res[0];
            return $authToken;   
        }
        else
        {
            return false;
        }
    }

    public static function LogoutAllClients()
    {
        $userid = self::$loggedUser[self::$config->userIdFieldName];

        $query = "DELETE FROM ".self::$config->sessionTableName." WHERE ".self::$config->sessionUserIdFieldName." = ?";
        $res = Db::Query($query, array($userid));
        if($res === false) return false;
        self::$loggedUser = null;
        return true;
    }

    public static function LogoutClient()
    {
        $authToken = Auth::GetCurrentToken();

        $query = "DELETE FROM ".self::$config->sessionTableName." WHERE ".self::$config->sessionTokenFieldName." = ?";
        $res = Db::Query($query, array($authToken));
        self::$loggedUser = null;
        return true;
    }
}

?>