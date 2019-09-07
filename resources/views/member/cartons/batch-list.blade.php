@extends('layouts.ajax')

@section('content')

    <div class="x_panel">
        <div class="x_content">
            <script type="text/x-kendo-template" id="batchToolbarTemplate">
                <div class="pull-left">
                    <a class="k-button" id="batchSelectButton">
                        <span class="k-icon k-i-plus">&nbsp;</span>
                        <span>Select</span>
                    </a>
                </div>
                <div class="pull-right">
                    <span class="k-textbox k-space-left">
                        <a class="k-icon k-i-search">&nbsp;</a>
                        <input type="text" id="searchBatchFilter" placeholder="search"/>
                    </span>
                </div>
                <div class="clearfix"></div>
            </script>
            <div>
                <div id="batch_grid"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

@endsection

@section('page-scripts')

<!-- page scripts -->
<script src="{{ asset('js/apps/member/cartons/CartonBatchList.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection