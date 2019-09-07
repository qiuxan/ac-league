<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [ 'category_id', 'language', 'icon', 'feature_image', 'title', 'excerpt', 'content', 'published' ];

    public function category() {
        return $this->belongsTo( PostCategory::class );
    }
}
