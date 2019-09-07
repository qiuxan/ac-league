<?php

namespace App\Http\Controllers\ProductionPartner;
use App\Constant;
use App\Ingredient;
use App\ProductionPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Exception;
use App\Utils;

class IngredientController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view( 'production-partner.ingredients.index' );
    }

    public function getIngredients(Request $request) {
        $production_partner = ProductionPartner::where(['production_partners.user_id' => auth()->user()->id, 'deleted' => 0])->first();

        $pageSize = $request->input('pageSize');

        if(Auth::user()->hasRole(Constant::INGREDIENT_SUPPLIER))
        {
            $ingredients = DB::table('ingredients')->select('ingredients.*')
                ->where(['ingredients.production_partner_id' => $production_partner->id, 'ingredients.deleted' => 0])
                ->orderBy('id', 'desc')->paginate( $pageSize );
        }
        else if(Auth::user()->hasRole(Constant::CONTRACT_MANUFACTURER))
        {
            $ingredients = DB::table('ingredients')->select('ingredients.*')
                ->where(['ingredients.member_id' => $production_partner->member_id, 'ingredients.deleted' => 0])
                ->orderBy('id', 'desc')->paginate( $pageSize );
        }

        return response()->json($ingredients);
    }

    public function getIngredientsForDropdown(Request $request) {
        $production_partner = ProductionPartner::where(['production_partners.user_id' => auth()->user()->id, 'deleted' => 0])->first();

        if(Auth::user()->hasRole(Constant::INGREDIENT_SUPPLIER))
        {
            $ingredients = DB::table('ingredients')->select('ingredients.*')
                ->where(['ingredients.production_partner_id' => $production_partner->id, 'ingredients.deleted' => 0])
                ->orderBy('id', 'desc')->get();
        }
        else if(Auth::user()->hasRole(Constant::CONTRACT_MANUFACTURER))
        {
            $ingredients = DB::table('ingredients')->select('ingredients.*')
                ->where(['ingredients.member_id' => $production_partner->member_id, 'ingredients.deleted' => 0])
                ->orderBy('id', 'desc')->get();
        }

        return response()->json($ingredients);
    }

    public function getIngredientForm() {
        return view( 'production-partner.ingredients.form' );
    }

    public function getIngredientList() {
        return view( 'production-partner.ingredients.list' );
    }

    public function getIngredient(Request $request) {
        $id = $request->input('id');
        $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();
        if(Auth::user()->hasRole(Constant::INGREDIENT_SUPPLIER))
        {
            $ingredient = DB::table('ingredients')->select('ingredients.*')
                ->where(['ingredients.production_partner_id' => $production_partner->id, 'ingredients.deleted' => 0])
                ->first();
        }
        else if(Auth::user()->hasRole(Constant::CONTRACT_MANUFACTURER))
        {
            $ingredient = DB::table('ingredients')->select('ingredients.*')
                ->where(['ingredients.member_id' => $production_partner->member_id, 'ingredients.deleted' => 0])
                ->first();
        }

        return response()->json($ingredient);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $production_partner = ProductionPartner::where(['production_partners.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            $product_id = $request->input('id');
            $product = Ingredient::find($product_id);
            if(!$product)
            {
                $product = new Ingredient();
                $product->production_partner_id = $production_partner->id;
                $product->member_id = $production_partner->member_id;
                $product->created_by = auth()->user()->id;
            }
            else
            {
                if($product->production_partner_id != $production_partner->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'ingredient_id' => 0
                    ]);
                }
                $product->updated_by = auth()->user()->id;
            }
            $product->fill($request->all());

            $product->save();

            DB::commit();
            return json_encode([
                'ingredient_id' => $product->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'ingredient_id' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function deleteIngredients(Request $request) {
        $ids = $request->input('ids');
        try {
            DB::beginTransaction();
            $production_partner = ProductionPartner::where(['production_partners.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            foreach($ids as $id)
            {
                $product = Ingredient::find($id);
                if($product->production_partner_id != $production_partner->id)
                {
                    DB::rollBack();
                    Utils::trace("Invalid access!");
                    return json_encode([
                        'result' => 0
                    ]);
                }
                if($product)
                {
                    $product->deleted = 1;
                    $product->save();
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

    public function getAllIngredients(Request $request) {
        $production_partner = ProductionPartner::where(['user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $products = Ingredient::where(['deleted'=>0, 'production_partner_id'=>$production_partner->id])->orderBy('name_en', 'asc')->get();

        return response()->json($products);
    }
}