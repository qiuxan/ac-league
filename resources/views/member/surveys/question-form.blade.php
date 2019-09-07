@extends('layouts.ajax')

@section('content')
    <!-- page content -->
    <div class="x_panel">
        <div id="surveyQuestionFormDiv" class="x_content">
            <div id="questionTabs">
                <ul>
                    <li class="k-state-active">Question Info</li>
                    <li data-bind="visible: hasOptions">Question Options</li>
                </ul>
                <div class="formWrap">
                    <form id="surveyQuestionForm" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <input data-bind="value: id" name="id" type="hidden"/>
                        <input data-bind="value: survey_id" name="survey_id" type="hidden"/>
                        <fieldset class="pull-left">
                            <ul>
                                <li>
                                    <label for="type">Type</label>
                                    <select data-bind="value: type" name="type" id="type" tabindex="1" required validationMessage="This field is required">
                                        <option value="{{ \App\SurveyQuestion::TYPE_OPEN_QUESTION }}">Open Question</option>
                                        <option value="{{ \App\SurveyQuestion::TYPE_MULTIPLE_CHOICES }}">Multiple Choices Question</option>
                                        <option value="{{ \App\SurveyQuestion::TYPE_SCALING_QUESTION }}">Scaling Question</option>
                                    </select>
                                    <span class="k-invalid-msg" data-for="type"></span>
                                </li>
                                <li>
                                    <label for="question_en" class="required">Question (EN)</label>
                                    <input data-bind="value: question_en" type="text" tabindex="2" class="k-textbox" id="question_en" name="question_en" placeholder="Question (EN)" required validationMessage="This field is required"/>
                                </li>
                                <li>
                                    <label for="question_cn" class="required">Question (CN)</label>
                                    <input data-bind="value: question_cn" type="text" tabindex="3" class="k-textbox" id="question_cn" name="question_cn" placeholder="Question (CN)" required validationMessage="This field is required"/>
                                </li>
                                <li>
                                    <label for="question_tr" class="required">Question (TR)</label>
                                    <input data-bind="value: question_tr" type="text" tabindex="4" class="k-textbox" id="question_tr" name="question_tr" placeholder="Question (TR)" required validationMessage="This field is required"/>
                                </li>
                                <li>
                                    <label for="required">Required</label>
                                    <input type="hidden" name="required" value="0"/>
                                    <input data-bind="checked: required" type="checkbox" id="required" name="required" value="1"/>
                                </li>
                                <li>
                                    <label for="published">Published</label>
                                    <input type="hidden" name="published" value="0"/>
                                    <input data-bind="checked: published" type="checkbox" id="published" name="published" value="1"/>
                                </li>
                            </ul>
                        </fieldset>
                        <div class="clearfix"></div>
                        <div class="spacer"></div>
                        <div class="actionBar">
                            <div class="actionBarLeft">
                                <button class="k-button" type="button" id="cancelQuestionButton">Cancel</button>
                            </div>
                            <div class="actionBarRight">
                                <span class="status"></span>
                                <button class="k-button" type="button" id="saveQuestionButton">Save</button>
                                <button class="k-button" type="button" id="doneQuestionButton">Done</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="formWrap" data-bind="visible: hasOptions">
                    <div class="x_title">
                        <h2>Question Options</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div id="questionOptionGrid"></div>
                    <div id="questionOptionFormContainer"></div>
                    <script type="text/x-kendo-template" id="optionToolbarTemplate">
                        <div class="toolbar">
                            <div class="toolbarButtons">
                                <a class="k-button" id="addOptionButton">
                                    <span class="k-icon k-i-add">&nbsp;</span>
                                    <span>Add</span>
                                </a>
                                <a class="k-button k-state-disabled" id="editOptionButton">
                                    <span class="k-icon k-edit">&nbsp;</span>
                                    <span>Open</span>
                                </a>
                                <a class="k-button k-state-disabled" id="deleteOptionButton">
                                    <span class="k-icon k-close">&nbsp;</span>
                                    <span>Delete</span>
                                </a>
                            </div>
                        </div>
                    </script>
                </div>
            </div>
        </div>
    </div>

    <!-- /page content -->


<!-- page scripts -->
<script src="{{ asset('js/apps/member/surveys/QuestionForm.js') }}?v=1.0.0" type="text/javascript"></script>
<!-- /page scripts -->

@endsection