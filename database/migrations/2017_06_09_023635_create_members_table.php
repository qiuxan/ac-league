<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_en');
            $table->string('company_cn');
            $table->string('phone', 20);
            $table->string('company_email');
            $table->string('website')->nullable();
            $table->string('country_en');
            $table->string('country_cn');
            $table->string('logo')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('user_id');
            $table->boolean('deleted')->default(false);
            $table->integer('created_by');
            $table->integer('updated_by')->default(0);
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
        Schema::dropIfExists('members');
    }
}
