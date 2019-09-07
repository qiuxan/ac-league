<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [ 'file_id', 'language', 'priority', 'title', 'published'];
    
    public function file() {
        return $this->belongsTo( File::class );
    }
}
