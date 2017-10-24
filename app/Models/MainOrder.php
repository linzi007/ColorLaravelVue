<?php

namespace App\Models;


use App\Http\Controllers\Traits\ExcelTrait;
use Carbon\Carbon;

class MainOrder extends Model
{
    use ExcelTrait;

    const Export_FIELDS = [
        'pay_sn'           => '支付单号',
        'add_time'         => '订单时间',
        'goods_amount'     => '货品金额',
        'quehuo'           => '缺货金额',
        'jushou'           => '拒收金额',
        'shifa'            => '实发金额',
        'qiandan'          => '签单金额',
        'ziti'             => '自提金额',
        'qita'             => '其他金额',
        'weicha'           => '尾差金额',
        'desc_remark'      => '扣减备注',
        'promotion_amount' => '代金券',
        'yingshou'         => '应收金额',
        'pd_amount'        => '预存款',
        'pos'              => 'POS金额',
        'out_pay_sn'       => '刷卡单号',
        'weixin'           => '微信金额',
        'alipay'           => '支付宝金额',
        'cash'             => '现金金额',
        'yizhifu'          => '翼支付金额',
        'shishou'         => '应收金额',
        'jkr'              => '交款人',
        'jk_at'            => '收款时间',
        'ck_at'            => '存款时间',
        'delivery_fee'     => '配送费',
        'second_driver_id' => '二次配送司机',
        'jlr'              => '录入人',
        'updater'          => '变更人',
        'updated_at'       => '变更时间',
        'status'           => '录入状态',
    ];

    protected $table = 'main_order';

    protected $primaryKey = 'main_order_id';

    /**
     * suborders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subOrders()
    {
        return $this->hasMany(\App\Models\Order::class, 'pay_id', 'pay_id');
    }

    /**
     * order goods
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderGoods()
    {
        return $this->hasMany(\App\Models\OrderGoods::class, 'pay_id', 'pay_id');
    }

    /**
     * order goods
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mainOrderPayment()
    {
        return $this->hasOne(\App\Models\MainOrderPayment::class, 'pay_id', 'pay_id');
    }

    public function list($where)
    {
        return $this->leftJoin('main_order_payments', 'main_order.pay_id', '=', 'main_order_payments.pay_id')
            ->where($where)->orderBy('main_order.pay_id', 'desc');
    }

    public function export()
    {
        $data = $this->get((array_keys(self::Export_FIELDS)))->toArray();
        $this->exportExcel($data, array_values(self::Export_FIELDS), '主收款登记表');
    }

    public function getAddTimeAttribute($value)
    {
        return Carbon::createFromTimestamp($value)->toDateTimeString();
    }

    public function setAddTimeAttribute($value)
    {
        return strtotime($value);
    }

    /**
     * 应收=实发金额-签单-自提-其他-尾差-优惠金额(代金券) + 扣代金券金额（因退货等原因造成代金券无法抵扣全额）
     *
     * @param $mainOrderPayment
     * @return mixed
     */
    public function getYingshouAmount($mainOrderPayment)
    {
        $promotionAmount = 0;
        $mainOrder = $this->select('pay_id', 'union_promotion', 'site_promotion', 'order_amount')->where('pay_id', $mainOrderPayment['pay_id'])->first();

        if ($mainOrder) {
            $promotionAmount = $mainOrder['union_promotion'] + $mainOrder['site_promotion'];
        }
        return $mainOrderPayment['shifa'] - $mainOrderPayment['qiandan']
            - $mainOrderPayment['ziti'] - $mainOrderPayment['qita'] - $mainOrderPayment['weicha'] - $promotionAmount + $mainOrderPayment['reduce_coupon'];
    }

    /**
     * 实收=预存款+POS+微信+支付宝+现金 + 收客户拒收运费 + 代扣预存款(协助使用预存款金额) + 收客户拒收运费
     *
     * @param $mainOrder
     * @return mixed
     */
    public function getShishouAmount($mainOrder)
    {
        return $mainOrder['pd_amount'] + $mainOrder['pos'] + $mainOrder['weixin'] + $mainOrder['alipay'] + $mainOrder['yizhifu']
            + $mainOrder['cash'] + $mainOrder['help_pd_amount'] + $mainOrder['refuse_delivery_fee'];
    }
}
