<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id');            
            $table->tinyInteger('storage_type')->default(0);
            $table->string('name', 100);
            $table->string('original_name', 100);
            $table->string('type', 12)->nullable();
            $table->string('size', 20)->nullable();
            $table->string('location');
            $table->integer('created_by')->default(0);
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
        Schema::dropIfExists('member_files');        
    }
}
