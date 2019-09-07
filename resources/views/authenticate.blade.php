@extends('layouts.public-product-verification')

@section('background-style')
    @if($member->background_image)
        <style type='text/css'>
            body:before {
                content: "";
                display: block;
                position: fixed;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                z-index: -10;
                background: url('{{$member->background_image}}') no-repeat center fixed;
                -webkit-background-size: cover;
                -moz-background-size: cover;
                -o-background-size: cover;
                background-size: cover;
            }
            #content.page-wrap {
                padding-left: 15px;
                padding-right: 15px;
            }
            .page-wrap .content-wrapper {
                background: rgba(249,249,249,.9);
            }
        </style>
    @endif
@endsection

@section('header')
    <header id="masthead" class="site-header" role="banner">
        <div class="header-wrap">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="site-title">

                            @if($product->company_website)
                                <a href="{{$product->company_website}}">
                            @else
                                <a href="{{$member->website}}">
                            @endif
                                    <div class="league_logo">
                                        @if($product->company_logo)
                                            <img src="{{$product->company_logo}}" alt="{{$product->company}}" />
                                        @else
                                            <img src="{{$member->logo}}" alt="{{$member->company}}" />
                                        @endif
                                    </div>
                                </a>
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header><!-- #masthead -->
@endsection

@section('content')
    <div class="container content-wrapper">
        <div id="primary" class="content-area fullwidth">
            <main id="main" class="site-main" role="main">
                <section class="verification">
                    @if($existed == 0)
                        <p class="message success">{{__('verify.authentic')}}</p>
                    @else
                        <p class="message warning">{{__('verify.authentic_sold', ['existed' => $existed])}}</p>
                    @endif
                    <header class="page-header">
                        @if($product->company)
                            <h3>{{$product->company}}</h3>
                        @else
                            <h3>{{$member->company}}</h3>
                        @endif
                    </header><!-- .page-header -->
                    <header class="page-header">
                        <h4 class="red">{{$product->name}}</h4>
                    </header><!-- .page-header -->
                    @if(count($product_images) > 0)
                        <div>
                            <div id="slideshow" class="header-slider" data-speed="4000" data-mobileslider="responsive">
                                <div class="slides-container">
                                    @foreach($product_images as $image)
                                        <div class="slide-item" style="background-image:url('{{$image->location}}');">
                                            <img class="mobile-slide preserve" src="{{$image->location}}"/>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                        @if(isset($configurations['promotion_form']) && ($configurations['promotion_form'] == \App\ProductAttribute::DISPLAY_BOTH
                         || $configurations['promotion_form'] == \App\ProductAttribute::DISPLAY_AUTHENTICATION ))
                            <div class="row surveyForm">
                            @if($survey && $survey_questions && count($survey_questions) > 0 )
                                @if($survey->title)
                                    <header class="page-header">
                                        <span class="red product-name">{{$survey->title}}</span>
                                    </header><!-- .page-header -->
                                @endif
                                @if($survey->description)
                                    <div class="product-feature">
                                        <div class="value-full">{!! $survey->description !!}</div>
                                    </div>
                                @endif
                                <form id="publicSurveyForm" role="form" method="POST" action="/survey-response">
                                    {!! csrf_field() !!}
                                    <input type="hidden" name="scan_id" value="{{$history->id}}" />
                                    @php
                                    $i = 1;
                                    @endphp
                                    @foreach($survey_questions as $survey_question)
                                        @php
                                            $required  = '';
                                            if($survey_question->required == 1)
                                            {
                                                $required  = 'required';
                                            }
                                        @endphp
                                        @if($survey_question->type == \App\SurveyQuestion::TYPE_OPEN_QUESTION)
                                            <div class="product-feature">
                                                <div>
                                                    <strong>{{$i++}}</strong>. {{$survey_question->question}}
                                                    @if($required == 'required')
                                                        (*)
                                                    @endif
                                                </div>
                                                <div><input type="text" {{$required}} name="question_{{$survey_question->id}}" width="100%" /></div>
                                            </div>
                                        @elseif ($survey_question->type == \App\SurveyQuestion::TYPE_SCALING_QUESTION)
                                            <div class="product-feature">
                                                <div>
                                                    <strong>{{$i++}}</strong>. {{$survey_question->question}}
                                                    @if($required == 'required')
                                                        (*)
                                                    @endif
                                                </div>
                                                <div>
                                                    @for ($j = 0; $j <= 10; $j++)
                                                        <div><input type="radio" {{$required}} name="question_{{$survey_question->id}}" value="{{$j}}" /> {{$j}}</div>
                                                    @endfor
                                                </div>
                                            </div>
                                        @else
                                            @if(count($survey_question->question_options) > 0)
                                                <div class="product-feature">
                                                    <div>
                                                        <strong>{{$i++}}</strong>. {{$survey_question->question}}
                                                        @if($required == 'required')
                                                            (*)
                                                        @endif
                                                    </div>
                                                    <div>
                                                        @foreach($survey_question->question_options as $question_option)
                                                            <div><input type="radio" {{$required}} name="question_{{$survey_question->id}}" value="{{$question_option->id}}" /> {{$question_option->option}}</div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                    <div class="product-feature">
                                        <button type="submit" id="surveySubmit" class="submit">
                                            {{__('common.submit')}}
                                        </button>
                                    </div>
                                </form>
                            </div>
                            @endif
                        @endif
                        @if($product->gtin)
                            <div class="product-feature">
                                <div class="attribute">{{__('verify.gtin')}}</div>
                                <div class="value">{{$product->gtin}}</div>
                            </div>
                        @endif

                        @if($code->full_code)
                            <div class="product-feature">
                                <div class="attribute">{{__('verify.serial_number')}}</div>
                                <div class="value">{{$code->full_code}}</div>
                            </div>
                        @endif

                        @if((!isset($configurations['batch_code']) || $configurations['batch_code'] == \App\ProductAttribute::DISPLAY_BOTH
                         || $configurations['batch_code'] == \App\ProductAttribute::DISPLAY_AUTHENTICATION) && $batch->batch_code)
                            <div class="product-feature">
                                <div class="attribute">{{__('verify.batch_number')}}</div>
                                <div class="value">{{$batch->batch_code}}</div>
                            </div>
                        @endif

                        @if((!isset($configurations['production_date']) || $configurations['production_date'] == \App\ProductAttribute::DISPLAY_BOTH
                         || $configurations['production_date'] == \App\ProductAttribute::DISPLAY_AUTHENTICATION) && $batch->production_date)
                            <div class="product-feature">
                                <div class="attribute">{{__('verify.production_date')}}</div>
                                <div class="value">{{$batch->production_date}}</div>
                            </div>
                        @endif

                        @if((!isset($configurations['expiration_date']) || $configurations['expiration_date'] == \App\ProductAttribute::DISPLAY_BOTH
                         || $configurations['expiration_date'] == \App\ProductAttribute::DISPLAY_AUTHENTICATION) && $batch->expiration_date)
                            <div class="product-feature">
                                <div class="attribute">{{__('verify.expiration_date')}}</div>
                                <div class="value">
                                    {{$batch->expiration_date}}
                                </div>
                            </div>
                        @endif

                        @if((!isset($configurations['reseller']) || $configurations['reseller'] == \App\ProductAttribute::DISPLAY_BOTH
                         || $configurations['reseller'] == \App\ProductAttribute::DISPLAY_AUTHENTICATION) && $code->reseller)
                            <div class="product-feature">
                                <div class="attribute">{{__('verify.reseller_name')}}</div>
                                <div class="value">
                                    {{$code->reseller}}
                                </div>
                            </div>
                        @endif

                    @if(count($product_attributes) > 0)
                        @foreach($product_attributes as $attribute)
                            @if($attribute->displayed_at == \App\ProductAttribute::DISPLAY_BOTH
                             || $attribute->displayed_at == \App\ProductAttribute::DISPLAY_AUTHENTICATION)
                                @if($attribute->type == \App\ProductAttribute::TYPE_IMAGE)
                                    <div class="product-feature">
                                        <div class="attribute">{{$attribute->name}}</div>
                                        <div class="value">
                                            <img src="{{$attribute->value}}" width="100%"/>
                                        </div>
                                    </div>
                                @elseif($attribute->type == \App\ProductAttribute::TYPE_TEXT_AREA)
                                    <div class="product-feature-full">
                                        <div class="attribute-full">{{$attribute->name}}</div>
                                        <div class="value-full">{!! $attribute->value !!}</div>
                                    </div>
                                @else
                                    <div class="product-feature">
                                        <div class="attribute">{{$attribute->name}}</div>
                                        <div class="value">{{$attribute->value}}</div>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    @endif
                </section>

            </main><!-- #main -->
        </div><!-- #primary -->
    </div>
@endsection
