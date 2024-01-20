<?php

include_once("../../../lib/tinyphp.php");

TinyPHP::RegisterRoot("/tinyphp/examples");
TinyPHP::RegisterRoute("/", "html/demo.php");
TinyPHP::RegisterRoute("/demo", "html/demo.php");
TinyPHP::RegisterRoute("/test", "html/demo.php");
TinyPHP::Register404("html/404.php");

TinyPHP::Run();

?>