@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Production Partner Details</h2>
            <div class="clearfix"></div>
        </div>
        <div id="productionPartnerFormDiv" class="x_content">
            <div class="formWrap">
                <form id="productionPartnerForm" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <input data-bind="value: user_id" name="user_id" type="hidden"/>
                    <input id="priority" name="priority" type="hidden"/>
                    <fieldset class="pull-left">
                        <ul>        
                            <li>
                                <label for="company_en" class="required">Company</label>
                                <select data-bind="value: member_id" name="member_id" id="member_id" required validationMessage="This field is required">
                                    <option value=""></option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->company_en }}</option>
                                    @endforeach
                                </select>
                            </li>                        
                            <li>
                                <label for="name_en" class="required">Name (EN)</label>
                                <input data-bind="value: name_en" type="text" id="name_en" name="name_en" class="k-textbox" required validationMessage="This field is required" placeholder="Name (EN)"/>
                            </li>                                                                            
                            <li>
                                <label for="name_cn" class="required">Name (CN)</label>
                                <input data-bind="value: name_cn" type="text" id="name_cn" name="name_cn" class="k-textbox" required validationMessage="This field is required" placeholder="Name (CN)"/>
                            </li>                                                                                                        
                            <li>
                                <label for="name_tr" class="required">Name (TR)</label>
                                <input data-bind="value: name_tr" type="text" id="name_tr" name="name_tr" class="k-textbox" required validationMessage="This field is required" placeholder="Name (TR)"/>
                            </li>                                                                                                        
                            <li>
                                <label for="address">Address</label>
                                <input data-bind="value: address" type="text" id="address" name="address" class="k-textbox" placeholder="Address"/>
                            </li>                                                                                                        
                            <li>
                                <label for="phone">Phone</label>
                                <input data-bind="value: phone" type="text" id="phone" name="phone" class="k-textbox" placeholder="Phone"/>
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
    <script src="{{ asset('js/apps/admin/production-partners/ProductionPartnerForm.js') }}?v=1.0.0" type="text/javascript"></script>
    <!-- /page scripts -->

@endsection