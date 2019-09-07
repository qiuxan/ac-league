<?php

namespace App\Http\Controllers;

use App\Batch;
use App\Disposition;
use App\FactoryBatch;
use App\FactoryCode;
use App\History;
use App\Jobs\SetLocationWithIP;
use App\Location;
use App\Member;
use App\Product;
use App\ProductAttribute;
use App\QuestionOption;
use App\QuestionResult;
use App\Roll;
use App\Survey;
use App\SurveyQuestion;
use App\SurveyResponse;
use App\SystemVariable;
use App\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Code;
use App\PostCategory;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Facades\Agent;
use GeoIp2\Database\Reader;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locale = App::getLocale();
        
        $slides = DB::table('slides')->select('slides.*', 'files.location')
            ->join('files','slides.file_id','=','files.id')
            ->where(['slides.language' => $locale, 'slides.deleted' => 0])
            ->orderBy('slides.priority', 'asc')->get();

        $services = DB::table('posts')->select('posts.*')
            ->where(['posts.category_id' => PostCategory::SERVICES, 'posts.language' => $locale])
            ->get();

        $why_us_list = DB::table('posts')->select('posts.*')
            ->where(['posts.category_id' => PostCategory::WHY_US, 'posts.language' => $locale])
            ->get();

        return view('home', compact('slides', 'services', 'why_us_list'));
    }

    public function verify(Request $request, $full_code = null)
    {
        if($request->segment(1) == 'qrscan-html')
        {
            $full_code = $request->input('search');
        }

        if(!$full_code)
        {
            if($request->segment(2) == 'qrscan.html')
            {
                $full_code = $request->input('search');
            }
        }

        if(!$full_code)
        {
            $full_code = $request->input('code');
        }

        $full_code = strtoupper(trim($full_code));

        $code = DB::table('codes')->select('codes.*', 'production_partners.name_' . App::getLocale() . ' AS reseller')
            ->leftJoin('production_partners','codes.reseller_id','=','production_partners.id')
            ->where(['codes.full_code' => $full_code])
            ->first();
        if($code && $code->id > 0 && $code->batch_id > 0)
        {
            $batch = DB::table('batches')->select('batches.*', 'production_partners.name_' . App::getLocale() . ' AS reseller')
                ->leftJoin('production_partners','batches.reseller_id','=','production_partners.id')
                ->where(['batches.id' => $code->batch_id])
                ->first();
            $product = Product::find($batch->product_id);
            $member_configurations = DB::table('member_configurations')->select('system_variables.variable', 'member_configurations.value')
                ->join('system_variables','member_configurations.system_variable_id','=','system_variables.id')
                ->where(['member_configurations.deleted' => 0, 'member_configurations.member_id' => $batch->member_id])
                ->whereIn('system_variables.type', array(SystemVariable::TYPE_VER_AUTH_DISPLAY, SystemVariable::TYPE_PROMOTION_DISPLAY))
                ->get()
                ->toArray();

            $configurations = array();
            foreach($member_configurations as $configuration)
            {
                $configurations[$configuration->variable] = $configuration->value;
            }

            if($product && $product->id)
            {
                $product_images = DB::table('product_images')->select('product_images.*', 'files.location', 'files.original_name')
                    ->join('files','product_images.file_id','=','files.id')
                    ->where(['product_images.product_id' => $product->id])
                    ->orderBy('product_images.priority', 'asc')->get();

                $product_attributes = ProductAttribute::where(['product_id' => $product->id,
                    'language' => App::getLocale(), 'deleted' => 0])
                    ->orderBy('priority', 'asc')
                    ->get();

                $member = Member::find($batch->member_id);

                $history = $this->storeHistory($request, $code->id);
                $request->session()->put('history_id', $history->id);
                $encrypted_id = sha1($history->id);

                $from_authentic = 0;

                if($batch->member_id == 1)
                {
                    $scan_history = DB::table('histories')
                        ->select('histories.*', 'locations.location')
                        ->join('locations','histories.location_id','=','locations.id')
                        ->where(['code_id' => $code->id])
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
                else
                {
                    $scan_history = null;
                }

                return view('verify', compact('member', 'product', 'product_images', 'product_attributes', 'batch',
                    'code', 'encrypted_id', 'from_authentic', 'history', 'configurations', 'scan_history'));
            }
            else
            {
                $request->session()->forget('history_id');
                return view('verify-failed');
            }
        }
        else
        {
            $request->session()->forget('history_id');
            return view('verify-failed');
        }
    }

    public function authenticate(Request $request)
    {
        $uid = $request->input('uid');
        $password = $request->input('password');
        $password = strtoupper(trim($password));

        if(strlen($password) == 5 && ctype_digit($password))
        {
            $password = "0" . $password;
        }

        if($request->session()->get('history_id') && sha1($request->session()->get('history_id')) == $uid)
        {
            $from_authentic = 1;
            $history = History::find($request->session()->get('history_id'));
            if($history && $history->id > 0)
            {
                $code = DB::table('codes')->select('codes.*', 'production_partners.name_' . App::getLocale() . ' AS reseller')
                    ->leftJoin('production_partners','codes.reseller_id','=','production_partners.id')
                    ->where(['codes.id' => $history->code_id])
                    ->first();
                $batch = DB::table('batches')->select('batches.*', 'production_partners.name_' . App::getLocale() . ' AS reseller')
                    ->leftJoin('production_partners','batches.reseller_id','=','production_partners.id')
                    ->where(['batches.id' => $code->batch_id])
                    ->first();
                $product = Product::find($batch->product_id);
                $product_images = DB::table('product_images')->select('product_images.*', 'files.location', 'files.original_name')
                    ->join('files','product_images.file_id','=','files.id')
                    ->where(['product_images.product_id' => $product->id])
                    ->orderBy('product_images.priority', 'asc')->get();

                $product_attributes = ProductAttribute::where(['product_id' => $product->id,
                    'language' => App::getLocale(), 'deleted' => 0])
                    ->orderBy('priority', 'asc')
                    ->get();

                if($batch->member_id == 1)
                {
                    $scan_history = DB::table('histories')
                        ->select('histories.*', 'locations.location')
                        ->join('locations','histories.location_id','=','locations.id')
                        ->where(['code_id' => $code->id])
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
                else
                {
                    $scan_history = null;
                }

                $member_configurations = DB::table('member_configurations')->select('system_variables.variable', 'member_configurations.value')
                    ->join('system_variables','member_configurations.system_variable_id','=','system_variables.id')
                    ->where(['member_configurations.deleted' => 0, 'member_configurations.member_id' => $batch->member_id])
                    ->whereIn('system_variables.type', array(SystemVariable::TYPE_VER_AUTH_DISPLAY, SystemVariable::TYPE_PROMOTION_DISPLAY))
                    ->get()
                    ->toArray();

                $configurations = array();
                foreach($member_configurations as $configuration)
                {
                    $configurations[$configuration->variable] = $configuration->value;
                }

                $member = Member::find($batch->member_id);
                $encrypted_id = sha1($request->session()->get('history_id'));

                if($history->count >= History::MAX_INPUT_PASSWORD)
                {
                    $rules = ['captcha' => 'required|captcha'];
                    $validator = Validator::make($request->all(), $rules);
                    if ($validator->fails())
                    {
                        $captcha_failed = 1;
                        return view('verify', compact('member', 'product', 'product_images', 'product_attributes',
                            'batch', 'code', 'encrypted_id', 'from_authentic', 'history', 'captcha_failed',
                            'configurations', 'scan_history'));
                    }
                }

                if($code->password == sha1($password))
                {
                    try
                    {
                        if($code->disposition_id == Disposition::ACTIVE || $code->disposition_id == Disposition::TRANSIT || $code->disposition_id == Disposition::SELLING)
                        {
                            $existed = 0;
                        }
                        else
                        {
                            $existed = History::where(['code_id' => $code->id, 'status' => History::STATUS_AUTHENTICATED])->count();
                            if($existed == 0)
                            {
                                $existed = 1;
                            }
                        }
                        //$existed = History::where(['code_id' => $code->id, 'status' => History::STATUS_AUTHENTICATED])->count();
                        DB::beginTransaction();
                        if($existed == 0)
                        {
                            $update_code = Code::find($code->id);
                            $update_code->disposition_id = Disposition::SOLD;
                            $update_code->save();
                        }
                        $history->status = History::STATUS_AUTHENTICATED;
                        $history->save();
                        DB::commit();
                    }
                    catch (Exception $e){
                        DB::rollBack();
                        Utils::trace($e->getMessage());
                        return view('verify', compact('member', 'product', 'product_images', 'product_attributes',
                            'batch', 'code', 'encrypted_id', 'from_authentic', 'history', 'configurations', 'scan_history'));
                    }
                    $request->session()->forget('history_id');
                    $survey = Survey::where(['member_id' => $batch->member_id])->first();
                    if($survey && $survey->id > 0)
                    {
                        $survey_response = DB::table('survey_responses')->select('survey_responses.*')
                            ->join('histories','survey_responses.history_id','=','histories.id')
                            ->where(['histories.code_id' => $code->id])
                            ->first();

                        if($survey_response && $survey_response->id > 0 && !($code->full_code == 'UV031IMXN0ZFH'
                            || $code->full_code == 'UV031IMXV4XQI' || $code->full_code == 'UV031IMXS1GF0'))
                        {
                            $survey_questions = null;
                        }
                        else
                        {
                            $survey_questions = SurveyQuestion::where(['survey_id' => $survey->id, 'published' => 1, 'deleted' => 0])->orderBy('priority', 'asc')->get();
                            foreach($survey_questions as $survey_question)
                            {
                                if($survey_question->type == SurveyQuestion::TYPE_MULTIPLE_CHOICES)
                                {
                                    $question_options = QuestionOption::where(['question_id' => $survey_question->id,
                                        'deleted' => 0])->orderBy('priority', 'asc')->get();
                                    $survey_question->question_options = $question_options;
                                }
                            }
                        }
                    }
                    else
                    {
                        $survey_questions = null;
                    }
                    return view('authenticate', compact('member', 'product', 'product_images', 'product_attributes',
                        'batch', 'code', 'existed', 'history', 'configurations', 'survey', 'survey_questions'));
                }
                else
                {
                    try
                    {
                        DB::beginTransaction();
                        $history->status = History::STATUS_FAILED;
                        $history->count = $history->count + 1;
                        $history->save();
                        DB::commit();
                    }
                    catch (Exception $e){
                        DB::rollBack();
                        Utils::trace($e->getMessage());
                        return view('verify', compact('member', 'product', 'product_images', 'product_attributes',
                            'batch', 'code', 'encrypted_id', 'from_authentic', 'history', 'configurations', 'scan_history'));
                    }
                    return view('verify', compact('member', 'product', 'product_images', 'product_attributes', 'batch',
                        'code', 'encrypted_id', 'from_authentic', 'history', 'configurations', 'scan_history'));
                }
            }
            else
            {
                Utils::trace("Invalid! Should not be here!");
                $request->session()->forget('history_id');
                return view('authenticate-failed');
            }
        }
        else
        {
            $request->session()->forget('history_id');
            return Redirect::to("/verification");
        }
    }

    private  function storeHistory(Request $request, $code_id)
    {
        try {
            DB::beginTransaction();

            $history = new History();
            $history->code_id = $code_id;
            $history->is_mobile = Agent::isMobile();
            $history->operation_system = Agent::platform();
            $history->browser = Agent::browser();
            $history->ip_address = $request->server('REMOTE_ADDR');
            if($history->ip_address == '192.168.56.1')
            {
                $history->ip_address = '144.139.88.227';
            }
            $history->status = History::STATUS_VERIFIED;
            $history->language = App::getLocale();

            $history->save();

            DB::commit();

            SetLocationWithIP::dispatch($history)->onQueue('ozm');

            return $history;
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return null;
        }
    }

    public function verification(Request $request)
    {
        $request->session()->forget('history_id');
        return view('verification');
    }

    public function switchLang(Request $request)
    {
        $lang = $request->input('lang');

        if ($lang != 'cn' && $lang != 'en' && $lang != 'tr') {
            $lang = 'en';
        }

        $request->session()->put('locale', $lang);

        $locale = App::getLocale();
        if($locale != $lang)
        {
            App::setLocale($lang);
            return json_encode([
                'result' => 1
            ]);
        }

        return json_encode([
            'result' => 0
        ]);
    }

    public function setPosition(Request $request)
    {
        try {
            DB::beginTransaction();
            $lat = $request->input('lat');
            $lng = $request->input('lng');

            $history = History::find($request->session()->get('history_id'));
            if($history && $history->id > 0)
            {
                if($lat && $lng)
                {
                    $history->lat = $lat;
                    $history->lng = $lng;

                    $current_loc = $this->getLocation($lat, $lng);
                    if($current_loc)
                    {
                        $location = DB::table('locations')->where(['location' => $current_loc])->first();
                        if($location)
                        {
                            $history->location_id = $location->id;
                        }
                        else
                        {
                            $location = new Location();
                            $location->location = $current_loc;
                            $location->save();
                            $history->location_id = $location->id;
                        }
                    }
                    else
                    {
                        $this->setLocationFromIP($history);
                    }
                    $history->save();

                    DB::commit();
                    return json_encode([
                        'result' => 1
                    ]);
                }
                else
                {
                    if($history->ip_address)
                    {
                        $this->setLocationFromIP($history);
                        $history->save();

                        DB::commit();
                        return json_encode([
                            'result' => 1
                        ]);
                    }
                    else
                    {
                        DB::rollBack();
                        return json_encode([
                            'result' => 0
                        ]);
                    }
                }
            }
            else
            {
                DB::rollBack();
                return json_encode([
                    'result' => 0
                ]);
            }
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }

    public function setNoPosition(Request $request)
    {

    }

    private function getLocation($latitude, $longitude)
    {
        $geocode=file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyA0U7X_EOApONDxx7UNdqRIs7o8BDmE_z4&latlng={$latitude},{$longitude}");

        $geoData= json_decode($geocode);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Utils::trace('Google error!');
            return null;
        }

        $city = '';
        $state = '';
        $country = '';
        $result = null;

        $nonChinaAddress = false;
        foreach($geoData->results as $result) {

            if(in_array('street_address', $result->types) || in_array('route', $result->types)) {
                foreach($result->address_components as $component)
                {
                    if(in_array('locality', $component->types) && $nonChinaAddress == false) {
                        $city = $component->long_name;
                    }
                    else if(in_array('administrative_area_level_2', $component->types))
                    {
                        $nonChinaAddress = true;
                        $city = $component->long_name;
                    }
                    else if(in_array('administrative_area_level_1', $component->types)) {
                        $state = $component->long_name;
                    }
                    else if(in_array('country', $component->types))
                    {
                        $country = $component->long_name;
                    }
                }
            }
            else if(in_array('locality', $result->types)) {
                foreach($result->address_components as $component)
                {
                    if(in_array('locality', $component->types)) {
                        $city = $component->long_name;
                    }
                }
            }
            else if(in_array('administrative_area_level_2', $result->types))
            {
                foreach($result->address_components as $component)
                {
                    if(in_array('administrative_area_level_2', $component->types)) {
                        $city = $component->long_name;
                    }
                }
            }
            else if(in_array('administrative_area_level_1', $result->types)) {
                foreach($result->address_components as $component)
                {
                    if(in_array('administrative_area_level_1', $component->types)) {
                        $state = $component->long_name;
                    }
                }
            }
            else if(in_array('country', $result->types))
            {
                foreach($result->address_components as $component)
                {
                    if(in_array('country', $component->types)) {
                        $country = $component->long_name;
                    }
                }
            }
        }

        $location = '';
        if($city)
        {
            $location = $city;
        }

        if($state)
        {
            if($location)
            {
                $location .= ", " . $state;
            }
            else
            {
                $location .= $state;
            }
        }
        else
        {
            return '';
        }

        if($country)
        {
            if($location)
            {
                $location .= ", " . $country;
            }
            else
            {
                $location = '';
            }
        }

        return $location;
    }

    private function setLocationFromIP(&$history)
    {
        $reader = new Reader(public_path() . '/GeoIP/GeoLite2-City.mmdb');
        $record = $reader->city($history->ip_address);
        Utils::trace($history->ip_address);
        if($record && $record->location)
        {
            $history->lat = $record->location->latitude;
            $history->lng = $record->location->longitude;

            $current_loc = $this->getLocation($history->lat, $history->lng);

            if(!$current_loc)
            {
                if($record->city->name)
                {
                    $current_loc .= $record->city->name;
                }
                if($record->mostSpecificSubdivision->name)
                {
                    if($current_loc)
                    {
                        $current_loc .= ", " . $record->mostSpecificSubdivision->name;
                    }
                    else
                    {
                        $current_loc .= $record->mostSpecificSubdivision->name;
                    }
                }
                if($record->country->name)
                {
                    if($current_loc)
                    {
                        $current_loc .= ", " . $record->country->name;
                    }
                }
            }

            if($current_loc)
            {
                $location = DB::table('locations')->where(['location' => $current_loc])->first();
                if($location)
                {
                    $history->location_id = $location->id;
                }
                else
                {
                    $location = new Location();
                    $location->location = $current_loc;
                    $location->save();
                    $history->location_id = $location->id;
                }
            }
        }
    }

    public function updateHistory()
    {
        if(auth()->user()->id == 1)
        {
            try {
                DB::beginTransaction();
                $histories = History::where(['language' => null])
                    ->orWhere(['location_id' => 0])
                    ->get();
                $i = 0;
                foreach ($histories as $history)
                {
                    if (!($history->lat && $history->lng))
                    {
                        $reader = new Reader(public_path() . '/GeoIP/GeoLite2-City.mmdb');
                        $record = $reader->city($history->ip_address);
                        if ($record && $record->location) {
                            $history->lat = $record->location->latitude;
                            $history->lng = $record->location->longitude;

                            if($record->mostSpecificSubdivision->name)
                            {
                                $current_loc = $record->mostSpecificSubdivision->name . ", " . $record->country->name;
                            }
                            else if($record->city->name)
                            {
                                $current_loc = $record->city->name . ", " . $record->country->name;
                            }
                            else
                            {
                                $array_loc = $this->getLocation($history->lat, $history->lng);
                                if($array_loc)
                                {
                                    $current_loc = $array_loc['state'] . ', ' . $array_loc['country'];
                                }
                                else
                                {
                                    $current_loc = $record->country->name . ", " . $record->country->name;
                                }
                            }
                            if ($current_loc) {
                                $location = DB::table('locations')->where(['location' => $current_loc])->first();
                                if ($location) {
                                    $history->location_id = $location->id;
                                } else {
                                    $location = new Location();
                                    $location->location = $current_loc;
                                    $location->save();
                                    $history->location_id = $location->id;
                                }
                            }

                            if (!$history->language) {
                                if ($record->country->name == 'China') {
                                    $history->language = 'cn';
                                }
                                else if ($record->country->name == 'Hong Kong') {
                                    $history->language = 'tr';
                                }
                                else {
                                    $history->language = 'en';
                                }
                            }

                            $history->save();
                        }
                    }
                    else
                    {
                        $array_loc = $this->getLocation($history->lat, $history->lng);
                        if($array_loc)
                        {
                            $current_loc = $array_loc['state'] . ", " . $array_loc['country'];
                            $location = DB::table('locations')->where(['location' => $current_loc])->first();
                            if($location)
                            {
                                $history->location_id = $location->id;
                            }
                            else
                            {
                                $location = new Location();
                                $location->location = $current_loc;
                                $location->save();
                                $history->location_id = $location->id;
                            }
                            if (!$history->language) {
                                if ($array_loc['country'] == 'China') {
                                    $history->language = 'cn';
                                }
                                else if ($array_loc['country'] == 'Hong Kong')
                                {
                                    $history->language = 'tr';
                                }
                                else {
                                    $history->language = 'en';
                                }
                            }
                        }
                        else
                        {
                            $this->setLocationFromIP($history);
                        }
                        $history->save();
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


    public function adminTest()
    {
        if(auth()->user()->id == 1)
        {
            try {
                $this->updateResellerInfo();
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

    public function processRedundantCodes()
    {
        if(auth()->user()->id == 1)
        {
            $factory_batch_code = 'I1VB1M87';
            try {
                DB::beginTransaction();
                $factory_batch = FactoryBatch::where(['batch_code' => $factory_batch_code])->first();
                $factory_codes = FactoryCode::where(['factory_batch_id' => $factory_batch->id])->get();
                $count = 0;
                $roll = new Roll();
                $roll->roll_code = $factory_batch_code . "-87";
                $roll->factory_batch_id = $factory_batch->id;
                $roll->quantity = 0;
                $roll->created_by = auth()->user()->id;;
                $roll->save();
                foreach ($factory_codes as $factory_code)
                {
                    $existed_code = Code::where(['full_code' => $factory_code->full_code])->first();
                    if(!$existed_code)
                    {
                        $code = new Code();
                        $code->roll_id = $roll->id;
                        $code->full_code = $factory_code->full_code;
                        $code->password = sha1($factory_code->password);
                        $code->order = ++$count;
                        $code->save();
                    }

                }
                $roll->quantity = $count;
                $roll->save();
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

    public function updateLocation()
    {
        if(auth()->user()->id == 1)
        {
            try {
                DB::beginTransaction();
                $histories = History::all();
                $i = 0;
                foreach ($histories as $history)
                {
                    $current_loc = $this->getLocation($history->lat, $history->lng);
                    if($current_loc)
                    {
                        $location = DB::table('locations')->where(['location' => $current_loc])->first();
                        if($location)
                        {
                            $history->location_id = $location->id;
                        }
                        else
                        {
                            $location = new Location();
                            $location->location = $current_loc;
                            $location->save();
                            $history->location_id = $location->id;
                        }
                    }
                    else
                    {
                        $this->setLocationFromIP($history);
                    }
                    $history->save();
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

    public function updateResellerInfo()
    {
        if(auth()->user()->id == 1)
        {
            try {
                Utils::trace("KHOI");
                DB::beginTransaction();
                $batches = Batch::all();
                foreach ($batches as $batch)
                {
                    if($batch->production_partner_id > 0)
                    {
                        $batch->reseller_id = $batch->production_partner_id;
                        $batch->production_partner_id = 0;
                        $batch->save();
                    }
                }

                $codes = Code::all();
                foreach ($codes as $code)
                {
                    if($code->production_partner_id > 0)
                    {
                        $code->reseller_id = $code->production_partner_id;
                        $code->production_partner_id = 0;
                        $code->save();
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

    public function surveyResponse(Request $request)
    {
        $result = 0;
        try {
            $check_response = DB::table('survey_responses')->select('survey_responses.*')
                ->where(['survey_responses.history_id' => $request->get('scan_id')])
                ->first();

            if($check_response && $check_response->id > 0)
            {
                return view('survey-notification', compact('result'));
            }

            DB::beginTransaction();

            $batch = DB::table('histories')->select('batches.*')
                ->join('codes','histories.code_id','=','codes.id')
                ->join('batches','codes.batch_id','=','batches.id')
                ->where(['histories.id' => $request->get('scan_id')])
                ->first();
            if($batch && $batch->id > 0)
            {
                $survey = Survey::where(['member_id' => $batch->member_id, 'deleted' => 0])->first();

                if($survey && $survey->id > 0)
                {
                    $survey_questions = DB::table('survey_questions')
                        ->select('survey_questions.*')
                        ->join('surveys','survey_questions.survey_id','=','surveys.id')
                        ->where(['survey_questions.survey_id' => $survey->id, 'survey_questions.deleted' => 0])
                        ->orderBy('survey_questions.priority', 'asc')->get();

                    $response = new SurveyResponse();
                    $response->survey_id = $survey->id;
                    $response->history_id = $request->get('scan_id');

                    $response->save();

                    foreach($survey_questions as $survey_question)
                    {
                        if($request->get('question_' . $survey_question->id))
                        {
                            $answer = new QuestionResult();
                            $answer->response_id = $response->id;
                            $answer->question_id = $survey_question->id;
                            $answer->value = $request->get('question_' . $survey_question->id);
                            $answer->save();
                        }
                    }
                }

                DB::commit();
                $result = 1;
                return view('survey-notification', compact('result'));
            }
            else
            {
                DB::rollBack();
                return view('survey-notification', compact('result'));
            }
        }
        catch (Exception $e){
            return view('survey-notification', compact('result'));
        }
    }
}
