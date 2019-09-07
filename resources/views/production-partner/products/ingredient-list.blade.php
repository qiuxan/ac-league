@extends('layouts.ajax')

@section('content')

        <!-- page content -->
<div id="ingredientList">
    <div>
        <div class="pull-left">
            <div class="toolbar">
                <div class="toolbarButtons">
                    <button class="k-button k-state-disabled" id="selectIngredient" >Select</button>
                </div>
            </div>
        </div>
        <div class="pull-right">
            <span class="k-textbox k-space-left pull-right">
                <a class="k-icon k-i-search"></a>
                <input type="text" id="searchIngredient" placeholder="search"/>
            </span>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    <div id="ingredientGrid"></div>
</div>
<!-- /page content -->

@endsection

@section('page-scripts')

        <!-- page scripts -->
<script src="{{ asset('js/apps/production-partner/products/IngredientList.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection