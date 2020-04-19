<?php

namespace App\Export;

/**
 * Generate Csv file
 */
class Csv implements ExportInterface
{
    /**
     * @inheritdoc
     */
    public function generate(array $data = null)
    {
        $filename = sys_get_temp_dir() . '/' . 'export.csv';
        $fp = fopen($filename, 'w+');
        fputcsv($fp, ['County', 'Tax', 'Date'], ';');

        foreach($data as $item) {
            fputcsv($fp, [
                $item['county'],
                $item['tax'],
                $item['date']->format('Y-m-d H:i:s')
            ], ';');
        }

        fclose($fp);

        return $filename;
    }
}