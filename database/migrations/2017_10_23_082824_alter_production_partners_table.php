<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterProductionPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_partners', function (Blueprint $table) {
            DB::statement('ALTER TABLE `production_partners` MODIFY `address` VARCHAR(255) NULL;');
            DB::statement("UPDATE `production_partners` SET `address` = NULL WHERE `address` = '';");

            DB::statement('ALTER TABLE `production_partners` MODIFY `phone` VARCHAR(255) NULL;');
            DB::statement("UPDATE `production_partners` SET `phone` = NULL WHERE `phone` = '';");                        
        });    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {    
        DB::statement("UPDATE `production_partners` SET `address` = '' WHERE `address` IS NULL;");
        DB::statement('ALTER TABLE `production_partners` MODIFY `address` VARCHAR(255) NOT NULL;');
        
        DB::statement("UPDATE `production_partners` SET `phone` = '' WHERE `phone` IS NULL;");
        DB::statement('ALTER TABLE `production_partners` MODIFY `phone` VARCHAR(255) NOT NULL;');        
    }
}
