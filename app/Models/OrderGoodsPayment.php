<?php

namespace App\Models;

class OrderGoodsPayment extends Model
{
    protected $fillable = [
        'id', 'quehuo_number', 'jushou_number', 'shifa_number', 'shifa_amount', 'shipping_charging_type'
        , 'shipping_rate', 'unpack_fee', 'delivery_fee', 'driver_charging_type', 'driver_rate', 'driver_fee'
    ];
}
