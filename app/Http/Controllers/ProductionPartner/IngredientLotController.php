<?php

namespace App\Http\Controllers\ProductionPartner;
use App\Constant;
use App\TrackingEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\IngredientLot;
use App\ProductionPartner;
use Exception;
use App\Utils;

class IngredientLotController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'production-partner.ingredient-lots.index' );
    }

    public function getIngredientLots(Request $request) {
        $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();

        $filters = $request->input('filters');
        $whereRaw = " ingredient_lots.deleted = 0 AND ingredient_lots.production_partner_id = {$production_partner->id} ";
        $binding_array = array();
        if($filters['search'])
        {
            $binding_array['search_string1'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string2'] = strtolower("%{$filters['search']}%");
            $whereRaw .= " AND (LOWER(ingredient_lots.lot_code) LIKE :search_string1 ";
            $whereRaw .= " OR LOWER(ingredients.name) LIKE :search_string2)";
        }

        $pageSize = $request->input('pageSize');
        $ingredient_lots = DB::table('ingredient_lots')->select('ingredient_lots.*', 'ingredients.name AS ingredient_name')
            ->leftJoin('ingredients','ingredient_lots.ingredient_id','=','ingredients.id')
            ->whereRaw($whereRaw, $binding_array)
            ->orderBy('ingredient_lots.id', 'desc')->paginate( $pageSize );

        return response()->json($ingredient_lots);
    }

    public function getIngredientLotForm() {
        return view( 'production-partner.ingredient-lots.form' );
    }

    public function getIngredientLotList() {
        return view( 'production-partner.ingredient-lots.list' );
    }

    public function getIngredientLot(Request $request) {
        $id = $request->input('id');
        $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $batch = DB::table('ingredient_lots')
            ->select('ingredient_lots.*', 'created_event.event_time AS created_time',
                'shipped_event.event_time AS shipped_time', 'received_event.event_time AS received_time')
            ->leftJoin('tracking_events AS created_event',  function($join) use($production_partner)
            {
                $join->on('created_event.object_type','=', DB::raw(TrackingEvent::OBJECT_INGREDIENT_LOT));
                $join->on('created_event.object_id', '=', 'ingredient_lots.id');
                $join->on('created_event.action', '=', DB::raw(TrackingEvent::ACTION_ADD));
                $join->on('created_event.business_step', '=', DB::raw(TrackingEvent::BUSINESS_STEP_COMMISSIONING));
                $join->on('created_event.disposition', '=', DB::raw(TrackingEvent::DISPOSITION_ACTIVE));
            })
            ->leftJoin('tracking_events AS shipped_event',  function($join) use($production_partner)
            {
                $join->on('shipped_event.object_type','=', DB::raw(TrackingEvent::OBJECT_INGREDIENT_LOT));
                $join->on('shipped_event.object_id', '=', 'ingredient_lots.id');
                $join->on('shipped_event.action', '=', DB::raw(TrackingEvent::ACTION_OBSERVE));
                $join->on('shipped_event.business_step', '=', DB::raw(TrackingEvent::BUSINESS_STEP_SHIPPING));
                $join->on('shipped_event.disposition', '=', DB::raw(TrackingEvent::DISPOSITION_IN_TRANSIT));
            })
            ->leftJoin('tracking_events AS received_event',  function($join) use($production_partner)
            {
                $join->on('received_event.object_type','=', DB::raw(TrackingEvent::OBJECT_INGREDIENT_LOT));
                $join->on('received_event.object_id', '=', 'ingredient_lots.id');
                $join->on('received_event.action', '=', DB::raw(TrackingEvent::ACTION_OBSERVE));
                $join->on('received_event.business_step', '=', DB::raw(TrackingEvent::BUSINESS_STEP_RECEIVING));
                $join->on('received_event.disposition', '=', DB::raw(TrackingEvent::DISPOSITION_IN_TRANSIT));
            })
            ->where(['ingredient_lots.id' => $id, 'ingredient_lots.production_partner_id' => $production_partner->id, 'ingredient_lots.deleted' => 0])
            ->first();

        return response()->json($batch);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $is_new = false;
            $ingredient_lot_id = $request->input('id');
            $ingredient_lot = IngredientLot::find($ingredient_lot_id);
            $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();
            if(!$ingredient_lot)
            {
                $ingredient_lot = new IngredientLot();
                $ingredient_lot->member_id = $production_partner->member_id;
                $ingredient_lot->created_by = auth()->user()->id;
                $ingredient_lot->production_partner_id = $production_partner->id;
                $ingredient_lot->current_pp_id = $production_partner->id;

                $is_new = true;
            }
            else
            {
                if($ingredient_lot->production_partner_id != $production_partner->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'ingredient_lot_id' => 0
                    ]);
                }
                $ingredient_lot->updated_by = auth()->user()->id;
            }

            if($request->input('ingredient_id'))
            {
                $ingredient_lot->ingredient_id = $request->input('ingredient_id');
            }
            else
            {
                $ingredient_lot->ingredient_id = 0;
            }

            $ingredient_lot->certificate_url = $request->input('certificate_url');
            $ingredient_lot->lot_code = $request->input('lot_code');
            $ingredient_lot->production_date = Utils::toMySQLDate($request->input('production_date'));
            $ingredient_lot->expiration_date = Utils::toMySQLDate($request->input('expiration_date'));

            $ingredient_lot->save();

            if($is_new)
            {
                if(auth()->user()->hasRole(Constant::CONTRACT_MANUFACTURER))
                {
                    $tracking_event = new TrackingEvent();
                    $tracking_event->event_type = TrackingEvent::TYPE_OBJECT;
                    $tracking_event->action = TrackingEvent::ACTION_OBSERVE;
                    $tracking_event->business_step = TrackingEvent::BUSINESS_STEP_RECEIVING;
                    $tracking_event->disposition = TrackingEvent::DISPOSITION_IN_TRANSIT;
                    $tracking_event->source_id = $production_partner->id;
                    $tracking_event->destination_id = $production_partner->id;
                    $tracking_event->object_type = TrackingEvent::OBJECT_INGREDIENT_LOT;
                    $tracking_event->object_id = $ingredient_lot->id;
                    $tracking_event->event_time = Utils::toMySQLDateTime($request->input('received_time'));
                    $tracking_event->created_by = auth()->user()->id;

                    $tracking_event->save();
                }
                else if(auth()->user()->hasRole(Constant::INGREDIENT_SUPPLIER))
                {
                    $tracking_event = new TrackingEvent();
                    $tracking_event->event_type = TrackingEvent::TYPE_OBJECT;
                    $tracking_event->action = TrackingEvent::ACTION_ADD;
                    $tracking_event->business_step = TrackingEvent::BUSINESS_STEP_RECEIVING;
                    $tracking_event->disposition = TrackingEvent::DISPOSITION_IN_TRANSIT;
                    $tracking_event->source_id = $production_partner->id;
                    $tracking_event->destination_id = $production_partner->id;
                    $tracking_event->object_type = TrackingEvent::OBJECT_INGREDIENT_LOT;
                    $tracking_event->object_id = $ingredient_lot->id;
                    $tracking_event->event_time = Utils::toMySQLDateTime($request->input('created_time'));
                    $tracking_event->created_by = auth()->user()->id;

                    $tracking_event->save();
                }
            }
            else
            {
                if(auth()->user()->hasRole(Constant::CONTRACT_MANUFACTURER))
                {
                    $tracking_event = TrackingEvent::where(['event_type' => TrackingEvent::TYPE_OBJECT,
                        'action' => TrackingEvent::ACTION_OBSERVE,
                        'business_step' => TrackingEvent::BUSINESS_STEP_RECEIVING,
                        'disposition' => TrackingEvent::DISPOSITION_IN_TRANSIT,
                        'object_type' => TrackingEvent::OBJECT_INGREDIENT_LOT,
                        'object_id' => $ingredient_lot->id
                    ])->first();

                    if($tracking_event && $tracking_event->id > 0)
                    {
                        $tracking_event->event_time = Utils::toMySQLDateTime($request->input('received_time'));

                        $tracking_event->save();
                    }
                }
                else if(auth()->user()->hasRole(Constant::INGREDIENT_SUPPLIER))
                {
                    $tracking_event = TrackingEvent::where(['event_type' => TrackingEvent::TYPE_OBJECT,
                        'action' => TrackingEvent::ACTION_ADD,
                        'business_step' => TrackingEvent::BUSINESS_STEP_RECEIVING,
                        'disposition' => TrackingEvent::DISPOSITION_IN_TRANSIT,
                        'object_type' => TrackingEvent::OBJECT_INGREDIENT_LOT,
                        'object_id' => $ingredient_lot->id
                    ])->first();

                    if($tracking_event && $tracking_event->id > 0)
                    {
                        $tracking_event->event_time = Utils::toMySQLDateTime($request->input('created_time'));

                        $tracking_event->save();
                    }


                    $tracking_event = TrackingEvent::where(['event_type' => TrackingEvent::TYPE_OBJECT,
                        'action' => TrackingEvent::ACTION_OBSERVE,
                        'business_step' => TrackingEvent::BUSINESS_STEP_SHIPPING,
                        'disposition' => TrackingEvent::DISPOSITION_IN_TRANSIT,
                        'object_type' => TrackingEvent::OBJECT_INGREDIENT_LOT,
                        'object_id' => $ingredient_lot->id
                    ])->first();

                    if($tracking_event && $tracking_event->id > 0)
                    {
                        $tracking_event->event_time = Utils::toMySQLDateTime($request->input('shipped_time'));

                        $tracking_event->save();
                    }

                }
            }

            DB::commit();
            return json_encode([
                'ingredient_lot_id' => $ingredient_lot->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'ingredient_lot_id' => 0
            ]);
        }
    }

    public function deleteIngredientLots(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();
            $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();
            foreach($ids as $id)
            {
                $ingredient_lot = IngredientLot::find($id);
                if($ingredient_lot->production_partner_id != $production_partner->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'result' => 0
                    ]);
                }
                if($ingredient_lot)
                {
                    $ingredient_lot->deleted = 1;
                    $ingredient_lot->updated_by = auth()->user()->id;
                    $ingredient_lot->save();
                }
            }

            DB::commit();
            return json_encode([
                'result' => 1
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'result' => 0
            ]);
        }
    }
}