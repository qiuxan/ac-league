<?php

namespace App\Http\Controllers\Staff;
use App\Code;
use App\Disposition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;
use \Datetime;
use App\Post;
use App\PostCategory;
use App\Utils;

class PostController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'staff.posts.index' );
    }

    public function getPostForm() {
        return view( 'staff.posts.form');
    }

    public function getPostList() {
        return view( 'staff.posts.list' );
    }    

    public function getPost(Request $request) {
        $id = $request->input('id');
        $post = DB::table('posts')->select('posts.*')->where(['posts.id' => $id, 'posts.deleted' => 0])->first();

        return response()->json($post);
    }

    public function getPosts(Request $request) {
        $pageSize = $request->input('pageSize');
        $filters = $request->input('filters');
        if(isset($filters['search']) && $filters['search'])
        {
            $search = $filters['search'];
        }
        else
        {
            $search = '';
        }
        $posts = DB::table('posts')->select('posts.*', 'post_categories.category')
            ->leftJoin('post_categories', 'posts.category_id', '=', 'post_categories.id')
            ->where('posts.deleted', 0)
            ->where('title', 'like', "%$search%")
            ->orderBy('id', 'desc')->paginate( $pageSize );

        return response()->json($posts);
    }

    public function getPostCategories(Request $request) {
        $pageSize = $request->input('pageSize');
        $categories = PostCategory::all();

        return response()->json($categories);
    }

    public function deletePosts(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();

            foreach($ids as $id)
            {
                $post = Post::find($id);

                if($post)
                {
                    $post->deleted = 1;
                    $post->save();
                }
            }

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $post_id = $request->input('id');
            $post = Post::find($post_id);
            
            if (!$post) {
                $post = new Post();
                $post->created_by = auth()->user()->id;
            } else {
                $post->updated_by = auth()->user()->id;
            }

            // check if there is a post that has same language and alias
            $language = $request->input('language') ?? 'en';
            $alias =  $request->input('alias');
            $found_post = DB::table('posts')->select('posts.id')->where(['posts.language' => $language, 'posts.alias' => $alias, 'deleted' => 0])->first();
            
            if ($found_post && ($found_post->id != $post_id)){
                return json_encode([
                    'alias_duplicated'=>true
                ]);
            } 

            // store new post into database
            $post->category_id = $request->input('category_id');
            if ($request->input('language')) {
                $post->language = $request->input('language');
            }
            $post->icon = $request->input('icon') ?? null;
            $post->feature_image = $request->input('feature_image') ?? null;
            $post->title = $request->input('title') ?? null;
            $post->alias = $request->input('alias');
            $post->excerpt = $request->input('excerpt') ?? null;
            $post->content = $request->input('content') ?? null;
            $post->published = $request->input('published') ? 1 : 0;
            if ($request->input('date')) {
                $date = DateTime::createFromFormat('m/d/Y', $request->input('date'))->format('Y-m-d');
            }
            $post->date = $date ?? null;

            $post->save();

            DB::commit();
            return json_encode([
                'post_id' => $post->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            var_dump($e->getMessage());
            return json_encode([
                'post_id' => 0
            ]);
        }
    }

}