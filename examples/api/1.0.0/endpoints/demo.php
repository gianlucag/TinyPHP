<?php

function Error($msg)
{
    Api::Error(500, $msg);
}

// config
Config::Init("conf/config.json", "Error");
$test = Config::GetField("db");

// logger
Logger::Init(Config::GetField("logdir"));

// logger
Logger::Write("DB", "test");

// db
Db::Init(Config::GetField("db"), "Error");
//Db::Query("SELECT * FROM test WHERE id = ?", [12]);

// crypt
$uuid = Crypt::GetRandomUUID();
$rand = Crypt::GetRandomHex(6);

// api
$post = Api::Post();
$get = Api::Get();

/*
Mail::Send(
    "test@test.org",
    "Tester",
    "gianluca.ghettini@gmail.com",
    "Soggetto",
    "email-templates/template.html"
);
*/

if(isset($get->pippo)) $p = $get->pippo; else $p = "not set";
$res = [
    "test" => 123,
    "uuid" => $uuid,
    "rand" => $rand,
    "get" => $p
];

Api::Ok($res);

?>