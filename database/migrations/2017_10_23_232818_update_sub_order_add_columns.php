<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSubOrderAddColumns extends Migration
{
    /**
     * Run the migrations.
     * 新增财务字段
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_order_payments', function (Blueprint $table){
            $table->float('reduce_coupon')->default('0.00')->nullable()->comment('扣代金券');
            $table->float('help_pd_amount')->default('0.00')->nullable()->comment('代扣预存款');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_order_payments', function (Blueprint $table){
            $table->dropColumn('help_pd_amount');
            $table->dropColumn('reduce_coupon');
        });
    }
}
