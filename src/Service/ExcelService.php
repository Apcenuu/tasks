<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelService
{
    public function writeXLSX($filename, $rows, $keys = [], $formats = []) {

        $doc = new Spreadsheet();
        $sheet = $doc->getActiveSheet();

        if ($keys) {
            $offset = 2;
        } else {
            $offset = 1;
        }


        $i = 0;
        foreach($rows as $row) {
            $doc->getActiveSheet()->fromArray($row, null, 'A' . ($i++ + $offset));
        }


        if ($keys) {
            $doc->setActiveSheetIndex(0);
            $doc->getActiveSheet()->fromArray($keys, null, 'A1');
        }

        $last_column = $doc->getActiveSheet()->getHighestColumn();
        $last_row = $doc->getActiveSheet()->getHighestRow();

        for ($i = 'A'; $i <= $last_column; $i++) {
            $doc->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
        }

        if ($keys) {
            $doc->getActiveSheet()->freezePane('A2');
            $doc->getActiveSheet()->getStyle('A1:' . $last_column . '1')->getFont()->setBold(true);
        }

        $doc->getActiveSheet()->getStyle('A2:' . $last_column . $last_row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        if ($formats) {
            foreach ($formats as $col => $format) {
                $doc->getActiveSheet()->getStyle($col . $offset . ':' . $col . $last_row)->getNumberFormat()->setFormatCode($format);
            }
        }

        $writer = new Xlsx($doc);
        $writer->save($filename);
    }
}