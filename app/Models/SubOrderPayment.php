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
        'updated_at'
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

    /**
     * 子单拆分插入 @TODO test
     *
     * @param MainOrderPayment $mainOrderPayments
     * @throws Exception
     */
    public function splitSubOrder(MainOrderPayment $mainOrderPayments)
    {
        $mainOrderPayments->load(['subOrders.orderGoods.payments']);
        if (empty($subOrders = $mainOrderPayments['subOrders'])) {
            throw new Exception('子订单不存在!');
        }
        //取得更新的数据
        $orderPayments = [];
        //子单相同的值
        $orderPaymentDefault = [
            'pay_id'       => $mainOrderPayments['pay_id'],
            'pay_sn'       => $mainOrderPayments['pay_sn'],
            'add_time'     => strtotime($mainOrderPayments['add_time']),
            'desc_remark'  => $mainOrderPayments['desc_remark'],
            'quehuo' => 0,
            'jushou' => 0,
            'shifa' => 0
        ];

        //剩余扣减金额
        $mainOrderRemain = [
            'percent'      => 100,
            'qiandan'      => $mainOrderPayments['qiandan'],
            'ziti'         => $mainOrderPayments['ziti'],
            'qita'         => $mainOrderPayments['qita'],
            'weicha'       => $mainOrderPayments['weicha'],
            'pos'          => $mainOrderPayments['pos'],
            'weixin'       => $mainOrderPayments['weixin'],
            'alipay'       => $mainOrderPayments['alipay'],
            'yizhifu'      => $mainOrderPayments['yizhifu'],
            'cash'         => $mainOrderPayments['cash'],
            'delivery_fee' => $mainOrderPayments['delivery_fee'],
            'driver_fee'   => $mainOrderPayments['driver_fee'],
        ];

        //缺货,拒收,实发,百分比 需要根据实际发货计算
        $subOrderCount = count($subOrders);
        foreach ($subOrders as $key => $order) {
            if ($subOrderCount == 1) {
                $orderPayment = array_merge($orderPayments, $mainOrderRemain);
                $orderPayment['quehuo'] = $mainOrderPayments['quehuo'];
                $orderPayment['jushou'] = $mainOrderPayments['jushou'];
                $orderPayment['shifa'] = $mainOrderPayments['shifa'];
                $orderPayment['shishou'] = $mainOrderPayments['shishou'];
                $orderPayment['yingshou'] = $mainOrderPayments['yingshou'];
            } else {
                $orderPayment = $orderPaymentDefault;
                if ($goodsList = $order['orderGoods']) {
                    foreach ($goodsList as $goods) {
                        $goodsPayment = $goods['payments'];
                        $orderPayment['quehuo'] += $goods['goods_price'] * $goodsPayment['quehuo_number'];
                        $orderPayment['jushou'] += $goods['goods_price'] * $goodsPayment['jushou_number'];
                        $orderPayment['shifa'] += $goodsPayment['shifa_amount'];
                    }
                }

                $promotionAmount = $order['share_site_promotion'] + $order['share_union_promotion'];
                $pdAmount = $order['pd_amount'];

                $percent = round($order['order_amount']/$mainOrderPayments['yingshou'], 2) * 100;
                $orderPayment['percent'] = $percent;
                //金额赋值以及金额修正
                if (($subOrderCount - 1) == $key) {
                    //最后一个子单的金额为前边子单金额剩余数据
                    foreach ($mainOrderRemain as $field => $amount) {
                        $orderPayment[$field] = $mainOrderRemain[$field];
                    }
                } else {
                    //剩余金额保证非负数
                    foreach ($mainOrderRemain as $field => $amount) {
                        $orderPayment[$field]      = round(($mainOrderPayments[$field] * $percent / 100), 2);
                        if ($mainOrderRemain[$field] && ($mainOrderRemain[$field] > $orderPayment[$field])) {
                            $mainOrderRemain[$field] = $mainOrderRemain[$field] - $orderPayment[$field];
                        } else {
                            $orderPayment[$field] = $mainOrderRemain[$field];
                            $mainOrderRemain[$field] = 0;
                        }
                    }
                }

                $orderPayment['shishou'] = $pdAmount + $orderPayment['pos'] + $orderPayment['weixin']
                    + $orderPayment['alipay'] + $orderPayment['yizhifu'] + $orderPayment['cash'];
                $orderPayment['yingshou'] = $orderPayment['shifa'] - $orderPayment['qiandan']
                    - $orderPayment['ziti'] - $orderPayment['qita'] - $promotionAmount;
            }

            $condition = [
                'pay_id'   => $order['pay_id'],
                'store_id' => $order['store_id'],
            ];
            $orderPayment['store_id'] = $order['store_id'];
            $orderPayment['order_id'] = $order['order_id'];
            $orderPayment['order_sn'] = $order['order_sn'];
            //数据写入或则更新
            $result = $this->updateOrInsert($condition, $orderPayment);
            if ($result === false) {
                throw new Exception('子单收款登记表更新失败!');
            }
        }
    }
}
