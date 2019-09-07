@extends('layouts.ajax')

@section('content')

    <!-- page content -->

    @include('common.ingredient-shipment-list')

    <!-- /page content -->

@endsection

@section('page-scripts')

        <!-- page scripts -->
<script src="{{ asset('js/apps/production-partner/ingredient-shipments/IngredientShipmentList.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection