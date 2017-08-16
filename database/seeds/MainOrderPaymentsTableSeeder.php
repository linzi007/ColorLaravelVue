<?php

use Illuminate\Database\Seeder;
use App\Models\MainOrderPayment;

class MainOrderPaymentsTableSeeder extends Seeder
{
    public function run()
    {
        $main_order_payments = factory(MainOrderPayment::class)->times(50)->make()->each(function ($main_order_payment, $index) {
            if ($index == 0) {
                // $main_order_payment->field = 'value';
            }
        });

        MainOrderPayment::insert($main_order_payments->toArray());
    }

}

