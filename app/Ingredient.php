<?php

namespace App;

use App;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
    protected $fillable = [ 'gtin', 'name', 'origin', 'description'];
}
