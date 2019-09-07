@extends('layouts.public-page')

@section('content')
    <div class="container content-wrapper">
        <div id="primary" class="content-area fullwidth">
            <main id="main" class="site-main" role="main">
                <section class="verification">
                    <header class="page-header">
                        <h2 class="page-title">{{__('common.site_title')}}</h2>
                    </header><!-- .page-header -->

                    <div class="page-content">
                        <p class="text-left">{{__('verify.verify_instruction_1')}}<br/>{{__('verify.verify_instruction_2')}}</p>
                        <div class="search-code">
                            <form role="search" method="post" class="verify-form" action="/verify">
                                {!! csrf_field() !!}
                                <label>
                                    <span class="screen-reader-text">{{__('verify.search_for')}}:</span>
                                    <input type="search" class="search-field" placeholder="{{__('verify.verify_input')}}" maxlength="15" name="code">
                                </label>
                                <input type="submit" class="search-submit" value="{{__('common.search')}}">
                            </form>
                        </div>
                    </div><!-- .page-content -->
                </section>

            </main><!-- #main -->
        </div><!-- #primary -->
    </div>
@endsection
