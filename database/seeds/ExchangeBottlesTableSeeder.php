<?php

use Illuminate\Database\Seeder;
use App\Models\ExchangeBottle;

class ExchangeBottlesTableSeeder extends Seeder
{
    public function run()
    {
        $exchange_bottles = factory(ExchangeBottle::class)->times(50)->make()->each(function ($exchange_bottle, $index) {
            if ($index == 0) {
                // $exchange_bottle->field = 'value';
            }
        });

        ExchangeBottle::insert($exchange_bottles->toArray());
    }

}

