<?php

namespace App\Models;


use Carbon\Carbon;
use Exception;

class SubOrderPayment extends Model
{

    protected $fillable = [
        'order_id', 'order_sn', 'store_id', 'pay_id', 'pay_sn', 'add_time', 'desc_remark', 'desc_remark', 'quehuo', 'jushou', 'shifa', 'percent', 'store_id', 'qiandan', 'ziti', 'qita', 'weicha'
        , 'yingshou', 'pos', 'weixin', 'alipay', 'yizhifu', 'cash', 'shishou', 'delivery_fee', 'driver_fee'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'add_time'
    ];
    /**
     * sub order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subOrder()
    {
        return $this->belongsTo(\App\Models\Order::class, 'order_id');
    }

    /**
     * sub order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mainPayment()
    {
        return $this->belongsTo(\App\Models\Order::class, 'order_id');
    }

    public function updateDeliveryFee($payId)
    {
        $mainOrderInfo = app(\App\Models\MainOrderPayment::class)->select(['delivery_fee', 'driver_fee'])
            ->where('pay_id', $payId)->first();

        $orderPayments = $this->select(['id', 'pay_id', 'store_id', 'percent'])->where('pay_id', $payId)->get();
        foreach ($orderPayments as $payment) {
            $updateData = [
                'delivery_fee' => $payment['percent'] * $mainOrderInfo['delivery_fee'],
                'driver_fee'   => $payment['percent'] * $mainOrderInfo['driver_fee'],
            ];
            $result = $this->where('id', $payment['id'])->update($updateData);
            if ($result === false) {
                throw new Exception('更新主单配送费失败!' . $payId);
            }
        }

        return true;
    }

    public function getAddTimeAttribute($value)
    {
        return Carbon::createFromTimestamp($value)->toDateTimeString();
    }

    public function setAddTimeAttribute($value)
    {
        return $this->attributes['add_time'] = strtotime($value);
    }
}
