<?php


namespace App\Http\Controllers\Imports;


use Maatwebsite\Excel\Files\ImportHandler;

class GoodsSettingImportHandler implements ImportHandler
{
    public function handle(GoodsSettingImport $import)
    {
        // get the results
        $importData = $import->get();
        dd($importData);

    }
}