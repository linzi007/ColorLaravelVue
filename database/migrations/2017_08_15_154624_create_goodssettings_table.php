<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsSettingsTable extends Migration 
{
	public function up()
	{
		Schema::create('goods_settings', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('goods_id')->index()->comment('sku');
            $table->integer('store_id')->index()->comment('档口id');
            $table->boolean('shipping_charging_type')->nullable()->default(0)->comment('配送计费方式：0：计件，1：金额比例');
            $table->float('shipping_rate')->default(0)->comment('单件费用or费率');
            $table->float('unpack_fee')->default(0)->comment('单件拆包费');
            $table->boolean('driver_charging_type')->nullable()->default(0)->comment('司机计费方式');
            $table->float('driver_rate')->default(0)->comment('单件费用or费率');
            $table->timestamps();
        });
	}

	public function down()
	{
		Schema::drop('goods_settings');
	}
}
