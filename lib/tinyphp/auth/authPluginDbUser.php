<?php

class AuthPluginDbUser implements AuthUserInterface
{
    private static $config = null;
    private static $passwordCheckFunction = null;

    public function Init($config, $passwordCheckFunction = null)
    {
        self::$config = $config;
        self::$passwordCheckFunction = $passwordCheckFunction;
    }

    public function Login($username, $password)
    {
        $query = "SELECT * FROM ".self::$config->tableName." WHERE ".self::$config->usernameFieldName." = ?";
        $res = Db::Query($query, [$username]);
        if(count($res) != 1) return false;
        $storedPassword = $res[0][self::$config->passwordFieldName];

        if(self::$passwordCheckFunction)
        {
            $res = call_user_func(self::$passwordCheckFunction, $password, $storedPassword);
        }
        else
        {
            $res = password_verify($password, $storedPassword);
        }

        return $res;
    }

    public function GetUserId($username)
    {
        $query = "SELECT * FROM ".self::$config->tableName." WHERE ".self::$config->usernameFieldName." = ?";
        $res = Db::Query($query, [$username]);
        if(count($res) != 1) return false;
        return $res[0][self::$config->userIdFieldName];
    }

    public function GetUserInfo($id)
    {
        $query = "SELECT * FROM ".self::$config->tableName." WHERE ".self::$config->userIdFieldName." = ?";
        $res = Db::Query($query, [$id]);
        if(count($res) != 1) return false;
        return (object)$res[0];
    }
}

?>