@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>File Details</h2>
            <div class="clearfix"></div>
        </div>
        <div id="fileFormDiv" class="x_content">
            <div class="fileContainer">
                <img  data-bind="attr:{src: location}"/>
                <br>
                <div id="file_name" class="fileName"></div>
            </div>
            <div class="actionBar">
                <div class="actionBarLeft">
                    <button class="k-button" type="button" id="cancelButton">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- /page content -->
@endsection

@section('page-scripts')

<!-- page scripts -->
<script src="{{ asset('js/apps/member/files/FileForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection