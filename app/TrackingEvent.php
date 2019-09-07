<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    const TYPE_OBJECT = 1;
    const TYPE_TRANSFORMATION = 2;
    const TYPE_AGGREGATION = 3;


    const ACTION_ADD = 1;
    const ACTION_OBSERVE = 2;

    const BUSINESS_STEP_COMMISSIONING = 1;
    const BUSINESS_STEP_SHIPPING = 2;
    const BUSINESS_STEP_RECEIVING = 3;
    const BUSINESS_STEP_CREATING_PRODUCT = 4;
    const BUSINESS_STEP_PACKING = 5;
    const BUSINESS_STEP_UNPACKING = 6;

    const DISPOSITION_ACTIVE = 1;
    const DISPOSITION_IN_TRANSIT = 2;
    const DISPOSITION_IN_PROGRESS = 3;

    const OBJECT_INGREDIENT_LOT = 1;
    const OBJECT_INGREDIENT_SHIPMENT = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
}
