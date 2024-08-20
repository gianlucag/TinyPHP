<?php

class AuthPluginDbUser implements AuthUserInterface
{
    private $config = null;
    private $passwordCheckFunction = null;
    private $passwordSetFunction = null;

    public function Init($config, $passwordCheckFunction = null, $passwordSetFunction = null)
    {
        $this->config = $config;
        $this->passwordCheckFunction = $passwordCheckFunction;
        $this->passwordSetFunction = $passwordSetFunction;
    }

    public function Login($username, $password)
    {
        $query = "SELECT * FROM ".$this->config->tableName." WHERE ".$this->config->usernameFieldName." = ?";
        $res = Db::Query($query, [$username]);
        if(count($res) != 1) return false;
        $storedPassword = $res[0][$this->config->passwordFieldName];

        if($this->passwordCheckFunction)
        {
            $res = call_user_func($this->passwordCheckFunction, $password, $storedPassword);
        }
        else
        {
            $res = password_verify($password, $storedPassword);
        }

        return $res;
    }

    public function GetUserId($username)
    {
        $query = "SELECT * FROM ".$this->config->tableName." WHERE ".$this->config->usernameFieldName." = ?";
        $res = Db::Query($query, [$username]);
        if(count($res) != 1) return false;
        return $res[0][$this->config->userIdFieldName];
    }

    public function GetUserInfo($id)
    {
        $query = "SELECT * FROM ".$this->config->tableName." WHERE ".$this->config->userIdFieldName." = ?";
        $res = Db::Query($query, [$id]);
        if(count($res) != 1) return false;
        return (object)$res[0];
    }

    public function SetNewPassword($id, $password)
    {
        if($this->passwordSetFunction)
        {
            $storedPassword = call_user_func($this->passwordSetFunction, $password);
        }
        else
        {
            $storedPassword = password_hash($password, PASSWORD_BCRYPT);
        }

        $query = "UPDATE ".$this->config->tableName." SET ".$this->config->asswordFieldName." = ? WHERE ".$this->config->userIdFieldName." = ?";
        Db::Query($query, [$storedPassword, $id]);
        return true;
    }
}

?>