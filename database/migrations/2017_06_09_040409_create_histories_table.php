<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('code_id');
            $table->string('location')->nullable();
            $table->string('operation_system', 20)->nullable();
            $table->string('browser', 20)->nullable();
            $table->boolean('is_mobile')->default(0);
            $table->string('ip_address', 50)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
            $table->index('code_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('histories');
    }
}
