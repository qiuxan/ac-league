<?php

namespace App\Http\Controllers\Member;
use App\Member;
use App\QuestionOption;
use App\SurveyQuestion;
use App\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Survey;
use Exception;
use App\Utils;

class SurveyController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getSurveyForm() {
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $survey = Survey::where(['member_id' => $member->id, 'deleted' => 0])->first();

        $whereRaw = " batches.member_id = {$member->id} AND batches.deleted = 0 ";

        $batches = DB::table('histories')->select(DB::raw('DISTINCT batches.id, batches.batch_code'))
            ->join('codes','histories.code_id','=','codes.id')
            ->join('batches','codes.batch_id','=','batches.id')
            ->whereRaw($whereRaw)
            ->orderBy('batches.batch_code', 'asc')
            ->get();

        $products = DB::table('histories')->select(DB::raw('DISTINCT products.id, products.name_en AS name'))
            ->join('codes','histories.code_id','=','codes.id')
            ->join('batches','codes.batch_id','=','batches.id')
            ->join('products','batches.product_id','=','products.id')
            ->whereRaw($whereRaw)
            ->orderBy('products.name_en', 'asc')
            ->get();

        $locations = DB::table('histories')->select(DB::raw('DISTINCT locations.id, locations.location'))
            ->join('codes','histories.code_id','=','codes.id')
            ->join('batches','codes.batch_id','=','batches.id')
            ->join('locations','histories.location_id','=','locations.id')
            ->whereRaw($whereRaw)
            ->orderBy('locations.location', 'asc')
            ->get();

        $production_partners = DB::table('histories')->select(DB::raw('DISTINCT production_partners.id, production_partners.name_en AS name'))
            ->join('codes','histories.code_id','=','codes.id')
            ->join('batches','codes.batch_id','=','batches.id')
            ->join('production_partners','codes.production_partner_id','=','production_partners.id')
            ->whereRaw($whereRaw)
            ->orderBy('production_partners.name_en', 'asc')
            ->get();

        if($survey && $survey->id)
        {
            $survey_id = $survey->id;
        }
        else
        {
            $survey_id = 0;
        }

        DB::statement("SET @order_number:=0");

        $survey_questions = DB::table('survey_questions')
            ->select(DB::raw('@order_number:=@order_number+1 AS order_number'), 'survey_questions.*')
            ->join('surveys','survey_questions.survey_id','=','surveys.id')
            ->where(['survey_questions.survey_id' => $survey_id, 'surveys.member_id' => $member->id, 'survey_questions.deleted' => 0])
            ->orderBy('survey_questions.priority', 'asc')->get();

        foreach($survey_questions as $survey_question)
        {
            if($survey_question->type == SurveyQuestion::TYPE_MULTIPLE_CHOICES)
            {
                $question_options = DB::table('survey_question_options')
                    ->select(DB::raw('@order_number:=@order_number+1 AS order_number'), 'survey_question_options.*')
                    ->join('survey_questions','survey_questions.id','=','survey_question_options.question_id')
                    ->join('surveys','survey_questions.survey_id','=','surveys.id')
                    ->where(['survey_question_options.question_id' => $survey_question->id,
                        'surveys.member_id' => $member->id, 'survey_questions.deleted' => 0])
                    ->orderBy('survey_question_options.priority', 'asc')->get();
                $survey_question->options = $question_options;
            }
        }

