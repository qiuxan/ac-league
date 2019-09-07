@extends('layouts.public-page')

@section('content')
    <div class="container content-wrapper">
        <div id="primary" class="content-area fullwidth">
            <main id="main" class="site-main" role="main">
                <section>
                    <header class="page-header">
                        @if($result == 1)
                            <h3 class="page-title red">{{__('verify.submission_succeeded')}}</h3>
                        @else
                            <h3 class="page-title red">{{__('verify.submission_failed')}}</h3>
                        @endif
                    </header><!-- .page-header -->

                    <div class="page-content">
                        @if($result == 1)
                            <p class="message success">{{__('verify.submission_succeeded_info')}}</p>
                        @else
                            <p class="message error">{{__('verify.submission_failed_info')}}</p>
                        @endif
                    </div><!-- .page-content -->
                </section><!-- .error-404 -->
                @if($result == 0)
                    <section>
                        <div class="page-content">
                            <p class="text-left">{{__('verify.verify_instruction_1')}}<br/>{{__('verify.verify_instruction_2')}}</p>
                            <div class="search-form">
                                {!! csrf_field() !!}
                                <form role="search" method="post" class="verify-form" action="/verify">
                                    <label>
                                        <span class="screen-reader-text">{{__('verify.search_for')}}:</span>
                                        <input type="search" class="search-field" placeholder="{{__('verify.verify_input')}}" maxlength="15" name="code">
                                    </label>
                                    <input type="submit" class="search-submit" value="{{__('common.search')}}">
                                </form>
                            </div>
                        </div><!-- .page-content -->
                    </section>
                @endif

            </main><!-- #main -->
        </div><!-- #primary -->
    </div>
@endsection
