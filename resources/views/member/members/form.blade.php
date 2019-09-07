@extends('layouts.member')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Profile Details</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div id="memberFormDiv" class="formWrap">
                <form id="memberForm" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <input data-bind="value: user_id" name="user_id" type="hidden"/>
                    <fieldset class="pull-left">
                        <ul>
                            <li class="field-set-title"><h3>Login information</h3></li>
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
                    <fieldset class="pull-left">
                        <ul>
                            <li class="field-set-title"><h3>Company information</h3></li>
                            <li>
                                <label for="company_en" class="required">Company (EN)</label>
                                <input data-bind="value: company_en" type="text" id="company_en" name="company_en" class="k-textbox" required validationMessage="This field is required" placeholder="English Name"/>
                            </li>
                            <li>
                                <label for="company_cn" class="required">Company (CN)</label>
                                <input data-bind="value: company_cn" type="text" id="company_cn" name="company_cn" class="k-textbox" required validationMessage="This field is required" placeholder="Chinese Name"/>
                            </li>
                            <li>
                                <label for="company_tr" class="required">Company (TR)</label>
                                <input data-bind="value: company_tr" type="text" id="company_tr" name="company_tr" class="k-textbox" required validationMessage="This field is required" placeholder="Chinese Name"/>
                            </li>                            
                            <li>
                                <label for="phone" class="required">Phone</label>
                                <input data-bind="value: phone" type="text" id="phone" name="phone" required validationMessage="This field is required" class="k-textbox textBoxLarge" placeholder="Phone Number"/>
                            </li>
                            <li>
                                <label for="company_email" class="required">Email</label>
                                <input data-bind="value: company_email" type="email" id="company_email" required validationMessage="This field is required" name="company_email" class="k-textbox textBoxLarge" placeholder="Company Email"/>
                            </li>
                            <li>
                                <label for="website">Website</label>
                                <input data-bind="value: website" type="text" id="website" name="website" class="k-textbox textBoxLarge" placeholder="Website"/>
                            </li>
                            <li>
                                <label for="country_cn" class="required">Country (EN)</label>
                                <input data-bind="value: country_en" type="text" id="country_en" name="country_en" required validationMessage="This field is required" class="k-textbox textBoxLarge" placeholder="Country" />
                            </li>
                            <li>
                                <label for="country_cn" class="required">Country (CN)</label>
                                <input data-bind="value: country_cn" type="text" id="country_cn" name="country_cn" required validationMessage="This field is required" class="k-textbox textBoxLarge" placeholder="Country" />
                            </li>
                            <li>
                                <label for="country_tr" class="required">Country (TR)</label>
                                <input data-bind="value: country_tr" type="text" id="country_tr" name="country_tr" required validationMessage="This field is required" class="k-textbox textBoxLarge" placeholder="Country" />
                            </li>                            
                            <li>
                                <label for="logo">Logo</label>
                                <span class="file-upload-label">
                                    <input type="file" name="file" id="logo_file"/>
                                    <div><img data-bind="attr:{src: logo}" height="50px" width="auto" /></div>
                                </span>
                                <input data-bind="value: logo" type="hidden" name="logo" id="logo"/>
                            </li>
                            <li>
                                <label for="background_image">Background Image</label>
                                <span class="file-upload-label">
                                    <input type="file" name="file" id="background_image_file"/>
                                    <div><img  data-bind="attr: { src: background_image}" height="50px" width="auto" /></div>
                                </span>
                                <input data-bind="value: background_image" type="hidden" name="background_image" id="background_image"/>
                                <button class="k-button" type="button" id="clearBackgroundImage">Clear</button>
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
<script src="{{ asset('js/apps/member/members/MemberForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection