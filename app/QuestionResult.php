<?php

namespace App;

use App;
use Illuminate\Database\Eloquent\Model;

class QuestionResult extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "survey_answers";

    public function response() {
        return $this->belongsTo( SurveyResponse::class );
    }

    public function question() {
        return $this->belongsTo( SurveyQuestion::class );
    }
}
