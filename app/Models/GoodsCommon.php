<?php

namespace App\Models;

class GoodsCommon extends Model
{
    protected $table = 'goods_common';

    protected $primaryKey = 'goods_commonid';

    /**
     * goods
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Goods()
    {
        return $this->hasMany(\App\Models\Goods::class, 'goods_commonid', 'goods_commonid');
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

}
