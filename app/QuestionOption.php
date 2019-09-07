<?php

namespace App;

use App;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "survey_question_options";

    public function question() {
        return $this->belongsTo( SurveyQuestion::class );
    }

    public function getOptionAttribute()
    {
        return $this->{'option_'. App::getLocale()};
    }
}
