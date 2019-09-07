@extends('layouts.member')

@section('content')
    <script type="text/javascript">
        _survey_id = {{$survey_id}};
    </script>
    <!-- page content -->
    <div class="x_panel">
        <div class="x_title">
            <h2>Survey Detail</h2>
            <div class="clearfix"></div>
        </div>
        <div id="surveyFormDiv" class="x_content">
            <div id="tabs">
                <ul>
                    <li class="k-state-active">Survey Info</li>
                    <li data-bind="invisible: isNew">Questions</li>
                    <li data-bind="invisible: isNew">Survey Report</li>
                    <li data-bind="invisible: isNew">Survey Analysis</li>
                </ul>
                <div class="formWrap">
                    <form id="surveyForm" enctype="multipart/form-data">
                        {!! csrf_field() !!}

                        <input data-bind="value: id" name="id" type="hidden"/>
                        <fieldset class="pull-left">
                            <ul>
                                <li>
                                    <label for="title_en" class="required">Title (EN)</label>
                                    <input data-bind="value: title_en" type="text" id="title_en" name="title_en" class="k-textbox" placeholder="Title (EN)" required validationMessage="This field is required"/>
                                </li>
                                <li>
                                    <label for="title_cn" class="required">Title (CN)</label>
                                    <input data-bind="value: title_cn" type="text" id="title_cn" name="title_cn" class="k-textbox" placeholder="Title (CN)" required validationMessage="This field is required"/>
                                </li>
                                <li>
                                    <label for="title_tr" class="required">Title (TR)</label>
                                    <input data-bind="value: title_tr" type="text" id="title_tr" name="title_tr" class="k-textbox" placeholder="Title (TR)" required validationMessage="This field is required"/>
                                </li>
                                <li>
                                    <label for="description_en">Description (EN)</label>
                                    <div class="ckWrap">
                                        <textarea data-bind="value: description_en" id="description_en" name="description_en" placeholder="Description (EN)"></textarea>
                                    </div>
                                </li>
                                <li>
                                    <label for="description_cn">Description (CN)</label>
                                    <div class="ckWrap">
                                        <textarea data-bind="value: description_cn" id="description_cn" name="description_cn" placeholder="Description (CN)"></textarea>
                                    </div>
                                </li>
                                <li>
                                    <label for="description_tr">Description (TR)</label>
                                    <div class="ckWrap">
                                        <textarea data-bind="value: description_tr" id="description_tr" name="description_tr" placeholder="Description (TR)"></textarea>
                                    </div>
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

                <div class="formWrap" data-bind="invisible: isNew">
                    <div class="x_title">
                        <h2>Question List</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div id="surveyQuestionGrid"></div>
                    <div id="surveyQuestionFormContainer"></div>
                    <script type="text/x-kendo-template" id="toolbarTemplate">
                        <div class="toolbar">
                            <div class="toolbarButtons">
                                <a class="k-button" id="addButton">
                                    <span class="k-icon k-i-add">&nbsp;</span>
                                    <span>Add</span>
                                </a>
                                <a class="k-button k-state-disabled" id="editButton">
                                    <span class="k-icon k-edit">&nbsp;</span>
                                    <span>Open</span>
                                </a>
                                <a class="k-button k-state-disabled" id="deleteButton">
                                    <span class="k-icon k-close">&nbsp;</span>
                                    <span>Delete</span>
                                </a>
                            </div>
                        </div>
                    </script>
                </div>

                <div class="formWrap">
                    <script type="text/x-kendo-template" id="responsesToolbarTemplate">
                        <div class="pull-left">
                            <input type="date" id="from_date_filter" placeholder="From Date" style="width: 120px"/>
                            <input type="date" id="to_date_filter" placeholder="To Date" style="width: 120px"/>
                            <select id="batch_id_filter">
                                <option value="">All Batches</option>
                                @foreach($batches as $batch)
                                    <option value="{{$batch->id}}">{{$batch->batch_code}}</option>
                                @endforeach
                            </select>
                            <select id="product_id_filter">
                                <option value="">All Products</option>
                                @foreach($products as $product)
                                    <option value="{{$product->id}}">{{$product->name}}</option>
                                @endforeach
                            </select>
                            <select id="location_id_filter">
                                <option value="">All Locations</option>
                                @foreach($locations as $location)
                                    <option value="{{$location->id}}">{{$location->location}}</option>
                                @endforeach
                            </select>
                            <select id="production_partner_id_filter" style="width: 120px">
                                <option value="">All Partners</option>
                                @foreach($production_partners as $production_partner)
                                    <option value="{{$production_partner->id}}">{{$production_partner->name}}</option>
                                @endforeach
                            </select>
                            <select id="language_filter" style="width: 120px">
                                <option value="">All Languages</option>
                                <option value="en">English</option>
                                <option value="cn">Simplified Chinese</option>
                                <option value="tr">Traditional Chinese</option>
                            </select>
                            <a class="k-button k-button-icontext" id="exportButton">
                                <span class="k-icon k-i-excel"></span>
                                <span>Export to Excel</span>
                            </a>
                        </div>
                        <div class="pull-right">
                            <span class="k-textbox k-space-left">
                                <a class="k-icon k-i-search"></a>
                                <input type="text" id="search_filter" placeholder="search"/>
                            </span>
                        </div>
                    </script>
                    <div>
                        <div id="responseGrid"></div>
                        <div id="responseContainer"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="formWrap">
                    <div id="analysisContainter">
                        @php
                        $i = 1;
                        @endphp
                        @foreach($survey_questions as $survey_question)
                            <div id="questionSummary">
                                <div>
                                    <h3>Question {{$i++}}</h3>
                                </div>
                                <div>
                                    <p>{{$survey_question->question_en}}</p>
                                </div>
                                @if($survey_question->type == \App\SurveyQuestion::TYPE_OPEN_QUESTION)
                                    <div id="openQuestionGrid_{{$survey_question->id}}" class="questionGrid"></div>
                                    <div class="clearfix"></div>
                                @elseif($survey_question->type == \App\SurveyQuestion::TYPE_SCALING_QUESTION)
                                    <div class="row" style="padding-left:0.92em">
                                        <div class="col-md-6">
                                            <div id="multipleChoicesQuestionGrid_{{$survey_question->id}}" class="questionGrid"></div>                                       
                                        </div>
                                        <div id="pieChartContainer_{{$survey_question->id}}" class="col-md-6">
                                            <canvas id="pieChart_{{$survey_question->id}}"></canvas>                                        
                                        </div>
                                    </div>                                
                                    
                                    <div class="spacer"></div>
                                    <p>NPS Analysis</p>
                                    <div id="npsGrid_{{$survey_question->id}}" class="questionGrid"></div>
                                    <div class="spacer"></div>
                                    <div class="clearfix"></div>
                                @elseif($survey_question->type == \App\SurveyQuestion::TYPE_MULTIPLE_CHOICES)
                                    <div class="row" style="padding-left:0.92em" >
                                        <div class="col-md-6">
                                            <div id="multipleChoicesQuestionGrid_{{$survey_question->id}}" class="questionGrid"></div>   
                                            <div class="spacer"></div>
                                            <div>
                                                <label>Add Filter: </label>
                                                <select class="option_filter_{{$survey_question->id}}">
                                                    <option value="">All Options</option>
                                                    @foreach($survey_question->options as $option)
                                                        <option value="{{$survey_question->id . "_" . $option->id}}">{{$option->option_en}}</option>
                                                    @endforeach
                                                </select>
                                            </div>                                                                                
                                        </div>
                                        <div id="barChartContainer_{{$survey_question->id}}" class="col-md-6">
                                            <canvas id="barChart_{{$survey_question->id}}"></canvas>                                        
                                        </div>
                                    </div>
                                    <div class="spacer"></div>
                                    <div class="clearfix"></div>
                                @endif
                            </div>
                            <div class="clearfix"></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- /page content -->

@endsection

@section('page-scripts')

<!-- page scripts -->
    <script src="/js/Chart.js"></script>
    <script src="/js/ckeditor/ckeditor.js"></script>
    <script src="/js/ckeditor/adapters/jquery.js"></script>
    <script src="{{ asset('js/apps/member/surveys/SurveyForm.js') }}?v=1.0.0" type="text/javascript"></script>
    <script src="{{ asset('js/apps/member/surveys/ResponseList.js') }}?v=1.0.0" type="text/javascript"></script>
    <script src="{{ asset('js/apps/member/surveys/QuestionsAnalysis.js') }}?v=1.0.0" type="text/javascript"></script>
    <script src="{{ asset('js/jszip.min.js') }}" type="text/javascript"></script>
<!-- /page scripts -->

@endsection