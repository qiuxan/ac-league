<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIngredientLotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingredient_lots', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id')->default(0);
            $table->integer('production_partner_id')->default(0);
            $table->integer('current_pp_id')->default(0)->nullable();
            $table->string('lot_code', 12)->nullable();
            $table->integer('ingredient_id');
            $table->date('production_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('certificate_url')->nullable();
            $table->boolean('finished')->default(0);
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
        Schema::dropIfExists('ingredient_lots');
    }
}
