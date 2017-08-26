<?php

namespace App\Models;

class ExchangeBottle extends Model
{
    protected $fillable = ['store_id', 'amount', 'driver_id', 'pay_sn', 'creator'];
}
