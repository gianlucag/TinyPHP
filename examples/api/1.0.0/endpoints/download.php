<?php

$header = ["test1", "test2"];
$data = [
    ["data1", "data2"],
    ["data1", "data2"],
    ["data1", "data2"]
];

$res = SpreadSheet::BuildExcelBinary($header, $data);
Download::Start("output.xlsx", SpreadSheet::GetExcelMime(), $res);
?>