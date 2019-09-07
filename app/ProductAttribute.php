<?php

namespace App;

use App;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    const TYPE_TEXT_BOX                 = 1;
    const TYPE_TEXT_AREA                = 2;
    const TYPE_IMAGE                    = 3;

    const DISPLAY_BOTH             = 1;
    const DISPLAY_VERIFICATION     = 2;
    const DISPLAY_AUTHENTICATION   = 3;
    const DISPLAY_NONE             = 4;

    protected $fillable = [ 'language', 'type', 'name', 'displayed_at' ];

    public function product() {
        return $this->belongsTo( Product::class );
    }
}
