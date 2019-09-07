<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function batch() {
        return $this->belongsTo( Roll::class );
    }
}
