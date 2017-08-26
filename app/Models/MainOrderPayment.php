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
        , 'store_id', 'add_time'
    ];

    /**
     * 主订单
     */
    public function mainOrder()
    {
        $this->belongsTo(\App\Models\MainOrder::class, 'pay_id', 'pay_id');
    }

    /**
     * 子支付信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subOrderPayments()
    {
        return $this->hasMany(\App\Models\SubOrderPayment::class, 'pay_id', 'pay_id');
    }

    /**
     * 配送实际
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function driver()
    {
        return $this->belongsTo(\App\Models\Driver::class, 'driver_id');
    }

    public function jizhang($payIds)
    {
        return $this->where('status', 0)->whereIn('pay_id', $payIds)->update(['status' => 1]);
    }

    public function fanjizhang($payIds)
    {
        return $this->where('status', 1)->whereIn('pay_id', $payIds)->update(['status' => 0]);
    }

}
