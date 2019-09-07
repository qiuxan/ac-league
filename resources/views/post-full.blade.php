@extends('layouts.public-page')

@section('content')
    <div class="container content-wrapper">
        <div id="primary" class="content-area col-md-9 ">
            <main id="main" class="post-wrap" role="main">
                <section>
                    <header class="page-header">
                        <h2 class="page-title">{{$post->title}}</h2>
                    </header><!-- .page-header -->

                    <div class="page-content">
                        {!!$post->content!!}
                    </div><!-- .page-content -->
                </section>
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

