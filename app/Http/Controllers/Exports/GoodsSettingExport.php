<?php


namespace App\Http\Controllers\Exports;


use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Files\NewExcelFile;

class GoodsSettingExport extends NewExcelFile
{
    public $fileType = 'xlsx';
    public function getFilename()
    {
        return 'goods_settings' . Carbon::now();
    }

    public function doExport($data, $headers = [])
    {
        Excel::create($this->getFilename(), function($excel) use($data, $headers) {
            $excel->sheet('Sheet1', function($sheet) use($data, $headers) {
                $sheet->setAutoSize(true);
                $sheet->fromArray($data, null, 'A1', true, false);
                if ($headers) {
                    $sheet->prependRow($headers);
                }
            });
        })->export($this->fileType);
    }
}