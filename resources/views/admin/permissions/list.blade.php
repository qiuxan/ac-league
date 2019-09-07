@extends('layouts.ajax')

@section('content')

    <!-- page content -->
    @include('common.permission-list')
    <!-- /page content -->

@endsection

@section('page-scripts')

        <!-- page scripts -->
<script src="{{ asset('js/apps/admin/permission/PermissionList.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection