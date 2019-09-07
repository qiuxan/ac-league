<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRolePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->boolean('deleted')->default(0);
            $table->integer('created_by');
            $table->integer('updated_by')->default(0);
            $table->integer('priority')->default(0);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('pp_group')->default(0);
            $table->boolean('deleted')->default(0);
            $table->integer('created_by');
            $table->integer('updated_by')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
