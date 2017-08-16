<?php

use Illuminate\Database\Seeder;
use App\Models\SubOrderPayment;

class SubOrderPaymentsTableSeeder extends Seeder
{
    public function run()
    {
        $sub_order_payments = factory(SubOrderPayment::class)->times(50)->make()->each(function ($sub_order_payment, $index) {
            if ($index == 0) {
                // $sub_order_payment->field = 'value';
            }
        });

        SubOrderPayment::insert($sub_order_payments->toArray());
    }

}

