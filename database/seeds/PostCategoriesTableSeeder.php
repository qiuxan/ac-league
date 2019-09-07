<?php

use Illuminate\Database\Seeder;
use App\PostCategory;

class PostCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $post_category = new PostCategory();
        $post_category->category = "General";
        $post_category->save();

        $post_category = new PostCategory();
        $post_category->category = "Services";
        $post_category->save();

        $post_category = new PostCategory();
        $post_category->category = "News";
        $post_category->save();

        $post_category = new PostCategory();
        $post_category->category = "Why us";
        $post_category->save();
    }
}
