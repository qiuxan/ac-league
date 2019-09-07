@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div id="questionOptionFormDiv" class="x_content">
            <div class="formWrap">
                <form id="questionOptionForm" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <input data-bind="value: id" name="id" type="hidden"/>
                    <input data-bind="value: question_id" name="question_id" type="hidden"/>
                    <fieldset class="pull-left">
                        <ul>
                            <li>
                                <label for="option_en" class="required">Option (EN)</label>
                                <input data-bind="value: option_en" type="text" class="k-textbox" id="option_en" name="option_en" placeholder="Option (EN)" required validationMessage="This field is required"/>
                            </li>
                            <li>
                                <label for="option_cn" class="required">Option (CN)</label>
                                <input data-bind="value: option_cn" type="text" class="k-textbox" id="option_cn" name="option_cn" placeholder="Option (CN)" required validationMessage="This field is required"/>
                            </li>
                            <li>
                                <label for="option_tr" class="required">Option (TR)</label>
                                <input data-bind="value: option_tr" type="text" class="k-textbox" id="option_tr" name="option_tr" placeholder="Option (TR)" required validationMessage="This field is required"/>
                            </li>
                        </ul>
                    </fieldset>
                    <div class="clearfix"></div>
                    <div class="spacer"></div>
                    <div class="actionBar">
                        <div class="actionBarLeft">
                            <button class="k-button" type="button" id="cancelOptionButton">Cancel</button>
                        </div>
                        <div class="actionBarRight">
                            <span class="status"></span>
                            <button class="k-button" type="button" id="saveOptionButton">Save</button>
                            <button class="k-button" type="button" id="doneOptionButton">Done</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- /page content -->


<!-- page scripts -->
<script src="{{ asset('js/apps/member/surveys/OptionForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection