<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriversTable extends Migration 
{
	public function up()
	{
		Schema::create('drivers', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20)->index();
            $table->string('code', 20)->nullable();
            $table->string('mobile', 20)->index();
            $table->text('description')->nullable();
            $table->integer('admin_id')->nullable();
            $table->integer('operator_id')->nullable();
            $table->timestamps();
        });
	}

	public function down()
	{
		Schema::drop('drivers');
	}
}
