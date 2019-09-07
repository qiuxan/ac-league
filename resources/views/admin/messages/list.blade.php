@extends('layouts.ajax')

@section('content')

    <!-- page content -->
    @include('common.message-list')
    <!-- /page content -->

@endsection

@section('page-scripts')

        <!-- page scripts -->
<script src="{{ asset('js/apps/admin/batches/BatchList.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection