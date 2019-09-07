<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->integer('product_id');
            $table->integer('file_id');
            $table->text('description_en')->nullable();
            $table->text('description_cn')->nullable();
            $table->boolean('thumbnail')->default(false);
            $table->integer('priority')->default(0);
            $table->primary(array('product_id', 'file_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_images');
    }
}
