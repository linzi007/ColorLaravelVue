<?php

namespace App\Models;

use Carbon\Carbon;

class OrderGoods extends Model
{
    protected $table = 'order_goods';

    protected $primaryKey = 'rec_id';

    /**
     * goods
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function goods()
    {
        return $this->hasOne(\App\Models\Goods::class, 'goods_id', 'goods_id');
    }

    /**
     * subOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subOrder()
    {
        return $this->belongsTo(\App\Models\Order::class, 'order_id', 'order_id');
    }

    /**
     * main order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mainOrder()
    {
        return $this->belongsTo(\App\Models\MainOrder::class, 'pay_id', 'pay_id');
    }

    //记账信息
    public function payments()
    {
        return $this->hasOne(\App\Models\OrderGoodsPayment::class, 'id', 'rec_id');
    }

    public function getOnlyPayments()
    {
        return $this->with('payments')->get()->pluck('payments');
    }
}
