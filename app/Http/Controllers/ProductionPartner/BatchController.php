<?php

namespace App\Http\Controllers\ProductionPartner;
use App\BatchIngredient;
use App\Code;
use App\Disposition;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Batch;
use App\ProductionPartner;
use Exception;
use App\Utils;

class BatchController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'production-partner.batches.index' );
    }

    public function getBatches(Request $request) {
        $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();

        $filters = $request->input('filters');
        $whereRaw = " batches.deleted = 0 AND batches.production_partner_id = {$production_partner->id} ";
        $binding_array = array();
        if($filters['search'])
        {
            $binding_array['search_string1'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string2'] = strtolower("%{$filters['search']}%");
            $whereRaw .= " AND (LOWER(batches.batch_code) LIKE :search_string1 ";
            $whereRaw .= " OR LOWER(products.name_en) LIKE :search_string2)";
        }

        $pageSize = $request->input('pageSize');
        $batches = DB::table('batches')->select('batches.*', 'dispositions.disposition', 'members.company_en',
            'products.name_en AS product_name', 'resellers.name_en AS reseller')
            ->leftJoin('members','batches.member_id','=','members.id')
            ->leftJoin('products','batches.product_id','=','products.id')
            ->leftJoin('dispositions','batches.disposition_id','=','dispositions.id')
            ->leftJoin('production_partners AS resellers','batches.reseller_id','=','resellers.id')
            ->whereRaw($whereRaw, $binding_array)
            ->orderBy('id', 'desc')->paginate( $pageSize );

        return response()->json($batches);
    }

    public function getBatchForm() {
        $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $dispositions = Disposition::where('deleted', 0)->orderBy('id', 'asc')->get();
        $resellers = ProductionPartner::where([
            'member_id' => $production_partner->member_id,
            'deleted'   => 0
        ])->get();
        return view( 'production-partner.batches.form', compact('dispositions', 'resellers') );
    }

    public function getBatchList() {
        return view( 'production-partner.batches.list' );
    }

    public function getBatch(Request $request) {
        $id = $request->input('id');
        $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $batch = DB::table('batches')->select('batches.*')->where(['batches.id' => $id, 'batches.production_partner_id' => $production_partner->id, 'deleted' => 0])->first();

        return response()->json($batch);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $batch_id = $request->input('id');
            $batch = Batch::find($batch_id);
            $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();
            if(!$batch)
            {
                $batch = new Batch();
                $batch->member_id = $production_partner->member_id;
                $batch->created_by = auth()->user()->id;
            }
            else
            {
                if($batch->production_partner_id != $production_partner->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'batch_id' => 0
                    ]);
                }
                $batch->updated_by = auth()->user()->id;
            }
            $batch->production_partner_id = $production_partner->id;

            if($request->input('product_id'))
            {
                $batch->product_id = $request->input('product_id');
                if(!($batch->id > 0))
                {
                    $batch->disposition_id = Disposition::ACTIVE;
                }
                else
                {
                    if($batch->disposition_id != $request->input('disposition_id'))
                    {
                        //update roll codes here
                        DB::table('codes')
                            ->where('codes.batch_id', '=', $batch->id)
                            ->where('codes.disposition_id', '=', $batch->disposition_id)
                            ->update(['codes.disposition_id' => $request->input('disposition_id'), 'codes.updated_at' => Carbon::now()]);
                    }
                    $batch->disposition_id = $request->input('disposition_id');
                }
            }
            else
            {
                $batch->product_id = 0;
                $batch->disposition_id = 0;
            }

            // update the batch reseller

            if($batch->reseller_id != $request->input('reseller_id'))
            {
                //update roll codes here
                DB::table('codes')
                    ->where('codes.batch_id', '=', $batch->id)
                    ->update(['codes.reseller_id' => $request->input('reseller_id')]);
            }
            $batch->reseller_id = $request->input('reseller_id');

            $batch->quantity = $request->input('quantity');
            $batch->location = $request->input('location');
            $batch->batch_code = $request->input('batch_code');
            $batch->production_date = Utils::toMySQLDate($request->input('production_date'));
            $batch->expiration_date = Utils::toMySQLDate($request->input('expiration_date'));

            $batch->save();

            DB::commit();
            return json_encode([
                'batch_id' => $batch->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'batch_id' => 0
            ]);
        }
    }

    public function deleteBatches(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();
            $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();
            foreach($ids as $id)
            {
                $batch = Batch::find($id);
                if($batch->production_partner_id != $production_partner->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'result' => 0
                    ]);
                }
                if($batch)
                {
                    $batch->deleted = 1;
                    $batch->save();

                    $codes = Code::where(['batch_id' => $batch->id])->get();
                    foreach($codes as $code)
                    {
                        $code->batch_id = 0;
                        $code->reseller_id = 0;
                        $code->save();
                    }
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

    public function getIngredientLotListForBatch() {
        return view( 'production-partner.batches.ingredient-lots' );
    }

    public function getBatchIngredients(Request $request) {
        $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();

        $pageSize = $request->input('pageSize');
        $batch_id = $request->input('batch_id');
        DB::statement("SET @sequence_number:=0");
        $ingredient_lots = DB::table('batch_ingredients')
            ->select('ingredient_lots.*', 'ingredients.name AS ingredient_name', 'batch_ingredients.id', 'batch_ingredients.ingredient_lot_id',
                DB::raw('@sequence_number:=@sequence_number+1 AS sequence_number'))
            ->join('ingredient_lots', 'batch_ingredients.ingredient_lot_id', '=', 'ingredient_lots.id')
            ->join('ingredients', 'ingredient_lots.ingredient_id', '=', 'ingredients.id')
            ->where(['batch_ingredients.deleted' => 0, 'ingredient_lots.current_pp_id' => $production_partner->id,
            'batch_ingredients.batch_id' => $batch_id])
            ->orderBy('ingredient_lots.id', 'desc')->paginate( $pageSize );

        return response()->json($ingredient_lots);
    }

    public function getIngredientLotsForBatch(Request $request) {
        $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();

        $pageSize = $request->input('pageSize');
        $batch_id = $request->input('batch_id');
        $batch = Batch::find($batch_id);
        DB::statement("SET @sequence_number:=0");
        $ingredient_lots = DB::table('ingredient_lots')
            ->select('ingredient_lots.*', 'ingredients.name AS ingredient_name',
                DB::raw('@sequence_number:=@sequence_number+1 AS sequence_number'))
            ->join('ingredients', 'ingredient_lots.ingredient_id', '=', 'ingredients.id')
            ->join('product_ingredients',  function($join) use($batch)
            {
                $join->on('ingredient_lots.ingredient_id','=', 'product_ingredients.ingredient_id');
                $join->on('ingredient_lots.finished', '=', DB::raw(0));
                $join->on('product_ingredients.product_id', '=', DB::raw($batch->product_id));
            })
            ->where(['ingredient_lots.deleted' => 0, 'ingredient_lots.current_pp_id' => $production_partner->id,
            'finished' => 0])
            ->orderBy('ingredient_lots.id', 'desc')->paginate( $pageSize );

        return response()->json($ingredient_lots);
    }

    public function addBatchIngredients(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();
            $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();
            $batch = Batch::find($request->input('batch_id'));
            foreach($ids as $id)
            {
                if($batch->production_partner_id != $production_partner->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'result' => 0
                    ]);
                }
                $batch_ingredient = BatchIngredient::where(['ingredient_lot_id' => $id, 'batch_id' => $batch->id, 'deleted' => 0])->first();
                if(!$batch_ingredient)
                {
                    $batch_ingredient = new BatchIngredient();
                    $batch_ingredient->ingredient_lot_id = $id;
                    $batch_ingredient->batch_id = $batch->id;
                    $batch_ingredient->created_by = auth()->user()->id;

                    $batch_ingredient->save();
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

    public function deleteBatchIngredients(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();
            $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();
            foreach($ids as $id)
            {
                $batch_ingredient = BatchIngredient::find($id);
                $batch = Batch::find($batch_ingredient->batch_id);
                if($batch->production_partner_id != $production_partner->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'result' => 0
                    ]);
                }
                if($batch_ingredient)
                {
                    $batch_ingredient->deleted = 1;
                    $batch_ingredient->save();
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