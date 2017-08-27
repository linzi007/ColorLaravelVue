<?php

use Illuminate\Database\Seeder;
use App\Models\ExchangeBottle;

class ExchangeBottlesTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create('zh_CN');
        $model = new \App\Models\MainOrder();
        $mainOrders = $model->orderBy('pay_id', 'desc')->limit(50)->get()->toArray();
        $exchange_bottles = factory(ExchangeBottle::class)->times(50)->make()->each(function ($exchange_bottle, $index) use ($faker, $mainOrders) {
            $mainOrder = $faker->randomElement($mainOrders);
            $exchange_bottle['pay_sn'] = $mainOrder['pay_sn'];
            $exchange_bottle['amount'] = $faker->randomFloat(2, 0, 200);
            $exchange_bottle['driver_id'] = $faker->numberBetween(1, 50);
            $exchange_bottle['creator'] = $faker->numberBetween(1, 10);
            $exchange_bottle['store_id'] = 222;
        });

        ExchangeBottle::insert($exchange_bottles->toArray());
    }

}

