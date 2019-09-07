@extends('layouts.member')

@section('content')

    <!-- page content -->
    @include('common.production-partners-list')
    <!-- /page content -->

@endsection

@section('page-scripts')

<!-- page scripts -->
<script src="{{ asset('js/apps/member/production-partners/ProductionPartnerList.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection