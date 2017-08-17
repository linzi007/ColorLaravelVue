<?php

use Illuminate\Database\Seeder;
use App\Models\Driver;

class DriversTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create('zh_CN');
        $drivers = factory(Driver::class)->times(50)->make()->each(function ($driver, $index) use ($faker) {
            $driver->code = '0000' . $index;
            $driver->name = $faker->name;
            $driver->mobile = $faker->phoneNumber;
            $driver->description = $faker->words(5, true);
            if ($index == 0) {
                // $driver->field = 'value';
            }
        });

        Driver::insert($drivers->toArray());
    }

}

