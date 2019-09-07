<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IngredientLot extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [ 'lot_code', 'quantity' ];

    public function user() {
        return $this->belongsTo( Member::class );
    }
}
