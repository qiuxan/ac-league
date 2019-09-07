@extends('layouts.staff')

@section('content')

    <!-- page content -->

    @include('common.user-request-list')

    <!-- /page content -->

@endsection

@section('page-scripts')

<!-- page scripts -->
<script src="{{ asset('js/apps/staff/user_requests/UserRequestList.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection