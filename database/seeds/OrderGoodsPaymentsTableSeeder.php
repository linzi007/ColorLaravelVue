<?php

use Illuminate\Database\Seeder;
use App\Models\OrderGoodsPayment;

class OrderGoodsPaymentsTableSeeder extends Seeder
{
    public function run()
    {
        $order_goods_payments = factory(OrderGoodsPayment::class)->times(50)->make()->each(function ($order_goods_payment, $index) {
            if ($index == 0) {
                // $order_goods_payment->field = 'value';
            }
        });

        OrderGoodsPayment::insert($order_goods_payments->toArray());
    }

}

