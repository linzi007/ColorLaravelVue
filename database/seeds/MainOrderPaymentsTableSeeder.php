<?php

use Illuminate\Database\Seeder;
use App\Models\MainOrderPayment;

class MainOrderPaymentsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create('zh_CN');
        //取得50条交易订单信息
        $mainOrders = \App\Models\MainOrder::with('orderGoods')->where('pay_id', '<=', '10923')->orderBy('pay_id', 'desc')->limit(50)->get();
        //获取对应goods_id
        $main_order_payments = factory(MainOrderPayment::class)->times(50)->make()->each(function ($payment, $index) use($faker, $mainOrders) {
            $mainOrder = $mainOrders[$index];
            $payment['pay_id'] = $mainOrder['pay_id'];
            $payment['pay_sn'] = $mainOrder['pay_sn'];
            $payment['store_id'] = $mainOrder['store_id'];
            $payment['add_time'] = $mainOrder['add_time'];
        });

        MainOrderPayment::insert($main_order_payments->toArray());
    }

}

