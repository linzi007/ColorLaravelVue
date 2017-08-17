<?php

use Illuminate\Database\Seeder;
use App\Models\GoodsSetting;

class GoodsSettingsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create('zh_CN');
        //取得50条交易订单信息
        $mainOrders = \App\Models\MainOrder::with('orderGoods')->where('pay_id', '<=', '10923')->orderBy('pay_id', 'desc')->limit(50)->get();

        $goodsIds = $mainOrders->map(function ($mainOrder) {
            return $mainOrder->orderGoods;
        })->flatten(1)->map(function ($orderGoods) {
            return $orderGoods->goods_id;
        })->unique();
        //取得订单对应的goods_id
        $goodsList = \App\Models\Goods::with('goodsCommon')->whereIn('goods_id', $goodsIds)->get();
        //获取对应goods_id
        $times = $goodsList->count();

        $goods_settings = factory(GoodsSetting::class)->times($times)->make()->each(function ($setting, $index)  use ($faker, $goodsList){
            if (!empty($goods = $goodsList[$index])) {
                $setting['goods_id'] = $goods['goods_id'];
                $setting['store_id'] = $goods['store_id'];
                $setting['shipping_charging_type'] = $faker->boolean();
                $setting['shipping_rate'] = $faker->randomFloat(2, 0, 1);
                $setting['unpack_fee'] = $faker->randomFloat(1, 0.0, 0.5);
                $setting['driver_charging_type'] = $faker->boolean();
                $setting['driver_rate'] = $faker->randomFloat(2, 0, 1);
            }
        });

        GoodsSetting::insert($goods_settings->toArray());
    }

}

