@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Batch Details</h2>
            <div class="clearfix"></div>
        </div>
        <div id="batchFormDiv" class="x_content">
            <div id="tabs">
                <ul>
                    <li class="k-state-active">Batch Info</li>
                    <li data-bind="invisible: isNew">Batch Rolls</li>
                    <li data-bind="invisible: isNew">Batch Ingredients</li>
                </ul>
                <div class="formWrap">
                    <form id="batchForm" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input data-bind="value: id" name="id" type="hidden"/>
                        <fieldset class="pull-left">
                            <ul>
                                <li>
                                    <label for="batch_code" class="required">Batch #</label>
                                    <input data-bind="value: batch_code" type="text" id="batch_code" name="batch_code" class="k-textbox" placeholder="Batch #" required validationMessage="This field is required"/>
                                </li>
                                <li>
                                    <label for="product_id" class="required">Product</label>
                                    <input data-bind="value: product_id" id="product_id" name="product_id" required validationMessage="This field is required" />
                                    <!-- Template -->
                                    <script id="product_template" type="text/x-kendo-template">
                                        #:data.name_en#
                                    </script>
                                    <span class="k-invalid-msg" data-for="product_id"></span>
                                </li>
                                <li>
                                    <label for="quantity">Quantity</label>
                                    <input data-bind="value: quantity" type="number" id="quantity" name="quantity" placeholder="Quantity"/>
                                </li>
                                <li>
                                    <label for="location">Destination</label>
                                    <input data-bind="value: location" type="text" id="location" name="location" class="k-textbox" placeholder="Destination"/>
                                </li>
                                <li>
                                    <label for="reseller">Reseller</label>
                                    <select data-bind="value: reseller_id" name="reseller_id" id="reseller_id">
                                        <option value="">Select A Reseller</option>
                                        @foreach($resellers as $reseller)
                                            <option value="{{ $reseller->id }}">{{ $reseller->name_en }}</option>
                                        @endforeach
                                    </select>
                                </li>                     
                                <li data-bind="visible: isAssigned">
                                    <label for="disposition_id">Disposition</label>
                                    <select data-bind="value: disposition_id" name="disposition_id" id="disposition_id">
                                        @foreach($dispositions as $disposition)
                                            <option value="{{ $disposition->id }}">{{ $disposition->disposition }}</option>
                                        @endforeach
                                    </select>
                                </li>
                                <li>
                                    <label for="production_date">Production Date</label>
                                    <input data-bind="value: production_date" type="text" id="production_date" name="production_date" placeholder="Production Date"/>
                                </li>
                                <li>
                                    <label for="expiration_date">Expiration Date</label>
                                    <input data-bind="value: expiration_date" type="text" id="expiration_date" name="expiration_date" placeholder="Expiration Date"/>
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

                <div class="formWrap">
                    <div class="x_title">
                        <h2>Roll List</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div id="grid"></div>
                    <div id="batchRollFormContainer"></div>
                    <script type="text/x-kendo-template" id="toolbarTemplate">
                        <div class="toolbar">
                            <div class="toolbarButtons">
                                <a class="k-button" id="addButton">
                                    <span class="k-icon k-i-add">&nbsp;</span>
                                    <span>Add</span>
                                </a>
                                <a class="k-button k-state-disabled" id="editButton">
                                    <span class="k-icon k-edit">&nbsp;</span>
                                    <span>Open</span>
                                </a>
                                <a class="k-button k-state-disabled" id="deleteButton">
                                    <span class="k-icon k-close">&nbsp;</span>
                                    <span>Delete</span>
                                </a>
                            </div>
                        </div>
                    </script>
                </div>
                <div class="formWrap">
                    <div class="x_title">
                        <h2>Ingredient List</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div id="ingredientGrid"></div>
                    <div id="batchIngredientListContainer"></div>
                    <script type="text/x-kendo-template" id="ingredientToolbarTemplate">
                        <div class="toolbar">
                            <div class="toolbarButtons">
                                <a class="k-button" id="addIngredientButton">
                                    <span class="k-icon k-i-add">&nbsp;</span>
                                    <span>Add</span>
                                </a>
                                <a class="k-button k-state-disabled" id="deleteIngredientButton">
                                    <span class="k-icon k-close">&nbsp;</span>
                                    <span>Delete</span>
                                </a>
                            </div>
                        </div>
                    </script>
                </div>
            </div>
        </div>
    </div>

    <!-- /page content -->


<!-- page scripts -->
<script src="{{ asset('js/apps/production-partner/batches/BatchForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection