<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FactoryCode extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "factory_codes";

    public $timestamps = false;

    public function factoryBatch() {
        return $this->belongsTo( FactoryBatch::class );
    }
}
