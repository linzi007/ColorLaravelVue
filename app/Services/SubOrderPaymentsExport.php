<?php


namespace App\Services;


use App\Models\SubOrderPayment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\Exception;

class SubOrderPaymentsExport
{
    /**
     * @var SubOrderPayment
     */
    private $subOrderPayment;

    /**
     * MainOrderPaymentsExport constructor.
     * @param SubOrderPayment $subOrderPayment
     */
    public function __construct(SubOrderPayment $subOrderPayment)
    {
        $this->subOrderPayment = $subOrderPayment;
    }


    public function excel($params)
    {
        $condition = [];
        if (!empty($params['jzr'])) {
            $condition['jzr'] = $params['jzr'];
        }
        if (isset($params['status']) && in_array($params['status'], [0, 1])) {
            $condition['status'] = $params['status'];
        }
        if (!empty($params['add_time_start'])) {
            $this->subOrderPayment = $this->subOrderPayment->whereBetween('main_order_payments.add_time', [
                strtotime($params['add_time_start']),
                strtotime($params['add_time_end']),
            ]);
        }

        return $this->excelChunk($condition);
    }

    public function excelChunk($condition)
    {
        $data = $this->subOrderPayment->leftJoin('main_order_payments', 'sub_order_payments.pay_id', '=', 'main_order_payments.pay_id')
            ->select(['main_order_payments.*', 'sub_order_payments.*'])->where($condition)->orderByDesc('main_order_payments.pay_id');
        if ($data->count() <1) {
            throw new Exception('导出数据为空');
        }
        return Excel::create($this->getFileName(), function ($excel) use ($data) {
            $data->chunk(5000, function ($items) use ($excel) {
                $collection = $this->transformCollection($items);
                $excel->sheet('sheet1', function ($sheet) use ($collection) {
                    $sheet->fromModel($collection, null, 'A1', true);
                });
            });
        })->export('xls');
    }

    /**
     * @param Collection $collection
     * @return array
     */
    private function transformCollection($collection)
    {
        $adminIds = $driverIds = [];
        foreach ($collection as $item) {
            $driverIds[] = $item['jk_driver_id'];
            $driverIds[] = $item['second_driver_id'];
            $adminIds[] = $item['jlr'];
            $adminIds[] = $item['updater'];
        }
        $drivers = $admins = [];
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
            $exportData[] = [
                '支付单号'   => $payment['pay_sn'],
                '子订单号'   => $payment['order_sn'],
                '订单时间'   => $payment['add_time'],
                '档口名称'   => empty($stores[$payment['store_id']]) ? '' : $stores[$payment['store_id']],
                '货品金额'   => $payment['quehuo'] + $payment['jushou'] + $payment['shifa'],
                '缺货金额'   => $payment['quehuo'],
                '拒收金额'   => $payment['jushou'],
                '实发金额'   => $payment['shifa'],
                '签单金额'   => $payment['qiandan'],
                '自提金额'   => $payment['ziti'],
                '其他金额'   => $payment['qita'],
                '尾差金额'   => $payment['weicha'],
                '扣减备注'   => $payment['desc_remark'],
                '代金券'    => $payment['promotion_amount'],
                '扣代金券'    => $payment['reduce_coupon'],
                '应收金额'   => $payment['yingshou'],
                '预存款'    => $payment['pd_amount'],
                '代扣预存款'    => $payment['help_pd_amount'],
                'POS金额'  => $payment['pos'],
                '刷卡单号'   => $payment['out_pay_sn'],
                '微信金额'   => $payment['weixin'],
                '支付宝金额'  => $payment['alipay'],
                '现金金额'   => $payment['cash'],
                '翼支付金额'  => $payment['yizhifu'],
                '实收金额'   => $payment['shishou'],
                '交款人'    => empty($payment['jk_driver_id']) ? '' : $drivers[$payment['jk_driver_id']],
                '收款时间'   => $payment['jk_at'],
                '存款时间'   => $payment['ck_at'],
                '配送费'    => $payment['delivery_fee'],
                '二次配送司机' => empty($payment['second_driver_id']) ? '' : $drivers[$payment['second_driver_id']],
                '录入人'    => $admins[$payment['jlr']],
                '变更人'    => $admins[$payment['updater']],
                '变更时间'   => $payment['updated_at'],
                '录入状态'   => $payment['status'] == 1 ? '已记账' : '已录入',
            ];
        }
        //return array_map([$this, 'transform'], $collection->toArray());
        return $exportData;
    }


    private function transform($payment, $drivers, $admins)
    {
        dd($drivers, $admins);
        return [
            '支付单号'   => $payment['pay_sn'],
            '订单时间'   => date('Y-m-d H:i:s', $payment['add_time']),
            '货品金额'   => $payment['goods_amount'],
            '缺货金额'   => $payment['quehuo'],
            '拒收金额'   => $payment['jushou'],
            '实发金额'   => $payment['shifa'],
            '签单金额'   => $payment['qiandan'],
            '自提金额'   => $payment['ziti'],
            '其他金额'   => $payment['qita'],
            '尾差金额'   => $payment['weicha'],
            '扣减备注'   => $payment['desc_remark'],
            '代金券'    => $payment['promotion_amount'],
            '应收金额'   => $payment['yingshou'],
            '预存款'    => $payment['pd_amount'],
            'POS金额'  => $payment['pos'],
            '刷卡单号'   => $payment['out_pay_sn'],
            '微信金额'   => $payment['weixin'],
            '支付宝金额'  => $payment['alipay'],
            '现金金额'   => $payment['cash'],
            '翼支付金额'  => $payment['yizhifu'],
            '实收金额'   => $payment['shishou'],
            '交款人'    => empty($payment['jk_driver_id']) ? '' : $drivers[$payment['jk_driver_id']],
            '收款时间'   => $payment['jk_at'],
            '存款时间'   => $payment['ck_at'],
            '配送费'    => $payment['delivery_fee'],
            '二次配送司机' => empty($payment['second_driver_id']) ? '' : $drivers[$payment['second_driver_id']],
            '录入人'    => $admins[$payment['jlr']],
            '变更人'    => $admins[$payment['updater']],
            '变更时间'   => $payment['updated_at'],
            '录入状态'   => $payment['status'] == 1 ? '已记账' : '已录入',
        ];
    }

    /**
     * 导出表的名称
     * @return string
     */
    private function getFileName()
    {
        return '收款登记表-子订单' . Carbon::now();
    }
}