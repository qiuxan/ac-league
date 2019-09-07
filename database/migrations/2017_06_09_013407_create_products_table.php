<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gtin', 20)->nullable();
            $table->integer('member_id');
            $table->string('name_en');
            $table->string('name_cn');
            $table->string('origin_en');
            $table->string('origin_cn');
            $table->string('volume_en', 20)->nullable();
            $table->string('volume_cn', 20)->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_cn')->nullable();
            $table->text('ingredients_en')->nullable();
            $table->text('ingredients_cn')->nullable();
            $table->text('benefits_en')->nullable();
            $table->text('benefits_cn')->nullable();
            $table->string('manufacturer_name_en')->nullable();
            $table->string('manufacturer_name_cn')->nullable();
            $table->string('manufacturer_address')->nullable();
            $table->string('manufacturer_email')->nullable();
            $table->string('manufacturer_website')->nullable();
            $table->string('manufacturer_phone')->nullable();
            $table->string('production_partner_name_en')->nullable();
            $table->string('production_partner_name_cn')->nullable();
            $table->string('production_partner_address')->nullable();
            $table->string('production_partner_phone')->nullable();
            $table->string('safety_certificate')->nullable();
            $table->boolean('deleted')->default(false);
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('products');
    }
}
