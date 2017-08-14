<?php

use Illuminate\Database\Seeder;
use App\Models\Driver;

class DriversTableSeeder extends Seeder
{
    public function run()
    {
        $drivers = factory(Driver::class)->times(50)->make()->each(function ($driver, $index) {
            if ($index == 0) {
                // $driver->field = 'value';
            }
        });

        Driver::insert($drivers->toArray());
    }

}

