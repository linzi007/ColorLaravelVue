<?php

namespace App\Models;

class Driver extends Model
{
    protected $fillable = ['name', 'code', 'mobile', 'description', 'created_at', 'updated_at'];


    /**
     * 配送的单据信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mainOrderPayments()
    {
        return $this->hasMany(\App\Models\MainOrderPayment::class, 'driver_id');
    }

}
