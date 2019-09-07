@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div class="formWrap">
            <form id="MessageForm" enctype="multipart/form-data">
                {!! csrf_field() !!}
                <input data-bind="value: id" name="id" type="hidden"/>
                <input data-bind="value: order_id" name="order_id" type="hidden"/>
                <fieldset class="pull-left">
                    <ul>
                        <li>
                            <label for="type">Company: </label>
                            <span data-bind="html: company_name"></span>
                        </li>
                        <li>
                            <label for="message">Message: </label>
                            <span data-bind="html: message"></span>
                        </li>
                        <li>
                            <label for="time">Time: </label>
                            <span data-bind="html: created_at"></span>
                        </li>
                    </ul>
                </fieldset>
                <div class="clearfix"></div>
                <div class="spacer"></div>
                <div class="actionBar">
                    <div class="actionBarRight">
                        <button class="k-button" type="button" id="doneButtonMessageForm">Done</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- /page content -->


<!-- page scripts -->
<script src="{{ asset('js/apps/admin/member-messages/MemberMessageForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection