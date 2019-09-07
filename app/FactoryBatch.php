<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FactoryBatch extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    const STATUS_NOT_STARTED          = 0;
    const STATUS_IN_PROGRESS          = 1;
    const STATUS_DONE                 = 2;

    const MAX_QUANTITY       = 500000;


    protected $fillable = [ 'description' ];
}
