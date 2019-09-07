<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTraditionalChineseFieldsToProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('name_tr');
            $table->string('origin_tr');
            $table->string('volume_tr', 20)->nullable();
            $table->text('description_tr')->nullable();
            $table->text('ingredients_tr')->nullable();
            $table->text('benefits_tr')->nullable();
            $table->string('manufacturer_name_tr')->nullable();
            $table->string('production_partner_name_tr')->nullable();
            $table->string('expiration_notes_tr')->nullable();     
            $table->string('company_tr')->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function($table) {
            $table->dropColumn('name_tr');
            $table->dropColumn('origin_tr');
            $table->dropColumn('volume_tr');
            $table->dropColumn('description_tr');
            $table->dropColumn('ingredients_tr');
            $table->dropColumn('benefits_tr');
            $table->dropColumn('manufacturer_name_tr');
            $table->dropColumn('production_partner_name_tr');
            $table->dropColumn('expiration_notes_tr');            
            $table->dropColumn('company_tr');
        });
    }
}
