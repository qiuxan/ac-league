<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_question_options', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_id');
            $table->string('option_en');
            $table->string('option_cn');
            $table->string('option_tr');
            $table->integer('priority')->default(0);
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
        Schema::dropIfExists('question_options');
    }
}
