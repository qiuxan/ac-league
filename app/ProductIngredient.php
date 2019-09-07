<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductIngredient extends Model
{
    public function product() {
        return $this->belongsTo( Product::class );
    }
}
