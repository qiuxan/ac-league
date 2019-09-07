<?php

namespace App\Http\Controllers\ProductionPartner;
use App\Constant;
use App\ShipmentIngredientLot;
use App\TrackingEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\IngredientShipment;
use App\ProductionPartner;
use Exception;
use App\Utils;

class IngredientShipmentController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'production-partner.ingredient-shipments.index' );
    }

    public function getIngredientShipments(Request $request) {
        $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();

        $pageSize = $request->input('pageSize');
        DB::statement("SET @sequence_number:=0");
        if(auth()->user()->hasRole(Constant::INGREDIENT_SUPPLIER))
        {
            $ingredient_shipments = DB::table('ingredient_shipments')
                ->select('ingredient_shipments.*', 'destination.name_en AS destination_name',
                    'source.name_en AS source_name', DB::raw('@sequence_number:=@sequence_number+1 AS sequence_number'))
                ->leftJoin('production_partners AS source', 'ingredient_shipments.source_id', '=', 'source.id')
                ->leftJoin('production_partners AS destination', 'ingredient_shipments.destination_id', '=', 'destination.id')
                ->where(['ingredient_shipments.source_id' => $production_partner->id, 'ingredient_shipments.deleted' => 0])
                ->orderBy('ingredient_shipments.id', 'desc')->paginate( $pageSize );
        }
        else if(auth()->user()->hasRole(Constant::CONTRACT_MANUFACTURER))
        {
            $ingredient_shipments = DB::table('ingredient_shipments')
                ->select('ingredient_shipments.*', 'destination.name_en AS destination_name',
                    'source.name_en AS source_name', DB::raw('@sequence_number:=@sequence_number+1 AS sequence_number'))
                ->leftJoin('production_partners AS source', 'ingredient_shipments.source_id', '=', 'source.id')
                ->leftJoin('production_partners AS destination', 'ingredient_shipments.destination_id', '=', 'destination.id')
                ->where(['ingredient_shipments.destination_id' => $production_partner->id, 'ingredient_shipments.deleted' => 0])
                ->orderBy('ingredient_shipments.id', 'desc')->paginate( $pageSize );
        }
        else
        {
            $ingredient_shipments = DB::table('ingredient_shipments')
                ->select('ingredient_shipments.*', 'destination.name_en AS destination_name',
                    'source.name_en AS source_name', DB::raw('@sequence_number:=@sequence_number+1 AS sequence_number'))
                ->leftJoin('production_partners AS source', 'ingredient_shipments.source_id', '=', 'source.id')
                ->leftJoin('production_partners AS destination', 'ingredient_shipments.destination_id', '=', 'destination.id')
                ->where(['0' => 1, 'ingredient_shipments.deleted' => 0])
                ->orderBy('ingredient_shipments.id', 'desc')->paginate( $pageSize );
        }

        return response()->json($ingredient_shipments);
    }

    public function getIngredientShipmentForm() {
        return view( 'production-partner.ingredient-shipments.form' );
    }

    public function getIngredientShipmentList() {
        return view( 'production-partner.ingredient-shipments.list' );
    }

    public function getShipmentIngredientLots(Request $request) {
        $pageSize = $request->input('pageSize');
        $ingredient_shipment_id = $request->input('ingredient_shipment_id');
        DB::statement("SET @sequence_number:=0");
        $ingredient_lots = DB::table('shipment_ingredient_lots')
            ->select('shipment_ingredient_lots.*', 'ingredient_lots.lot_code',
                'ingredients.name AS ingredient_name', 'ingredients.gtin',DB::raw('@sequence_number:=@sequence_number+1 AS sequence_number'))
            ->join('ingredient_shipments', 'shipment_ingredient_lots.ingredient_shipment_id', '=', 'ingredient_shipments.id')
            ->join('ingredient_lots', 'shipment_ingredient_lots.ingredient_lot_id', '=', 'ingredient_lots.id')
            ->join('ingredients', 'ingredient_lots.ingredient_id', '=', 'ingredients.id')
            ->where(['shipment_ingredient_lots.ingredient_shipment_id' => $ingredient_shipment_id, 'ingredient_shipments.deleted' => 0,
                'shipment_ingredient_lots.deleted' => 0])
            ->paginate($pageSize);

        return response()->json($ingredient_lots);
    }

    public function getIngredientShipment(Request $request) {
        $id = $request->input('id');
        $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();

        if(auth()->user()->hasRole(Constant::INGREDIENT_SUPPLIER))
        {
            $batch = DB::table('ingredient_shipments')
                ->select('ingredient_shipments.*', 'destination.name_en AS destination_name', 'source.name_en AS source_name')
                ->leftJoin('production_partners AS source', 'ingredient_shipments.source_id', '=', 'source.id')
                ->leftJoin('production_partners AS destination', 'ingredient_shipments.destination_id', '=', 'destination.id')
                ->where(['ingredient_shipments.id' => $id, 'source.id' => $production_partner->id, 'ingredient_shipments.deleted' => 0])
                ->first();
        }
        else if(auth()->user()->hasRole(Constant::CONTRACT_MANUFACTURER))
        {
            $batch = DB::table('ingredient_shipments')
                ->select('ingredient_shipments.*', 'destination.name_en AS destination_name', 'source.name_en AS source_name')
                ->leftJoin('production_partners AS source', 'ingredient_shipments.source_id', '=', 'source.id')
                ->leftJoin('production_partners AS destination', 'ingredient_shipments.destination_id', '=', 'destination.id')
                ->where(['ingredient_shipments.id' => $id, 'destination_id' => $production_partner->id, 'ingredient_shipments.deleted' => 0])
                ->first();
        }
        else
        {
            $batch = DB::table('ingredient_shipments')
                ->select('ingredient_shipments.*', 'destination.name_en AS destination_name', 'source.name_en AS source_name')
                ->leftJoin('production_partners AS source', 'ingredient_shipments.source_id', '=', 'source.id')
                ->leftJoin('production_partners AS destination', 'ingredient_shipments.destination_id', '=', 'destination.id')
                ->where(['0' => 1, 'ingredient_shipments.deleted' => 0])
                ->first();
        }

        return response()->json($batch);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();

            $ingredient_shipment_id = $request->input('id');
            $ingredient_shipment = IngredientShipment::find($ingredient_shipment_id);
            $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();
            if(!$ingredient_shipment)
            {
                $ingredient_shipment = new IngredientShipment();
                $ingredient_shipment->tracking_code = $request->input('tracking_code');
                if(auth()->user()->hasRole(Constant::INGREDIENT_SUPPLIER))
                {
                    $ingredient_shipment->source_id = $production_partner->id;
                }
                $ingredient_shipment->destination_id = $request->input('destination_id');
                if(auth()->user()->hasRole(Constant::INGREDIENT_SUPPLIER))
                {
                    $ingredient_shipment->shipped_time = Utils::toMySQLDateTime($request->input('shipped_time'));
                }
                if(auth()->user()->hasRole(Constant::CONTRACT_MANUFACTURER))
                {
                    $ingredient_shipment->received_time = Utils::toMySQLDateTime($request->input('received_time'));
                }

                $ingredient_shipment->notes = $request->input('notes');
                $ingredient_shipment->created_by = auth()->user()->id;

                $ingredient_shipment->save();

                $ingredient_lot_ids = $request->input('ingredient_lot_ids');
                foreach($ingredient_lot_ids as $ingredient_lot_id)
                {
                    $shipment_lot = new ShipmentIngredientLot();
                    $shipment_lot->ingredient_shipment_id = $ingredient_shipment->id;
                    $shipment_lot->ingredient_lot_id = $ingredient_lot_id;
                    $shipment_lot->created_by = auth()->user()->id;
                    $shipment_lot->save();

                    if(auth()->user()->hasRole(Constant::INGREDIENT_SUPPLIER))
                    {
                        $tracking_event = new TrackingEvent();
                        $tracking_event->event_type = TrackingEvent::TYPE_OBJECT;
                        $tracking_event->action = TrackingEvent::ACTION_OBSERVE;
                        $tracking_event->business_step = TrackingEvent::BUSINESS_STEP_SHIPPING;
                        $tracking_event->disposition = TrackingEvent::DISPOSITION_IN_TRANSIT;
                        $tracking_event->source_id = $production_partner->id;
                        $tracking_event->destination_id = $ingredient_shipment->destination_id;
                        $tracking_event->object_type = TrackingEvent::OBJECT_INGREDIENT_LOT;
                        $tracking_event->object_id = $ingredient_lot_id;
                        $tracking_event->event_time = Utils::toMySQLDateTime($request->input('shipped_time'));
                        $tracking_event->created_by = auth()->user()->id;
                        $tracking_event->transaction = $shipment_lot->id;

                        $tracking_event->save();
                    }
                }
            }
            else
            {
                $ingredient_shipment->tracking_code = $request->input('tracking_code');
                if(auth()->user()->hasRole(Constant::INGREDIENT_SUPPLIER))
                {
                    $ingredient_shipment->source_id = $production_partner->id;
                }
                $ingredient_shipment->destination_id = $request->input('destination_id');
                if(auth()->user()->hasRole(Constant::INGREDIENT_SUPPLIER))
                {
                    $ingredient_shipment->shipped_time = Utils::toMySQLDateTime($request->input('shipped_time'));
                }
                if(auth()->user()->hasRole(Constant::CONTRACT_MANUFACTURER))
                {
                    $ingredient_shipment->received_time = Utils::toMySQLDateTime($request->input('received_time'));
                }
                $ingredient_shipment->notes = $request->input('notes');
                $ingredient_shipment->updated_by = auth()->user()->id;
                $ingredient_shipment->save();

                $ingredient_lot_ids = $request->input('ingredient_lot_ids');
                $old_ingredient_lots = ShipmentIngredientLot::where(['ingredient_shipment_id' => $ingredient_shipment->id, 'deleted' => 0])->get();
                foreach($old_ingredient_lots as $old_ingredient_lot)
                {
                    if(!(in_array($old_ingredient_lot->id, $ingredient_lot_ids)))
                    {
                        $old_ingredient_lot->deleted = 0;
                        $old_ingredient_lot->save();

                        $tracking_event = TrackingEvent::where(['transaction' => $old_ingredient_lot->id, 'deleted' => 0])->first();
                        $tracking_event->deleted = 0;
                        $tracking_event->save();
                    }
                }
                foreach($ingredient_lot_ids as $ingredient_lot_id)
                {
                    $shipment_lot = ShipmentIngredientLot::where(['ingredient_lot_id' => $ingredient_lot_id, 'deleted' => 0])->first();
                    if(!$shipment_lot)
                    {
                        $shipment_lot = new ShipmentIngredientLot();
                        $shipment_lot->ingredient_shipment_id = $ingredient_shipment->id;
                        $shipment_lot->ingredient_lot_id = $ingredient_lot_id;
                        $shipment_lot->created_by = auth()->user()->id;
                        $shipment_lot->save();
                    }

                    $tracking_event = TrackingEvent::where(['transaction' => $shipment_lot->id, 'deleted' => 0,
                        'business_step' => TrackingEvent::BUSINESS_STEP_SHIPPING, 'disposition' => TrackingEvent::DISPOSITION_IN_TRANSIT])->first();

                    if(auth()->user()->hasRole(Constant::INGREDIENT_SUPPLIER))
                    {
                        if(!$tracking_event)
                        {
                            $tracking_event = new TrackingEvent();
                            $tracking_event->event_type = TrackingEvent::TYPE_OBJECT;
                            $tracking_event->action = TrackingEvent::ACTION_OBSERVE;
                            $tracking_event->business_step = TrackingEvent::BUSINESS_STEP_SHIPPING;
                            $tracking_event->disposition = TrackingEvent::DISPOSITION_IN_TRANSIT;
                            $tracking_event->source_id = $production_partner->id;
                            $tracking_event->destination_id = $ingredient_shipment->destination_id;
                            $tracking_event->object_type = TrackingEvent::OBJECT_INGREDIENT_LOT;
                            $tracking_event->object_id = $ingredient_lot_id;
                            $tracking_event->event_time = Utils::toMySQLDateTime($request->input('shipped_time'));
                            $tracking_event->created_by = auth()->user()->id;
                            $tracking_event->transaction = $shipment_lot->id;

                            $tracking_event->save();
                        }
                        else
                        {
                            $tracking_event->destination_id = $ingredient_shipment->destination_id;
                            $tracking_event->event_time = Utils::toMySQLDateTime($request->input('shipped_time'));
                            $tracking_event->save();
                        }
                    }
                }
            }


            DB::commit();
            return json_encode([
                'ingredient_shipment_id' => $ingredient_shipment->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'ingredient_shipment_id' => 0
            ]);
        }
    }

    public function deleteIngredientShipments(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();
            $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();
            foreach($ids as $id)
            {
                $ingredient_shipment = IngredientShipment::find($id);
                if($ingredient_shipment->production_partner_id != $production_partner->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'result' => 0
                    ]);
                }
                if($ingredient_shipment)
                {
                    $ingredient_shipment->deleted = 1;
                    $ingredient_shipment->updated_by = auth()->user()->id;
                    $ingredient_shipment->save();
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

    public function getIngredientLotListForm() {
        return view( 'production-partner.ingredient-shipments.ingredient-lots' );
    }

    public function getIngredientLotsForShipping(Request $request) {
        $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();

        $pageSize = $request->input('pageSize');
        DB::statement("SET @sequence_number:=0");
        $ingredient_lots = DB::table('ingredient_lots')
            ->select('ingredient_lots.*', 'ingredients.name AS ingredient_name',
                DB::raw('@sequence_number:=@sequence_number+1 AS sequence_number'))
            ->join('ingredients', 'ingredient_lots.ingredient_id', '=', 'ingredients.id')
            ->leftJoin('shipment_ingredient_lots',  function($join)
            {
                $join->on('shipment_ingredient_lots.ingredient_lot_id', '=', 'ingredient_lots.id');
                $join->on('shipment_ingredient_lots.deleted', '=', DB::raw("0"));
            })
            ->leftJoin('ingredient_shipments',  function($join)
            {
                $join->on('shipment_ingredient_lots.ingredient_shipment_id', '=', 'ingredient_shipments.id');
                $join->on('ingredient_shipments.deleted', '=', DB::raw("0"));
            })
            ->where(['ingredient_lots.deleted' => 0, 'ingredient_lots.current_pp_id' => $production_partner->id])
            ->whereNull('shipment_ingredient_lots.id')
            ->orderBy('ingredient_lots.id', 'desc')->paginate( $pageSize );

        return response()->json($ingredient_lots);
    }

    public function getMemberContractManufacturers(Request $request){
        $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();

        $production_partners = DB::table('production_partners')
            ->select('production_partners.*')
            ->join('model_has_roles',  function($join)
            {
                $join->on('model_has_roles.model_id', '=', 'production_partners.user_id');
                $join->on('model_has_roles.model_type', '=', DB::raw("'App\\\User'"));
            })
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where(['production_partners.member_id' => $production_partner->member_id,
                'production_partners.deleted' => 0, 'roles.name' => Constant::CONTRACT_MANUFACTURER])
            ->orderBy('production_partners.id', 'desc')
            ->get();

        return response()->json($production_partners);
    }
}