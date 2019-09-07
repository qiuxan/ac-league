<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id')->default(0);
            $table->string('batch_code', 12)->nullable();
            $table->integer('product_id');
            $table->integer('quantity')->default(0);
            $table->string('location', 100)->nullable();
            $table->tinyInteger('disposition_id')->nullable();
            $table->date('production_date')->nullable();
            $table->date('expiration_date')->nullable();
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
        Schema::dropIfExists('batches');
    }
}
