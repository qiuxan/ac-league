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
                    @if($code->disposition_id == \App\Disposition::RECALLED)
                        <p class="message warning">{{__('verify.authentic_recalled')}}</p>
                    @elseif($code->disposition_id == \App\Disposition::BLACKLISTED)
                        <p class="message warning">{{__('verify.authentic_blacklisted')}}</p>
                    @elseif(isset($batch->expiration_date) && strtotime($batch->expiration_date) < strtotime(date('Y-m-d')))
                        <p class="message warning">{{__('verify.expired_product')}}</p>
                    @elseif($from_authentic == 1)
                        <p class="message error">{{__('verify.authentic_failed')}}</p>
                    @else
                        <p class="message success">{{__('verify.verified_product')}}:<br>{{__('verify.succeeded')}}</p>
                    @endif
                    @if($code->disposition_id != \App\Disposition::RECALLED && $code->disposition_id != \App\Disposition::BLACKLISTED
                     && $code->full_code != 'UV031IMXVRH42')
                        <section>
                            <form method="post" class="search-form" action="/authenticate">
                                {!! csrf_field() !!}
                                <p>{{__('verify.authentic_instruction')}}</p>
                                @if($history->count >= \App\History::MAX_INPUT_PASSWORD)
                                    <div class="authentic-input">
                                    @php
                                        echo captcha_img();
                                    @endphp
                                    </div>
                                    <div>{{__('verify.input_captcha')}}</div>
                                    <div class="authentic-input">
                                        <input type="text" name="captcha">
                                    </div>
                                    @if(isset($captcha_failed) && $captcha_failed == 1)
                                        <p class="message error">{{__('verify.invalid_captcha')}}</p>
                                    @endif
                                @endif
                                <div class="authentic-input">
                                    <label>
                                        <input type="search" class="authentic-field" placeholder="{{__('verify.authentic_input')}}" name="password">
                                    </label>
                                    <input type="hidden" name="uid" value="{{$encrypted_id}}">
                                    <input type="submit" class="search-submit" value="{{__('common.submit')}}">
                                </div>
                            </form>
                        </section>
                    @endif
                    <header class="page-header">
                        @if($product->company)
                            <h3 class="product-company">{{$product->company}}</h3>
                        @else
                            <h3 class="product-company">{{$member->company}}</h3>
                        @endif
                    </header><!-- .page-header -->
                    <header class="page-header">
                        <h4 class="red product-name">{{$product->name}}</h4>
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
                    @if($product->gtin)
                        <div class="product-feature">
                            <div class="attribute">{{__('verify.gtin')}}</div>
                            <div class="value">{{$product->gtin}}</div>
                        </div>
                    @endif

                        @if($code->full_code && $code->full_code != 'UV031IMXVRH42')
                            <div class="product-feature">
                                <div class="attribute">{{__('verify.serial_number')}}</div>
                                <div class="value">{{$code->full_code}}</div>
                            </div>
                        @endif

                    @if((!isset($configurations['batch_code']) || $configurations['batch_code'] == \App\ProductAttribute::DISPLAY_BOTH
                     || $configurations['batch_code'] == \App\ProductAttribute::DISPLAY_VERIFICATION) && $batch->batch_code && $code->full_code != 'UV031IMXVRH42')
                            <div class="product-feature">
                                <div class="attribute">{{__('verify.batch_number')}}</div>
                                <div class="value">{{$batch->batch_code}}</div>
                            </div>
                    @endif

                    @if((!isset($configurations['production_date']) || $configurations['production_date'] == \App\ProductAttribute::DISPLAY_BOTH
                     || $configurations['production_date'] == \App\ProductAttribute::DISPLAY_VERIFICATION) && $batch->production_date && $code->full_code != 'UV031IMXVRH42')
                            <div class="product-feature">
                                <div class="attribute">{{__('verify.production_date')}}</div>
                                <div class="value">{{$batch->production_date}}</div>
                            </div>
                    @endif

                    @if((!isset($configurations['expiration_date']) || $configurations['expiration_date'] == \App\ProductAttribute::DISPLAY_BOTH
                     || $configurations['expiration_date'] == \App\ProductAttribute::DISPLAY_VERIFICATION) && $batch->expiration_date && $code->full_code != 'UV031IMXVRH42')
                            <div class="product-feature">
                                <div class="attribute">{{__('verify.expiration_date')}}</div>
                                <div class="value">
                                    {{$batch->expiration_date}}
                                </div>
                            </div>
                    @endif

                    @if((!isset($configurations['reseller']) || $configurations['reseller'] == \App\ProductAttribute::DISPLAY_BOTH
                     || $configurations['reseller'] == \App\ProductAttribute::DISPLAY_VERIFICATION) && $code->reseller && $code->full_code != 'UV031IMXVRH42')
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
                             || $attribute->displayed_at == \App\ProductAttribute::DISPLAY_VERIFICATION)
                                @if($attribute->type == \App\ProductAttribute::TYPE_IMAGE)
                                    <div class="product-feature">
                                        <div class="attribute">{{$attribute->name}}</div>
                                        <div class="value">
                                            <img src="{{$attribute->value}}" width="150px"/>
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
                    @if(isset($scan_history) && $scan_history)
                        <div class="product-feature-full">
                            <div class="attribute-full">Scan History</div>
                            <div class="value-full">
                                <table border="0" cellpadding="1" cellspacing="1" sytle="text-align:left" width="500">
                                    <tbody>
                                    @foreach($scan_history as $scan)
                                        <tr>
                                            <td style="padding:2px; border:double">
                                                @if($scan->status == \App\History::STATUS_AUTHENTICATED)
                                                    {{__('verify.product_is_authenticated')}}
                                                @else
                                                    {{__('verify.product_is_verified')}}
                                                @endif
                                            </td>
                                            <td style="padding:2px; border:double">{{$scan->location}}</td>
                                            <td style="padding:2px; border:double">{{$scan->created_at}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <p>&nbsp;</p>

                            </div>
                        </div>
                    @endif
                </section>
                <div class="clearfix"></div>
                <div class="spacer"></div>
            </main><!-- #main -->
        </div><!-- #primary -->
    </div>
    <script src="{{ asset('js/sites/Location.js') }}?v=1.0.0" type="text/javascript"></script>
@endsection
