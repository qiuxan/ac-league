@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_content">
            <div id="systemVariableFormDiv" class="formWrap">
                <form id="systemVariableForm" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <fieldset class="pull-left">
                        <ul>
                            <li>
                                <label for="type" class="required">Type</label>
                                <select data-bind="value: type" name="type" id="type" required validationMessage="This field is required">
                                    <option value=""></option>
                                    <option value="{{\App\SystemVariable::TYPE_VER_AUTH_DISPLAY}}">Verification/Authentication Display</option>
                                    <option value="{{\App\SystemVariable::TYPE_PROMOTION_DISPLAY}}">Promotion Attribute Display</option>
                                    <option value="{{\App\SystemVariable::TYPE_VERIFICATION_RULE}}">Verification Rule</option>
                                </select>
                            </li>
                            <li>
                                <label for="variable" class="required">Variable</label>
                                <input data-bind="value: variable" type="text" id="variable" name="variable" class="k-textbox required" required validationMessage="This field is required" placeholder="Variable"/>
                            </li>
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
<script src="{{ asset('js/apps/admin/system-variables/SystemVariableForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection