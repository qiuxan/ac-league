@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Post Details</h2>
            <div class="clearfix"></div>
        </div>
        <div id="postFormDiv" class="x_content">
            <div class="formWrap">
                <form id="postForm" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <input data-bind="value: user_id" name="user_id" type="hidden"/>
                    <ul>
                        <li>
                            <label for="category">Category</label>
                            <input data-bind="value: category_id" id="category_id" name="category_id" required validationMessage="This field is required"/>
                            <!-- Template -->
                            <script id="category_template" type="text/x-kendo-template">
                                #:data.category#
                            </script>
                        </li>
                        <li>
                            <label for="language" class="required">Language</label>
                            <select name="language" id="language" data-bind="value: language">
                                <option value="en">English</option>
                                <option value="cn">Chinese</option>
                                <option value="tr">Traditional Chinese</option>
                            </select>
                        </li>
                        <li>
                            <label for="icon">Icon</label>
                            <input data-bind="value: icon" type="text" id="icon" name="icon" class="k-textbox" placeholder="Font Awesome Icon"/>
                        </li>
                        <li>
                            <label for="featuer_image">Feature Image</label>
                            <span class="file-upload-label">
                                <input type="file" name="file" id="feature_image_file"/>
                                <div><img data-bind="attr:{src: feature_image}" height="50px" width="auto" /></div>
                            </span>
                            <input data-bind="value: feature_image" type="hidden" name="feature_image" id="feature_image" />
                        </li>
                        <li>
                            <label for="title">Title</label>
                            <input data-bind="value: title" type="text" id="title" name="title" class="k-textbox textBoxLarge" placeholder="Title" required validationMessage="This field is required"/>
                        </li>
                        <li>
                            <label for="alias">Alias</label>
                            <input data-bind="value: alias" type="text" id="alias" name="alias" class="k-textbox textBoxLarge" placeholder="Alias"/>
                        </li>
                        <li>
                            <label for="excerpt">Excerpt</label>
                            <textarea data-bind="value: excerpt" type="text" id="excerpt" name="excerpt" class="k-textbox textAreaLarge" placeholder="Excerpt"></textarea>
                        </li>
                        <li>
                            <label for="published">Published</label>
                            <input type="hidden" name="published" value="0"/>
                            <input data-bind="checked: published" type="checkbox" id="published" name="published" value="1"/>
                        </li>
                        <li>
                        </li>
                            <label for="date">Date</label>
                            <input data-bind="value: date" id="date" name="date" title="date" />                        
                        <li>
                            <label for="content">Content</label>
                            <div class="ckWrap">
                                <textarea data-bind="value: content" type="text" class="textAreaLarge" id="content" name="content"></textarea>
                            </div>
                        </li>
                    </ul>
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
<script src="/js/ckeditor/ckeditor.js"></script>
<script src="/js/ckeditor/adapters/jquery.js"></script>
<script src="{{ asset('js/apps/staff/posts/PostForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection