<?php

namespace App;

use App;
use Illuminate\Database\Eloquent\Model;

class SystemVariable extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    const TYPE_VER_AUTH_DISPLAY         = 1;
    const TYPE_PROMOTION_DISPLAY        = 2;
    const TYPE_VERIFICATION_RULE        = 3;
    const TYPE_MEMBER_VARIABLES         = 4;

    protected $fillable = [ 'type', 'variable' ];
}
