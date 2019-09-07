@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Product Details</h2>
            <div class="clearfix"></div>
        </div>
        <div id="productFormDiv" class="x_content">
            <div id="tabs">
                <ul>
                    <li class="k-state-active">Product Info</li>
                    <li data-bind="invisible: isNew">Product Attributes</li>
                    <li data-bind="invisible: isNew">Product Images</li>
                </ul>
                <div class="formWrap">
                    <form id="productForm" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input data-bind="value: id" name="id" type="hidden"/>
                        <input data-bind="value: user_id" name="user_id" type="hidden"/>
                        <input data-bind="value: member_id" name="member_id" type="hidden"/>
                        <fieldset class="pull-left">
                            <ul>
                                <li>
                                    <label for="gtin">GTIN</label>
                                    <input data-bind="value: gtin" type="text" id="gtin" name="gtin" class="k-textbox" placeholder="GTIN"/>
                                </li>
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
                            </ul>
                        </fieldset>

                        <fieldset class="pull-left">
                            <ul>
                                @if(auth()->user()->id == 2)
                                    <li>
                                        <label for="company_en">Company (EN)</label>
                                        <input data-bind="value: company_en" type="text" id="company_en" name="company_en" class="k-textbox" placeholder="Company (EN)"/>
                                    </li>
                                    <li>
                                        <label for="company_cn">Company (CN)</label>
                                        <input data-bind="value: company_cn" type="text" id="company_cn" name="company_cn" class="k-textbox" placeholder="Company (CN)"/>
                                    </li>
                                    <li>
                                        <label for="company_tr">Company (TR)</label>
                                        <input data-bind="value: company_tr" type="text" id="company_tr" name="company_tr" class="k-textbox" placeholder="Company (TR)"/>
                                    </li>                                    
                                    <li>
                                        <label for="company_website">Company Website</label>
                                        <input data-bind="value: company_website" type="text" id="company_website" name="company_website" class="k-textbox" placeholder="Company Website"/>
                                    </li>
                                    <li>
                                        <label for="company_logo">Logo</label>
                                    <span class="file-upload-label">
                                        <input type="file" name="file" id="company_logo_file"/>
                                        <div><img  data-bind="attr: { src: company_logo}" height="50px" width="auto" /></div>
                                    </span>
                                        <input data-bind="value: company_logo" type="hidden" name="company_logo" id="company_logo"/>
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
                <div>
                    <script type="text/x-kendo-template" id="toolbarTemplate">
                        <div class="pull-left">
                            <a class="k-button" id="addButton">
                                <span class="k-icon k-i-plus">&nbsp;</span>
                                <span>Add</span>
                            </a>
                            <a class="k-button k-state-disabled" id="editButton">
                                <span class="k-icon k-edit">&nbsp;</span>
                                <span>Open</span>
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
                        <div id="productAttributeFormContainer"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div>
                    <input type="file" name="file" id="image"/>
                    <div class="product-images-header">
                        <div class="imageDiv col-md-2 col-xs-6 text-center padding-5">
                            <span>Image</span>
                        </div>
                        <div class="col-md-3 col-xs-6 text-center padding-5">
                            <span>Description (EN)</span>
                        </div>
                        <div class="col-md-3 col-xs-6 text-center padding-5">
                            <span>Description (CN)</span>
                        </div>
                        <div class="col-md-2 col-xs-3 text-center">
                            <span>Thumbnail</span>
                        </div>
                        <div class="col-md-2 col-xs-3 text-center">
                            <span>Action</span>
                        </div>
                    </div>
                    <div id="productImages"></div>
                    <div class="clear"></div>
                    <script type="text/x-kendo-template" id="imageTemplate">
                        <div class="productImage">
                            <div class="imageDiv col-md-2 col-xs-6 text-center padding-5">
                                <img src="#: location #" alt="#: original_name #" width="100%"/>
                            </div>
                            <div class="col-md-2 col-xs-6 text-center padding-5">
                                <textarea id="imageDescription_en_#:file_id#" rows="10" placeholder="Description (EN)" style="width: 100%;" onblur="ProductForm.setProductImageDescription( #: file_id #, 1 )" >#if (description_en != null){# #=description_en# #}#</textarea>
                            </div>
                            <div class="col-md-2 col-xs-6 text-center padding-5">
                                <textarea id="imageDescription_cn_#:file_id#" rows="10" placeholder="Description (CN)" style="width: 100%;" onblur="ProductForm.setProductImageDescription( #: file_id #, 0 )" >#if (description_cn != null){# #=description_cn# #}#</textarea>
                            </div>
                            <div class="col-md-2 col-xs-6 text-center padding-5">
                                <textarea id="imageDescription_tr_#:file_id#" rows="10" placeholder="Description (TR)" style="width: 100%;" onblur="ProductForm.setProductImageDescription( #: file_id #, 2 )" >#if (description_tr != null){# #=description_tr# #}#</textarea>
                            </div>                            
                            <div class="col-md-2 col-xs-3 text-center">
                                <input type="radio" name="thumbnail" #if (thumbnail == 1) {# #='checked'# #} else {# #=''# #}# onchange="ProductForm.setProductThumbnail( #: file_id # )" />
                            </div>
                            <div class="col-md-2 col-xs-3 text-center">
                                <button class="k-button" type="button" onclick="ProductForm.deleteProductImage( #: file_id # )">Delete</button>
                            </div>
                        </div>
                    </script>
                </div>
            </div>
        </div>
    </div>

    <!-- /page content -->


<!-- page scripts -->
<script src="/js/ckeditor/ckeditor.js"></script>
<script src="/js/ckeditor/adapters/jquery.js"></script>
<script src="{{ asset('js/apps/member/products/ProductForm.js') }}?v=1.0.0" type="text/javascript"></script>
<script src="{{ asset('js/apps/member/products/ProductAttributeList.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection