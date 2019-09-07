<div class="x_panel">
    <div class="x_title">
        <h2>Reports</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        {!! csrf_field() !!}
        <div id="tabs">
            <!-- tabs title -->
            <ul>
                <li class="k-state-active">Report Table</li>
                @if ($should_display_map_graph)
                    <li>Map</li>
                    <li>Graph</li>
                @endif
            </ul>

            <!-- tabs content -->
            <!-- report grid -->
            <div>
                <script type="text/x-kendo-template" id="toolbarTemplate">
                    <div class="pull-left">
                        <input type="date" id="from_date_filter" placeholder="From Date" style="width: 120px"/>
                        <input type="date" id="to_date_filter" placeholder="To Date" style="width: 120px"/>
                        <select id="batch_id_filter">
                            <option value="">All Batches</option>
                            @foreach($batches as $batch)
                                <option value="{{$batch->id}}">{{str_replace(["#", "'"],["\\#", "&apos;"], $batch->batch_code)}}</option>
                            @endforeach
                        </select>
                        <select id="product_id_filter">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{$product->id}}">{{str_replace(["#", "'"],["\\#", "&apos;"], $product->name)}}</option>
                            @endforeach
                        </select>
                        <select id="location_id_filter">
                            <option value="">All Locations</option>
                            @foreach($locations as $location)
                                <option value="{{$location->id}}">{{str_replace(["#", "'"],["\\#", "&apos;"], $location->location)}}</option>
                            @endforeach
                        </select>
                        <select id="reseller_id_filter" style="width: 120px">
                            <option value="">All Resellers</option>
                            @foreach($resellers as $reseller)
                                <option value="{{$reseller->id}}">{{str_replace(["#", "'"],["\\#", "&apos;"], $reseller->name)}}</option>
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
                <div id="grid"></div>
                <div class="clearfix"></div>                
            </div>
            @if( $should_display_map_graph)
                <!-- report map -->
                <div id="mapContainer" style="height:600px;display:none">
                    <a id="refresh_map" class="k-button refresh-button" href="#">
                        <strong>Refresh Map</strong> <span class="k-icon k-i-reload"></span>   
                    </a>   
                    <div id="map" style="width:100%;height:90%;margin-top:15px"></div>
                </div>
                <!-- report graph -->
                <div style="display:none">
                    <a id="refresh_chart" class="k-button refresh-button" href="#">
                        <strong>Refresh Charts</strong> <span class="k-icon k-i-reload"></span>   
                    </a>
                    <div class="row" style="width:100%">
                        <div class="col-md-4 custom-breakpoint">
                            <div id="chartContainer_date"></div>
                        </div>
                        <div class="col-md-4 custom-breakpoint">
                            <div id="chartContainer_month"></div>                        
                        </div>
                        <div class="col-md-4 custom-breakpoint">
                            <div id="chartContainer_batch"></div>
                        </div>
                        <div class="col-md-4 custom-breakpoint">
                            <div id="chartContainer_product"></div>
                        </div>
                        <div class="col-md-4 custom-breakpoint">
                            <div id="chartContainer_location"></div>
                        </div>
                        <div class="col-md-4 custom-breakpoint">
                            <div id="chartContainer_reseller"></div>
                        </div>
                        <div class="col-md-4 custom-breakpoint">
                            <div id="chartContainer_language"></div>
                        </div>
                        <div class="col-md-4 custom-breakpoint">
                            <div id="chartContainer_status"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>