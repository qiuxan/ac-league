<?php

namespace App\Http\Controllers\ProductionPartner;
use App\Constant;
use App\IngredientLot;
use App\ShipmentIngredientLot;
use App\TrackingEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\IngredientShipment;
use App\ProductionPartner;
use Exception;
use App\Utils;

class IngredientReceiptController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'production-partner.ingredient-receipts.index' );
    }

    public function getIngredientReceipts(Request $request) {
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
                ->where(['ingredient_shipments.source_id' => $production_partner->id, 'ingredient_shipments.deleted' => 0,
                'ingredient_shipments.destination_id' => $production_partner->id])
                ->orderBy('ingredient_shipments.id', 'desc')->paginate( $pageSize );
        }
        else if(auth()->user()->hasRole(Constant::CONTRACT_MANUFACTURER))
        {
            $ingredient_shipments = DB::table('ingredient_shipments')
                ->select('ingredient_shipments.*', 'destination.name_en AS destination_name',
                    'source.name_en AS source_name', DB::raw('@sequence_number:=@sequence_number+1 AS sequence_number'))
                ->leftJoin('production_partners AS source', 'ingredient_shipments.source_id', '=', 'source.id')
                ->leftJoin('production_partners AS destination', 'ingredient_shipments.destination_id', '=', 'destination.id')
                ->where(['ingredient_shipments.destination_id' => $production_partner->id, 'ingredient_shipments.deleted' => 0,
                    'ingredient_shipments.destination_id' => $production_partner->id])
                ->orderBy('ingredient_shipments.id', 'desc')->paginate( $pageSize );
        }
        else
        {
            $ingredient_shipments = DB::table('ingredient_shipments')
                ->select('ingredient_shipments.*', 'destination.name_en AS destination_name',
                    'source.name_en AS source_name', DB::raw('@sequence_number:=@sequence_number+1 AS sequence_number'))
                ->leftJoin('production_partners AS source', 'ingredient_shipments.source_id', '=', 'source.id')
                ->leftJoin('production_partners AS destination', 'ingredient_shipments.destination_id', '=', 'destination.id')
                ->where(['0' => 1, 'ingredient_shipments.deleted' => 0,
                    'ingredient_shipments.destination_id' => $production_partner->id])
                ->orderBy('ingredient_shipments.id', 'desc')->paginate( $pageSize );
        }

        return response()->json($ingredient_shipments);
    }

    public function getIngredientReceiptForm() {
        return view( 'production-partner.ingredient-receipts.form' );
    }

    public function getIngredientReceiptList() {
        return view( 'production-partner.ingredient-receipts.list' );
    }

    public function getReceiptIngredientLots(Request $request) {
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

    public function getIngredientReceipt(Request $request) {
        $id = $request->input('id');
        $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();

        if(auth()->user()->hasRole(Constant::CONTRACT_MANUFACTURER))
        {
            $batch = DB::table('ingredient_shipments')
                ->select('ingredient_shipments.*', 'destination.name_en AS destination_name', 'source.name_en AS source_name')
                ->leftJoin('production_partners AS source', 'ingredient_shipments.source_id', '=', 'source.id')
                ->leftJoin('production_partners AS destination', 'ingredient_shipments.destination_id', '=', 'destination.id')
                ->where(['ingredient_shipments.id' => $id, 'destination_id' => $production_partner->id,
                    'ingredient_shipments.deleted' => 0, 'ingredient_shipments.destination_id' => $production_partner->id])
                ->first();
        }
        else
        {
            $batch = DB::table('ingredient_shipments')
                ->select('ingredient_shipments.*', 'destination.name_en AS destination_name', 'source.name_en AS source_name')
                ->leftJoin('production_partners AS source', 'ingredient_shipments.source_id', '=', 'source.id')
                ->leftJoin('production_partners AS destination', 'ingredient_shipments.destination_id', '=', 'destination.id')
                ->where(['0' => 1, 'ingredient_shipments.deleted' => 0, 'ingredient_shipments.destination_id' => $production_partner->id])
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
                DB::rollBack();
                return json_encode([
                    'ingredient_shipment_id' => 0
                ]);
            }
            else
            {
                if(auth()->user()->hasRole(Constant::CONTRACT_MANUFACTURER))
                {
                    $ingredient_shipment->received_time = Utils::toMySQLDateTime($request->input('received_time'));
                }
                $ingredient_shipment->notes = $request->input('notes');
                $ingredient_shipment->updated_by = auth()->user()->id;
                $ingredient_shipment->save();

                $shipment_lots = ShipmentIngredientLot::where(['ingredient_shipment_id' => $ingredient_shipment->id, 'deleted' => 0])->get();
                foreach($shipment_lots as $shipment_lot)
                {
                    $tracking_event = TrackingEvent::where(['transaction' => $shipment_lot->id, 'deleted' => 0,
                    'business_step' => TrackingEvent::BUSINESS_STEP_RECEIVING, 'disposition' => TrackingEvent::DISPOSITION_IN_TRANSIT])->first();

                    if(auth()->user()->hasRole(Constant::INGREDIENT_SUPPLIER))
                    {
                        if(!$tracking_event && $request->input('received_time'))
                        {
                            $tracking_event = new TrackingEvent();
                            $tracking_event->event_type = TrackingEvent::TYPE_OBJECT;
                            $tracking_event->action = TrackingEvent::ACTION_OBSERVE;
                            $tracking_event->business_step = TrackingEvent::BUSINESS_STEP_RECEIVING;
                            $tracking_event->disposition = TrackingEvent::DISPOSITION_IN_TRANSIT;
                            $tracking_event->source_id = $production_partner->id;
                            $tracking_event->destination_id = $ingredient_shipment->destination_id;
                            $tracking_event->object_type = TrackingEvent::OBJECT_INGREDIENT_LOT;
                            $tracking_event->object_id = $shipment_lot->ingredient_lot_id;
                            $tracking_event->event_time = Utils::toMySQLDateTime($request->input('received_time'));
                            $tracking_event->created_by = auth()->user()->id;
                            $tracking_event->transaction = $shipment_lot->id;

                            $tracking_event->save();

                            $ingredient_lot = IngredientLot::find($shipment_lot->ingredient_lot_id);
                            $ingredient_lot->current_pp_id = $production_partner->id;
                            $ingredient_lot->save();
                        }
                        else
                        {
                            if($request->input('received_time'))
                            {
                                $tracking_event->event_time = Utils::toMySQLDateTime($request->input('received_time'));
                                $tracking_event->save();
                            }
                            else
                            {
                                $tracking_event->deleted = 1;
                                $tracking_event->save();

                                $ingredient_lot = IngredientLot::find($shipment_lot->ingredient_lot_id);
                                $ingredient_lot->current_pp_id = $ingredient_lot->production_partner_id;
                                $ingredient_lot->save();
                            }
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
}