<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionPartner extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = ['name_en', 'name_cn', 'name_tr', 'address', 'phone'];     
}
