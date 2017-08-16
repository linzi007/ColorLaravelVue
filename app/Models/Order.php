<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order';

    protected $primaryKey = 'order_id';

    /**
     * main order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function MainOrder()
    {
        return $this->belongsTo(\App\Models\MainOrder::class, 'pay_id', 'pay_id');
    }

    /**
     * store
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Store()
    {
        return $this->belongsTo(\App\Models\Store::class, 'store_id', 'store_id');
    }

    /**
     * order goods
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function OrderGoods()
    {
        return $this->hasMany(\App\Models\OrderGoods::class, 'order_id', 'order_id');
    }


}
