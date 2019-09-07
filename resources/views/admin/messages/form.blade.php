@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Batch Details</h2>
            <div class="clearfix"></div>
        </div>
        <div id="batchFormDiv" class="x_content">
            <div class="formWrap">
                <form id="batchForm" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <input data-bind="value: user_id" name="user_id" type="hidden"/>
                    <fieldset class="pull-left">
                        <ul>
                            <li>
                                <label for="company_en">Company</label>
                                <input data-bind="value: member_id" id="member_id" name="member_id" />
                                <!-- Template -->
                                <script id="member_template" type="text/x-kendo-template">
                                    #:data.company_en#
                                </script>
                            </li>
                            <li>
                                <label for="product_id">Product</label>
                                <input data-bind="value: product_id" id="product_id" name="product_id" />
                                <!-- Template -->
                                <script id="product_template" type="text/x-kendo-template">
                                    #:data.name_en#
                                </script>
                            </li>
                            <li data-bind="invisible: isNew">
                                <label for="batch_code">Batch Code</label>
                                <input data-bind="value: batch_code" type="text" id="batch_code" name="batch_code" class="k-textbox" disabled/>
                            </li>
                            <li>
                                <label for="quantity" class="required">Quantity</label>
                                <input data-bind="value: quantity, disabled: isOld" type="number" id="quantity" name="quantity" required validationMessage="This field is required" placeholder="Quantity"/>
                            </li>
                            <li>
                                <label for="location">Location</label>
                                <input data-bind="value: location" type="text" id="location" name="location" class="k-textbox" placeholder="Location"/>
                            </li>
                            <li data-bind="visible: isAssigned">
                                <label for="disposition">Disposition</label>
                                <select data-bind="value: disposition" name="disposition" id="disposition">
                                </select>
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
<script src="{{ asset('js/apps/admin/batches/BatchForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection