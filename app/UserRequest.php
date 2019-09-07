<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = ['status_id', 'full_name', 'email', 'subject', 'message'];

    public function status() {
        return $this->belongsTo ( UserRequestStatus::class );
    }
}