        return view( 'member.surveys.form', compact(['survey_id', 'batches', 'products', 'locations', 'production_partners', 'survey_questions']) );
    }

    public function getSurveyQuestionForm() {
        return view( 'member.surveys.question-form' );
    }

    public function getQuestionOptionForm() {
        return view( 'member.surveys.question-option-form' );
    }

    public function getSurvey(Request $request) {
        $id = $request->input('id');
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $survey = DB::table('surveys')->select('surveys.*')->where(['surveys.id' => $id, 'surveys.member_id' => $member->id, 'deleted' => 0])->first();

        return response()->json($survey);
    }

    public function getSurveyQuestion(Request $request) {
        $question_id = $request->input('id');
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $survey_question = DB::table('survey_questions')->select('survey_questions.*')
            ->join('surveys','survey_questions.survey_id','=','surveys.id')
            ->where(['survey_questions.id' => $question_id, 'surveys.member_id' => $member->id, 'survey_questions.deleted' => 0])
            ->first();

        return response()->json($survey_question);
    }

    public function getQuestionOption(Request $request) {
        $option_id = $request->input('id');
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();

        DB::statement("SET @order_number:=0");

        $survey_questions = DB::table('survey_question_options')
            ->select(DB::raw('@order_number:=@order_number+1 AS order_number'), 'survey_question_options.*')
            ->join('survey_questions','survey_questions.id','=','survey_question_options.question_id')
            ->join('surveys','survey_questions.survey_id','=','surveys.id')
            ->where(['survey_question_options.id' => $option_id,
                'surveys.member_id' => $member->id, 'survey_question_options.deleted' => 0])
            ->first();

        return response()->json($survey_questions);
    }

    public function getSurveyQuestions(Request $request) {
        $survey_id = $request->input('survey_id');
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();

        DB::statement("SET @order_number:=0");

        $survey_questions = DB::table('survey_questions')
            ->select(DB::raw('@order_number:=@order_number+1 AS order_number'), 'survey_questions.*')
            ->join('surveys','survey_questions.survey_id','=','surveys.id')
            ->where(['survey_questions.survey_id' => $survey_id, 'surveys.member_id' => $member->id, 'survey_questions.deleted' => 0])
            ->orderBy('survey_questions.priority', 'asc')->get();

        return response()->json($survey_questions);
    }

    public function getQuestionOptions(Request $request) {
        $question_id = $request->input('question_id');
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();

        DB::statement("SET @order_number:=0");

        $question_options = DB::table('survey_question_options')
            ->select(DB::raw('@order_number:=@order_number+1 AS order_number'), 'survey_question_options.*')
            ->join('survey_questions','survey_questions.id','=','survey_question_options.question_id')
            ->join('surveys','survey_questions.survey_id','=','surveys.id')
            ->where(['survey_question_options.question_id' => $question_id,
                'surveys.member_id' => $member->id, 'survey_question_options.deleted' => 0])
            ->orderBy('survey_question_options.priority', 'asc')->get();

        return response()->json($question_options);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $survey_id = $request->input('id');
            $survey = Survey::find($survey_id);
            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            if(!$survey)
            {
                $survey = new Survey();
                $survey->created_by = auth()->user()->id;
            }
            else
            {
                if($survey->member_id != $member->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'survey_id' => 0
                    ]);
                }
                $survey->updated_by = auth()->user()->id;
            }
            $survey->member_id = $member->id;
            $survey->title_en = $request->input('title_en');
            $survey->title_cn = $request->input('title_cn');
            $survey->title_tr = $request->input('title_tr');
            $survey->description_en = $request->input('description_en');
            $survey->description_cn = $request->input('description_cn');
            $survey->description_tr = $request->input('description_tr');

            $survey->save();

            DB::commit();
            return json_encode([
                'survey_id' => $survey->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'survey_id' => 0
            ]);
        }
    }

    public function storeQuestion( Request $request )
    {
        try {
            DB::beginTransaction();
            $question_id = $request->input('id');
            $survey_id = $request->input('survey_id');
            $survey = Survey::find($survey_id);
            $question = SurveyQuestion::find($question_id);
            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            if($survey->member_id != $member->id)
            {
                DB::rollBack();
                Utils::trace("Invalid access!");
                return json_encode([
                    'question_id' => 0
                ]);
            }
            if(!$question)
            {
                $question = new SurveyQuestion();
                $question->created_by = auth()->user()->id;

                $max_priority = DB::table('survey_questions')
                    ->where(['survey_questions.survey_id' => $survey_id, 'survey_questions.deleted' => 0])
                    ->max('priority');
                $question->priority = $max_priority + 1;
            }
            $question->survey_id = $survey_id;
            $question->type = $request->input('type');
            $question->question_en = $request->input('question_en');
            $question->question_cn = $request->input('question_cn');
            $question->question_tr = $request->input('question_tr');
            $question->required = $request->input('required');
            $question->published = $request->input('published');

            $question->save();

            DB::commit();
            return json_encode([
                'question_id' => $question->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'question_id' => 0
            ]);
        }
    }

    public function storeQuestionOption( Request $request )
    {
        try {
            DB::beginTransaction();
            $option_id = $request->input('id');
            $question_id = $request->input('question_id');
            $option = QuestionOption::find($option_id);
            $question = SurveyQuestion::find($question_id);
            $survey = Survey::find($question->survey_id);
            $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            if($survey->member_id != $member->id)
            {
                DB::rollBack();
                Utils::trace("Invalid access!");
                return json_encode([
                    'option_id' => 0
                ]);
            }
            if(!$option)
            {
                $option = new QuestionOption();
                $option->created_by = auth()->user()->id;

                $max_priority = DB::table('survey_question_options')
                    ->where(['survey_question_options.question_id' => $question_id, 'survey_question_options.deleted' => 0])
                    ->max('priority');
                $option->priority = $max_priority + 1;
            }
            $option->question_id = $question_id;
            $option->option_en = $request->input('option_en');
            $option->option_cn = $request->input('option_cn');
            $option->option_tr = $request->input('option_tr');

            $option->save();

            DB::commit();
            return json_encode([
                'option_id' => $option->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'option_id' => 0
            ]);
        }
    }

    public function updateQuestionPriorities ( Request $request) {
        try {
            DB::beginTransaction();
            $question_priorities = $request->input('question_priorities');

            foreach ($question_priorities as $question_priority) {
                $question_id = $question_priority['id'];
                $question_priority = $question_priority['priority'];

                $question = SurveyQuestion::find($question_id);
                $question->priority = $question_priority;
                $question->save();
            }

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        } catch(Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }

    public function updateOptionPriorities ( Request $request) {
        try {
            DB::beginTransaction();
            $option_priorities = $request->input('option_priorities');

            foreach ($option_priorities as $option_priority) {
                $option_id = $option_priority['id'];
                $option_priority = $option_priority['priority'];

                $option = QuestionOption::find($option_id);
                $option->priority = $option_priority;
                $option->save();
            }

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        } catch(Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }

    public function getResponseAnswers($response_id)
    {
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $survey = Survey::where(['member_id' => $member->id, 'deleted' => 0])->first();

        $response = DB::table('survey_responses')
            ->select('survey_responses.*')
            ->join('surveys','survey_responses.survey_id','=','surveys.id')
            ->where(['survey_responses.id' => $response_id, 'surveys.member_id' => $member->id, 'surveys.deleted' => 0])
            ->first();
        if($response && $response->id > 0)
        {
            $survey_questions = DB::table('survey_questions')
                ->select('survey_questions.*', 'survey_answers.value')
                ->leftJoin('survey_answers','survey_answers.question_id','=','survey_questions.id')
                ->where(['survey_questions.survey_id' => $survey->id, 'survey_answers.response_id' => $response_id,
                    'survey_questions.deleted' => 0])
                ->orderBy('survey_questions.priority', 'asc')->get();

            foreach($survey_questions as $survey_question)
            {
                if($survey_question->type == SurveyQuestion::TYPE_MULTIPLE_CHOICES)
                {
                    $question_options = QuestionOption::where(['question_id' => $survey_question->id,
                        'deleted' => 0])->orderBy('priority', 'asc')->get();
                    $survey_question->question_options = $question_options;
                }
            }

            return view('member.surveys.answer-list', compact(['survey', 'survey_questions']));
        }
        return null;
    }

    public function getResponses(Request $request) {
        $pageSize = $request->input('pageSize');
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();

        $sorts = $request->input('sort');
        $sort_field = '';
        $sort_dir = '';
        if(isset($sorts[0]))
        {
            $sort_field = $sorts[0]['field'];
            if($sort_field == 'id')
            {
                $sort_field = 'histories.id';
            }
            $sort_dir = $sorts[0]['dir'];
        }
        if(!$sort_field)
        {
            $sort_field = 'histories.id';
        }
        if(!$sort_dir)
        {
            $sort_dir = 'desc';
        }

        $whereRaw = " batches.member_id = {$member->id} AND batches.deleted = 0 ";

        $filters = $request->input('filters');

        $binding_array = array();

        if($filters['search'])
        {
            $binding_array['search_string1'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string2'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string3'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string4'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string5'] = strtolower("%{$filters['search']}%");
            $whereRaw .= " AND (LOWER(codes.full_code) LIKE ? ";
            $whereRaw .= "      OR LOWER(batches.batch_code) LIKE ? ";
            $whereRaw .= "      OR LOWER(products.name_en) LIKE ? ";
            $whereRaw .= "      OR LOWER(locations.location) LIKE ? ";
            $whereRaw .= "      OR LOWER(production_partners.name_en) LIKE ? ) ";
        }
        if($filters['from_date'])
        {
            $binding_array['from_date'] = Utils::toMySQLDate($filters['from_date']);
            $whereRaw .= " AND DATE(survey_responses.created_at) >= ? ";
        }
        if($filters['to_date'])
        {
            $binding_array['to_date'] = Utils::toMySQLDate($filters['to_date']);
            $whereRaw .= " AND DATE(survey_responses.created_at) <= ? ";
        }
        if($filters['batch_id'])
        {
            $binding_array['batch_id'] = $filters['batch_id'];
            $whereRaw .= " AND codes.batch_id = ? ";
        }
        if($filters['product_id'])
        {
            $binding_array['product_id'] = $filters['product_id'];
            $whereRaw .= " AND batches.product_id = ? ";
        }
        if($filters['production_partner_id'])
        {
            $binding_array['production_partner_id'] = $filters['production_partner_id'];
            $whereRaw .= " AND codes.production_partner_id = ? ";
        }
        if($filters['location_id'])
        {
            $binding_array['location_id'] = $filters['location_id'];
            $whereRaw .= " AND histories.location_id = ? ";
        }
        if($filters['language'])
        {
            $binding_array['language'] = "{$filters['language']}";
            $whereRaw .= " AND histories.language = ? ";
        }

        $first = true;
        $response_ids = array();
        if($filters['option_filters'])
        {
            $option_list = explode(',', $filters['option_filters']);
            if(is_array($option_list))
            {
                foreach($option_list as $option_info)
                {
                    if(!$option_info)
                    {
                        continue;
                    }
                    $option_array = explode('_', $option_info);
                    if($first == true)
                    {
                        $survey_responses = DB::table('survey_responses')
                            ->select('survey_responses.id')
                            ->join('survey_answers','survey_responses.id','=','survey_answers.response_id')
                            ->where(['survey_answers.question_id' => $option_array[0],
                                'survey_answers.value' => $option_array[1],
                                'survey_answers.deleted' => 0])
                            ->pluck('survey_responses.id')->toArray();
                        $response_ids = $survey_responses;
                        $first = false;
                    }
                    else
                    {
                        if(!(count($response_ids) > 0))
                        {
                            break;
                        }
                        else
                        {
                            $survey_responses = DB::table('survey_responses')
                                ->select('survey_responses.id')
                                ->join('survey_answers','survey_responses.id','=','survey_answers.response_id')
                                ->where(['survey_answers.question_id' => $option_array[0],
                                    'survey_answers.value' => $option_array[1],
                                    'survey_answers.deleted' => 0])
                                ->whereIn('survey_responses.id', $response_ids)
                                ->pluck('survey_responses.id')->toArray();
                            $response_ids = $survey_responses;
                        }
                    }
                }
            }
        }

        if($first == true)
        {
            $codes = DB::table('survey_responses')->select('survey_responses.id', 'codes.full_code',
                'batches.batch_code', 'products.name_en AS name',
                'survey_responses.created_at', 'histories.lat', 'histories.lng', 'histories.status',
                'production_partners.name_en AS production_partner', 'locations.location',
                DB::raw("CASE histories.language
                        WHEN 'cn' THEN 'Simplified Chinese'
                        WHEN 'tr' THEN 'Traditional Chinese'
                        ELSE 'English'
                     END AS language"))
                ->join('histories','histories.id','=','survey_responses.history_id')
                ->join('codes','histories.code_id','=','codes.id')
                ->join('batches','codes.batch_id','=','batches.id')
                ->join('products','batches.product_id','=','products.id')
                ->leftJoin('production_partners','codes.production_partner_id','=','production_partners.id')
                ->leftJoin('locations','histories.location_id','=','locations.id')
                ->whereRaw($whereRaw, $binding_array)
                ->orderBy($sort_field, $sort_dir)->paginate($pageSize);
        }
        else
        {
            if(count($response_ids) > 0)
            {
                //$response_list = implode(',', $response_ids);
                $bindingsString = trim( str_repeat('?,', count($response_ids)), ',');
                $binding_array = array_merge($binding_array, $response_ids);
                $whereRaw .= " AND survey_responses.id IN ({$bindingsString}) ";
            }
            else
            {
                $whereRaw .= " AND survey_responses.id IN (0) ";
            }


            //$whereRaw .= " AND survey_responses.id IN (:response_list) ";
            //$binding_array['response_list'] = $response_ids;

            $codes = DB::table('survey_responses')->select('survey_responses.id', 'codes.full_code', 'batches.batch_code', 'products.name_en AS name',
                'survey_responses.created_at', 'histories.lat', 'histories.lng', 'histories.status',
                'production_partners.name_en AS production_partner', 'locations.location',
                DB::raw("CASE histories.language
                        WHEN 'cn' THEN 'Simplified Chinese'
                        WHEN 'tr' THEN 'Traditional Chinese'
                        ELSE 'English'
                     END AS language"))
                ->leftJoin('histories','histories.id','=','survey_responses.history_id')
                ->leftJoin('codes','histories.code_id','=','codes.id')
                ->leftJoin('batches','codes.batch_id','=','batches.id')
                ->leftJoin('products','batches.product_id','=','products.id')
                ->leftJoin('production_partners','codes.production_partner_id','=','production_partners.id')
                ->leftJoin('locations','histories.location_id','=','locations.id')
                ->whereRaw($whereRaw, $binding_array)
                ->orderBy($sort_field, $sort_dir)->paginate($pageSize);
            //Utils::trace($response_list);
            //Utils::trace($codes);
        }


        return response()->json($codes);
    }

    public function getQuestionAnalysis(Request $request)
    {
        $question_id = $request->input('question_id');
        $filters = $request->input('filters');
        $question = SurveyQuestion::find($question_id);

        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();


        $whereRaw = " batches.member_id = {$member->id} AND batches.deleted = 0 ";

        $filters = $request->input('filters');

        $binding_array = array();

        if($filters['search'])
        {
            $binding_array['search_string1'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string2'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string3'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string4'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string5'] = strtolower("%{$filters['search']}%");
            $whereRaw .= " AND (LOWER(codes.full_code) LIKE ? ";
            $whereRaw .= "      OR LOWER(batches.batch_code) LIKE ? ";
            $whereRaw .= "      OR LOWER(products.name_en) LIKE ? ";
            $whereRaw .= "      OR LOWER(locations.location) LIKE ? ";
            $whereRaw .= "      OR LOWER(production_partners.name_en) LIKE ? ) ";
        }
        if($filters['from_date'])
        {
            $binding_array['from_date'] = Utils::toMySQLDate($filters['from_date']);
            $whereRaw .= " AND DATE(survey_responses.created_at) >= ? ";
        }
        if($filters['to_date'])
        {
            $binding_array['to_date'] = Utils::toMySQLDate($filters['to_date']);
            $whereRaw .= " AND DATE(survey_responses.created_at) <= ? ";
        }
        if($filters['batch_id'])
        {
            $binding_array['batch_id'] = $filters['batch_id'];
            $whereRaw .= " AND codes.batch_id = ? ";
        }
        if($filters['product_id'])
        {
            $binding_array['product_id'] = $filters['product_id'];
            $whereRaw .= " AND batches.product_id = ? ";
        }
        if($filters['production_partner_id'])
        {
            $binding_array['production_partner_id'] = $filters['production_partner_id'];
            $whereRaw .= " AND codes.production_partner_id = ? ";
        }
        if($filters['location_id'])
        {
            $binding_array['location_id'] = $filters['location_id'];
            $whereRaw .= " AND histories.location_id = ? ";
        }
        if($filters['language'])
        {
            $binding_array['language'] = "{$filters['language']}";
            $whereRaw .= " AND histories.language = ? ";
        }

        $first = true;
        $response_ids = array();
        if($filters['option_filters'])
        {
            $option_list = explode(',', $filters['option_filters']);
            if(is_array($option_list))
            {
                foreach($option_list as $option_info)
                {
                    if(!$option_info)
                    {
                        continue;
                    }
                    $option_array = explode('_', $option_info);
                    if($first == true)
                    {
                        $survey_responses = DB::table('survey_responses')
                            ->select('survey_responses.id')
                            ->join('survey_answers','survey_responses.id','=','survey_answers.response_id')
                            ->where(['survey_answers.question_id' => $option_array[0],
                                'survey_answers.value' => $option_array[1],
                                'survey_answers.deleted' => 0])
                            ->pluck('survey_responses.id')->toArray();
                        $response_ids = $survey_responses;
                        $first = false;
                    }
                    else
                    {
                        if(!(count($response_ids) > 0))
                        {
                            break;
                        }
                        else
                        {
                            $survey_responses = DB::table('survey_responses')
                                ->select('survey_responses.id')
                                ->join('survey_answers','survey_responses.id','=','survey_answers.response_id')
                                ->where(['survey_answers.question_id' => $option_array[0],
                                    'survey_answers.value' => $option_array[1],
                                    'survey_answers.deleted' => 0])
                                ->whereIn('survey_responses.id', $response_ids)
                                ->pluck('survey_responses.id')->toArray();
                            $response_ids = $survey_responses;
                        }
                    }
                }
            }
        }

        $survey_answers = array();

        if($question->type == SurveyQuestion::TYPE_OPEN_QUESTION)
        {
            $pageSize = $request->input('pageSize');

            DB::statement("SET @order_number:=0");

            $whereRaw .= " AND survey_answers.question_id = ? ";
            $binding_array['question_id'] = $question_id;

            $whereRaw .= " AND surveys.member_id = ? ";
            $binding_array['member_id'] = $member->id;

            $whereRaw .= " AND survey_questions.deleted = ? ";
            $binding_array['is_deleted'] = 0;

            if($first == true)
            {
                $survey_answers = DB::table('survey_answers')
                    ->select(DB::raw('@order_number:=@order_number+1 AS order_number'), 'survey_answers.value')
                    ->join('survey_questions','survey_questions.id','=','survey_answers.question_id')
                    ->join('surveys','survey_questions.survey_id','=','surveys.id')
                    ->join('survey_responses','survey_answers.response_id','=','survey_responses.id')
                    ->join('histories','histories.id','=','survey_responses.history_id')
                    ->join('codes','histories.code_id','=','codes.id')
                    ->join('batches','codes.batch_id','=','batches.id')
                    ->join('products','batches.product_id','=','products.id')
                    ->leftJoin('production_partners','codes.production_partner_id','=','production_partners.id')
                    ->leftJoin('locations','histories.location_id','=','locations.id')
                    ->whereRaw($whereRaw, $binding_array)
                    ->orderBy('survey_answers.created_at', 'desc')
                    ->paginate($pageSize);
            }
            else
            {
                if(count($response_ids) > 0)
                {
                    //$response_list = implode(',', $response_ids);
                    $bindingsString = trim( str_repeat('?,', count($response_ids)), ',');
                    $binding_array = array_merge($binding_array, $response_ids);
                    $whereRaw .= " AND survey_responses.id IN ({$bindingsString}) ";
                }
                else
                {
                    $whereRaw .= " AND survey_responses.id IN (0) ";
                    //$response_list = '0';
                }

                //$whereRaw .= " AND survey_answers.response_id IN (:response_list) ";
                //$binding_array['response_list'] = $response_list;

                $survey_answers = DB::table('survey_answers')
                    ->select(DB::raw('@order_number:=@order_number+1 AS order_number'), 'survey_answers.value')
                    ->join('survey_questions','survey_questions.id','=','survey_answers.question_id')
                    ->join('surveys','survey_questions.survey_id','=','surveys.id')
                    ->join('survey_responses','survey_answers.response_id','=','survey_responses.id')
                    ->join('histories','histories.id','=','survey_responses.history_id')
                    ->join('codes','histories.code_id','=','codes.id')
                    ->join('batches','codes.batch_id','=','batches.id')
                    ->join('products','batches.product_id','=','products.id')
                    ->leftJoin('production_partners','codes.production_partner_id','=','production_partners.id')
                    ->leftJoin('locations','histories.location_id','=','locations.id')
                    ->whereRaw($whereRaw, $binding_array)
                    ->orderBy('survey_answers.created_at', 'desc')
                    ->paginate($pageSize);
            }

        }
        else if($question->type == SurveyQuestion::TYPE_MULTIPLE_CHOICES)
        {
            $main_where = " survey_question_options.question_id = ? ";
            $binding_array['question_id'] = $question->id;

            $main_where .= " AND survey_question_options.deleted = ? ";
            $binding_array['is_deleted'] = 0;

            if($first == true)
            {
                $temp_table =
                    DB::raw("(SELECT survey_question_options.id,
                                    count(survey_responses.id) as answer_count
                                FROM survey_question_options
                                LEFT JOIN survey_questions ON survey_questions.id = survey_question_options.question_id
                                LEFT JOIN surveys ON survey_questions.survey_id = surveys.id
                                LEFT JOIN survey_answers ON survey_question_options.id = survey_answers.value
                                LEFT JOIN survey_responses ON survey_responses.id = survey_answers.response_id
                                LEFT JOIN histories ON histories.id = survey_responses.history_id
                                LEFT JOIN codes ON codes.id = histories.code_id
                                LEFT JOIN batches ON batches.id = codes.batch_id
                                LEFT JOIN products ON products.id = batches.product_id
                                LEFT JOIN production_partners ON production_partners.id = codes.production_partner_id
                                LEFT JOIN locations ON locations.id = histories.location_id
                                WHERE survey_question_options.question_id = {$question->id}
                                  AND surveys.member_id = {$member->id}
                                  AND survey_answers.question_id = {$question->id}
                                  AND survey_question_options.deleted = 0
                                  AND {$whereRaw}
                                GROUP BY survey_question_options.id) AS answer_analysis");

                $survey_answers = DB::table('survey_question_options')
                    ->select('survey_question_options.id','survey_question_options.option_en',
                        DB::raw('IF(answer_analysis.answer_count IS NULL, 0, answer_analysis.answer_count) AS answer_count'))
                    ->leftJoin($temp_table, 'survey_question_options.id', '=', 'answer_analysis.id')
                    ->whereRaw($main_where, $binding_array)
                    ->get();

                /*
                $survey_anwsers = DB::table('survey_question_options')
                    ->select('survey_question_options.option_en', DB::raw('count(survey_responses.id) as answer_count'))
                    ->leftJoin('survey_questions','survey_questions.id','=','survey_question_options.question_id')
                    ->leftJoin('surveys','survey_questions.survey_id','=','surveys.id')
                    ->leftJoin('survey_answers','survey_question_options.id','=','survey_answers.value')
                    ->leftJoin('survey_responses','survey_responses.id','=','survey_answers.response_id')
                    ->where(['survey_question_options.question_id' => $question->id, 'surveys.member_id' => $member->id,
                        'survey_answers.question_id' => $question->id,
                        'survey_question_options.deleted' => 0])
                    ->groupBy('survey_question_options.option_en', 'survey_question_options.priority')
                    ->orderBy('survey_question_options.priority', 'asc')->get();
                */
            }
            else
            {
                if(count($response_ids) > 0)
                {
                    $response_list = implode(',', $response_ids);
                }
                else
                {
                    $response_list = '0';
                }
                $temp_table =
                    DB::raw("(SELECT survey_question_options.id,
                                    count(survey_responses.id) as answer_count
                                FROM survey_question_options
                                LEFT JOIN survey_questions ON survey_questions.id = survey_question_options.question_id
                                LEFT JOIN surveys ON survey_questions.survey_id = surveys.id
                                LEFT JOIN survey_answers ON survey_question_options.id = survey_answers.value
                                LEFT JOIN survey_responses ON (survey_responses.id = survey_answers.response_id
                                                           AND survey_responses.id IN ({$response_list}))
                                LEFT JOIN histories ON histories.id = survey_responses.history_id
                                LEFT JOIN codes ON codes.id = histories.code_id
                                LEFT JOIN batches ON batches.id = codes.batch_id
                                LEFT JOIN products ON products.id = batches.product_id
                                LEFT JOIN production_partners ON production_partners.id = codes.production_partner_id
                                LEFT JOIN locations ON locations.id = histories.location_id
                                WHERE survey_question_options.question_id = {$question->id}
                                  AND surveys.member_id = {$member->id}
                                  AND survey_answers.question_id = {$question->id}
                                  AND survey_question_options.deleted = 0
                                  AND {$whereRaw}
                                GROUP BY survey_question_options.id) AS answer_analysis");

                $survey_answers = DB::table('survey_question_options')
                    ->select('survey_question_options.id','survey_question_options.option_en',
                        DB::raw('IF(answer_analysis.answer_count IS NULL, 0, answer_analysis.answer_count) AS answer_count'))
                    ->leftJoin($temp_table, 'survey_question_options.id', '=', 'answer_analysis.id')
                    ->whereRaw($main_where, $binding_array)
                    ->get();

                /*

                $survey_anwsers = DB::table('survey_question_options')
                    ->select('survey_question_options.option_en', DB::raw('count(survey_responses.id) as answer_count'))
                    ->leftJoin('survey_questions','survey_questions.id','=','survey_question_options.question_id')
                    ->leftJoin('surveys','survey_questions.survey_id','=','surveys.id')
                    ->leftJoin('survey_answers','survey_question_options.id','=','survey_answers.value')
                    ->leftJoin('survey_responses', function ($join) use ($response_ids) {
                        $join->on('survey_responses.id', '=', 'survey_answers.response_id')
                            ->whereIn('survey_responses.id', $response_ids);
                    })
                    ->where(['survey_question_options.question_id' => $question->id, 'surveys.member_id' => $member->id,
                        'survey_answers.question_id' => $question->id,
                        'survey_question_options.deleted' => 0])
                    ->groupBy('survey_question_options.option_en', 'survey_question_options.priority')
                    ->orderBy('survey_question_options.priority', 'asc')->get();
                */
            }
        }
        else if($question->type == SurveyQuestion::TYPE_SCALING_QUESTION)
        {
            $main_where = " 1 ";

            if($first == true)
            {
                $temp_table =
                    DB::raw("(SELECT survey_answers.value,
                                    count(survey_responses.id) as answer_count
                                FROM survey_answers
                                LEFT JOIN survey_questions ON survey_questions.id = survey_answers.question_id
                                LEFT JOIN surveys ON survey_questions.survey_id = surveys.id
                                LEFT JOIN survey_responses ON survey_responses.id = survey_answers.response_id
                                LEFT JOIN histories ON histories.id = survey_responses.history_id
                                LEFT JOIN codes ON codes.id = histories.code_id
                                LEFT JOIN batches ON batches.id = codes.batch_id
                                LEFT JOIN products ON products.id = batches.product_id
                                LEFT JOIN production_partners ON production_partners.id = codes.production_partner_id
                                LEFT JOIN locations ON locations.id = histories.location_id
                                WHERE surveys.member_id = {$member->id}
                                  AND survey_answers.question_id = {$question->id}
                                  AND {$whereRaw}
                                GROUP BY survey_answers.value) AS answer_analysis");

                $survey_answers = DB::table('survey_scaling_options')
                    ->select('survey_scaling_options.scaling_value AS id','survey_scaling_options.scaling_value AS option_en',
                        DB::raw('IF(answer_analysis.answer_count IS NULL, 0, answer_analysis.answer_count) AS answer_count'))
                    ->leftJoin($temp_table, 'survey_scaling_options.scaling_value', '=', 'answer_analysis.value')
                    ->whereRaw($main_where, $binding_array)
                    ->orderBy('survey_scaling_options.scaling_value', 'asc')
                    ->get();
            }
            else
            {
                if(count($response_ids) > 0)
                {
                    $response_list = implode(',', $response_ids);
                }
                else
                {
                    $response_list = '0';
                }
                $temp_table =
                    DB::raw("(SELECT survey_answers.value,
                                    count(survey_responses.id) as answer_count
                                FROM survey_answers
                                LEFT JOIN survey_questions ON survey_questions.id = survey_answers.question_id
                                LEFT JOIN surveys ON survey_questions.survey_id = surveys.id
                                LEFT JOIN survey_responses ON (survey_responses.id = survey_answers.response_id
                                                           AND survey_responses.id IN ({$response_list}))
                                LEFT JOIN histories ON histories.id = survey_responses.history_id
                                LEFT JOIN codes ON codes.id = histories.code_id
                                LEFT JOIN batches ON batches.id = codes.batch_id
                                LEFT JOIN products ON products.id = batches.product_id
                                LEFT JOIN production_partners ON production_partners.id = codes.production_partner_id
                                LEFT JOIN locations ON locations.id = histories.location_id
                                WHERE surveys.member_id = {$member->id}
                                  AND survey_answers.question_id = {$question->id}
                                  AND {$whereRaw}
                                GROUP BY survey_answers.value) AS answer_analysis");

                $survey_answers = DB::table('survey_scaling_options')
                    ->select('survey_scaling_options.scaling_value AS id','survey_scaling_options.scaling_value AS option_en',
                        DB::raw('IF(answer_analysis.answer_count IS NULL, 0, answer_analysis.answer_count) AS answer_count'))
                    ->leftJoin($temp_table, 'survey_scaling_options.scaling_value', '=', 'answer_analysis.value')
                    ->whereRaw($main_where, $binding_array)
                    ->orderBy('survey_scaling_options.scaling_value', 'asc')
                    ->get();
            }
        }

        return response()->json($survey_answers);
    }

    public function getNPSAnalysis(Request $request)
    {
        $question_id = $request->input('question_id');
        $filters = $request->input('filters');
        $question = SurveyQuestion::find($question_id);

        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();


        $whereRaw = " batches.member_id = {$member->id} AND batches.deleted = 0 ";

        $filters = $request->input('filters');

        $binding_array = array();

        if($filters['search'])
        {
            $binding_array['search_string1'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string2'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string3'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string4'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string5'] = strtolower("%{$filters['search']}%");
            $whereRaw .= " AND (LOWER(codes.full_code) LIKE ? ";
            $whereRaw .= "      OR LOWER(batches.batch_code) LIKE ? ";
            $whereRaw .= "      OR LOWER(products.name_en) LIKE ? ";
            $whereRaw .= "      OR LOWER(locations.location) LIKE ? ";
            $whereRaw .= "      OR LOWER(production_partners.name_en) LIKE ? ) ";
        }
        if($filters['from_date'])
        {
            $binding_array['from_date'] = Utils::toMySQLDate($filters['from_date']);
            $whereRaw .= " AND DATE(survey_responses.created_at) >= ? ";
        }
        if($filters['to_date'])
        {
            $binding_array['to_date'] = Utils::toMySQLDate($filters['to_date']);
            $whereRaw .= " AND DATE(survey_responses.created_at) <= ? ";
        }
        if($filters['batch_id'])
        {
            $binding_array['batch_id'] = $filters['batch_id'];
            $whereRaw .= " AND codes.batch_id = ? ";
        }
        if($filters['product_id'])
        {
            $binding_array['product_id'] = $filters['product_id'];
            $whereRaw .= " AND batches.product_id = ? ";
        }
        if($filters['production_partner_id'])
        {
            $binding_array['production_partner_id'] = $filters['production_partner_id'];
            $whereRaw .= " AND codes.production_partner_id = ? ";
        }
        if($filters['location_id'])
        {
            $binding_array['location_id'] = $filters['location_id'];
            $whereRaw .= " AND histories.location_id = ? ";
        }
        if($filters['language'])
        {
            $binding_array['language'] = "{$filters['language']}";
            $whereRaw .= " AND histories.language = ? ";
        }

        $first = true;
        $response_ids = array();
        if($filters['option_filters'])
        {
            $option_list = explode(',', $filters['option_filters']);
            if(is_array($option_list))
            {
                foreach($option_list as $option_info)
                {
                    if(!$option_info)
                    {
                        continue;
                    }
                    $option_array = explode('_', $option_info);
                    if($first == true)
                    {
                        $survey_responses = DB::table('survey_responses')
                            ->select('survey_responses.id')
                            ->join('survey_answers','survey_responses.id','=','survey_answers.response_id')
                            ->where(['survey_answers.question_id' => $option_array[0],
                                'survey_answers.value' => $option_array[1],
                                'survey_answers.deleted' => 0])
                            ->pluck('survey_responses.id')->toArray();
                        $response_ids = $survey_responses;
                        $first = false;
                    }
                    else
                    {
                        if(!(count($response_ids) > 0))
                        {
                            break;
                        }
                        else
                        {
                            $survey_responses = DB::table('survey_responses')
                                ->select('survey_responses.id')
                                ->join('survey_answers','survey_responses.id','=','survey_answers.response_id')
                                ->where(['survey_answers.question_id' => $option_array[0],
                                    'survey_answers.value' => $option_array[1],
                                    'survey_answers.deleted' => 0])
                                ->whereIn('survey_responses.id', $response_ids)
                                ->pluck('survey_responses.id')->toArray();
                            $response_ids = $survey_responses;
                        }
                    }
                }
            }
        }

        $results = array();

        if($question->type == SurveyQuestion::TYPE_SCALING_QUESTION)
        {
            $main_where = " products.deleted = 0 AND products.member_id = " . $member->id;

            if($first == true)
            {
                $temp_table =
                    DB::raw("(SELECT products.id,
                                    count(survey_answers.id) as answer_count
                                FROM survey_answers
                                LEFT JOIN survey_questions ON survey_questions.id = survey_answers.question_id
                                LEFT JOIN surveys ON survey_questions.survey_id = surveys.id
                                LEFT JOIN survey_responses ON (survey_responses.id = survey_answers.response_id)
                                LEFT JOIN histories ON histories.id = survey_responses.history_id
                                LEFT JOIN codes ON codes.id = histories.code_id
                                LEFT JOIN batches ON batches.id = codes.batch_id
                                LEFT JOIN products ON batches.product_id = products.id
                                LEFT JOIN production_partners ON production_partners.id = codes.production_partner_id
                                LEFT JOIN locations ON locations.id = histories.location_id
                                WHERE products.member_id = {$member->id}
                                  AND surveys.member_id = {$member->id}
                                  AND survey_answers.question_id = {$question->id}
                                  AND survey_answers.value >= 0
                                  AND survey_answers.value <= 6
                                  AND {$whereRaw}
                                GROUP BY products.id) AS answer_analysis");
                $products_0_6 = DB::table('products')
                    ->select('products.id','products.name_en',
                        DB::raw('IF(answer_analysis.answer_count IS NULL, 0, answer_analysis.answer_count) AS answer_0_6'))
                    ->leftJoin($temp_table, 'products.id', '=', 'answer_analysis.id')
                    ->whereRaw($main_where, $binding_array)
                    ->orderBy('products.name_en', 'asc')
                    ->get()
                    ->toArray();
                $temp_table =
                    DB::raw("(SELECT products.id,
                                    count(survey_answers.id) as answer_count
                                FROM survey_answers
                                LEFT JOIN survey_questions ON survey_questions.id = survey_answers.question_id
                                LEFT JOIN surveys ON survey_questions.survey_id = surveys.id
                                LEFT JOIN survey_responses ON (survey_responses.id = survey_answers.response_id)
                                LEFT JOIN histories ON histories.id = survey_responses.history_id
                                LEFT JOIN codes ON codes.id = histories.code_id
                                LEFT JOIN batches ON batches.id = codes.batch_id
                                LEFT JOIN products ON batches.product_id = products.id
                                LEFT JOIN production_partners ON production_partners.id = codes.production_partner_id
                                LEFT JOIN locations ON locations.id = histories.location_id
                                WHERE products.member_id = {$member->id}
                                  AND surveys.member_id = {$member->id}
                                  AND survey_answers.question_id = {$question->id}
                                  AND survey_answers.value >= 7
                                  AND survey_answers.value <= 8
                                  AND {$whereRaw}
                                GROUP BY products.id) AS answer_analysis");
                $products_7_8 = DB::table('products')
                    ->select('products.id','products.name_en',
                        DB::raw('IF(answer_analysis.answer_count IS NULL, 0, answer_analysis.answer_count) AS answer_7_8'))
                    ->leftJoin($temp_table, 'products.id', '=', 'answer_analysis.id')
                    ->whereRaw($main_where, $binding_array)
                    ->orderBy('products.name_en', 'asc')
                    ->get()
                    ->toArray();

                $temp_table =
                    DB::raw("(SELECT products.id,
                                    count(survey_answers.id) as answer_count
                                FROM survey_answers
                                LEFT JOIN survey_questions ON survey_questions.id = survey_answers.question_id
                                LEFT JOIN surveys ON survey_questions.survey_id = surveys.id
                                LEFT JOIN survey_responses ON (survey_responses.id = survey_answers.response_id)
                                LEFT JOIN histories ON histories.id = survey_responses.history_id
                                LEFT JOIN codes ON codes.id = histories.code_id
                                LEFT JOIN batches ON batches.id = codes.batch_id
                                LEFT JOIN products ON batches.product_id = products.id
                                LEFT JOIN production_partners ON production_partners.id = codes.production_partner_id
                                LEFT JOIN locations ON locations.id = histories.location_id
                                WHERE products.member_id = {$member->id}
                                  AND surveys.member_id = {$member->id}
                                  AND survey_answers.question_id = {$question->id}
                                  AND survey_answers.value >= 9
                                  AND survey_answers.value <= 10
                                  AND {$whereRaw}
                                GROUP BY products.id) AS answer_analysis");
                $products_9_10 = DB::table('products')
                    ->select('products.id','products.name_en',
                        DB::raw('IF(answer_analysis.answer_count IS NULL, 0, answer_analysis.answer_count) AS answer_9_10'))
                    ->leftJoin($temp_table, 'products.id', '=', 'answer_analysis.id')
                    ->whereRaw($main_where, $binding_array)
                    ->orderBy('products.name_en', 'asc')
                    ->get()
                    ->toArray();
                $temp_results = array();
                foreach($products_0_6 as $item)
                {
                    $temp_results[$item->id] = array('id' => $item->id, 'name_en' => $item->name_en, 'answer_0_6' => $item->answer_0_6);
                }
                foreach($products_7_8 as $item)
                {
                    $product_info = $temp_results[$item->id];
                    $product_info['answer_7_8'] = $item->answer_7_8;
                    $temp_results[$item->id] = $product_info;
                }
                foreach($products_9_10 as $item)
                {
                    $product_info = $temp_results[$item->id];
                    $product_info['answer_9_10'] = $item->answer_9_10;
                    $temp_results[$item->id] = $product_info;
                }

                foreach($temp_results as $key => $result)
                {
                    $total = $result['answer_0_6'] + $result['answer_7_8'] + $result['answer_9_10'];
                    if($total == 0)
                    {
                        $result['nps'] = 'Not Available';
                    }
                    else
                    {
                        $nps = round(($result['answer_9_10'] - $result['answer_0_6'])/$total, 2) * 100;
                        $result['nps'] = $nps;
                    }
                    $results[] = $result;
                }

            }
            else
            {
                if(count($response_ids) > 0)
                {
                    $response_list = implode(',', $response_ids);
                }
                else
                {
                    $response_list = '0';
                }
                $temp_table =
                    DB::raw("(SELECT products.id,
                                    count(survey_answers.id) as answer_count
                                FROM survey_answers
                                LEFT JOIN survey_questions ON survey_questions.id = survey_answers.question_id
                                LEFT JOIN surveys ON survey_questions.survey_id = surveys.id
                                LEFT JOIN survey_responses ON (survey_responses.id = survey_answers.response_id
                                                           AND survey_responses.id IN ({$response_list}))
                                LEFT JOIN histories ON histories.id = survey_responses.history_id
                                LEFT JOIN codes ON codes.id = histories.code_id
                                LEFT JOIN batches ON batches.id = codes.batch_id
                                LEFT JOIN products ON batches.product_id = products.id
                                LEFT JOIN production_partners ON production_partners.id = codes.production_partner_id
                                LEFT JOIN locations ON locations.id = histories.location_id
                                WHERE products.member_id = {$member->id}
                                  AND surveys.member_id = {$member->id}
                                  AND survey_answers.question_id = {$question->id}
                                  AND survey_answers.value >= 0
                                  AND survey_answers.value <= 6
                                  AND {$whereRaw}
                                GROUP BY products.id) AS answer_analysis");
                $products_0_6 = DB::table('products')
                    ->select('products.id','products.name_en',
                        DB::raw('IF(answer_analysis.answer_count IS NULL, 0, answer_analysis.answer_count) AS answer_0_6'))
                    ->leftJoin($temp_table, 'products.id', '=', 'answer_analysis.id')
                    ->whereRaw($main_where, $binding_array)
                    ->orderBy('products.name_en', 'asc')
                    ->get()
                    ->toArray();
                $temp_table =
                    DB::raw("(SELECT products.id,
                                    count(survey_answers.id) as answer_count
                                FROM survey_answers
                                LEFT JOIN survey_questions ON survey_questions.id = survey_answers.question_id
                                LEFT JOIN surveys ON survey_questions.survey_id = surveys.id
                                LEFT JOIN survey_responses ON (survey_responses.id = survey_answers.response_id
                                                           AND survey_responses.id IN ({$response_list}))
                                LEFT JOIN histories ON histories.id = survey_responses.history_id
                                LEFT JOIN codes ON codes.id = histories.code_id
                                LEFT JOIN batches ON batches.id = codes.batch_id
                                LEFT JOIN products ON batches.product_id = products.id
                                LEFT JOIN production_partners ON production_partners.id = codes.production_partner_id
                                LEFT JOIN locations ON locations.id = histories.location_id
                                WHERE products.member_id = {$member->id}
                                  AND surveys.member_id = {$member->id}
                                  AND survey_answers.question_id = {$question->id}
                                  AND survey_answers.value >= 7
                                  AND survey_answers.value <= 8
                                  AND {$whereRaw}
                                GROUP BY products.id) AS answer_analysis");
                $products_7_8 = DB::table('products')
                    ->select('products.id','products.name_en',
                        DB::raw('IF(answer_analysis.answer_count IS NULL, 0, answer_analysis.answer_count) AS answer_7_8'))
                    ->leftJoin($temp_table, 'products.id', '=', 'answer_analysis.id')
                    ->whereRaw($main_where, $binding_array)
                    ->orderBy('products.name_en', 'asc')
                    ->get()
                    ->toArray();

                $temp_table =
                    DB::raw("(SELECT products.id,
                                    count(survey_answers.id) as answer_count
                                FROM survey_answers
                                LEFT JOIN survey_questions ON survey_questions.id = survey_answers.question_id
                                LEFT JOIN surveys ON survey_questions.survey_id = surveys.id
                                LEFT JOIN survey_responses ON (survey_responses.id = survey_answers.response_id
                                                           AND survey_responses.id IN ({$response_list}))
                                LEFT JOIN histories ON histories.id = survey_responses.history_id
                                LEFT JOIN codes ON codes.id = histories.code_id
                                LEFT JOIN batches ON batches.id = codes.batch_id
                                LEFT JOIN products ON batches.product_id = products.id
                                LEFT JOIN production_partners ON production_partners.id = codes.production_partner_id
                                LEFT JOIN locations ON locations.id = histories.location_id
                                WHERE products.member_id = {$member->id}
                                  AND surveys.member_id = {$member->id}
                                  AND survey_answers.question_id = {$question->id}
                                  AND survey_answers.value >= 9
                                  AND survey_answers.value <= 10
                                  AND {$whereRaw}
                                GROUP BY products.id) AS answer_analysis");
                $products_9_10 = DB::table('products')
                    ->select('products.id','products.name_en',
                        DB::raw('IF(answer_analysis.answer_count IS NULL, 0, answer_analysis.answer_count) AS answer_9_10'))
                    ->leftJoin($temp_table, 'products.id', '=', 'answer_analysis.id')
                    ->whereRaw($main_where, $binding_array)
                    ->orderBy('products.name_en', 'asc')
                    ->get()
                    ->toArray();
                $temp_results = array();
                foreach($products_0_6 as $item)
                {
                    $temp_results[$item->id] = array('id' => $item->id, 'name_en' => $item->name_en, 'answer_0_6' => $item->answer_0_6);
                }
                foreach($products_7_8 as $item)
                {
                    $product_info = $temp_results[$item->id];
                    $product_info['answer_7_8'] = $item->answer_7_8;
                    $temp_results[$item->id] = $product_info;
                }
                foreach($products_9_10 as $item)
                {
                    $product_info = $temp_results[$item->id];
                    $product_info['answer_9_10'] = $item->answer_9_10;
                    $temp_results[$item->id] = $product_info;
                }

                foreach($temp_results as $key => $result)
                {
                    $total = $result['answer_0_6'] + $result['answer_7_8'] + $result['answer_9_10'];
                    if($total == 0)
                    {
                        $result['nps'] = 'Not Available';
                    }
                    else
                    {
                        $nps = round(($result['answer_9_10'] - $result['answer_0_6'])/$total, 2) * 100;
                        $result['nps'] = $nps;
                    }
                    $results[] = $result;
                }
            }
        }

        return response()->json($results);
    }

    public function deleteQuestions(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();

            foreach($ids as $id)
            {
                $question = SurveyQuestion::find($id);

                if($question)
                {
                    $question->deleted = 1;
                    $question->save();
                }
            }

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }

    public function deleteQuestionOptions(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();

            foreach($ids as $id)
            {
                $question_option = QuestionOption::find($id);

                if($question_option)
                {
                    $question_option->deleted = 1;
                    $question_option->save();
                }
            }

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }
}