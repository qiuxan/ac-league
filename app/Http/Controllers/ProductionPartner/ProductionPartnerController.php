<?php

namespace App\Http\Controllers\ProductionPartner;
use App\ProductionPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;
use App\Utils;

class ProductionPartnerController
{
    public function getProductionPartnerForm() {
        return view( 'production-partner.production-partners.form' );
    }

    public function getProductionPartner(Request $request) {
        $production_partner = DB::table('production_partners')->select('production_partners.*','users.name','users.email','users.avatar')->join('users','production_partners.user_id','=','users.id')->where(['production_partners.user_id' => auth()->user()->id])->first();

        return response()->json($production_partner);
    }

    public function store( Request $request )
    {
        try {
            DB::beginTransaction();
            $user_id = $request->input('user_id');
            $user = User::find($user_id);

            if(!$user || $user->id != auth()->user()->id)
            {
                DB::rollBack();
                Utils::trace("Invalid request.");
                return json_encode([
                    'production_partner_id' => 0
                ]);
            }

            if ($request->input('password')) {
                $user->password = bcrypt($request->input('password'));
            }
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->avatar = $request->input('avatar');

            $user->save();

            $production_partner = ProductionPartner::where(['production_partners.user_id' => auth()->user()->id, 'deleted' => 0])->first();
            $production_partner->updated_by = auth()->user()->id;

            $production_partner->fill($request->all());

            $production_partner->save();

            DB::commit();
            return json_encode([
                'production_partner_id' => $production_partner->id
            ]);
        }
        catch (Exception $e){
            DB::rollBack();
            Utils::trace($e->getMessage());
            return json_encode([
                'production_partner_id' => 0
            ]);
        }
    }
}
