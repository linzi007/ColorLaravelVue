<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;

class MainOrderPayment extends Model
{
    protected $fillable = [
        'pay_id', 'pay_sn', 'add_time', 'quehuo', 'jushou', 'qiandan', 'ziti', 'qita',
        'weicha', 'desc_remark', 'pos', 'weixin', 'alipay',
        'yizhifu', 'out_pay_sn', 'cash', 'delivery_fee',
        'driver_fee', 'second_driver_id', 'jk_driver_id', 'updater',
        'shifa', 'yingshou', 'shishou', 'jk_at', 'ck_at', 'remark'
    ];

    // 日期字段
    /*protected $dates = [
        'created_at',
        'updated_at',
        'jk_at',
        'ck_at',
        'jz_at',
        'add_time'
    ];*/

    protected $casts = [
        'quehuo'  => 'float',
        'jushou'  => 'float',
        'qiandan' => 'float',
        'ziti'    => 'float',
        'qita'    => 'float',
        'weicha'  => 'float',
        'pos'     => 'float',
        'weixin'  => 'float',
        'alipay'  => 'float',
        'yizhifu' => 'float',
        'cash'    => 'float',
    ];

    /**
     * 主订单
     */
    public function mainOrder()
    {
        $this->belongsTo(\App\Models\MainOrder::class, 'pay_id', 'pay_id');
    }

    /**
     * 子支付信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subOrderPayments()
    {
        return $this->hasMany(\App\Models\SubOrderPayment::class, 'pay_id', 'pay_id');
    }

    /**
     * 配送实际
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function driver()
    {
        return $this->belongsTo(\App\Models\Driver::class, 'driver_id');
    }

    /**
     * 交款司机信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function jkDriver()
    {
        return $this->hasOne(\App\Models\Driver::class, 'id', 'jk_driver_id');
    }

    /**
     * 记账人信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function jzAdmin()
    {
        return $this->hasOne(\App\Models\Admin::class, 'admin_id', 'jzr');
    }

    public function jizhang($payIds)
    {
        $data = [
            'status' => 1,
            'jz_at' => Carbon::now(),
            'jzr' => currentUserId(),
            'updater' => currentUserId(),
        ];
        return $this->where('status', 0)->whereIn('pay_id', $payIds)->update($data);
    }

    public function fanjizhang($payIds)
    {
        $data = [
            'status' => 0,
            'jz_at' => Carbon::now(),
            'jzr' => currentUserId(),
            'updater' => currentUserId(),
        ];

        return $this->where('status', 1)->whereIn('pay_id', $payIds)->update($data);
    }

    public function updateDeliveryFee($payId)
    {
        $orderGoodsPayments = app(\App\Models\OrderGoods::class)->with('payments')->where('pay_id', $payId)->get()->pluck('payments');
        $deliveryFee = $orderGoodsPayments->sum('delivery_fee');
        $driverFee = $orderGoodsPayments->sum('driver_fee');
        $result = $this->where('pay_id', $payId)->update(['delivery_fee' => $deliveryFee, 'driver_fee' => $driverFee]);
        if ($result === false) {
            throw new Exception('更新主单配送费失败!' . $payId);
        }
        return true;
    }

    public function getAddTimeAttribute($value)
    {
        return Carbon::createFromTimestamp($value)->toDateTimeString();
    }

    public function setAddTimeAttribute($value)
    {
        return strtotime($value);
    }
}
