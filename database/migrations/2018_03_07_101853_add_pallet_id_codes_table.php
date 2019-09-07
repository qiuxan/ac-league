<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPalletIdCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('codes', function(Blueprint $table){
            $table->integer('pallet_id')->default(0);
            $table->index('pallet_id', 'pallet_id_index');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('codes', function(Blueprint $table){
            $table->dropIndex('pallet_id_index');
            $table->dropColumn('pallet_id');
        });
    }
}
