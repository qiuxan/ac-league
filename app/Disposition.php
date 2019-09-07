<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Disposition extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    const ACTIVE         = 1;
    const TRANSIT        = 2;
    const SELLING        = 3;
    const SOLD           = 4;
    const RECALLED       = 5;
    const BLACKLISTED    = 6;

    public function batch() {
        return $this->belongsTo( Batch::class );
    }

    public function code() {
        return $this->belongsTo( Code::class );
    }
}
