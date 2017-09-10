<?php

namespace App\Models;


use Cache;

class Store extends Model
{
    protected $table = 'store';

    protected $primaryKey = 'store_id';

    protected $cacheMinutes = 60 * 1;

    protected $cacheKey = 'stores';

    /**
     * goods
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goods()
    {
        return $this->hasMany(\App\Models\Goods::class, 'store_id', 'store_id');
    }

    public function list($request)
    {
        return Cache::remember($this->getCacheKey(), $this->cacheMinutes, function () use ($request) {
            $stores = $this->whereIn('store_state', [0, 1])
                ->orderBy('store_state', 'desc');
            if($request->get('name')){
                $stores->where('store_name', 'like', '%' . $request->get('name') . '%');
            }
            return $stores->get()->toArray();
        });
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

    public function getStoreCache()
    {
        return Cache::remember($this->getCacheKey(), $this->cacheMinutes, function () {
            $stores = $this->whereIn('store_state', [0, 1])
                ->orderBy('store_state', 'desc');
            return $stores->get()->toArray();
        });
    }

    public function getCacheKey()
    {
        return $this->cacheKey;
    }
}
