<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;

class Driver extends Model
{
    protected $fillable = ['name', 'code', 'mobile', 'description', 'created_at', 'updated_at'];


    protected $casts = [
        'id' => 'string',
    ];

    /**
     * 配送的单据信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mainOrderPayments()
    {
        return $this->hasMany(\App\Models\MainOrderPayment::class, 'driver_id');
    }

    public function getCache()
    {
        return Cache::remember($this->getCacheKey(), 60*6, function () {
            return app(\App\Models\Driver::class)->orderBy('id', 'desc')->get();
        });
    }

    public function clearCache()
    {
        Cache::forget($this->getCacheKey);
    }

    public function getCacheKey()
    {
        return 'drivers';
    }

}
