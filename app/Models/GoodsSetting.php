<?php

namespace App\Models;

class GoodsSetting extends Model
{
    protected $fillable = [
        'goods_id', 'store_id', 'shipping_charging_type'
        , 'shipping_rate', 'unpack_fee', 'driver_charging_type'
        , 'driver_rate'
    ];

    /**
     * sku
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo(\App\Models\Goods::class, 'goods_id', 'goods_id');
    }

    /**
     * 档口
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(\App\Models\Store::class, 'store_id', 'store_id');
    }
}
