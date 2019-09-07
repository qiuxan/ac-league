@extends('layouts.admin')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Profile Details</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div id="userFormDiv" class="formWrap">
                <form id="userForm" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <fieldset class="pull-left">
                        <ul>
                            <li>
                                <label for="name" class="required">User Name</label>
                                <input data-bind="value: name" type="text" id="name" name="name" class="k-textbox required" required validationMessage="This field is required" placeholder="User Name"/>
                            </li>
                            <li>
                                <label for="email" class="required">Login Email</label>
                                <input data-bind="value: email" type="text" id="email" name="email" class="k-textbox required" required validationMessage="This field is required" placeholder="Login Email"/>
                            </li>
                            <li>
                                <label for="password">Password</label>
                                <input data-bind="value: fakePassword" type="password" id="password" name="password" class="k-textbox textBoxLarge" placeholder="Password" autocomplete="off"/>
                            </li>
                            <li>
                                <label for="avatar">Avatar</label>
                                <span class="file-upload-label">
                                    <input type="file" name="file" id="avatar_file"/>
                                    <div><img data-bind="attr:{src: avatar}" height="50px" width="auto" /></div>
                                </span>
                                <input data-bind="value: avatar" type="hidden" name="avatar" id="avatar"/>
                            </li>
                        </ul>
                    </fieldset>
                    <div class="clearfix"></div>
                    <div class="actionBar">
                        <div class="actionBarLeft">
                            <button class="k-button" type="button" id="cancelButton">Back</button>
                        </div>
                        <div class="actionBarRight">
                            <span class="status"></span>
                            <button class="k-button" type="button" id="saveButton">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- /page content -->

@endsection

@section('page-scripts')

<!-- page scripts -->
<script src="{{ asset('js/apps/admin/users/UserProfile.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection