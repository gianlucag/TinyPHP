<?php

class AuthPluginDbSession implements AuthSessionInterface
{
    private $config = null;

    public function Init($config)
    {
        $this->config = $config;
    }

    public function AddSession($id, $token)
    {
        $query = "INSERT INTO ".$this->config->tableName." (".$this->config->sessionIdFieldName.", ".$this->config->tokenFieldName.") VALUES (?, ?);";
        Db::Query($query, [$id, $token]);
    }

    public function DeleteSessions($id)
    {
        $query = "DELETE FROM ".$this->config->tableName." WHERE ".$this->config->sessionIdFieldName." = ?";
        Db::Query($query, [$id]);
    }

    public function DeleteSession($token)
    {
        $query = "DELETE FROM ".$this->config->tableName." WHERE ".$this->config->tokenFieldName." = ?";
        Db::Query($query, array($token));
    }

    public function GetSessionId($token)
    {
        $query = "SELECT * FROM ".$this->config->tableName." WHERE ".$this->config->tokenFieldName." = ?";
        $res = Db::Query($query, [$token]);
        if(count($res) != 1) return null;
        return $res[0][$this->config->sessionIdFieldName];
    }

    public function TruncateSessions()
    {
        $query = "TRUNCATE TABLE ".$this->config->tableName;
        Db::Query($query, []);
    }
}

?>