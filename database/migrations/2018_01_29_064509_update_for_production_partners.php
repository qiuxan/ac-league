<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateForProductionPartners extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->integer('reseller_id')->default(0);
        });

        Schema::table('codes', function (Blueprint $table) {
            $table->integer('reseller_id')->default(0);
        });

        Schema::table('rolls', function (Blueprint $table) {
            $table->integer('production_partner_id')->default(0);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->integer('production_partner_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
