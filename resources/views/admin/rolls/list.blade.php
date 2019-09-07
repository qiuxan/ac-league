@extends('layouts.ajax')

@section('content')

    <!-- page content -->

    @include('common.rolls-list')

    <!-- /page content -->

@endsection

@section('page-scripts')

        <!-- page scripts -->
<script src="{{ asset('js/apps/admin/rolls/RollList.js') }}?v=1.0.0" type="text/javascript"></script>
<script src="{{ asset('js/apps/admin/rolls/ImportRolls.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection