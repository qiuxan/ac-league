<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{__('common.site_title')}}</title>
    <style type="text/css">
        img.wp-smiley,
        img.emoji {
            display: inline !important;
            border: none !important;
            box-shadow: none !important;
            height: 1em !important;
            width: 1em !important;
            margin: 0 .07em !important;
            vertical-align: -0.1em !important;
            background: none !important;
            padding: 0 !important;
        }
    </style>
    <link rel='stylesheet' href='{{ asset('templates/bootstrap/dist/css/bootstrap.min.css') }}' type='text/css' media='all' />
    <link rel='stylesheet' href='{{ asset('css/front-flex.css') }}' type='text/css' media='all' />
    <!-- <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Source+Sans+Pro%3A400%2C400italic%2C600%7CRaleway%3A400%2C500%2C600' type='text/css' media='all' /> -->
    <link rel='stylesheet' href='{{ asset('css/site.css') }}' type='text/css' media='all' />
    <style id='sydney-style-inline-css' type='text/css'>
        .site-header { background-color:rgba(0,0,0,0.9);}
        .site-header.float-header {padding:20px 0;}
        .site-title { font-size:32px; }
        .site-description { font-size:14px; }
        #mainnav ul li a { font-size:14px; }
        h1 { font-size:52px; }
        h2 { font-size:42px; }
        h3 { font-size:32px; }
        h4 { font-size:25px; }
        h5 { font-size:20px; }
        h6 { font-size:18px; }
        body { font-size:14px; }
        .header-image { background-size:cover;}
        .header-image { height:300px; }
        .site-header.float-header { background-color:rgba(0,0,0,0.9);}
        @media only screen and (max-width: 1024px) { .site-header { background-color:#000000;}}
        .site-title a, .site-title a:hover { color:#ffffff}
        .site-description { color:#ffffff}
        #mainnav ul li a, #mainnav ul li::before { color:#ffffff}
        #mainnav .sub-menu li a { color:#ffffff}
        #mainnav .sub-menu li a { background:#1c1c1c}
        .text-slider .maintitle, .text-slider .subtitle { color:#ffffff}
        body { color:#767676}
        #secondary { background-color:#ffffff}
        #secondary, #secondary a, #secondary .widget-title { color:#767676}
        .footer-widgets { background-color:#252525}
        .btn-menu { color:#ffffff}
        #mainnav ul li a:hover { color:#d65050}
        .site-footer { background-color:#1c1c1c}
        .site-footer,.site-footer a { color:#666666}
        .overlay { background-color:#000000}
        .page-wrap { padding-top:50px;}
        .page-wrap { padding-bottom:50px;}
        @media only screen and (max-width: 1025px) {
            .mobile-slide {
                display: block;
            }
            .slide-item {
                background-image: none !important;
            }
            .header-slider {
            }
            .slide-item {
                height: auto !important;
            }
            .slide-inner {
                min-height: initial;
            }
        }

    </style>
    <link href="{{ asset('templates/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!--[if lte IE 9]>
    <link rel='stylesheet' id='sydney-ie9-css'  href='{{ asset('css/ie9.css') }}' type='text/css' media='all' />
    <![endif]-->
    <script type='text/javascript' src='{{ asset('js/jquery-1.12.4.min.js') }}'></script>
    <script type='text/javascript' src='{{ asset('js/jquery-migrate-1.4.1.min.js') }}'></script>

    <style type="text/css">
        .recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}
    </style>
    <style type="text/css" media="all" id="siteorigin-panels-layouts-head">
        /* Layout 8 */ #pgc-8-0-0 , #pgc-8-2-0 , #pgc-8-3-0 , #pgc-8-4-0 , #pgc-8-5-0 , #pgc-8-6-0 , #pgc-8-8-0 , #pgc-8-10-0 , #pgc-8-11-0 , #pgc-8-12-0 { width:100%;width:calc(100% - ( 0 * 30px ) ) } #pg-8-0 , #pg-8-1 , #pg-8-2 , #pg-8-3 , #pg-8-4 , #pg-8-5 , #pg-8-6 , #pg-8-7 , #pg-8-8 , #pg-8-9 , #pg-8-10 , #pg-8-11 , #pl-8 .so-panel , #pl-8 .so-panel:last-child { margin-bottom:0px } #pgc-8-1-0 , #pgc-8-1-1 , #pgc-8-9-0 , #pgc-8-9-1 { width:50%;width:calc(50% - ( 0.5 * 30px ) ) } #pgc-8-7-0 { width:50.0356%;width:calc(50.0356% - ( 0.49964413675836 * 30px ) ) } #pgc-8-7-1 { width:49.9644%;width:calc(49.9644% - ( 0.50035586324164 * 30px ) ) } #pg-8-1> .panel-row-style , #pg-8-10> .panel-row-style { background-color:#f5f5f5 } #pg-8-2> .panel-row-style { background-color:#252525;padding:50px } #pg-8-4> .panel-row-style , #pg-8-12> .panel-row-style { background-color:#d65050;padding:50px } #pg-8-7> .panel-row-style { background-color:#f5f5f5;padding:0px } #panel-8-7-0-0> .panel-widget-style { padding: 30px;background-color:#d65050;color: #ffffff } #panel-8-7-1-0> .panel-widget-style { padding: 30px } #pg-8-8> .panel-row-style { background-color:#252525;padding:30px } @media (max-width:780px){ #pg-8-0.panel-no-style, #pg-8-0.panel-has-style > .panel-row-style , #pg-8-1.panel-no-style, #pg-8-1.panel-has-style > .panel-row-style , #pg-8-2.panel-no-style, #pg-8-2.panel-has-style > .panel-row-style , #pg-8-3.panel-no-style, #pg-8-3.panel-has-style > .panel-row-style , #pg-8-4.panel-no-style, #pg-8-4.panel-has-style > .panel-row-style , #pg-8-5.panel-no-style, #pg-8-5.panel-has-style > .panel-row-style , #pg-8-6.panel-no-style, #pg-8-6.panel-has-style > .panel-row-style , #pg-8-7.panel-no-style, #pg-8-7.panel-has-style > .panel-row-style , #pg-8-8.panel-no-style, #pg-8-8.panel-has-style > .panel-row-style , #pg-8-9.panel-no-style, #pg-8-9.panel-has-style > .panel-row-style , #pg-8-10.panel-no-style, #pg-8-10.panel-has-style > .panel-row-style , #pg-8-11.panel-no-style, #pg-8-11.panel-has-style > .panel-row-style , #pg-8-12.panel-no-style, #pg-8-12.panel-has-style > .panel-row-style { -webkit-flex-direction:column;-ms-flex-direction:column;flex-direction:column } #pg-8-0 .panel-grid-cell , #pg-8-1 .panel-grid-cell , #pg-8-2 .panel-grid-cell , #pg-8-3 .panel-grid-cell , #pg-8-4 .panel-grid-cell , #pg-8-5 .panel-grid-cell , #pg-8-6 .panel-grid-cell , #pg-8-7 .panel-grid-cell , #pg-8-8 .panel-grid-cell , #pg-8-9 .panel-grid-cell , #pg-8-10 .panel-grid-cell , #pg-8-11 .panel-grid-cell , #pg-8-12 .panel-grid-cell { margin-right:0 } #pg-8-0 .panel-grid-cell , #pg-8-1 .panel-grid-cell , #pg-8-2 .panel-grid-cell , #pg-8-3 .panel-grid-cell , #pg-8-4 .panel-grid-cell , #pg-8-5 .panel-grid-cell , #pg-8-6 .panel-grid-cell , #pg-8-7 .panel-grid-cell , #pg-8-8 .panel-grid-cell , #pg-8-9 .panel-grid-cell , #pg-8-10 .panel-grid-cell , #pg-8-11 .panel-grid-cell , #pg-8-12 .panel-grid-cell { width:100% } #pgc-8-1-0 , #pgc-8-7-0 , #pgc-8-9-0 , #pl-8 .panel-grid .panel-grid-cell-mobile-last { margin-bottom:0px } #pl-8 .panel-grid-cell { padding:0 } #pl-8 .panel-grid .panel-grid-cell-empty { display:none }  }
    </style>

    <!-- My Custom CSS -->

    <link rel='stylesheet' href='{{ asset('css/site-custom.css') }}?v=1.0.0' type='text/css' media='all' />
    <!-- My Custom CSS -->
</head>

<body class="siteScrolled">
{!! csrf_field() !!}
<div class="preloader">
    <div class="spinner">
        <div class="pre-bounce1"></div>
        <div class="pre-bounce2"></div>
    </div>
</div>

<div id="page" class="hfeed site">
    <div class="header-clone" style="height: 78px;"></div>
    <header id="masthead" class="site-header fixed" role="banner">
        <div class="header-wrap">
            <div class="container">
                <div class="row">
                    <div class="col-md-2 col-sm-8 col-xs-12">
                        <h1 class="site-title">
                            <a href="/" rel="home">
                                <div class="league_logo">
                                    <img src="/images/logo.png" alt="Australian Manufacturer Anti-Counterfeiting League" />
                                </div>
                            </a>
                        </h1>
                        <h2 class="site-description"></h2>
                    </div>
                    <div class="col-md-10 col-sm-4 col-xs-12">
                        <div class="btn-menu"></div>
                        <nav id="mainnav" class="mainnav" role="navigation">
                            <div class="menu-menu-1-container">
                                <ul id="menu-menu-1" class="menu">
                                    @foreach($menus as $menu)
                                        <li class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home">
                                            @if ($menu->external_link != '')
                                                <a href="{{$menu->external_link}}">{{$menu->name}}</a>
                                            @elseif ($menu->alias != '')
                                                <a href="/{{$menu->alias}}">{{$menu->name}}</a>
                                            @else
                                                <a href="#">{{$menu->name}}</a>
                                            @endif
                                            @if ($menu->hasChildren == 1)
                                                <ul role="menu" class="sub-menu">
                                                    @foreach($menu->children as $child)
                                                        <li id="menu-item-118" class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home menu-item-118 active">
                                                            @if ($child->external_link != '')
                                                                <a href="{{$child->external_link}}">{{$child->name}}</a>
                                                            @elseif ($child->alias != '')
                                                                <a href="/{{$child->alias}}">{{$child->name}}</a>
                                                            @else
                                                                <a href="#">{{$child->name}}</a>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                    <li class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home">
                                        @if(App::getLocale() == 'cn')
                                            <a href="#"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAALCAIAAAD5gJpuAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAFqSURBVHjaYrzOwPAPjJgYQEDAleHVbhADIvgHLPgHiQ0QQCxAlkR9NW8sw+cV/1gV/7Gb/hV4+vfzhj8Mv/78//Pn/+/f/8AkhH1t0yaAAAJp4I37zyz2lDfu79uqv/++/WYz+cuq/vvLxt8gdb+A5K9/v34B2SyyskBLAAII5JAva/7/+/z367a/f3/8ZuT9+//Pr78vQUrB6n4CSSj6/RuoASCAWEDO/fD3ddEfhv9/OE3/sKj8/n7k9/fDQNUIs/+DVf8HawAIIJCT/v38C3Hr95N/GDh/f94AVvT7N8RUBpjxQAVADQABBNLw/y/Ifwy/f/399ufTOpDBEPf8g5sN0QBEDAwAAQTWABEChgOSA9BVA00E2wAQQCANQBbEif/AzoCqgLkbbBYwWP/+//sXqBYggFhAkfL7D7OkJFCOCSj65zfUeFjwg8z++/ffX5AGoGKAAGI8jhSRyIw/SJH9D4aAYQoQYAA6rnMw1jU2vQAAAABJRU5ErkJggg==" title="中文（简体）" alt="中文（简体）"> 中文（简体）</a>
                                        @elseif(App::getLocale() == 'tr')
                                            <a href="#"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAALCAIAAAD5gJpuAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTMyIDc5LjE1OTI4NCwgMjAxNi8wNC8xOS0xMzoxMzo0MCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUuNSAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OEIxRjNBOEI5NkFFMTFFN0E3NUJFMUFDM0ZGMDdEMEYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OEIxRjNBOEM5NkFFMTFFN0E3NUJFMUFDM0ZGMDdEMEYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo4QjFGM0E4OTk2QUUxMUU3QTc1QkUxQUMzRkYwN0QwRiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo4QjFGM0E4QTk2QUUxMUU3QTc1QkUxQUMzRkYwN0QwRiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Prfi914AAAFYSURBVHjaYnyemsnCy8NAHPjz+QsLEycnk6g4w59faHL///xlZGFGEWJhY/rzl+Xvu/f/vn9HqPv+g5GTQyA+Fsh+N3UaIwc7IwsbsizLvy+fGP/+/v/rD5DPyMby7/1HblcXDmvL76fOAAX/f/wCUsjJAdXx9zfL/09fgQjIBupkNzMRiE/4/ebln8dP/zx6LFRW/OfuvY8z5/z/+JGRnR3qrt/Pn/7/+hXC+TF30Z/nrwTCgp/4BXDaWPF4ebyuqET2BSM3N+M9TV2ovaBQ+P3z1ROFfftBzv31l9PB5gYHByu/CBM/H8Of/yAFHGxM/38iwuff16+sQuKcjg5A1Z+3bAI6g5mDi+HPX5SggpsN5jEz8XC/bWj5vGbdr7s3mdjYmJjZGdjZGH5ADf35n4HxqpQCBwMjUtz8xhltLKw/GP6z/Przh4GFBUmUBXdEA8P5D0CAAQCCTJRKTL2dNgAAAABJRU5ErkJggg==" title="中文（繁體）" alt="中文（繁體）"> 中文（繁體）</a>
                                        @else
                                            <a href="#"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAALCAIAAAD5gJpuAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAIzSURBVHjaYjxe3nyAQ6Vyx7veD+tY/v3L/+dWKvXIyUTOc9Ybhq8fGBj+AJFnssv2uZsYGN4xMPwCCAAxAM7/AUtNjZ95PPsXHfjPzf/49wEfIhAVELzd+MzU5v38/vf6+1tNLQQEAd7j77fB3KOMjwIAMQDO/wHNCQkZhYYD7Or78vL++fkFDAv5/gH29/qJCD3w/AH6+PodGQ9GOyGJm8UgHRGrko8CiOmQjg+Ttj6HluZfYVEGWQUuM7Pfsop3ZfR+/Pnv56jCwMBw4/5roOrKdBsJYW4Ghm8AAcT0ISSJQVh4wz+F5zziL1gF1gmZMevofuQTcbZTlRXnLUyy+P7jd4SXFisLo6uVIgPDD4AAADEAzv8DLAEa6w0YwN/4+/b43/UCuNbx2/QDEP73rcbkJSIUq7fV6ev07O/3EQ8IqLXU3NDDAgAxAM7/A8veKS1ELvXw9N77Cd76BwT8+ujr9M/o+/3//8bN4+nt9P///1dLK6Cu0QkIBd7RvgKICRRwf/79/vMvyF6pNsX81++/f/7+Y/j39/evP//+/fv/9//Pn3965hz7+Onbv79/gYoBAgio4devP0Dj/psbSMtJ8gW4afz89fvX7z9g9BcYrNISfOWpVj9///379z9QA0AAsQA1AE0S4ufceeSuvprowZMPZCX4fv4Eyv778+f/9x+/ihLNtZTFfv76u2bnNaCnAQKIkYHBFxydP4A6kdAfZK6qY9nt/U0MDP+AoQwQYAAK+BukFnf4xAAAAABJRU5ErkJggg==" title="English" alt="English"> English</a>
                                        @endif
                                        <ul role="menu" class="sub-menu">
                                            <li class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home">
                                                <a href="#" onclick="OZM.switchLang('en')">
                                                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAALCAIAAAD5gJpuAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAIzSURBVHjaYjxe3nyAQ6Vyx7veD+tY/v3L/+dWKvXIyUTOc9Ybhq8fGBj+AJFnssv2uZsYGN4xMPwCCAAxAM7/AUtNjZ95PPsXHfjPzf/49wEfIhAVELzd+MzU5v38/vf6+1tNLQQEAd7j77fB3KOMjwIAMQDO/wHNCQkZhYYD7Or78vL++fkFDAv5/gH29/qJCD3w/AH6+PodGQ9GOyGJm8UgHRGrko8CiOmQjg+Ttj6HluZfYVEGWQUuM7Pfsop3ZfR+/Pnv56jCwMBw4/5roOrKdBsJYW4Ghm8AAcT0ISSJQVh4wz+F5zziL1gF1gmZMevofuQTcbZTlRXnLUyy+P7jd4SXFisLo6uVIgPDD4AAADEAzv8DLAEa6w0YwN/4+/b43/UCuNbx2/QDEP73rcbkJSIUq7fV6ev07O/3EQ8IqLXU3NDDAgAxAM7/A8veKS1ELvXw9N77Cd76BwT8+ujr9M/o+/3//8bN4+nt9P///1dLK6Cu0QkIBd7RvgKICRRwf/79/vMvyF6pNsX81++/f/7+Y/j39/evP//+/fv/9//Pn3965hz7+Onbv79/gYoBAgio4devP0Dj/psbSMtJ8gW4afz89fvX7z9g9BcYrNISfOWpVj9///379z9QA0AAsQA1AE0S4ufceeSuvprowZMPZCX4fv4Eyv778+f/9x+/ihLNtZTFfv76u2bnNaCnAQKIkYHBFxydP4A6kdAfZK6qY9nt/U0MDP+AoQwQYAAK+BukFnf4xAAAAABJRU5ErkJggg==" title="English" alt="English"> English
                                                </a>
                                            </li>
                                            <li class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home">
                                                <a href="#" onclick="OZM.switchLang('cn')">
                                                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAALCAIAAAD5gJpuAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAFqSURBVHjaYrzOwPAPjJgYQEDAleHVbhADIvgHLPgHiQ0QQCxAlkR9NW8sw+cV/1gV/7Gb/hV4+vfzhj8Mv/78//Pn/+/f/8AkhH1t0yaAAAJp4I37zyz2lDfu79uqv/++/WYz+cuq/vvLxt8gdb+A5K9/v34B2SyyskBLAAII5JAva/7/+/z367a/f3/8ZuT9+//Pr78vQUrB6n4CSSj6/RuoASCAWEDO/fD3ddEfhv9/OE3/sKj8/n7k9/fDQNUIs/+DVf8HawAIIJCT/v38C3Hr95N/GDh/f94AVvT7N8RUBpjxQAVADQABBNLw/y/Ifwy/f/399ufTOpDBEPf8g5sN0QBEDAwAAQTWABEChgOSA9BVA00E2wAQQCANQBbEif/AzoCqgLkbbBYwWP/+//sXqBYggFhAkfL7D7OkJFCOCSj65zfUeFjwg8z++/ffX5AGoGKAAGI8jhSRyIw/SJH9D4aAYQoQYAA6rnMw1jU2vQAAAABJRU5ErkJggg==" title="中文（简体）" alt="中文（简体）"> 中文（简体）
                                                </a>
                                            </li>
                                            <li class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home">
                                                <a href="#" onclick="OZM.switchLang('tr')">
                                                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAALCAIAAAD5gJpuAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyhpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTMyIDc5LjE1OTI4NCwgMjAxNi8wNC8xOS0xMzoxMzo0MCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUuNSAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OEIxRjNBOEI5NkFFMTFFN0E3NUJFMUFDM0ZGMDdEMEYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OEIxRjNBOEM5NkFFMTFFN0E3NUJFMUFDM0ZGMDdEMEYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo4QjFGM0E4OTk2QUUxMUU3QTc1QkUxQUMzRkYwN0QwRiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo4QjFGM0E4QTk2QUUxMUU3QTc1QkUxQUMzRkYwN0QwRiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Prfi914AAAFYSURBVHjaYnyemsnCy8NAHPjz+QsLEycnk6g4w59faHL///xlZGFGEWJhY/rzl+Xvu/f/vn9HqPv+g5GTQyA+Fsh+N3UaIwc7IwsbsizLvy+fGP/+/v/rD5DPyMby7/1HblcXDmvL76fOAAX/f/wCUsjJAdXx9zfL/09fgQjIBupkNzMRiE/4/ebln8dP/zx6LFRW/OfuvY8z5/z/+JGRnR3qrt/Pn/7/+hXC+TF30Z/nrwTCgp/4BXDaWPF4ebyuqET2BSM3N+M9TV2ovaBQ+P3z1ROFfftBzv31l9PB5gYHByu/CBM/H8Of/yAFHGxM/38iwuff16+sQuKcjg5A1Z+3bAI6g5mDi+HPX5SggpsN5jEz8XC/bWj5vGbdr7s3mdjYmJjZGdjZGH5ADf35n4HxqpQCBwMjUtz8xhltLKw/GP6z/Przh4GFBUmUBXdEA8P5D0CAAQCCTJRKTL2dNgAAAABJRU5ErkJggg==" title="中文（繁體）" alt="中文（繁體）"> 中文（繁體）
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </nav><!-- #site-navigation -->
                    </div>
                </div>
            </div>
        </div>
    </header><!-- #masthead -->

    <div class="clearfix"></div>
    @yield('sliders')

    <div id="content" class="page-wrap">
        @yield('content')
    </div><!-- #content -->

    <a class="go-top"><i class="fa fa-angle-up"></i></a>

    <footer id="colophon" class="site-footer" role="contentinfo">
        <div class="site-info container">
            Copyright © {{date('Y')}} <a href="https://oz-manufacturer.org/">{{__('common.site_title')}}</a></div><!-- .site-info -->
    </footer><!-- #colophon -->


</div><!-- #page -->

<style type="text/css" media="all"
       id="siteorigin-panels-layouts-footer">/* Layout w56f52953c3d68 */ #pgc-w56f52953c3d68-0-0 { width:100%;width:calc(100% - ( 0 * 30px ) ) } #pg-w56f52953c3d68-0 , #pl-w56f52953c3d68 .so-panel , #pl-w56f52953c3d68 .so-panel:last-child { margin-bottom:0px } #pgc-w56f52953c3d68-1-0 , #pgc-w56f52953c3d68-1-1 { width:50%;width:calc(50% - ( 0.5 * 30px ) ) } #pg-w56f52953c3d68-0> .panel-row-style , #pg-w56f52953c3d68-1> .panel-row-style { padding:0px } #panel-w56f52953c3d68-1-0-0> .panel-widget-style , #panel-w56f52953c3d68-1-1-0> .panel-widget-style { background-color: rgba(0,0,0,0.3) } @media (max-width:780px){ #pg-w56f52953c3d68-0.panel-no-style, #pg-w56f52953c3d68-0.panel-has-style > .panel-row-style , #pg-w56f52953c3d68-1.panel-no-style, #pg-w56f52953c3d68-1.panel-has-style > .panel-row-style { -webkit-flex-direction:column;-ms-flex-direction:column;flex-direction:column } #pg-w56f52953c3d68-0 .panel-grid-cell , #pg-w56f52953c3d68-1 .panel-grid-cell { margin-right:0 } #pg-w56f52953c3d68-0 .panel-grid-cell , #pg-w56f52953c3d68-1 .panel-grid-cell { width:100% } #pgc-w56f52953c3d68-1-0 , #pl-w56f52953c3d68 .panel-grid .panel-grid-cell-mobile-last { margin-bottom:0px } #pl-w56f52953c3d68 .panel-grid-cell { padding:0 } #pl-w56f52953c3d68 .panel-grid .panel-grid-cell-empty { display:none }  }
</style>
<script type='text/javascript' src='{{ asset('js/sites/super-slides.js') }}'></script>
<script type='text/javascript' src='{{ asset('js/sites/main.min.js') }}'></script>
<script type='text/javascript' src='{{ asset('js/sites/skip-link-focus-fix.js') }}'></script>
<script type='text/javascript' src='{{ asset('js/sites/Ozm.js') }}?v=1.0.0'></script>
<script type='text/javascript'>
    /* <![CDATA[ */
    var panelsStyles = {"fullContainer":"body"};
    /* ]]> */
</script>
<script type='text/javascript' src='{{ asset('js/sites/styling-25.min.js') }}'></script>
<script type="text/javascript">document.body.className = document.body.className.replace("siteorigin-panels-before-js","");</script>
</body>
</html>