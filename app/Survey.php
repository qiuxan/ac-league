<?php

namespace App;

use App;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function member() {
        return $this->belongsTo( Member::class );
    }

    public function getTitleAttribute()
    {
        return $this->{'title_'. App::getLocale()};
    }

    public function getDescriptionAttribute()
    {
        return $this->{'description_'. App::getLocale()};
    }
}
