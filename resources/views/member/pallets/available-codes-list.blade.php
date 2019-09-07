@extends('layouts.ajax')

@section('content')

    <div class="x_panel">
        <div class="x_content">
            <script type="text/x-kendo-template" id="availableCodeToolbarTemplate">
                <div class="pull-left">
                    <a class="k-button" id="availableCodeSelectButton">
                        <span class="k-icon k-i-plus">&nbsp;</span>
                        <span>Select</span>
                    </a>
                </div>
                <div class="pull-right">
                    <span class="k-textbox k-space-left">
                        <a class="k-icon k-i-search">&nbsp;</a>
                        <input type="text" id="searchAvailableCodeFilter" placeholder="search"/>
                    </span>
                </div>
                <div class="clearfix"></div>
            </script>
            <div>
                <div id="available_code_grid"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

@endsection

@section('page-scripts')

<!-- page scripts -->
<script src="{{ asset('js/apps/member/pallets/AvailableCodeList.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection