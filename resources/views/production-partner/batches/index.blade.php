@extends('layouts.production-partner')

@section('content')

    <!-- page content -->

    @include('common.batch-list')

    <!-- /page content -->

@endsection

@section('page-scripts')

<!-- page scripts -->
<script src="{{ asset('js/apps/production-partner/batches/BatchList.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection