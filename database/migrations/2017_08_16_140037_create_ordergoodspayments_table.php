<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderGoodsPaymentsTable extends Migration 
{
	public function up()
	{
		Schema::create('order_goods_payments', function(Blueprint $table) {
            $table->increments('id')->comment('order_goods.rec_id');
            $table->integer('goods_id')->index()->comment('货品id');
            $table->integer('order_id')->index()->comment('子单id');
            $table->integer('store_id')->index()->comment('档口id');
            $table->string('order_sn', 30)->comment('子单编号');
            $table->integer('quehuo_number')->nullable()->default(0)->comment('缺货数量');
            $table->integer('jushou_number')->nullable()->default(0)->comment('拒收数量');
            $table->integer('shifa_number')->nullable()->default(0)->comment('实发数量');
            $table->float('shifa_amount')->nullable()->default(0)->comment('实发金额');
            $table->boolean('shipping_charging_type')->nullable()->default(null)->comment('配送计费方式：0数量，1金额比例');
            $table->float('shipping_rate')->nullable()->default(0.00)->comment('配送单件运费/费率');
            $table->float('unpack_fee')->nullable()->default(0.00)->comment('配送单件拆包费');
            $table->float('delivery_fee')->nullable()->default(0.00)->comment('应付配送费合计');
            $table->boolean('driver_charging_type')->nullable()->default(null)->comment('司机费用计费方式：0数量，1金额比例');
            $table->float('driver_rate')->nullable()->default(0.00)->comment('司机单件费用/费率');
            $table->float('driver_fee')->nullable()->default(0.00)->comment('应付司机费用合计');
            $table->timestamps();
        });
	}

	public function down()
	{
		Schema::drop('order_goods_payments');
	}
}
