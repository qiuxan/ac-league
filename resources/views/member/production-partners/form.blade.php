@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Production Partner Details</h2>
            <div class="clearfix"></div>
        </div>
        <div id="productionPartnerFormDiv" class="x_content">
            <div id="tabs">
                <ul>
                    <li class="k-state-active">Information</li>
                    <li data-bind="visible: isAccount">Roles</li>
                </ul>
                <div class="formWrap">
                    <form id="productionPartnerForm" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input data-bind="value: id" name="id" type="hidden"/>
                        <input data-bind="value: user_id" name="user_id" type="hidden"/>
                        <fieldset class="pull-left">
                            <ul>
                                <li class="field-set-title"><h3>Login Info</h3></li>
                                <li>
                                    <label for="name">User Name</label>
                                    <input data-bind="value: name" type="text" id="name" name="name" class="k-textbox required" placeholder="User Name"/>
                                </li>
                                <li>
                                    <label for="email">Login Email</label>
                                    <input data-bind="value: email" type="text" id="email" name="email" class="k-textbox required" placeholder="Login Email"/>
                                </li>
                                <li>
                                    <label for="password">Password</label>
                                    <input data-bind="value: fakePassword" type="password" id="password" name="password" class="k-textbox textBoxLarge" placeholder="Password" autocomplete="off"/>
                                </li>
                                <li>
                                    <label for="avatar">Avatar</label>
                                    <span class="file-upload-label">
                                        <input type="file" name="file" id="avatar_file"/>
                                        <div><img data-bind="attr: { src: avatar}" height="50px" width="auto" /></div>
                                    </span>
                                    <input data-bind="value: avatar" type="hidden" name="avatar" id="avatar"/>
                                </li>
                            </ul>
                        </fieldset>
                        <fieldset class="pull-left">
                            <ul>
                                <li class="field-set-title"><h3>Production Partner Info</h3></li>
                                <li>
                                    <label for="name_en" class="required">Name (EN)</label>
                                    <input data-bind="value: name_en" type="text" id="name_en" name="name_en" class="k-textbox" required validationMessage="This field is required" placeholder="Name (EN)"/>
                                </li>
                                <li>
                                    <label for="name_cn" class="required">Name (CN)</label>
                                    <input data-bind="value: name_cn" type="text" id="name_cn" name="name_cn" class="k-textbox" required validationMessage="This field is required" placeholder="Name (CN)"/>
                                </li>
                                <li>
                                    <label for="name_tr" class="required">Name (TR)</label>
                                    <input data-bind="value: name_tr" type="text" id="name_tr" name="name_tr" class="k-textbox" required validationMessage="This field is required" placeholder="Name (TR)"/>
                                </li>
                                <li>
                                    <label for="address">Address</label>
                                    <input data-bind="value: address" type="text" id="address" name="address" class="k-textbox" placeholder="Address"/>
                                </li>
                                <li>
                                    <label for="phone">Phone</label>
                                    <input data-bind="value: phone" type="text" id="phone" name="phone" class="k-textbox" placeholder="Phone"/>
                                </li>
                            </ul>
                        </fieldset>
                        <div class="clearfix"></div>
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
                <div>
                    <script type="text/x-kendo-template" id="toolbarTemplate">
                        <div class="pull-left">
                            <a class="k-button" id="addButton">
                                <span class="k-icon k-i-plus">&nbsp;</span>
                                <span>Add</span>
                            </a>
                            <a class="k-button k-state-disabled" id="deleteButton">
                                <span class="k-icon k-i-close">&nbsp;</span>
                                <span>Delete</span>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                    </script>
                    <div>
                        <div id="grid"></div>
                        <div id="roleListContainer"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
        </div>
    </div>
    <!-- /page content -->

    <!-- page scripts -->
    <script src="{{ asset('js/apps/member/production-partners/ProductionPartnerForm.js') }}?v=1.0.0" type="text/javascript"></script>
    <!-- /page scripts -->

@endsection