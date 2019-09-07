<?php

namespace App;

use App;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
    protected $fillable = [ 'gtin', 'name_en', 'name_cn', 'name_tr',
                            'company_en', 'company_cn', 'company_tr', 'company_website', 'company_logo'];

    public function user() {
        return $this->belongsTo( Member::class );
    }

    public function getNameAttribute()
    {
        return $this->{'name_'. App::getLocale()};
    }

    public function getCompanyAttribute()
    {
        return $this->{'company_'. App::getLocale()};
    }
}
