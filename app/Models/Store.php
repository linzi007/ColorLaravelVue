<?php

namespace App\Models;


class Store extends Model
{
    protected $table = 'store';

    protected $primaryKey = 'store_id';
    /**
     * goods
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goods()
    {
        return $this->hasMany(\App\Models\Goods::class, 'store_id', 'store_id');
    }

    /**
     * 配送费配置
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goodsSetting()
    {
        return $this->hasMany(\App\Models\GoodsSetting::class, 'store_id', 'store_id');
    }

    /**
     * order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subOrder()
    {
        return $this->hasMany(\App\Models\Order::class, 'store_id', 'store_id');
    }
}
