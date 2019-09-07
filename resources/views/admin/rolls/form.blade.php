@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Roll Details</h2>
            <div class="clearfix"></div>
        </div>
        <div id="rollFormDiv" class="x_content">
            <div class="formWrap">
                <form id="rollForm" enctype="multipart/form-data">
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
                                <label for="roll_code">Roll Code</label>
                                <input data-bind="value: roll_code" type="text" id="roll_code" name="roll_code" class="k-textbox" disabled/>
                            </li>
                            <li>
                                <label for="quantity" class="required">Quantity</label>
                                <input data-bind="value: quantity" type="number" id="quantity" name="quantity" disabled/>
                            </li>
                            <li>
                                <label for="finished">Finished</label>
                                <input type="hidden" name="finished" value="0"/>
                                <input data-bind="checked: finished" type="checkbox" id="finished" name="finished" value="1"/>
                            </li>
                            <li>
                                <label for="reversed">Reversed</label>
                                <input type="checkbox" id="reversed" name="reversed" value="1"/>
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
                    <h2>Roll Codes</h2>
                    <div class="clearfix"></div>
                </div>
                <div id="grid"></div>
                <div id="codeFormContainer"></div>
                <script type="text/x-kendo-template" id="toolbarTemplate">
                    <div class="pull-left">
                        <a class="k-button k-state-disabled" id="editButton">
                            <span class="k-icon k-edit">&nbsp;</span>
                            <span>Open</span>
                        </a>
                        <a class="k-button k-button-icontext" id="exportButton">
                            <span class="k-icon k-i-excel"></span>
                            <span>Export to Excel</span>
                        </a>
                    </div>
                    <div class="pull-right">
                        <span>
                            <select id="filter_disposition_id">
                                <option value="">All Disposition</option>
                                <option value="1">Active</option>
                                <option value="2">Transit</option>
                                <option value="3">Selling</option>
                                <option value="4">Sold</option>
                                <option value="5">Recalled</option>
                                <option value="6">Blacklisted</option>
                            </select>
                        </span>
                        <span class="k-textbox k-space-left">
                            <a class="k-icon k-i-search">&nbsp;</a>
                            <input type="text" id="searchFilter" placeholder="search"/>
                        </span>
                    </div>
                </script>
            </div>
        </div>
    </div>

    <!-- /page content -->


<!-- page scripts -->
<script src="{{ asset('js/apps/admin/rolls/RollForm.js') }}?v=1.0.0" type="text/javascript"></script>
<script src="{{ asset('js/jszip.min.js') }}" type="text/javascript"></script>
<!-- /page scripts -->

@endsection