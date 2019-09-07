<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    public $timestamps = false;

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public function file() {
        return $this->belongsTo( File::class );
    }
}
