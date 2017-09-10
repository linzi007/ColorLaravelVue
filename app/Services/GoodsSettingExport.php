<?php


namespace App\Services;


use App\Models\GoodsSetting;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class GoodsSettingExport
{
    const CHUNK_SIZE = 5000;
    /**
     * @var GoodsSetting
     */
    private $goodsSetting;

    public function __construct(GoodsSetting $goodsSetting)
    {

        $this->goodsSetting = $goodsSetting;
    }

    public function excel($params)
    {
        $condition = [];

        if (isset($params['goods_id'])) {
            $condition['goods.goods_id'] = $params['goods_id'];
        }
        if (isset($params['goods_serial'])) {
            $condition['goods.goods_serial'] = $params['goods_serial'];
        }
        if (isset($params['goods_name'])) {
            $condition['goods.goods_name'] = ['like', $params['goods_name'] . '%'];
        }

        if (!empty($params['store_id'])) {
            $condition['goods_settings.store_id'] = $params['store_id'];
        }

        if (isset($params['shipping_charging_type'])) {
            $condition['goods_settings.shipping_charging_type'] = $params['shipping_charging_type'];
        }

        $fields = ['goods_settings.*', 'goods.goods_name', 'goods.goods_serial', 'goods.goods_price', 'goods.g_unit', 'goods.store_name'];
        $data = $this->goodsSetting->leftJoin('goods', 'goods_settings.goods_id', '=', 'goods.goods_id')
            ->select($fields)
            ->where($condition)
            ->orderByDesc('goods_settings.id');

        return Excel::create($this->getFileName(), function ($excel) use ($data) {
            $data->chunk($this->getChunkSize(), function ($items) use ($excel) {
                $collection = $this->transformCollection($items);
                $excel->sheet('sheet1', function ($sheet) use ($collection) {
                    $sheet->fromModel($collection, null, 'A1', true);
                });
            });
        })->export('xls');

    }

    /**
     * 导出表的名称
     * @return string
     */
    public function getFileName()
    {
        return '货品配送费' . Carbon::now();
    }

    public function getChunkSize()
    {
        return self::CHUNK_SIZE ? self::CHUNK_SIZE : 5000;
    }

    /**
     * @param \Illuminate\Support\Collection $collection
     * @return array
     */
    private function transformCollection($collection)
    {
        $collection = $collection->toArray();
        $exportData = [];
        foreach ($collection as $payment) {
            $exportData[] = [
                '档口名称'   => $payment['store_name'],
                'SKU'   => $payment['goods_id'],
                '货品名称'   => $payment['goods_name'],
                '条码'   => $payment['goods_serial'],
                '货品金额'   => $payment['goods_price'],
                '单位'   => $payment['g_unit'],
                '配送计费方式'   => $this->getChargingTypeName($payment['shipping_charging_type']),
                '费率'   => $payment['shipping_rate'],
                '拆包费'   => $payment['unpack_fee'],
                '司机计费方式'   => $this->getChargingTypeName($payment['driver_charging_type']),
                '司机费率'   => $payment['driver_rate'],
            ];
        }
        return $exportData;
    }

    private function getChargingTypeName($key = '')
    {
        return 1 == $key ? '金额' : '数量';
    }
}