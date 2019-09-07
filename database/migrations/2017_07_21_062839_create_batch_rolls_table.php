<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchRollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_rolls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('batch_id');
            $table->integer('roll_id');
            $table->string('start_code', 13);
            $table->string('end_code', 13);
            $table->integer('code_quantity')->default(0);
            $table->boolean('deleted')->default(0);
            $table->integer('created_by');
            $table->integer('updated_by')->default(0);
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
        Schema::dropIfExists('batch_rolls');
    }
}
