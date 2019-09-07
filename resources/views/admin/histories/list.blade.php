@extends('layouts.ajax')

@section('content')

    <!-- page content -->

    <div class="x_panel">
        <div class="x_title">
            <h2>Reports</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            {!! csrf_field() !!}
            <script type="text/x-kendo-template" id="toolbarTemplate">
                <div class="pull-left">
                    <input type="date" id="from_date_filter" placeholder="From Date" style="width: 120px"/>
                    <input type="date" id="to_date_filter" placeholder="To Date" style="width: 120px"/>
                    <select id="member_id_filter">
                        <option value="">All Companies</option>
                        @foreach($members as $member)
                            <option value="{{$member->id}}">{{str_replace("#", "\\#", $member->name)}}</option>
                        @endforeach
                    </select>
                    <select id="batch_id_filter">
                        <option value="">All Batches</option>
                        @foreach($batches as $batch)
                            <option value="{{$batch->id}}">{{str_replace("#","\\#", $batch->batch_code)}}</option>
                        @endforeach
                    </select>
                    <select id="product_id_filter">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{$product->id}}">{{str_replace("#","\\#", $product->name)}}</option>
                        @endforeach
                    </select>
                    <select id="location_id_filter">
                        <option value="">All Locations</option>
                        @foreach($locations as $location)
                            <option value="{{$location->id}}">{{str_replace("#","\\#", $location->location)}}</option>
                        @endforeach
                    </select>
                    <select id="production_partner_id_filter" style="width: 120px">
                        <option value="">All Partners</option>
                        @foreach($production_partners as $production_partner)
                            <option value="{{$production_partner->id}}">{{str_replace("#","\\#", $production_partner->name)}}</option>
                        @endforeach
                    </select>
                    <select id="language_filter" style="width: 120px">
                        <option value="">All Languages</option>
                        <option value="en">English</option>
                        <option value="cn">Simplified Chinese</option>
                        <option value="tr">Traditional Chinese</option>
                    </select>
                    <select id="status_filter" style="width: 120px">
                        <option value="">All Statuses</option>
                        <option value="0">Verified</option>
                        <option value="1">Authentic</option>
                        <option value="2">Failed</option>
                    </select>
                    <a class="k-button k-button-icontext" id="exportButton">
                        <span class="k-icon k-i-excel"></span>
                        <span>Export to Excel</span>
                    </a>
                </div>
                <div class="pull-right">
                    <span class="k-textbox k-space-left">
                        <a class="k-icon k-i-search"></a>
                        <input type="text" id="search_filter" placeholder="search"/>
                    </span>
                </div>
            </script>
            <div>
                <div id="grid"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <!-- /page content -->

@endsection

@section('page-scripts')

        <!-- page scripts -->
<script src="{{ asset('js/apps/admin/batches/BatchList.js') }}?v=1.0.0" type="text/javascript"></script>
<script src="{{ asset('js/jszip.min.js') }}" type="text/javascript"></script>
<!-- /page scripts -->

@endsection