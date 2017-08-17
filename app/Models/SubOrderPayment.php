<?php

namespace App\Models;


class SubOrderPayment extends Model
{
    protected $table = 'sub_order_payment';

    protected $fillable = [
        //缺货     拒收     实发金额    签单      自提      其他      尾差      扣减备注
        'quehuo', 'jusou', 'shifa', 'qiandan', 'ziti', 'qita', 'weicha', 'desc_remark'
        //应收金额      pos刷卡   微信      支付宝     翼支付   现金    实收金额
        , 'yingshou', 'pos', 'weixin', 'alipay', 'yizhifu', 'cash', 'shishou'
        //  配送费            司机费用    订单id 金额占比
        , 'shipping_fee', 'driver_fee', 'id', 'percent', 'pay_sn', 'pay_id', 'order_sn'
        , 'order_id', 'store_id', 'add_time'
    ];

    /**
     * sub order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subOrder()
    {
        return $this->belongsTo(\App\Models\Order::class, 'order_id');
    }

    /**
     * sub order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mainPayment()
    {
        return $this->belongsTo(\App\Models\Order::class, 'order_id');
    }

}
