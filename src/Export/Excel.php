<?php

namespace App\Export;

use \PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Generate Excel file
 */
class Excel implements ExportInterface
{
    /**
     * @inheritdoc
     */
    public function generate(array $data = null)
    {
        $spreadsheet = new Spreadsheet();
        $activeSheet = $spreadsheet->getActiveSheet();

        $cell = $activeSheet->getCellByColumnAndRow(1, 1);
        $cell->setValue('County');

        $cell = $activeSheet->getCellByColumnAndRow(2, 1);
        $cell->setValue('Tax');

        $cell = $activeSheet->getCellByColumnAndRow(3, 1);
        $cell->setValue('Date');

        foreach($data as $idx => $item) {
            $cell = $activeSheet->getCellByColumnAndRow(1, $idx + 2);
            $cell->setValue($item['county']);

            $cell = $activeSheet->getCellByColumnAndRow(2, $idx + 2);
            $cell->setValue($item['tax']);

            $cell = $activeSheet->getCellByColumnAndRow(3, $idx + 2);
            $cell->getStyle()->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
            $cell->setValue(Date::PHPToExcel($item['date']));
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = sys_get_temp_dir() . '/' . 'export.xlsx';
        $writer->save($filename);

        return $filename;
    }
}