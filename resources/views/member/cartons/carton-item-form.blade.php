@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div id="cartonItemFormDiv" class="x_content">
            <div class="formWrap">
                <form id="cartonItemForm" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <input id="carton_items_carton_id" data-bind="value: carton_id" name="carton_id" type="hidden"/>
                    <fieldset class="pull-left">
                        <ul>
                            <li>
                                <label for="full_code" class="required">Code</label>
                                <input data-bind="value: full_code" type="text" maxlength="13" class="k-textbox" id="full_code" name="full_code" placeholder="Code" required validationMessage="This field is required"/>
                            </li>
                        </ul>
                    </fieldset>
                    <div class="clearfix"></div>
                    <div class="spacer"></div>
                    <div class="actionBar">
                        <div class="actionBarLeft">
                            <button class="k-button" type="button" id="cancelButtonCartonItem">Cancel</button>
                        </div>
                        <div class="actionBarRight">
                            <span class="status"></span>
                            <button class="k-button" type="button" id="saveButtonCartonItem">Save</button>
                            <button class="k-button" type="button" id="doneButtonCartonItem">Done</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /page content -->

    <!-- page scripts -->
    <script src="{{ asset('js/apps/member/cartons/CartonItemForm.js') }}?v=1.0.0" type="text/javascript"></script>
    <!-- /page scripts -->
@endsection