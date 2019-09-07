<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    const GENERAL        = 1;
    const SERVICES       = 2;
    const NEWS           = 3;
    const WHY_US         = 4;

    public function posts() {
        return $this->hasMany( Post::class );
    }
}
