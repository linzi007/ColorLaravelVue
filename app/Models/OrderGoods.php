<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderGoods extends Model
{
    protected $table = 'order_goods';

    protected $primaryKey = 'rec_id';

    /**
     * goods
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Goods()
    {
        return $this->belongsTo(\App\Models\Goods::class, 'goods_id', 'goods_id');
    }

    /**
     * subOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function SubOrder()
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
}
