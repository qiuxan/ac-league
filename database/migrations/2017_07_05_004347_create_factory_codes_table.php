<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFactoryCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factory_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('factory_batch_id');
            $table->string('full_code', 13);
            $table->string('password', 6);
            $table->unique('full_code');
            $table->index('factory_batch_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factory_codes');
    }
}
