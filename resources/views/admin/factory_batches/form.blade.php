@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Factory Batch Details</h2>
            <div class="clearfix"></div>
        </div>
        <div id="factoryBatchFormDiv" class="x_content">
            <div class="formWrap">
                <form id="factoryBatchForm" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <input data-bind="value: user_id" name="user_id" type="hidden"/>
                    <fieldset class="pull-left">
                        <ul>
                            <li data-bind="invisible: isNew">
                                <label for="batch_code">Batch Code</label>
                                <input data-bind="value: batch_code" type="text" id="batch_code" name="batch_code" class="k-textbox" disabled/>
                            </li>
                            <li data-bind="invisible: isNew">
                                <label for="status">Status</label>
                                <select name="status" id="status" data-bind="value: status">
                                    <option value="{{\App\FactoryBatch::STATUS_NOT_STARTED}}">Not Started</option>
                                    <option value="{{\App\FactoryBatch::STATUS_IN_PROGRESS}}">In Progress</option>
                                    <option value="{{\App\FactoryBatch::STATUS_DONE}}">Done</option>
                                </select>
                            </li>
                            <li>
                                <label for="quantity" class="required">Quantity</label>
                                <input data-bind="value: quantity, disabled: isOld" type="number" id="quantity" name="quantity" required validationMessage="This field is required" placeholder="Quantity"/>
                            </li>
                            <li>
                                <label for="description" class="required">Description</label>
                                <textarea data-bind="value: description" id="description" name="description" rows="10" required validationMessage="This field is required" class="k-textbox" placeholder="Description"></textarea>
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
<script src="{{ asset('js/apps/admin/factory-batches/FactoryBatchForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection