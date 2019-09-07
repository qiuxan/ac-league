<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberFile extends Model
{
    const TYPE_STORAGE_LOCAL                 = 0;
    const TYPE_STORAGE_CLOUD                 = 1;

    protected $fillable = [ 'name', 'original_name', 'type', 'size', 'location' ];
}
