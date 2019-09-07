<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchRoll extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [ 'batch_id', 'roll_id', 'start_code', 'end_code' ];

    public function batch() {
        return $this->belongsTo( Batch::class );
    }

    public function rolls() {
        return $this->hasMany( Roll::class );
    }
}
