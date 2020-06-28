<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingRatesTable extends Migration
{
    public function up()
    {
        Schema::create('butik_shipping_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('shipping_zone_id')->index();
            $table->string('title');
            $table->integer('price');
            $table->integer('minimum');

            $table->timestamps();

            $table->foreign('shipping_zone_id')
                ->references('id')
                ->on('butik_shipping_zones')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('butik_shipping_rates');
    }
}
