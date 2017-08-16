<?php

use Illuminate\Database\Seeder;
use App\Models\GoodsSetting;

class GoodsSettingsTableSeeder extends Seeder
{
    public function run()
    {
        $goods_settings = factory(GoodsSetting::class)->times(50)->make()->each(function ($goods_setting, $index) {
            if ($index == 0) {
                // $goods_setting->field = 'value';
            }
        });

        GoodsSetting::insert($goods_settings->toArray());
    }

}

