<?php

$res = SpreadSheet::Load("test.xlsx");
echo SpreadSheet::GetTotRows();
echo SpreadSheet::GetTotCols();
for($r = 0; $r < SpreadSheet::GetTotRows(); $r++)
{
    echo json_encode(SpreadSheet::GetRow($r));
}

?>