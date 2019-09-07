@extends('layouts.staff')

@section('content')

    <!-- page content -->

    @include('common.file-list')

    <!-- /page content -->

@endsection

@section('page-scripts')

<!-- page scripts -->
<script src="{{ asset('js/apps/staff/files/FileList.js') }}?v=1.0.0" type="text/javascript"></script>
<script src="{{asset('js/clipboard.min.js') }}"></script>
<!-- /page scripts -->

@endsection