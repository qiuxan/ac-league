@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div id="batchRollFormDiv" class="x_content">
            <div class="formWrap">
                <form id="batchRollForm" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <input data-bind="value: batch_id" name="batch_id" type="hidden"/>
                    <fieldset class="pull-left">
                        <ul>
                            <li>
                                <label for="product_id">Roll</label>
                                <input data-bind="value: roll_id" id="roll_id" name="roll_id" />
                                <!-- Template -->
                                <script id="roll_template" type="text/x-kendo-template">
                                    #:data.roll_code#
                                </script>
                            </li>
                            <li>
                                <label for="start_code" class="required">Start Code</label>
                                <input data-bind="value: start_code" type="text" maxlength="13" class="k-textbox" id="start_code" name="start_code" placeholder="Start Code" required validationMessage="This field is required"/>
                            </li>
                            <li>
                                <label for="end_code" class="required">End Code</label>
                                <input data-bind="value: end_code" type="text" maxlength="13" class="k-textbox" id="end_code" name="end_code" placeholder="End Code" required validationMessage="This field is required"/>
                            </li>
                            <li>
                                <label for="code_quantity" class="required">Quantity</label>
                                <input data-bind="value: code_quantity" type="number" id="code_quantity" name="code_quantity" placeholder="Quantity" required validationMessage="This field is required"/>
                                <span class="k-invalid-msg" data-for="code_quantity"></span>
                            </li>
                        </ul>
                    </fieldset>
                    <div class="clearfix"></div>
                    <div class="spacer"></div>
                    <div class="actionBar">
                        <div class="actionBarLeft">
                            <button class="k-button" type="button" id="cancelButtonBatchRoll">Cancel</button>
                        </div>
                        <div class="actionBarRight">
                            <span class="status"></span>
                            <button class="k-button" type="button" id="saveButtonBatchRoll">Save</button>
                            <button class="k-button" type="button" id="doneButtonBatchRoll">Done</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- /page content -->


<!-- page scripts -->
<script src="{{ asset('js/apps/member/batches/BatchRollForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection