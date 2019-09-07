<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRequestStatus extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    const OPEN = 1;
    const IN_PROGRESS = 2;
    const COMPLETED = 3;
    const CANCEL = 4;

    protected $table = 'user_request_status';
    
    public function requests() {
        return $this->hasMany( UserRequest::class );
    }
}
