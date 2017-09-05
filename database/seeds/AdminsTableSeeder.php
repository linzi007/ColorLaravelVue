<?php

use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminsTableSeeder extends Seeder
{
    public function run()
    {
        $admins = factory(Admin::class)->times(50)->make()->each(function ($admin, $index) {
            if ($index == 0) {
                // $admin->field = 'value';
            }
        });

        Admin::insert($admins->toArray());
    }

}

