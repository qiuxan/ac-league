@extends('layouts.public-page')

@section('content')
    <div class="container content-wrapper">
        <div id="primary" class="content-area col-md-9 ">
            <main id="main" class="post-wrap" role="main">
                <article id="post-{{$post->id}}" class="post type-post status-publish format-standard has-post-thumbnail hentry category-uncategorized">


                    <div class="entry-thumb">
                        <img width="830" height="553" src="{{$post->feature_image}}" class="attachment-sydney-large-thumb size-sydney-large-thumb wp-post-image" alt="" sizes="(max-width: 830px) 100vw, 830px">
                    </div>

                    <header class="entry-header">
                        <h1 class="title-post entry-title">{{$post->title}}</h1>
                        @if($post->date)
                        <div class="meta-post">
                            <span class="posted-on"> {{__('news.posted_on')}} <time class="entry-date published">{{date('d M Y', strtotime($post->date))}}</time></span>
                        </div><!-- .entry-meta -->
                        @endif
                    </header><!-- .entry-header -->

                    <div class="entry-content">
                        {!! $post->content !!}
                    </div><!-- .entry-content -->

                    <footer class="entry-footer">
                    </footer><!-- .entry-footer -->


                </article><!-- #post-## -->

            </main><!-- #main -->
        </div><!-- #primary -->

        <div id="secondary" class="widget-area col-md-3" role="complementary">
            <aside id="search-3" class="widget widget_search">
                <form role="search" method="get" class="search-form" action="/search">
                    <label>
                        <span class="screen-reader-text">{{__('common.search_for')}}:</span>
                        <input type="search" class="search-field" placeholder="{{__('common.search')}} …" name="search_string">
                    </label>
                    <input type="submit" class="search-submit" value="{{__('common.search')}}">
                </form>
            </aside>
            @if(count($recent_posts) > 0)
            <aside id="recent-posts-3" class="widget widget_recent_entries">
                <h3 class="widget-title">{{__('common.recent_posts')}}</h3>
                <ul>
                    @foreach($recent_posts as $recent_post)
                    <li>
                        <div>
                            <div class="recent-thumbnail" style="background-image:url({{$recent_post->feature_image}})"><img width="100%" height="100%" src="{{$recent_post->feature_image}}" /></div>
                            <div class="recent-title"><a href="/{{$recent_post->alias}}">{{$recent_post->title}}</a></div>
                            <div class="clearfix"></div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </aside>
            @endif
        </div><!-- #secondary -->
    </div>
@endsection
