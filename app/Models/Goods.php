<?php

namespace App\Models;


class Goods extends Model
{

    protected $table = 'goods';

    protected $primaryKey = 'goods_id';

    /**
     * 配送费率配置嘻嘻
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function GoodsSetting()
    {
        return $this->hasOne(\App\Models\GoodsSetting::class, 'goods_id', 'goods_id');
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

    /**
     * 货号
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function GoodsCommon()
    {
        return $this->belongsTo(\App\Models\GoodsCommon::class, 'goods_commonid', 'goods_commonid');
    }


}
