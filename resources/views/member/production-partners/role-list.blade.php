@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div id="roleList">
            <div>
                <div class="pull-left">
                    <div class="toolbar">
                        <div class="toolbarButtons">
                            <button class="k-button k-state-disabled" id="selectRoles" >Select</button>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
            <div id="roleGrid"></div>
            <script src="{{ asset('js/apps/member/production-partners/ProductionPartnerRoleList.js') }}?v=1.0.0" type="text/javascript"></script>
        </div>
    </div>

    <!-- /page content -->

@endsection