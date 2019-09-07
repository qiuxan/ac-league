@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Role Details</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="formWrap" id="roleFormDiv">
                <form id="roleForm" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <fieldset class="pull-left">
                        <ul>
                            <li>
                                <label for="name">Name</label>
                                <input data-bind="value: name" type="text" id="name" name="name" class="k-textbox required" required validationMessage="This field is required" placeholder="Role Name"/>
                            </li>
                            <li>
                                <label for="guard_name">Guard Name</label>
                                <select data-bind="value: guard_name" name="guard_name" id="guard_name">
                                    <option value="web">web</option>
                                </select>
                            </li>
                            <li>
                                <label for="pp_group">Production Partner Group</label>
                                <input type="hidden" name="pp_group" value="0"/>
                                <input data-bind="checked: pp_group" type="checkbox" id="pp_group" name="pp_group" value="1"/>
                            </li>
                            @if(count($permissions) > 0)
                                <li>
                                    <label></label>
                                </li>
                                <li>
                                    <label></label>
                                    <strong>Permissions</strong>
                                </li>
                                <li>
                                    <fieldset class="pull-left">
                                        <ul>
                                        @foreach($permissions as $permission)
                                            <li>
                                                <label></label>
                                                <input type="checkbox" name="permissions[]" value="{{$permission->name}}" @if($permission->checked == 1) checked @endif /> {{$permission->name}}
                                            </li>
                                        @endforeach
                                        </ul>
                                    </fieldset>
                                </li>
                            @endif
                        </ul>
                    </fieldset>
                    <div class="clearfix"></div>
                    <div class="spacer"></div>
                    <div class="actionBar">
                        <div class="actionBarLeft">
                            <button class="k-button" type="button" id="cancelButton">Cancel</button>
                        </div>
                        <div class="actionBarRight">
                            <span class="status"></span>
                            <button class="k-button" type="button" id="saveButton">Save</button>
                            <button class="k-button" type="button" id="doneButton">Done</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- /page content -->


<!-- page scripts -->
<script src="{{ asset('js/apps/admin/roles/RoleForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection