<?php


namespace App\Services;


use App\Models\MainOrderPayment;
use Carbon\Carbon;
use Excel;
use Illuminate\Support\Collection;

class MainOrderPaymentsExport
{
    /**
     * @var MainOrderPayment
     */
    private $mainOrderPayment;

    /**
     * MainOrderPaymentsExport constructor.
     */
    public function __construct(MainOrderPayment $mainOrderPayment)
    {

        $this->mainOrderPayment = $mainOrderPayment;
    }

    const Export_FIELDS = [
        'main_order.pay_sn'           => '支付单号',
        'main_order.add_time'         => '订单时间',
        'main_order.goods_amount'     => '货品金额',
        'quehuo'                      => '缺货金额',
        'jushou'                      => '拒收金额',
        'shifa'                       => '实发金额',
        'qiandan'                     => '签单金额',
        'ziti'                        => '自提金额',
        'qita'                        => '其他金额',
        'weicha'                      => '尾差金额',
        'desc_remark'                 => '扣减备注',
        'main_order.promotion_amount' => '代金券',
        'yingshou'                    => '应收金额',
        'main_order.pd_amount'        => '预存款',
        'pos'                         => 'POS金额',
        'out_pay_sn'                  => '刷卡单号',
        'weixin'                      => '微信金额',
        'alipay'                      => '支付宝金额',
        'cash'                        => '现金金额',
        'yizhifu'                     => '翼支付金额',
        'shishou'                     => '应收金额',
        'jk_driver_id'                => '交款人',
        'jk_at'                       => '收款时间',
        'ck_at'                       => '存款时间',
        'delivery_fee'                => '配送费',
        'second_driver_id'            => '二次配送司机',
        'jlr'                         => '录入人',
        'updater'                     => '变更人',
        'updated_at'                  => '变更时间',
        'status'                      => '录入状态',
        'reduce_coupon'               => '扣代金券',
        'help_pd_amount'              => '代扣预存款',
        'refuse_delivery_fee'         => '收客户拒收运费',
    ];

    public function excel($params)
    {
        $condition = [];
        if (!empty($params['pay_sn'])) {
            $condition['main_order.pay_sn'] = $params['pay_sn'];
        }

        if (!empty($params['jk_driver_id'])) {
            $condition['jk_driver_id'] = $params['jk_driver_id'];
        }
        if (!empty($params['jzr'])) {
            $condition['jzr'] = $params['jzr'];
        }
        if (isset($params['status']) && in_array($params['status'], [0, 1])) {
            $condition['status'] = $params['status'];
        }

        if (!empty($params['add_time_start'])) {
            $this->mainOrderPayment = $this->mainOrderPayment->whereBetween('main_order.add_time', [
                strtotime($params['add_time_start']),
                strtotime($params['add_time_end']),
            ]);
        }

        //$data = $this->mainOrderPayment->leftJoin('main_order', 'main_order_payments.pay_id', '=', 'main_order.pay_id')->select(array_keys(self::Export_FIELDS))->where($condition)->get();
        //$this->exportExcel($data->toArray(), array_values(self::Export_FIELDS), '收款登记表');
        return $this->excelChunk($condition);
    }

    public function excelChunk($condition)
    {
        $data = $this->mainOrderPayment
            ->leftJoin('main_order', 'main_order_payments.pay_id', '=', 'main_order.pay_id')
            ->select(array_keys(self::Export_FIELDS))
            ->where($condition)
            ->orderByDesc('main_order_payments.pay_id');
        if($data->count() < 1) {
            return false;
        }
        return Excel::create($this->getFileName(), function ($excel) use ($data) {
            $data->chunk(5000, function ($items) use ($excel) {
                $collection = $this->transformCollection($items);
                $excel->sheet('收款登记表', function ($sheet) use ($collection) {
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
        $exportData = [];
        foreach ($collection as $payment) {
            $exportData[] = [
                '支付单号'   => $payment['pay_sn'],
                '订单时间'   => $payment['add_time'],
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
                '收客户拒收运费'  => $payment['refuse_delivery_fee'],
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
        return '收款登记表' . Carbon::now();
    }
}