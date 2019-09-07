@extends('layouts.public-page')

@section('content')
    <div class="container content-wrapper">
        <div id="primary" class="content-area col-md-9 ">
            <main id="main" class="post-wrap" role="main">
                @if(count($news_list)>0)
                    <h3>
                        {{__('common.search_results_for')}}: {{$search_string}}
                    </h3>
                    @foreach($news_list as $news)
                    <article class="page type-page status-publish hentry">
                        <header class="entry-header">
                            <h2 class="title-post entry-title"><a href="{{$news->alias}}" rel="bookmark">{{$news->title}}</a></h2>
                        </header><!-- .entry-header -->

                        <div class="feature-image">
                            <img src="{{$news->feature_image}}" alt="" sizes="(max-width: 400px) 100vw, 400px">
                        </div><!-- .entry-post -->
                        
                        <div class="entry-post">
                            <p>{{$news->content}}</p>
                        </div><!-- .entry-post -->
                        
                        <footer class="entry-footer">
                        </footer><!-- .entry-footer -->
                    </article><!-- #post-## -->                
                    @endforeach
                    <?php echo $news_list->render(); ?>
                @else
                    <section class="no-results not-found">
                        <header class="page-header">
                            <h1 class="page-title">{{__('common.nothing_found')}}</h1>
                        </header><!-- .page-header -->

                        <div class="page-content">
                                <p>{{__('common.nothing_found_message')}}</p>
                                <form role="search" method="get" class="search-form" action="/search">
                                    <label>
                                        <span class="screen-reader-text">{{__('common.search_for')}}:</span>
                                        <input type="search" class="search-field" value="{{$search_string}}" name="search_string" />
                                    </label>
                                    <input type="submit" class="search-submit" value="{{__('common.search')}}" />
                                </form>
                                </div><!-- .page-content -->
                    </section><!-- .no-results -->
                @endif
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
            @if(count($recent_news_list) > 0)
            <aside id="recent-posts-3" class="widget widget_recent_entries">
                <h3 class="widget-title">{{__('common.recent_posts')}}</h3>
                <ul>
                    @foreach($recent_news_list as $recent_news)
                    <li>
                        <div>
                            <div class="recent-thumbnail" style="background-image:url({{$recent_news->feature_image}})"><img width="100%" height="100%" src="{{$recent_news->feature_image}}" /></div>
                            <div class="recent-title"><a href="/{{$recent_news->alias}}">{{$recent_news->title}}</a></div>
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
