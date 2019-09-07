@extends('layouts.staff')

@section('content')

    <!-- page content -->

    @include('common.post-list')

    <!-- /page content -->

@endsection

@section('page-scripts')

<!-- page scripts -->
<script src="{{ asset('js/apps/staff/posts/PostList.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection