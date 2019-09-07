<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIngredientShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingredient_shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tracking_code', 50)->nullable();
            $table->integer('source_id');
            $table->integer('destination_id');
            $table->dateTime('shipped_time');
            $table->dateTime('received_time')->nullable();
            $table->string('notes')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->default(0);
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        Schema::create('shipment_ingredient_lots', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ingredient_shipment_id');
            $table->integer('ingredient_lot_id');
            $table->integer('created_by');
            $table->integer('updated_by')->default(0);
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingredient_shipments');
        Schema::dropIfExists('shipment_ingredient_lots');
    }
}
