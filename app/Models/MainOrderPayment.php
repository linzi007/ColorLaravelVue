<?php

namespace App\Models;

class MainOrderPayment extends Model
{
    protected $fillable = [
        //缺货     拒收     实发金额    签单      自提      其他      尾差      扣减备注
        'quehuo', 'jusou', 'shifa', 'qiandan', 'ziti', 'qita', 'weicha', 'desc_remark'
        //应收金额      pos刷卡   微信      支付宝     翼支付   现金    实收金额
        , 'yingshou', 'pos', 'weixin', 'alipay', 'yizhifu', 'cash', 'shishou'
        //  配送费         司机费用     id    首次司机       第二次司机      状态      记录人  记账人 记账时间
        , 'delivery_fee', 'driver_fee', 'id', 'driver', 'driver_second', 'status', 'jlr', 'jzr', 'jz_at'
        , 'pay_sn', 'store_id', 'add_time'
    ];

    /**
     * 主订单
     */
    public function MainOrder()
    {
        $this->belongsTo(\App\Models\MainOrder::class, 'pay_id');
    }

}
