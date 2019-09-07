@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div id="productAttributeFormDiv" class="x_content">
            <div class="formWrap">
                <form id="productAttributeForm" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <input data-bind="value: product_id" name="product_id" type="hidden"/>
                    <fieldset>
                        <ul>
                            <li>
                                <label for="name" class="required">Name</label>
                                <input data-bind="value: name" type="text" class="k-textbox" id="name" name="name" placeholder="Name" required validationMessage="This field is required"/>
                            </li>
                            <li>
                                <label for="language" class="required">Language</label>
                                <select name="language" id="language" data-bind="value: language" required validationMessage="This field is required">
                                    <option value="en">English</option>
                                    <option value="cn">Simplified Chinese</option>
                                    <option value="tr">Traditional Chinese</option>
                                </select>
                            </li>
                            <li>
                                <label for="type" class="required">Type</label>
                                <select name="type" id="type" data-bind="value: type" required validationMessage="This field is required">
                                    <option value="{{\App\ProductAttribute::TYPE_TEXT_BOX}}">Text</option>
                                    <option value="{{\App\ProductAttribute::TYPE_TEXT_AREA}}">Content</option>
                                    <option value="{{\App\ProductAttribute::TYPE_IMAGE}}">Image</option>
                                </select>
                            </li>
                            <li>
                                <label for="displayed_at" class="required">Displayed At</label>
                                <select name="displayed_at" id="displayed_at" data-bind="value: displayed_at" required validationMessage="This field is required">
                                    <option value="{{\App\ProductAttribute::DISPLAY_BOTH}}">Verification + Authentication</option>
                                    <option value="{{\App\ProductAttribute::DISPLAY_VERIFICATION}}">Verification Page</option>
                                    <option value="{{\App\ProductAttribute::DISPLAY_AUTHENTICATION}}">Authentication Page</option>
                                    <option value="{{\App\ProductAttribute::DISPLAY_NONE}}">None</option>
                                </select>
                            </li>
                            <li data-bind="visible: isTextBox" class="required">
                                <label for="value_textBox">Value</label>
                                <input data-bind="value: value_textBox" type="text" id="value_textBox" name="value_textBox" class="k-textbox" placeholder="Value"/>
                            </li>
                            <li data-bind="visible: isTextArea" class="required">
                                <label for="value_textArea">Value</label>
                                <div class="ckWrap">
                                    <textarea data-bind="value: value_textArea" type="text" id="value_textArea" name="value_textArea" placeholder="Value"></textarea>
                                </div>
                            </li>
                            <li data-bind="visible: isImage">
                                <label for="value_image" class="required">Image</label>
                                    <span class="file-upload-label">
                                        <input type="file" name="file" id="image_file"/>
                                        <div><img  data-bind="attr: { src: value_image}" height="50px" width="auto" /></div>
                                    </span>
                                <input data-bind="value: value_image" type="hidden" name="value_image" id="value_image"/>
                            </li>
                        </ul>
                    </fieldset>
                    <div class="clearfix"></div>
                    <div class="spacer"></div>
                    <div class="actionBar">
                        <div class="actionBarLeft">
                            <button class="k-button" type="button" id="cancelButtonProductAttribute">Cancel</button>
                        </div>
                        <div class="actionBarRight">
                            <span class="status"></span>
                            <button class="k-button" type="button" id="saveButtonProductAttribute">Save</button>
                            <button class="k-button" type="button" id="doneButtonProductAttribute">Done</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- /page content -->


<!-- page scripts -->
<script src="{{ asset('js/apps/production-partner/products/ProductAttributeForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection