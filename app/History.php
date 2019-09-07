<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    const STATUS_VERIFIED       = 0;
    const STATUS_AUTHENTICATED  = 1;
    const STATUS_FAILED         = 2;
    const MAX_INPUT_PASSWORD    = 4;

    public function code() {
        return $this->belongsTo( Code::class );
    }
}
