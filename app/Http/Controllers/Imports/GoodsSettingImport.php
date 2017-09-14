<?php


namespace App\Http\Controllers\Imports;


use App\Http\Controllers\Exports\GoodsSettingExport;
use App\Http\Controllers\Traits\ExcelTrait;
use App\Jobs\RemoveExportExcelFiles;
use App\Models\Goods;
use App\Models\GoodsSetting;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Files\ExcelFile;

class GoodsSettingImport extends ExcelFile
{
    use ExcelTrait;

    public function getFile()
    {
        $files = request()->file('file');
        return $files->getPathname();
    }

    public function getFilters()
    {
        return [
            'chunk'
        ];
    }

    public function doImport()
    {
        $importData = $this->all()->toArray();
        $goodsModel =new Goods();
        $goodsSetting =new GoodsSetting();

        $typeArr = [0, 1];
        foreach($importData as $key => $row)
        {
            if (empty($goodsId = $row['goods_id'])) {
                $importData[$key]['error'] .= 'sku不存在';
                continue;
            }

            $goods = $goodsModel->find($goodsId);
            if (empty($goods)) {
                $importData[$key]['error'] .= 'sku不存在;';
                continue;
            }
            $data['goods_id'] = $goodsId;
            $data['store_id'] = $goods['store_id'];
            $shippingChargingType = intval($row['shipping_charging_type']);
            if (!in_array($shippingChargingType, $typeArr)) {
                $importData[$key]['error'] .= 'shipping_charging_type 类型错误，请填写：0：计件，1：金额比例';
                continue;
            }
            $data['shipping_charging_type'] = $shippingChargingType;

            $shippingRate = $row['shipping_rate'] ? $row['shipping_rate'] : 0;
            if ($shippingRate > 1 || $shippingRate < 0) {
                $importData[$key]['error'] .= 'shipping_rate 为 0-1 之间的小数;';
                continue;
            }
            $data['shipping_rate'] = $shippingRate;

            $unpackFee = $row['unpack_fee'] ? $row['unpack_fee'] : 0;

            $data['unpack_fee'] = $unpackFee;

            $driverChargingType = intval($row['driver_charging_type']);
            if (!in_array($driverChargingType, $typeArr)) {
                $importData[$key]['error'] .= 'driver_charging_type 类型错误，请填写：金额 或者 数量;';
                continue;
            }
            $data['driver_charging_type'] = $driverChargingType;

            $driverRate = $row['driver_rate'] ? $row['driver_rate'] : 0;
            if ($driverRate > 1 || $driverRate < 0) {
                $importData[$key]['error'] .= 'driver_fee 为 0-1 之间的小数;';
                continue;
            }
            $data['driver_rate'] = $driverRate;
            $result = $goodsSetting->updateOrInsert(['goods_id' => $goodsId], $data);
            if ($result === false) {
                $importData[$key]['error'] .= '数据库更新失败';
                continue;
            }
            unset($importData[$key]);
        }

        if (count($importData)) {
            $data = $this->doExport($importData);
            //定时删除任务，10分钟后删除文件
            $filePath = $data['path'];
            RemoveExportExcelFiles::dispatch($filePath)->delay(Carbon::now()->addMinutes(10));

            return ['status' => false, 'data' => $data];
        }

        return ['status'=>true];
    }

    public function doExport($exportData)
    {
        $headers = [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Cache-Control'       => 'cache, must-revalidate',
            'Pragma'              => 'public',
        ];
        return Excel::create('goods_setting' . Carbon::now()->getTimestamp(), function($excel) use($exportData) {
            $excel->sheet('Sheet1', function($sheet) use($exportData) {
                $sheet->setAutoSize(true);
                $sheet->fromArray($exportData, null, 'A1', true, true);
            });
        })->store('xls', false, true);
    }
}