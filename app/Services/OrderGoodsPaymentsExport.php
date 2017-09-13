<?php


namespace App\Services;


use App\Models\OrderGoodsPayment;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class OrderGoodsPaymentsExport
{
    /**
     * @var OrderGoodsPayment
     */
    private $orderGoodsPayment;

    const CHUNK_SIZE = 5000;

    /**
     * OrderGoodsPaymentsExport constructor.
     */
    public function __construct(OrderGoodsPayment $orderGoodsPayment)
    {

        $this->orderGoodsPayment = $orderGoodsPayment;
    }

    public function excel($params)
    {
        $condition = [];
        if (!empty($params['jzr'])) {
            $condition['jzr'] = $params['jzr'];
        }

        if (!empty($params['is_second_delivery'])) {
            $this->orderGoodsPayment = $this->orderGoodsPayment->whereNotNull('is_second_delivery');
        }

        if (isset($params['status']) && in_array($params['status'], [0, 1])) {
            $condition['status'] = $params['status'];
        }

        if (!empty($params['add_time_start'])) {
            $this->orderGoodsPayment = $this->orderGoodsPayment->whereBetween('add_time', [
                strtotime($params['add_time_start']),
                strtotime($params['add_time_end']),
            ]);
        }

        if (!empty($params['store_id'])) {
            $condition['order_goods_payments.store_id'] = $params['store_id'];
        }

        if (!empty($params['order_sn'])) {
            $condition['order_goods_payments.order_sn'] = $params['order_sn'];
        }

        if (!empty($params['jk_driver_id'])) {
            $condition['jk_driver_id'] = $params['jk_driver_id'];
        }
        $fields = ['order_goods_payments.*', 'pay_sn', 'add_time', 'jk_driver_id', 'second_driver_id', 'status', 'jzr'];
        $data = $this->orderGoodsPayment->leftJoin('main_order_payments', 'order_goods_payments.pay_id', '=', 'main_order_payments.pay_id')
                ->with(['orderGoods'])->select($fields)->where($condition)->orderByDesc('order_goods_payments.pay_id');

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
        return '配送费明细' . Carbon::now();
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
        $adminIds = $driverIds = [];
        foreach ($collection as $item) {
            $driverIds[] = $item['jk_driver_id'];
            $driverIds[] = $item['second_driver_id'];
            $adminIds[] = $item['jzr'];
        }
        $drivers = $admins = $stores = [];
        if ($driverIds = array_filter($driverIds)) {
            $drivers = app(\App\Models\Driver::class)->select('id', 'name')->whereIn('id', $driverIds)->get()->toArray();
        }
        if ($adminIds = array_filter($adminIds)) {
            $admins = app(\App\Models\Admin::class)->select('admin_id', 'admin_name')->whereIn('admin_id', $adminIds)->get()->toArray();
        }
        $drivers = array_column($drivers, 'name', 'id');
        $admins = array_column($admins, 'admin_name', 'admin_id');
        $stores = app(\App\Models\Store::class)->getStoreCache();
        $stores = array_column($stores, 'store_name', 'store_id');
        $exportData = [];
        foreach ($collection as $payment) {
            $orderGoods = $payment['order_goods'];
            if (empty($orderGoods)) {
                continue;
            }
            $exportData[] = [
                '支付单号'   => $payment['pay_sn'],
                '子订单号'   => $payment['order_sn'],
                '订单时间'   => $payment['add_time'],
                '档口名称'   => empty($stores[$payment['store_id']]) ? '' : $stores[$payment['store_id']],
                '货品名称'   => $orderGoods['goods_name'],
                '条码'   => $orderGoods['goods_serial'],
                '单价'   => $orderGoods['goods_price'],
                '订单数量'   => $orderGoods['goods_num'],
                '缺货数量'   => $payment['quehuo_number'],
                '拒收数量'   => $payment['jushou_number'],
                '实发数量'   => $payment['shifa_number'],
                '实发金额'   => $payment['shifa_amount'],
                '配送计费方式'   => $this->getChargingTypeName($payment['shipping_charging_type']),
                '费率'   => $payment['shipping_rate'],
                '拆包费'   => $payment['unpack_fee'],
                '交款人'    => empty($payment['jk_driver_id']) ? '' : $drivers[$payment['jk_driver_id']],
                '司机计费方式'   => $this->getChargingTypeName($payment['driver_charging_type']),
                '司机费率'   => $payment['driver_rate'],
                '首次配送司机' => empty($payment['second_driver_id']) ? '' : $drivers[$payment['second_driver_id']],
                '录入状态'   => $payment['status'] == 1 ? '已记账' : '已录入',
                '记账人'    => empty($admins[$payment['jzr']]) ? '' : $admins[$payment['jzr']]
            ];
        }
        return $exportData;
    }

    private function getChargingTypeName($key = '')
    {
        return 1 == $key ? '金额' : '数量';
    }
}