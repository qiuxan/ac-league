@extends('layouts.production-partner')

@section('content')

    <!-- page content -->
    @include('common.code-list')
    <!-- /page content -->

@endsection

@section('page-scripts')

<!-- page scripts -->
<script src="{{ asset('js/apps/production-partner/codes/CodeList.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection