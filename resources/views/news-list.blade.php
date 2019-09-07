@extends('layouts.public-page')

@section('content')
    <div class="container content-wrapper">
        <div id="primary" class="content-area">
            <main id="main" class="post-wrap roll-team no-carousel" role="main">
                <section class="news-list">
                    <header class="page-header">
                        <h2 class="page-title">{{__('common.news')}}</h2>
                    </header><!-- .page-header -->
                    @foreach($news_list as $index => $news)
                        <div class="team-item col-md-4">
                            <a href="/{{$news->alias}}">
                                <div class="team-inner">
                                    <div class="feature-image">
                                        <img width="100%" height="100%" src="{{$news->feature_image}}" class="attachment-employees-image size-employees-image wp-post-image" alt="" sizes="(max-width: 400px) 100vw, 400px">
                                    </div>
                                </div>
                                <div class="team-content">
                                    <div class="title">
                                        <a href="/{{$news->alias}}">{{$news->title}}</a>
                                    </div>
                                </div>
                            </a>
                        </div><!-- /.team-item -->
                        @if(($index+1)%3 == 0)
                            <div class="clearfix"></div>
                        @endif
                    @endforeach
                </section>
            </main><!-- #main -->
        </div><!-- #primary -->

    </div>
@endsection
