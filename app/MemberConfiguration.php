<?php

namespace App;

use App;
use Illuminate\Database\Eloquent\Model;

class MemberConfiguration extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    const TYPE_SHOW = 1;
    const TYPE_HIDE = 2;

    protected $fillable = [ 'member_id', 'system_variable_id' ];
}
