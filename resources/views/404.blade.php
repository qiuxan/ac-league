@extends('layouts.public-page')

@section('content')
    <div class="container content-wrapper">
        <div class="row">
            <div id="primary" class="content-area fullwidth">
                <main id="main" class="site-main" role="main">

                    <section class="error-404 not-found">
                        <header class="page-header">
                            <h1 class="page-title">{{__('common.404_title')}}</h1>
                        </header><!-- .page-header -->

                        <div class="page-content">
                            <p>{{__('common.404_message')}}</p>
                             <p class="testing"> Please Search for more detail:</p>


                            <form role="search" method="get" class="search-form" action="/search">
                                <label>
                                    <span class="screen-reader-text">Search for:</span>
                                    <input type="search" class="search-field" placeholder="{{__('common.search')}} …" value="" name="search_string">
                                </label>
                                <input type="submit" class="search-submit" value="{{__('common.search')}}">
                            </form>
                        </div><!-- .page-content -->
                    </section><!-- .error-404 -->

                </main><!-- #main -->
            </div><!-- #primary -->

        </div>
    </div>
@endsection
