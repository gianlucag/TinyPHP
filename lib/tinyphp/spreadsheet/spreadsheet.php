<?php

require_once(TINYPHP_ROOT.'/vendor/PhpSpreadsheet-1.29.0/autoload.php');

class SpreadSheet
{
    private static $totCols = null;
    private static $totRows = null;
    private static $spreadsheet = null;

    private static function GetCellCoords($columnIndex, $rowIndex) // zero based indexes 0,0 -> 'A1'
    {
        $columnName = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1);
        $rowName = ''.($rowIndex + 1);
        return $columnName . $rowName;
    }

    private static function BuildSpreadsheet($header, $data)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        for($c = 0; $c < count($header); $c++)
        {
            $cellCoords = self::GetCellCoords($c, 0);
            $cellContent = $header[$c];
            $sheet->setCellValue($cellCoords, $cellContent);
        }
    
        for($r = 0; $r < count($data); $r++)
        {
            $row = $data[$r];
            for($c = 0; $c < count($row); $c++)
            {
                $cellCoords = self::GetCellCoords($c, $r + 1);
                $cellContent = $row[$c];
                $sheet->setCellValue($cellCoords, $cellContent);        
            }
        }

        return $spreadsheet;
    }

    private static function SaveToOutput($writer)
    {
        ob_clean();
        ob_start();
        $writer->save('php://output');
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    private static function SaveToFile($writer, $destFilePath)
    {
        $writer->save($destFilePath);
    }

    public static function Load($filePath)
    {
        try
        {
            $type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filePath);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($type);
            $reader->setReadDataOnly(true);
            if($type == "Csv")
            {
                $reader->setInputEncoding(\PhpOffice\PhpSpreadsheet\Reader\Csv::GUESS_ENCODING);
                $reader->setFallbackEncoding('CP1252');
                $reader->setDelimiter(';');
                $reader->setEnclosure('');
            }
            self::$spreadsheet = $reader->load($filePath);
            self::$spreadsheet->setActiveSheetIndex(0);
            self::$totCols = self::$spreadsheet->getActiveSheet()->getHighestColumn();
            self::$totRows = self::$spreadsheet->getActiveSheet()->getHighestDataRow();
        }
        catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e)
        {
            return false;
        }

        return true;
    }

    public static function GetTotRows()
    {
        return self::$totRows;
    }

    public static function GetTotCols()
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString(self::$totCols);
    }

    public static function GetRow($index, $maxCellLength = null)
    {
        $i = $index + 1; // 0 based index
        $row = array();
        for ($c = 'A'; $c <= self::$totCols; $c++)
        {
            $value = self::$spreadsheet->setActiveSheetIndex(0)->getCell($c.$i)->getValue();
            $value = trim($value);
            if($maxCellLength)
            {
                $value = substr($value, 0, $maxCellLength);
            }
            array_push($row, $value);
        }
        return $row;
    }
    
    public static function BuildCSVBinary($header, $data, $separatorChar, $enclosingChar, $destFilePath = null)
    {
        $spreadsheet = self::BuildSpreadsheet($header, $data);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Csv");
        $writer->setDelimiter($separatorChar);
        $writer->setEnclosure($enclosingChar);
        //$writer->setEnclosureRequired(false);

        if($destFilePath)
        {
            self::SaveToFile($writer, $destFilePath);
            return true;
        }
        else
        {
            return Sself::aveToOutput($writer);
        }
    }

    public static function BuildExcelBinary($header, $data, $destFilePath = null)
    {
        $spreadsheet = self::BuildSpreadsheet($header, $data);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
    
        if($destFilePath)
        {
            self::SaveToFile($writer, $destFilePath);
            return true;
        }
        else
        {
            return self::SaveToOutput($writer);
        }
    }

    public static function GetExcelMime()
    {
        return "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
    }

    public static function GetCSVMime()
    {
        return "text/csv";
    }
}

?>