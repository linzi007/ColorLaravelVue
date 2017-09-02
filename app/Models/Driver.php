<?php

namespace App\Models;

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

    public function list($request)
    {
        return Cache::remember('drivers', 60*6, function () use ($request) {
            $stores = app(\App\Models\Store::class)->whereIn('store_state', [0, 1])
                ->orderBy('store_state', 'desc');
            if($request->get('name')){
                $stores->where('store_name', 'like', '%' . $request->get('name') . '%');
            }
            return $stores->get();
        });
    }

}
