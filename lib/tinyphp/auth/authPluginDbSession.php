<?php

class AuthPluginDbSession implements AuthSessionInterface
{
    private static $config = null;

    public function Init($config)
    {
        self::$config = $config;
    }

    public function AddSession($id, $token)
    {
        $query = "INSERT INTO ".self::$config->tableName." (".self::$config->sessionIdFieldName.", ".self::$config->tokenFieldName.") VALUES (?, ?);";
        Db::Query($query, [$id, $token]);
    }

    public function DeleteSessions($id)
    {
        $query = "DELETE FROM ".self::$config->tableName." WHERE ".self::$config->sessionIdFieldName." = ?";
        Db::Query($query, [$id]);
    }

    public function DeleteSession($token)
    {
        $query = "DELETE FROM ".self::$config->tableName." WHERE ".self::$config->tokenFieldName." = ?";
        Db::Query($query, array($token));
    }

    public function GetSessionId($token)
    {
        $query = "SELECT * FROM ".self::$config->tableName." WHERE ".self::$config->tokenFieldName." = ?";
        $res = Db::Query($query, [$token]);
        if(count($res) != 1) return null;
        return $res[0][self::$config->sessionIdFieldName];
    }
}

?>