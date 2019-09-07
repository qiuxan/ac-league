@extends('layouts.public-home')

@section('sliders')
    <div class="sydney-hero-area">

        <div id="slideshow" class="header-slider" data-speed="5000" data-mobileslider="responsive">
            <div class="slides-container">
                @foreach($slides as $slide)
                    <div class="slide-item" style="background-image:url('{{$slide->location}}');">
                        <img class="mobile-slide preserve" src="{{$slide->location}}"/>
                        <div class="slide-inner">
                            <div class="contain animated fadeInRightBig text-slider">
                                <!--
                                <h2 class="maintitle">{{$slide->title}}</h2>
                                 <p class="subtitle"></p>
                                -->
                            </div>
                            <!--
                            <a href="#primary" class="roll-button button-slider">{{__('home.click_to_begin')}}</a>
                            -->
                        </div>
                    </div>                    
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container content-wrapper">
        <div class="row">
            <div id="primary" class="fp-content-area">
                <main id="main" class="site-main" role="main">

                    <div class="entry-content">
                        <div id="pl-8"  class="panel-layout" >
                            <div id="pg-8-0"  class="panel-grid panel-has-style" >
                                <div class="siteorigin-panels-stretch panel-row-style panel-row-style-for-8-0" style="border-bottom: 1px solid #e0e0e0;padding: 100px 0; " data-stretch-type="full" data-overlay="true" >
                                    <div id="pgc-8-0-0"  class="panel-grid-cell" >
                                        <div id="panel-8-0-0-0" class="so-panel widget widget_sydney_services_type_a sydney_services_widget panel-first-child panel-last-child" data-index="0" >
                                            <div class="test panel-widget-style panel-widget-style-for-8-0-0-0" style="text-align: left;" data-title-color="#443f3f" data-headings-color="#443f3f" >
                                                <h3 class="widget-title">{{__('home.our_services')}}</h3>
                                                @foreach($services as $service)
                                                    <div class="service col-md-4">
                                                        <div class="roll-icon-box">
                                                            <a href="{{$service->alias}}">
                                                                <div class="icon">
                                                                    <i class="{{$service->icon}}"></i>
                                                                </div>
                                                            </a>
                                                            <div class="content">
                                                                <h3>
                                                                    <a href="{{$service->alias}}"> {{$service->title}} </a>
                                                                </h3>
                                                                <p>{{$service->excerpt}}</p>
                                                                <p>&nbsp;</p>
                                                            </div><!--.info-->
                                                        </div>
                                                    </div>                                                
                                                @endforeach
                                                <a href="#pg-8-1" class="roll-button more-button">{{__('home.see_why_us')}}</a>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="pg-8-1"  class="panel-grid panel-has-style" >
                                <div class="siteorigin-panels-stretch panel-row-style panel-row-style-for-8-1" style="border-bottom: 1px solid #e0e0e0;padding: 100px 0; " data-stretch-type="full" data-overlay="true" >
                                    <div id="pgc-8-1-0"  class="panel-grid-cell" >
                                        <div id="panel-8-1-0-0" class="so-panel widget widget_sydney_services_type_b sydney_services_b_widget panel-first-child panel-last-child" data-index="1" >
                                            <h3 class="widget-title">{{__('home.why_us')}}</h3>
                                            <div class="roll-icon-list">
                                                @foreach($why_us_list as $why_us) 
                                                <div class="service clearfix ">
                                                    <div class="list-item clearfix">
                                                        <a href="{{$why_us->alias}}">
                                                            <div class="icon">
                                                                <i class="{{$why_us->icon}}"></i>																			</div>
                                                            <div class="content">
                                                        </a>
                                                            <h3>
                                                                <a href="{{$why_us->alias}}">{{$why_us->title}}</a>
                                                            </h3>
                                                            <p>{{$why_us->excerpt}}</p>
                                                        </div><!--.info-->
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>


                                        </div>
                                    </div>
                                    <div id="pgc-8-1-1"  class="panel-grid-cell" >
                                        <div id="panel-8-1-1-0" class="so-panel widget widget_sow-image panel-first-child panel-last-child" data-index="2" >
                                            <div class="so-widget-sow-image so-widget-sow-image-default-813df796d9b1">
                                                <div class="sow-image-container">
                                                    <img src="/storage/ed1508db1b2b877303a60288b1cd34fd0774098b-Phonescanning.jpg" width="550" height="363" class="so-widget-image"/>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- .entry-content -->

                </main><!-- #main -->
            </div><!-- #primary -->

        </div>
    </div>
@endsection
