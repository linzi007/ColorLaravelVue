<?php


namespace App\Http\Controllers\Traits;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

trait ExcelTrait
{
    /**
     * @param array $exportData
     * @param array $headers
     * @param string $tableName
     * @param string $fileType
     */
    public function exportExcel($exportData, $headers, $tableName, $fileType = 'xls')
    {
        Excel::create($tableName . Carbon::now(), function($excel) use($exportData, $headers) {
            $excel->sheet('Sheet1', function($sheet) use($exportData, $headers) {
                $sheet->setAutoSize(true);
                $sheet->fromArray($exportData, null, 'A1', true, false);
                $sheet->prependRow($headers);
            });
        })->export($fileType);
    }
}