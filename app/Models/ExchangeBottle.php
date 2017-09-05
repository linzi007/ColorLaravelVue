<?php

namespace App\Models;

class ExchangeBottle extends Model
{
    protected $fillable = ['store_id', 'amount', 'driver_id', 'pay_sn', 'creator'];

    /**
     * store
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function store()
    {
        return $this->hasOne(\App\Models\Store::class, 'store_id', 'store_id');
    }

    /**
     * driver
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function driver()
    {
        return $this->hasOne(\App\Models\Driver::class, 'id', 'driver_id');
    }
    /**
     * admin
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function admin()
    {
        return $this->hasOne(\App\Models\Admin::class, 'admin_id', 'creator');
    }



}
