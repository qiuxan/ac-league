@extends('layouts.ajax')

@section('content')

    <!-- page content -->

    @include('common.factory-batch-list')

    <!-- /page content -->

@endsection

@section('page-scripts')

        <!-- page scripts -->
<script src="{{ asset('js/apps/admin/factory-batches/FactoryBatchList.js') }}?v=1.0.0" type="text/javascript"></script>
<script src="{{ asset('js/apps/admin/factory-batches/ImportFactoryBatches.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection