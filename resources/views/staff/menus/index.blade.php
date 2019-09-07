@extends('layouts.staff')

@section('content')

        <!-- page content -->
<div class="x_panel">
    <div class="x_title">
        <h2>Menu List</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <div>
            <div class="col-sm-3">
                <div class="menuWrap">
                    <div>
                        <a class="k-button" id="addButton">
                            <span class="k-icon k-i-plus">&nbsp;</span>
                            <span>Add Menu</span>
                        </a>
                        <a class="k-button k-state-disabled" id="editButton">
                            <span class="k-icon k-edit">&nbsp;</span>
                            <span>Edit</span>
                        </a>
                        <a class="k-button k-state-disabled" id="deleteButton">
                            <span class="k-icon k-i-close">&nbsp;</span>
                            <span>Delete</span>
                        </a>
                    </div>
                    <div class="spacer"></div>
                    <div id="menuTree"></div>
                </div>
            </div>
            <div class="col-sm-9">
                <div id="menuFormContainer">
                    <div id="menu">

                        <div class="formWrap">
                            <form id="menuForm">
                                <h3>Menu Details</h3>
                                {!! csrf_field() !!}
                                <input data-bind="value: id" name="id" type="hidden"/>
                                <input data-bind="value: parent_id" name="parent_id" type="hidden"/>
                                <fieldset>
                                    <ul>
                                        <li>
                                            <label for="language" class="required">Language</label>
                                            <select name="language" id="language" data-bind="value: language">
                                                <option value="en">English</option>
                                                <option value="cn">Simplified Chinese</option>
                                                <option value="tr">Traditional Chinese</option>
                                            </select>
                                        </li>
                                        <li>
                                            <label for="name" class="required">Name</label>
                                            <input data-bind="value: name" type="text" id="name" name="name" class="k-textbox required" required validationMessage="This field is required" placeholder="Menu Name"/>
                                        </li>
                                        <li>
                                            <label for="alias">Alias</label>
                                            <input data-bind="value: alias" type="text" id="alias" name="alias" class="k-textbox required" placeholder="Menu Alias"/>
                                        </li>
                                        <li>
                                            <label for="external_link">External Link</label>
                                            <input data-bind="value: external_link" type="text" id="external_link" name="external_link" class="k-textbox textBoxLarge" placeholder="External Link"/>
                                        </li>
                                        <li>
                                            <label for="published">Published</label>
                                            <input type="hidden" name="published" value="0"/>
                                            <input data-bind="checked: published" type="checkbox" id="published" name="published" value="1"/>
                                        </li>
                                    </ul>
                                </fieldset>
                            </form>

                            <div class="actionBar">
                                <div class="actionBarLeft">
                                    <button class="k-button left" type="button" id="cancelButton">Clear</button>
                                </div>
                                <div class="actionBarRight">
                                    <span class="status"></span>
                                    <button class="k-button" type="button" id="saveButton">Save</button>
                                    <button class="k-button" type="button" id="doneButton">Done</button>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- /page content -->

@endsection

@section('page-scripts')

        <!-- page scripts -->
<script src="{{ asset('js/apps/staff/menus/MenuList.js') }}?v=1.0.0" type="text/javascript"></script>
<script src="{{ asset('js/apps/staff/menus/MenuForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection