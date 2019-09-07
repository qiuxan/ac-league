<?php

namespace App\Http\Controllers;
use App\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;
use App\Post;
use App\Utils;
use App;

class PostController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function show($post_slug)
    {
        $post = Post::where(['deleted' => 0, 'alias' => $post_slug, 'language' => App::getLocale()])->first();
        if(!($post && $post->id > 0))
        {
            $post = Post::where(['deleted' => 0, 'alias' => $post_slug])->first();
        }
        if($post && $post->id > 0)
        {
            $recent_posts = $this->getRecentNews();

            if($post->category_id == PostCategory::NEWS)
            {
                return view('news-detail', compact('post', 'recent_posts'));
            }
            else 
            {
                return view('post-full', compact('post', 'recent_posts'));
            }
        }
        else
        {
            return view('404');
        }
    }

    public function news() {

        $news_list = Post::where(['deleted' => 0, 'category_id' => PostCategory::NEWS, 'language' => App::getLocale() ])->orderBy('date', 'desc')->get();

        return view('news-list', compact('news_list'));
    }

    public function search(Request $request) {
        $search_string = $request->input('search_string');
        
        $news_list = $this->searchNews($search_string);
        $recent_news_list = $this->getRecentNews();

        foreach ($news_list as $key => $news) {
            $news_content = strip_tags($news->content);
            if (strlen($news_content)>300) {
                $news_list[$key]->content = substr($news_content, 0, 300) . '...';
            }
        }
        $news_list->appends(['search_string'=>$search_string])->links();
        return view('search', compact('search_string', 'recent_news_list', 'news_list'));
    }

    private function getRecentNews() {
        $recent_news = Post::where(['deleted' => 0, 'language' => App::getLocale(), 'category_id' => PostCategory::NEWS])
            ->orderBy('date', 'desc')
            ->limit(9)
            ->get();

        if(count($recent_news) == 0)
        {
            $recent_news = Post::where(['deleted' => 0, 'category_id' => PostCategory::NEWS])->orderBy('date', 'desc')->limit(9)->get();
        }
        
        return $recent_news;
    }

    private function searchNews($search_string) {
        $news_category_id = PostCategory::NEWS;
        $language = App::getLocale();
        $where_raw = " posts.deleted = 0 AND posts.category_id = :news_category_id AND language = :language";

        // lower case the search string
        $search_string = strtolower($search_string);
        // split the search string by space, comma or perid
        $search_words_list = preg_split("/[\s,.]+/", $search_string);
        $search_words_string = '%' . implode('%', $search_words_list) . '%';

        $where_raw .= " AND ( LOWER(posts.title) LIKE :search_title OR LOWER(posts.content) LIKE :search_content)";

        $news_list = Post::whereRaw($where_raw, [
            'news_category_id' => $news_category_id, 
            'language'         => $language, 
            'search_title'     => $search_words_string, 
            'search_content'   => $search_words_string
        ])->paginate(5);

        return $news_list;
    }
}