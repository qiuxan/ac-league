<?php

namespace App;

use App;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    const TYPE_INACTIVE                 = 0;
    const TYPE_ACTIVE                   = 1;
    // each member maximun upload amount in Bytes
    const MAX_UPLOAD                    = 524288000;

    protected $fillable = [ 'company_en', 'company_cn', 'company_tr', 'phone', 'company_email', 'website', 'country_en', 'country_cn', 'country_tr', 'status', 'logo', 'background_image' ];

    public function user() {
        return $this->belongsTo( User::class );
    }

    public function products() {
        return $this->hasMany( Product::class );
    }

    public function getCompanyAttribute()
    {
        return $this->{'company_'. App::getLocale()};
    }
}
