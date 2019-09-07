<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionPartnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_partners', function(Blueprint $table){
            $table->increments('id');
            $table->integer('member_id');
            $table->string('name_en');
            $table->string('name_cn');
            $table->string('name_tr');
            $table->string('address');
            $table->string('phone');
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
        Schema::dropIfExists('production_partners');
    }
}
