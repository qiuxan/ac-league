<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta name="description" content="Australian Manufacturers Anti-Counterfeiting League (AMACL) has been created by Yuto Company Pty Ltd and Australian Trading Bridge Pty Ltd along with other Australian local manufacturers. The League offers a series of anti-counterfeiting services for Australian manufacturers, including complete anti-counterfeiting packaging solutions to various sectors and powerful IT cloud system to support each product from all league members.">
    <meta name="keywords" content="Anti-counterfeiting, Brand Protection, Smart Packaging, Product Authentication, Consumer engagement, Customer trust, Food fraud, Traceability, Security label, australia china trading, australia china business">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Anti-Counterfeiting League</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/kendo.common.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/kendo.silver.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}?v=1.0.0" rel="stylesheet">
</head>

<body class="nav-md">


<div class="container body" id="app">
    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                    <a href="/">
                        <div class="league_logo">
                            <img src="/images/logo.png" alt="Australian Manufacturer Anti-Counterfeiting League" />
                        </div>
                    </a>
                </div>

                <div class="clearfix"></div>

                <!-- menu profile quick info -->
                <div class="profile clearfix">
                    <div class="profile_pic">
                        <img src="{{Auth::user()->avatar}}" alt="..." class="img-circle profile_img">
                    </div>
                    <div class="profile_info">
                        <span>Welcome,</span>
                        <h2>{{Auth::user()->name}}</h2>
                    </div>
                </div>
                <!-- /menu profile quick info -->

                <br />

                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <ul class="nav side-menu">
                            <li><a href="/production-partner"><i class="fa fa-home"></i> Home</a>
                            </li>
                            @if(Gate::check('access ingredient') || Gate::check('manage ingredient'))
                            <li><a href="/production-partner/ingredients"><i class="fa fa-tree"></i> Ingredients</a>
                            </li>
                            @endif
                            @if(Gate::check('access ingredient lot') || Gate::check('manage ingredient lot'))
                                <li><a href="/production-partner/ingredient-lots"><i class="fa fa-database"></i> Ingredient Lots</a>
                                </li>
                            @endif
                            @if(Gate::check('access ingredient shipping') || Gate::check('manage ingredient shipping'))
                                <li><a href="/production-partner/ingredient-shipments"><i class="fa fa-truck"></i> Ingredient Shipping</a>
                                </li>
                            @endif
                            @if(Gate::check('access ingredient receiving') || Gate::check('manage ingredient receiving'))
                                <li><a href="/production-partner/ingredient-receipts"><i class="fa fa-ship"></i> Ingredient Receipts</a>
                                </li>
                            @endif
                            @if(Gate::check('access product') || Gate::check('manage product'))
                            <li><a href="/production-partner/products"><i class="fa fa-product-hunt"></i> Products</a>
                            </li>
                            @endif
                            @if(Gate::check('access batch') || Gate::check('manage batch'))
                            <li><a href="/production-partner/batches"><i class="fa fa-list-ol"></i> Batches</a>
                            </li>
                            @endif
                            @if(Gate::check('access roll') || Gate::check('manage roll'))
                            <li><a href="/production-partner/rolls"><i class="fa fa-circle-o"></i> Rolls</a>
                            </li>
                            @endif
                            @if(Gate::check('access code') || Gate::check('manage code'))
                            <li><a href="/production-partner/codes"><i class="fa fa-barcode"></i> Serials</a>
                            </li>
                            @endif
                            @if(Gate::check('access file') || Gate::check('manage file'))
                            <li><a href="/production-partner/files"><i class="fa fa-picture-o"></i> Media Library</a>
                            </li>
                            @endif
                            @if(Gate::check('access message') || Gate::check('manage message'))
                            <li><a href="/production-partner/messages"><i class="fa fa-envelope-o"></i> Messages</a>
                            </li>
                            @endif
                            <!--
                            <li><a href="/admin/messages"><i class="fa fa-comments-o"></i> Messages</a>
                            </li>
                            -->
                        </ul>
                    </div>

                </div>
                <!-- /sidebar menu -->

                <!-- /menu footer buttons -->
                <div class="sidebar-footer hidden-small">
                    <a data-toggle="tooltip" data-placement="top" title="Settings">
                        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Lock">
                        <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Logout" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                    </a>
                </div>
                <!-- /menu footer buttons -->
            </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
            <div class="nav_menu">
                <nav>
                    <div class="nav toggle">
                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                    </div>

                    <ul class="nav navbar-nav navbar-right">
                        <li class="">
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <img src="{{Auth::user()->avatar}}" alt="">{{Auth::user()->name}}
                                <span class=" fa fa-angle-down"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                <li><a href="/production-partner/profile"> Profile</a></li>
                                <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                            </ul>
                        </li>
                        <li role="presentation" class="dropdown">
                            @if($unread_messages_count==0)
                                <a href="/production-partner/messages">
                            @else
                                <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                            @endif
                                <i class="fa fa-envelope-o"></i>
                                @if($unread_messages_count>0)
                                    <span class="badge bg-green">{{$unread_messages_count}}</span>
                                @endif
                            </a>
                            <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                                @foreach($unread_messages as $unread_message)
                                    <li>
                                        <a href="/production-partner/messages">
                                        <span>
                                            @if($unread_message->type==1)
                                                AUTB Administrator
                                            @else
                                                System
                                            @endif
                                        </span>
                                        @if($unread_messages_current_time->diffInMinutes($unread_message->created_at)<60)
                                            <span class="time">{{$unread_messages_current_time->diffInMinutes($unread_message->created_at)}}m ago</span>                                        
                                        @elseif($unread_messages_current_time->diffInHours($unread_message->created_at)<24)
                                            <span class="time">{{$unread_messages_current_time->diffInHours($unread_message->created_at)}}h ago</span>                                    
                                        @else
                                            <span class="time">{{$unread_messages_current_time->diffInDays($unread_message->created_at)}}d ago</span>                                                                            
                                        @endif
                                        </span>
                                        <span class="message">
                                            {{$unread_message->message}}
                                        </span>
                                        </a>
                                    </li>
                                @endforeach                            
                                <li>
                                    <div class="text-center">
                                    <a href="messages">
                                    <strong>See All Alerts</strong>
                                    <i class="fa fa-angle-right"></i>
                                    </a>
                                    </div>
                                </li>
                            </ul>
                        </li>                        
                    </ul>
                </nav>
            </div>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12" id="mainContentDiv">

                    @yield('content')

                </div>
            </div>
        </div>

        <!-- /page content -->

        <!-- footer content -->
        <footer>
            <div class="pull-right">
                Copyright © {{date('Y')}}, <a href="https://oz-manufacturer.org">Anti-Counterfeiting League</a>. All Rights Reserved.
            </div>
            <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
    </div>
</div>


<!-- Scripts -->

<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/custom.js') }}?v=1.0.0"></script>
<script src="{{ asset('js/kendo.all.min.js') }}"></script>
<script src="{{ asset('js/Utils.js') }}?v=1.0.0"></script>

<!-- Scripts -->

@yield('page-scripts')

</body>
</html>
