<?php

namespace App\Models;


class Goods extends Model
{

    const FIELDS = [
        'goods_id'
    ];
    protected $table = 'goods';

    protected $primaryKey = 'goods_id';

    /**
     * 配送费率配置嘻嘻
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function goodsSetting()
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
    public function goodsCommon()
    {
        return $this->belongsTo(\App\Models\GoodsCommon::class, 'goods_commonid', 'goods_commonid');
    }

    public function list($where = [])
    {
        return $this->leftJoin('goods_settings', 'goods.goods_id', '=', 'goods_settings.goods_id')
            ->select(['goods.goods_id', 'goods.goods_name', 'goods.goods_price', 'goods.goods_serial', 'goods.store_id'
                      , 'goods_settings.shipping_charging_type', 'goods_settings.shipping_rate', 'goods_settings.unpack_fee'
                      , 'goods_settings.driver_charging_type', 'goods_settings.driver_rate'])
            ->where($where)->orderByDesc('goods_settings.goods_id')->orderByDesc('goods.goods_id');
    }

}
