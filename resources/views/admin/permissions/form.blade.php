@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="formWrap">
            <form id="permissionForm" enctype="multipart/form-data">
                {!! csrf_field() !!}
                <input data-bind="value: id" name="id" type="hidden"/>
                <fieldset class="pull-left">
                    <ul>
                        <li>
                            <label for="name">Name</label>
                            <input data-bind="value: name" type="text" id="name" name="name" class="k-textbox required" required validationMessage="This field is required" placeholder="Permission Name"/>
                        </li>
                        <li>
                            <label for="guard_name">Guard Name</label>
                            <select data-bind="value: guard_name" name="guard_name" id="guard_name">
                                <option value="web">web</option>
                            </select>
                        </li>
                    </ul>
                </fieldset>
                <div class="clearfix"></div>
                <div class="spacer"></div>
                <div class="actionBar">
                    <div class="actionBarLeft">
                        <button class="k-button" type="button" id="cancelButtonPermissionForm">Cancel</button>
                    </div>
                    <div class="actionBarRight">
                        <span class="status"></span>
                        <button class="k-button" type="button" id="saveButtonPermissionForm">Save</button>
                        <button class="k-button" type="button" id="doneButtonPermissionForm">Done</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- /page content -->


<!-- page scripts -->
<script src="{{ asset('js/apps/admin/permissions/PermissionForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection