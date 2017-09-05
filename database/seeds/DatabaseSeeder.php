<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(UsersTableSeeder::class);
		$this->call(AdminsTableSeeder::class);
		$this->call(ExchangeBottlesTableSeeder::class);
        $this->call(DriversTableSeeder::class);
        $this->call(GoodsSettingsTableSeeder::class);
        //$this->call(OrderGoodsPaymentsTableSeeder::class);
        //$this->call(SubOrderPaymentsTableSeeder::class);
        //$this->call(MainOrderPaymentsTableSeeder::class);
    }
}
