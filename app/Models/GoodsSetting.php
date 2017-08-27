<?php

namespace App\Models;

class GoodsSetting extends Model
{
    const PAY_TYPE_NUMBER = 0;
    const PAY_TYPE_PERCENT = 1;

    protected $fillable = [
        'goods_id', 'store_id', 'shipping_charging_type'
        , 'shipping_rate', 'unpack_fee', 'driver_charging_type'
        , 'driver_rate'
    ];

    /**
     * sku
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo(\App\Models\Goods::class, 'goods_id', 'goods_id');
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

    public function doCalculate($goodsPayment, GoodsSetting $goodsSetting)
    {
        if (empty($goodsSetting)) {
            return [];
        }
        $shippingFee = $driverFee = 0;
        $unPackFee = $goodsSetting['unpack_fee'] * $goodsPayment['shifa_number'];
        if (self::PAY_TYPE_NUMBER == $goodsSetting['shipping_charging_type']) {
            $shippingFee = $goodsPayment['shifa_number'] * $goodsSetting['shipping_rate'];
        } else if(self::PAY_TYPE_PERCENT == $goodsSetting['shipping_charging_type']){
            $shippingFee = $goodsPayment['shifa_amount'] * $goodsSetting['shipping_rate'];
        }

        if (self::PAY_TYPE_NUMBER == $goodsSetting['driver_charging_type']) {
            $driverFee = $goodsPayment['shifa_number'] * $goodsSetting['driver_rate'];
        } else if(self::PAY_TYPE_PERCENT == $goodsSetting['driver_charging_type']){
            $driverFee = $goodsPayment['shifa_amount'] * $goodsSetting['driver_rate'];
        }

        $goodsPayment['delivery_fee'] = $unPackFee + $shippingFee;
        $goodsPayment['driver_fee'] = $driverFee;

        //组织goodsPayment信息
        $goodsPayment['shipping_charging_type'] = $goodsSetting['shipping_charging_type'];
        $goodsPayment['shipping_rate'] = $goodsSetting['shipping_rate'];
        $goodsPayment['unpack_fee'] = $goodsSetting['unpack_fee'];
        $goodsPayment['driver_charging_type'] = $goodsSetting['driver_charging_type'];
        $goodsPayment['driver_rate'] = $goodsSetting['driver_rate'];
        $goodsPayment['driver_rate'] = $goodsSetting['driver_rate'];

        return $goodsPayment;
    }

    /**
     * 单个sku计算费用
     *
     * @param $goodsPayment $orderGoodsPayments
     * @return array
     */
    public function calculate($goodsPayment)
    {
        $goodsSetting = $this->where('goods_id', $goodsPayment['goods_id'])->first();
        if (empty($goodsSetting)) {
            return $goodsPayment;
        }

        return $this->doCalculate($goodsPayment, $goodsSetting);
    }

    /**
     * 计算配送费和司机费用
     * @param mixed $goodsPayments orderGoodsPaymentList
     */
    public function calculateMulti($goodsPayments)
    {
        $goodsIds = collect($goodsPayments)->pluck('goods_id');
        $goodsSettings = $this->whereIn('goods_id', $goodsIds)->get();
        if ($goodsIds->count() > $goodsSettings->count()) {
            return ['status' => false, '部分货品未设置配送费计算公式'];
        }
        $goodsSettings = $goodsSettings->keyBy('goods_id');
        foreach ($goodsPayments as $key => $payment) {
            $goodsId = $payment['goods_id'];
            $goodsPayments[$key] = $this->doCalculate($payment, $goodsSettings[$goodsId]);
        }

        return ['status' => true, 'data' => $goodsPayments];
    }
}
