@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Ingredient Lot Details</h2>
            <div class="clearfix"></div>
        </div>
        <div id="ingredientLotFormDiv" class="x_content">
            <div class="formWrap">
                <form id="ingredientLotForm" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <fieldset class="pull-left">
                        <ul>
                            @role(\App\Constant::INGREDIENT_SUPPLIER)
                            <li>
                                <label class="required">Created Time</label>
                                <input data-bind="value: created_time" type="text" id="created_time" name="created_time" required validationMessage="This field is required" placeholder="Created Time"/>
                                <span data-for="event_time" class="k-invalid-msg"></span>
                            </li>
                            <li data-bind="visible: hasShippedTime">
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
                                <label for="lot_code" class="required">Lot #</label>
                                <input data-bind="value: lot_code" type="text" id="lot_code" name="lot_code" class="k-textbox" placeholder="Lot #" required validationMessage="This field is required"/>
                            </li>
                            <li>
                                <label for="ingredient_id" class="required">Ingredient</label>
                                <input data-bind="value: ingredient_id" id="ingredient_id" name="ingredient_id" required validationMessage="This field is required" />
                                <!-- Template -->
                                <script id="ingredient_template" type="text/x-kendo-template">
                                    #:data.gtin# - #:data.name#
                                </script>
                                <span class="k-invalid-msg" data-for="ingredient_id"></span>
                            </li>
                            <li>
                                <label for="production_date">Production Date</label>
                                <input data-bind="value: production_date" type="text" id="production_date" name="production_date" placeholder="Production Date"/>
                            </li>
                            <li>
                                <label for="expiration_date">Expiration Date</label>
                                <input data-bind="value: expiration_date" type="text" id="expiration_date" name="expiration_date" placeholder="Expiration Date"/>
                            </li>
                            <li>
                                <label for="certificate_url">Certificate</label>
                                    <span class="file-upload-label">
                                        <input type="file" name="file" id="certificate_file"/>
                                        <div><img data-bind="attr: { src: certificate_url}" height="50px" width="auto" /></div>
                                    </span>
                                <input data-bind="value: certificate_url" type="hidden" name="certificate_url" id="certificate_url"/>
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
<script src="{{ asset('js/apps/production-partner/ingredient-lots/IngredientLotForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection