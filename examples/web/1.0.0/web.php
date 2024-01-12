<?php

include_once("../../../lib/tinyphp.php");

TinyPHP::RegisterRoute("/", "html/main.php");
TinyPHP::RegisterRoute("/main", "html/main.php");
TinyPHP::RegisterRoute("/login", "html/login.php");
TinyPHP::Register404("html/404.php");

TinyPHP::Run();

?>