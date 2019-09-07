@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Ingredient Details</h2>
            <div class="clearfix"></div>
        </div>
        <div id="ingredientFormDiv" class="x_content">
            <div class="formWrap">
                <form id="ingredientForm" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <ul>
                        <li>
                            <label for="gtin">GTIN</label>
                            <input data-bind="value: gtin" type="text" id="gtin" name="gtin" class="k-textbox" placeholder="GTIN"/>
                        </li>
                        <li>
                            <label for="name" class="required">Name</label>
                            <input data-bind="value: name" type="text" id="name" name="name" class="k-textbox" required validationMessage="This field is required" placeholder="Name"/>
                        </li>
                        <li>
                            <label for="origin" class="required">Origin</label>
                            <input data-bind="value: origin" type="text" id="origin" name="origin" class="k-textbox" required validationMessage="This field is required" placeholder="Origin"/>
                        </li>
                        <li>
                            <label for="description">Description</label>
                            <div class="ckWrap">
                                <textarea data-bind="value: description" type="text" id="description" name="description" placeholder="Description"></textarea>
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
<script src="{{ asset('js/apps/production-partner/ingredients/IngredientForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection