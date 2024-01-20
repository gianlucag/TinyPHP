<?php
include("shared/head.php");
?>

<p><b>Demo</b></p>

<br />
<br />
<b>Config</b>
<?php
Config::Init("conf/config.json");
$dbConfig = Config::GetField("db");
echo "<br />Config file section: ".json_encode($dbConfig);
?>

<br />
<br />
<b>Logger</b>
<?php
Logger::Init(Config::GetField("logdir"));
Logger::Write("DB", "test");
echo "<br />Writing to log file";
?>

<br />
<br />
<b>Database</b>
<?php
Db::Init(Config::GetField("db"), "Error");
//Db::Query("SELECT * FROM test WHERE id = ?", [12]);
echo "<br />Init db and basic query";
?>

<br />
<br />
<b>Crypto</b>
<?php
$uuid = Crypt::GetRandomUUID();
$rand = Crypt::GetRandomHex(6);

echo "<br />Random UUID -> ".$uuid;
echo "<br />Random 6 bytes hex -> ".$rand;
?>

<br />
<br />
<b>API</b>
<?php
$post = Api::Post();
$get = Api::Get();
echo "<br />POST parameters: ".json_encode($post);
echo "<br />GET parameters: ".json_encode($get);
?>

<br />
<br />
<b>Dictionary</b>
<?php
Dictionary::Init("it");
Dictionary::Add("it", "dictionaries/it.json");
Dictionary::Add("en", "dictionaries/en.json");
Dictionary::SetLanguage();
echo "<br />Basic translate: ".txt("TEST");
echo "<br />Translate with dynamic parmaters: ".txt("TEST_DYNAMIC", ["2", "7"]);
echo "<br />Translation not found: ".txt("KEY_NOT_FOUND");
?>

<br />
<br />
<b>Spreadsheet</b>
<?php
$res = SpreadSheet::Load("test-files/test.xls");
echo "<br />Tot rows: ".SpreadSheet::GetTotRows();
echo "<br />Tot columns: ".SpreadSheet::GetTotCols();
echo "<br />Content:"; 
for($r = 0; $r < SpreadSheet::GetTotRows(); $r++)
{
    echo "<br />".json_encode(SpreadSheet::GetRow($r));
}
?>

<br />

<?php
/*
Mail::Send(
    "noreply@tinyphp.org",
    "TinyPHP",
    "youremail@mail.com",
    "Subject",
    "email-templates/template.html"
);
*/
?>



<?php
include("shared/footer.php");
include("shared/tail.php");
?>


