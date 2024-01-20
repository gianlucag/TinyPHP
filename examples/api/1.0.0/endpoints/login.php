<?php

function Error($msg)
{
    Api::Error(500, $msg);
}

Config::Init("conf/config.json", "Error");
Db::Init(Config::GetField("db"), "Error");
Auth::Init("xauth", null);

DbAuth::Init(Config::GetField("dbauth"));     

$res = null;

// api
$post = Api::Post();
$get = Api::Get();


if(isset($get->logout))
{
    DbAuth::LogoutAllClients();
    $res = "logged out";
}
else if(DbAuth::IsLogged())
{
    $res = "logged in";
}
else
{
    $username = isset($get->username) ? $get->username : null;
    $password = isset($get->password) ? $get->password : null;
    $res = DbAuth::Login($username, $password);
}

Api::Ok($res);

?>