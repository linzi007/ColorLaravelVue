<?php

namespace App\Models;

use Exception;

class OrderGoodsPayment extends Model
{

    protected $fillable = [
        'id', 'goods_id','quehuo_number', 'jushou_number', 'shifa_number', 'shifa_amount', 'shipping_charging_type'
        , 'shipping_rate', 'unpack_fee', 'delivery_fee', 'driver_charging_type', 'driver_rate', 'driver_fee'
    ];

    /**
     * order goods
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orderGoods()
    {
        return $this->belongsTo(\App\Models\OrderGoods::class, 'id', 'rec_id');
    }

    public function mainOrder()
    {
        return $this->belongsTo(\App\Models\MainOrderPayment::class, 'pay_id', 'pay_id');
    }

    public function updateDeliveryFee($payId)
    {
        $orderGoodsPayments = app(\App\Models\OrderGoods::class)->with('payments')->where('pay_id', $payId)->get()->pluck('payments');
        $result = app(\App\Models\GoodsSetting::class)->calculateMulti($orderGoodsPayments);
        if (false === $result['status']) {
            throw new Exception('1111'.$result['msg']);
        }
        foreach ($result['data'] as $goodsPayment) {
            $result = $this->where('id', $goodsPayment['id'])->update($goodsPayment->toArray());
            if (false === $result['status']) {
                throw new Exception('2222'.$result['msg']);
            }
        }

        return true;
    }

}
