@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Ingredient Shipment Details</h2>
            <div class="clearfix"></div>
        </div>
        <div id="ingredientShipmentFormDiv" class="x_content">
            <div class="formWrap">
                <form id="ingredientShipmentForm" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <fieldset class="pull-left">
                        <ul>
                            @role(\App\Constant::INGREDIENT_SUPPLIER)
                            <li>
                                <label>Shipped Time</label>
                                <input data-bind="value: shipped_time" type="text" id="shipped_time" name="shipped_time" placeholder="Shipped Time"/>
                            </li>
                            @endrole
                            @role(\App\Constant::CONTRACT_MANUFACTURER)
                            <li>
                                <label>Received Time</label>
                                <input data-bind="value: received_time" type="text" id="received_time" name="received_time" placeholder="Received Time"/>
                            </li>
                            @endrole
                            <li>
                                <label>Tracking Code</label>
                                <input data-bind="value: tracking_code" type="text" id="tracking_code" class="k-textbox" name="tracking_code" placeholder="Tracking Code"/>
                            </li>
                            @role(\App\Constant::CONTRACT_MANUFACTURER)
                            <li>
                                <label for="source_id" class="required">Source</label>
                                <span data-bind="html: source_name"></span>
                            </li>
                            @endrole
                            <li>
                                <label for="destination_id" class="required">Destination</label>
                                <input data-bind="value: destination_id" id="destination_id" name="destination_id" required validationMessage="This field is required" />
                                <!-- Template -->
                                <script id="production_partner_template" type="text/x-kendo-template">
                                    #:data.name_en#
                                </script>
                                <span class="k-invalid-msg" data-for="destination_id"></span>
                            </li>
                            <li>
                                <label>Notes</label>
                                <textarea data-bind="value: notes" id="notes" name="notes" class="k-textbox" placeholder="Notes"></textarea>
                            </li>
                        </ul>
                    </fieldset>
                    <div class="clearfix"></div>
                    <div class="spacer"></div>
                    <div id="ingredientLotGrid"></div>
                    <script type="text/x-kendo-template" id="toolbarTemplate">
                        <div class="toolbar">
                            <div class="toolbarButtons">
                                <a class="k-button" id="addButton">
                                    <span class="k-icon k-i-plus">&nbsp;</span>
                                    <span>Add</span>
                                </a>
                                <a class="k-button k-state-disabled" id="deleteButton">
                                    <span class="k-icon k-i-close">&nbsp;</span>
                                    <span>Delete</span>
                                </a>
                            </div>
                        </div>
                    </script>
                    <div id="ingredientLotListContainer"></div>
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
<script src="{{ asset('js/apps/production-partner/ingredient-shipments/IngredientShipmentForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection