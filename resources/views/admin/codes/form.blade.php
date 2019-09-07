@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="formWrap">
            <form id="codeForm" enctype="multipart/form-data">
                {!! csrf_field() !!}
                <input data-bind="value: id" name="id" type="hidden"/>
                <fieldset class="pull-left">
                    <ul>
                        <li>
                            <label for="company_en">Company</label>
                            <span data-bind="html: company_en" id="company_en"></span>
                        </li>
                        <li>
                            <label for="batch_code">Batch</label>
                            <span data-bind="html: batch_code" id="batch_code"></span>
                        </li>
                        <li>
                            <label for="roll_code">Roll</label>
                            <span data-bind="html: roll_code" id="roll_code"></span>
                        </li>
                        <li>
                            <label for="location">Destination</label>
                            <span data-bind="html: location" id="location"></span>
                        </li>
                        <li>
                            <label for="full_code">Serial Number</label>
                            <span data-bind="html: full_code" id="full_code"></span>
                        </li>
                        <li>
                            <label for="updated_at">Updated At</label>
                            <span data-bind="html: updated_at" id="updated_at"></span>
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
                            <label for="reseller_id">Reseller</label>
                            <input data-bind="value: reseller_id" id="reseller_id" name="reseller_id" />
                            <!-- Template -->
                            <script id="reseller_template" type="text/x-kendo-template">
                                #:data.name_en#
                            </script>
                            <span class="k-invalid-msg" data-for="reseller_id"></span>
                        </li>                              
                    </ul>
                </fieldset>
                <div class="clearfix"></div>
                <div class="spacer"></div>
                <div class="actionBar">
                    <div class="actionBarLeft">
                        <button class="k-button" type="button" id="cancelButtonCodeForm">Cancel</button>
                    </div>
                    <div class="actionBarRight">
                        <span class="status"></span>
                        <button class="k-button" type="button" id="saveButtonCodeForm">Save</button>
                        <button class="k-button" type="button" id="doneButtonCodeForm">Done</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- /page content -->


<!-- page scripts -->
<script src="{{ asset('js/apps/admin/codes/CodeForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection