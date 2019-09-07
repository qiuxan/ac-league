<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roll extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function factoryBatch() {
        return $this->belongsTo( FactoryBatch::class );
    }

    public function member() {
        return $this->belongsTo( Member::class );
    }

    public function batch() {
        return $this->belongsTo( Batch::class );
    }
}
