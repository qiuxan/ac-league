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
                                <label for="company_en" class="required">Company</label>
                                <select data-bind="value: member_id" name="member_id" id="member_id" required validationMessage="This field is required">
                                    <option value=""></option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->company_en }}</option>
                                    @endforeach
                                </select>
                            </li>
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
                                <label for="system_variable_id" class="required">Variable</label>
                                <input data-bind="value: system_variable_id" id="system_variable_id" name="system_variable_id" />
                                <!-- Template -->
                                <script id="system_variable_template" type="text/x-kendo-template">
                                    #:data.variable#
                                </script>
                            </li>
                            <li data-bind="visible: isVerRule" class="required">
                                <label for="value_verification_rule" class="required">Value</label>
                                <input data-bind="value: value_verification_rule" type="text" id="value_verification_rule" name="value_verification_rule" class="k-textbox" placeholder="Value"/>
                            </li>
                            <li data-bind="visible: isVerAuthDisplay" class="required">
                                <label for="value_ver_auth_display" class="required">Value</label>
                                <select data-bind="value: value_ver_auth_display" name="value_ver_auth_display" id="value_ver_auth_display">
                                    <option value="{{\App\ProductAttribute::DISPLAY_BOTH}}">Verification + Authentication</option>
                                    <option value="{{\App\ProductAttribute::DISPLAY_VERIFICATION}}">Verification Page</option>
                                    <option value="{{\App\ProductAttribute::DISPLAY_AUTHENTICATION}}">Authentication Page</option>
                                    <option value="{{\App\ProductAttribute::DISPLAY_NONE}}">None</option>
                                </select>
                            </li>
                            <li data-bind="visible: isPromoDisplay" class="required">
                                <label for="value_promotion_display" class="required">Value</label>
                                <select data-bind="value: value_promotion_display" name="value_promotion_display" id="value_promotion_display">
                                    <option value="{{\App\MemberConfiguration::TYPE_SHOW}}">Show</option>
                                    <option value="{{\App\MemberConfiguration::TYPE_HIDE}}">Hide</option>
                                </select>
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
<script src="{{ asset('js/apps/admin/member-configurations/MemberConfigurationForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection