<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [ 'batch_code', 'location' ];

    public function user() {
        return $this->belongsTo( Member::class );
    }
}
