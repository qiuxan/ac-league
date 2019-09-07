@extends('layouts.ajax')

@section('content')

    <div class="x_panel">
        <div class="x_content">
            <script type="text/x-kendo-template" id="productToolbarTemplate">
                <div class="pull-left">
                    <a class="k-button" id="productSelectButton">
                        <span class="k-icon k-i-plus">&nbsp;</span>
                        <span>Select</span>
                    </a>
                </div>
                <div class="pull-right">
                    <span class="k-textbox k-space-left">
                        <a class="k-icon k-i-search">&nbsp;</a>
                        <input type="text" id="searchProductFilter" placeholder="search"/>
                    </span>
                </div>
                <div class="clearfix"></div>
            </script>
            <div>
                <div id="product_grid"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

@endsection

@section('page-scripts')

<!-- page scripts -->
<script src="{{ asset('js/apps/member/pallets/PalletProductList.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection