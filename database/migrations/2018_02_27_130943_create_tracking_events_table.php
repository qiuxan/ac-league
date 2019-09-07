<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackingEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_type')->default(0);
            $table->integer('action')->default(0);
            $table->dateTime('event_time');
            $table->integer('business_step');
            $table->integer('disposition');
            $table->integer('source_id');
            $table->integer('destination_id');
            $table->integer('object_type');
            $table->integer('object_id');
            $table->integer('transaction')->nullable();
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
        Schema::dropIfExists('tracking_events');
    }
}
