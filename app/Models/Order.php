<?php

namespace App\Models;

use Carbon\Carbon;

class Order extends Model
{
    protected $table = 'order';

    protected $primaryKey = 'order_id';

    /**
     * main order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mainOrder()
    {
        return $this->belongsTo(\App\Models\MainOrder::class, 'pay_id', 'pay_id');
    }

    /**
     * store
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(\App\Models\Store::class, 'store_id', 'store_id');
    }

    /**
     * order goods
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderGoods()
    {
        return $this->hasMany(\App\Models\OrderGoods::class, 'order_id', 'order_id');
    }

    /**
     * subOrderPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function subOrderPayment()
    {
        return $this->hasOne(\App\Models\SubOrderPayment::class, 'order_id', 'order_id');
    }

    public function getAddTimeAttribute($value)
    {
        return Carbon::createFromTimestamp($value)->toDateTimeString();
    }

    public function getYingshouAmount($mainOrder)
    {
        return $mainOrder['shifa'] - $mainOrder['qiandan']
            - $mainOrder['ziti'] - $mainOrder['qita'] - $mainOrder['weicha'] - $mainOrder['promotion_amount'];
    }

    /**
     * 实收=预存款+POS+微信+支付宝+现金
     *
     * @param $mainOrder
     * @return mixed
     */
    public function getShishou($mainOrder)
    {
        return $mainOrder['pd_amount'] + $mainOrder['pos'] + $mainOrder['weixin']
            + $mainOrder['alipay'] + $mainOrder['yizhifu'] + $mainOrder['cash'];
    }
}
