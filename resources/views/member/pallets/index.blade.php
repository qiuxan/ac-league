@extends('layouts.member')

@section('content')

    <!-- page content -->
    @include('common.pallet-list')
    <!-- /page content -->

@endsection

@section('page-scripts')

<!-- page scripts -->
<script src="{{ asset('js/apps/member/pallets/PalletList.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection