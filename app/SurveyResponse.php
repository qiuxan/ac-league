<?php

namespace App;

use App;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function survey() {
        return $this->belongsTo( Survey::class );
    }
}
