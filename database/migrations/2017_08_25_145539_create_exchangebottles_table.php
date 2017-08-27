<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangeBottlesTable extends Migration 
{
	public function up()
	{
		Schema::create('exchange_bottles', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->index();
            $table->float('amount')->default('0.0')->comment('换盖金额');
            $table->integer('driver_id')->nullable()->comment('司机id');
            $table->string('pay_sn')->nullable()->comment('支付单号');
            $table->string('creator')->comment('操作员');
            $table->timestamps();
        });
	}

	public function down()
	{
		Schema::drop('exchange_bottles');
	}
}
