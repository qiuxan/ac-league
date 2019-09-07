@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Slide Details</h2>
            <div class="clearfix"></div>
        </div>
        <div id="slideFormDiv" class="x_content">
            <div class="formWrap">
                <form id="slideForm" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <input data-bind="value: user_id" name="user_id" type="hidden"/>
                    <input id="priority" name="priority" type="hidden"/>
                    <fieldset class="pull-left">
                        <ul>
                            <li>
                                <label for="language" class="required">Language</label>
                                <select name="language" id="language" data-bind="value: language">
                                    <option value="en">English</option>
                                    <option value="cn">Chinese</option>
                                    <option value="tr">Traditional Chinese</option>
                                </select>
                            </li>
                            <li>
                                <label for="slide_image">Slide Image</label>
                                <span class="file-upload-label">
                                    <input type="file" name="file" id="slide_image_file" required validationMessage="This field is required"/>
                                    <div><img  data-bind="attr:{src: location}"  height="50px" width="auto" /></div>
                                </span>
                                <input data-bind="value: file_id" type="hidden" name="slide_image_file_id" id="slide_image" />
                            </li>
                            <li>
                                <label for="title">Title</label>
                                <input data-bind="value: title" style="width:600px" type="text" id="title" name="title" class="k-textbox" placeholder="Title" required validationMessage="This field is required"/>
                            </li>
                            <li>
                                <label for="published">Published</label>
                                <input type="hidden" name="published" value="0"/>
                                <input data-bind="checked: published" type="checkbox" id="published" name="published" value="1"/>
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
        </div>
    </div>

    <!-- /page content -->


<!-- page scripts -->
<script src="{{ asset('js/apps/staff/slides/SlideForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection