@extends('layouts.member')

@section('content')

<div class="x_panel">
<div class="x_title">
    <h2>Media Library</h2>
    <div class="clearfix"></div>
</div>
<div class="x_content">
    {!! csrf_field() !!}
    <span class="file-upload-label">
        <input type="file" name="file" id="file_upload"/>
    </span>       
    <br>
    <br>
    <!-- intermediate button for copying text to clipboard  -->
    <input id = "tmp" type="hidden" />
    <button id="copy_button" class="btn" style="display:none" data-clipboard-text="">Copy</button>

    Storage Usage: <span id="s_used">{{$storage_used}}</span> / <span id="s_total">{{$storage_total}}</span>
    <script type="text/x-kendo-template" id="toolbarTemplate">
        <div class="pull-left">
            <a class="k-button k-state-disabled" id="deleteButton">
                <span class="k-icon k-i-close">&nbsp;</span>
                <span>Delete</span>
            </a>
        </div>
        <div class="pull-right">
            <span class="k-textbox k-space-left">
                <a class="k-icon k-i-search">&nbsp;</a>
                <input type="text" id="searchFilter" placeholder="search"/>
            </span>
        </div>
        <div class="clearfix"></div>
    </script>

    <div>
        <div id="grid" style="height:1000px"></div>
        <div class="clearfix"></div>
    </div>
</div>
</div>

@endsection

@section('page-scripts')

<!-- page scripts -->
<script src="{{ asset('js/apps/member/files/FileList.js') }}?v=1.0.0" type="text/javascript"></script>
<script src="{{asset('js/clipboard.min.js') }}"></script>
<!-- /page scripts -->

@endsection