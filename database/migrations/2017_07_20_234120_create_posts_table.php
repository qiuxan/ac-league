<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->default(1);
            $table->string('language', 2)->default('en');
            $table->string('icon', 30)->nullable();
            $table->string('feature_image')->nullable();
            $table->string('title')->nullable();
            $table->string('alias');
            $table->text('excerpt')->nullable();
            $table->text('content')->nullable();
            $table->boolean('published')->default(0);
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
        Schema::dropIfExists('posts');
    }
}
