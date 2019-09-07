@extends('layouts.member')

@section('content')

    <!-- page content -->

    @include('common.history-list')

    <!-- /page content -->

@endsection

@section('page-scripts')

<!-- page scripts -->
<script src="/js/canvasjs.min.js"></script>
@if($should_display_map_graph)
    <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
    </script>
@endif
<script src="{{ asset('js/apps/member/histories/HistoryList.js') }}?v=1.0.0" type="text/javascript"></script>
@if($should_display_map_graph)
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDRS_FYlxJiv0lOhRZw-5J-xL6QNbVxh-A&callback=HistoryList.initMap">
    </script>
@endif
<script src="{{ asset('js/jszip.min.js') }}" type="text/javascript"></script>
<!-- /page scripts -->

@endsection