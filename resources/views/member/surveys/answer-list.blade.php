@extends('layouts.ajax')

@section('content')
        <!-- page content -->
<div class="x_panel">
    <div id="questionOptionFormDiv" class="x_content">
        <div class="formWrap">
            <div class="answerList">
                @if($survey && $survey_questions && count($survey_questions) > 0 )
                    @if($survey->title_en)
                        <header>
                            <span class="red product-name">{{$survey->title_en}}</span>
                        </header><!-- .page-header -->
                    @endif
                    @if($survey->description_en)
                        <div class="product-feature-full">
                            <div class="value-full">{!! $survey->description_en !!}</div>
                        </div>
                    @endif
                    @php
                    $i = 1
                    @endphp
                    @foreach($survey_questions as $survey_question)
                        @php
                        $required  = '';
                        if($survey_question->required == 1)
                        {
                        $required  = 'required';
                        }
                        @endphp
                        @if($survey_question->type == \App\SurveyQuestion::TYPE_OPEN_QUESTION)
                            <div class="product-feature">
                                <div>
                                    <strong>{{$i++}}</strong>. {{$survey_question->question_en}}
                                    @if($required == 'required')
                                        (*)
                                    @endif
                                </div>
                                <br/>
                                <div><input type="text" class="k-textbox" {{$required}} name="question_{{$survey_question->id}}" width="100%" value="{{$survey_question->value}}" /></div>
                            </div>
                        @elseif($survey_question->type == \App\SurveyQuestion::TYPE_SCALING_QUESTION)
                            <div class="product-feature">
                                <div>
                                    <strong>{{$i++}}</strong>. {{$survey_question->question_en}}
                                    @if($required == 'required')
                                        (*)
                                    @endif
                                </div>
                                <br/>
                                <div>
                                    @for($j = 0; $j <= 10; $j++)
                                        @if($survey_question->value == $j)
                                            <div><input type="radio" {{$required}} name="question_{{$j}}" value="{{$j}}" checked="true" /> {{$j}}</div>
                                        @else
                                            <div><input type="radio" {{$required}} name="question_{{$j}}" value="{{$j}}" /> {{$j}}</div>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        @else
                            @if(count($survey_question->question_options) > 0)
                                <div class="product-feature">
                                    <div>
                                        <strong>{{$i++}}</strong>. {{$survey_question->question_en}}
                                        @if($required == 'required')
                                            (*)
                                        @endif
                                    </div>
                                    <br/>
                                    <div>
                                        @foreach($survey_question->question_options as $question_option)
                                            @if($question_option->id == $survey_question->value)
                                                <div><input type="radio" {{$required}} name="question_{{$survey_question->id}}" value="{{$question_option->id}}" checked="true" /> {{$question_option->option_en}}</div>
                                            @else
                                                <div><input type="radio" {{$required}} name="question_{{$survey_question->id}}" value="{{$question_option->id}}" /> {{$question_option->option_en}}</div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection