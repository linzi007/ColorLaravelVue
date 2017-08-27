<?php

use Illuminate\Database\Seeder;
use App\Models\OrderGoodsPayment;

class OrderGoodsPaymentsTableSeeder extends Seeder
{
    public function run()
    {
        //OrderGoodsPayment::truncate();
        $faker = Faker\Factory::create('zh_CN');
        //取得50条交易订单信息
        $mainOrders = \App\Models\MainOrder::with('orderGoods')->where('pay_id', '<=', '10923')->orderBy('pay_id', 'desc')->limit(50)->get();

        $orderGoods = $mainOrders->map(function ($mainOrder) {
            return $mainOrder->orderGoods;
        })->flatten(1);

        $order_goods_payments = factory(OrderGoodsPayment::class)->times($orderGoods->count())
            ->make()->each(function ($payment, $index) use ($faker, $orderGoods){
                $goods = $orderGoods[$index];
                $goodsSetting = \App\Models\GoodsSetting::where('goods_id', $goods['goods_id'])->first();
                $payment['goods_id'] = $goods['goods_id'];

                $payment['id'] = $goods['rec_id'];
                $quehuoNumber = ($goods['goods_num'] - 1) >= 0 ? ($goods['goods_num'] - 1) : $goods['goods_num'];
                $payment['quehuo_number'] = $faker->numberBetween(0, $quehuoNumber);
                $payment['jushou_number'] = intval($faker->boolean(20));
                $payment['shifa_number'] = $goods['goods_num'] - $payment['quehuo_number'] - $payment['jushou_number'];
                $payment['shifa_amount'] = $goods['goods_price'] * $payment['shifa_number'];
                $payment['shipping_charging_type'] = $goodsSetting['shipping_charging_type'];
                $payment['shipping_rate'] = $goodsSetting['shipping_rate'];
                $payment['unpack_fee'] = $goodsSetting['unpack_fee'];
                $payment['delivery_fee'] = $this->getShippingFee($goodsSetting, $payment);
                $payment['driver_charging_type'] = $goodsSetting['driver_charging_type'];
                $payment['driver_rate'] = $goodsSetting['driver_rate'];
                $payment['driver_fee'] = $this->getDriverFee($goodsSetting, $payment);

                $payment['store_id'] = $goods['store_id'];
                $payment['order_id'] = $goods['order_id'];
                $orderSn = \App\Models\Order::where('order_id', $goods['order_id'])->first(['order_sn'])->order_sn;
                $payment['order_sn'] = $orderSn;
        });

        OrderGoodsPayment::insert($order_goods_payments->toArray());
    }

    public function getShippingFee($goodsSetting, $payment)
    {
        $baseFee = $payment['shifa_number'] * $goodsSetting['unpack_fee'];
        if ($goodsSetting['shipping_charging_type']) {
            $fee = $baseFee + $payment['shifa_amount'] * $goodsSetting['shipping_rate'];
        } else {
            $fee =  $payment['shifa_number'] * $goodsSetting['shipping_rate'];
        }

        return $fee;
    }

    public function getDriverFee($goodsSetting, $payment)
    {
        $baseFee = 0;
        if ($goodsSetting['shipping_charging_type']) {
            $fee = $baseFee + $payment['shifa_amount'] * $goodsSetting['driver_rate'];
        } else {
            $fee =  $baseFee + $payment['shifa_number'] * $goodsSetting['driver_rate'];
        }

        return $fee;
    }

}

