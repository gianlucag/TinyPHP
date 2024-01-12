<?php

include_once("../../../lib/tinyphp.php");

TinyPHP::RegisterRoot("/tinyphp/examples/api/1.0.0");
TinyPHP::RegisterRoute("/abc", "endpoints/demo.php");
TinyPHP::RegisterRoute("/login", "endpoints/login.php");
TinyPHP::RegisterRoute("/download", "endpoints/download.php");
TinyPHP::RegisterRoute("/import", "endpoints/import.php");
TinyPHP::Register404("endpoints/404.php");
TinyPHP::Run();

?>