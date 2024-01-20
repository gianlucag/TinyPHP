<?php

// api
$post = Api::Post();
$get = Api::Get();

$res = [
    "test" => 123,
    "get" => isset($get->test) ? $get->test : "not set"
];

Api::Ok($res);

?>