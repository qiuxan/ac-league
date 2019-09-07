<?php

namespace App;

use App;
use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    const TYPE_OPEN_QUESTION        = 1;
    const TYPE_MULTIPLE_CHOICES     = 2;
    const TYPE_SCALING_QUESTION     = 3;

    public function survey() {
        return $this->belongsTo( Survey::class );
    }

    public function getQuestionAttribute()
    {
        return $this->{'question_'. App::getLocale()};
    }
}
