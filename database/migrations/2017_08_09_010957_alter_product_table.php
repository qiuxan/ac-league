<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('company_en')->nullable();
            $table->string('company_cn')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('company_website')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('company_en');
            $table->dropColumn('company_cn');
            $table->dropColumn('company_logo');
            $table->dropColumn('company_website');
        });
    }
}
