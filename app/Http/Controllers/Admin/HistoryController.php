<?php

namespace App\Http\Controllers\Admin;
use App\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;
use App\Utils;

class HistoryController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $member = Member::where(['members.user_id' => auth()->user()->id, 'deleted' => 0])->first();
        $whereRaw = " batches.deleted = 0 ";

        $members = DB::table('histories')->select(DB::raw('DISTINCT members.id, members.company_en AS name'))
        ->join('codes','histories.code_id','=','codes.id')
        ->join('batches','codes.batch_id','=','batches.id')
        ->join('members','batches.member_id','=','members.id')
        ->whereRaw($whereRaw)
        ->orderBy('members.company_en', 'asc')
        ->get();

        $batches = DB::table('histories')->select(DB::raw('DISTINCT batches.id, batches.batch_code'))
            ->join('codes','histories.code_id','=','codes.id')
            ->join('batches','codes.batch_id','=','batches.id')
            ->whereRaw($whereRaw)
            ->orderBy('batches.batch_code', 'asc')
            ->get();

        $products = DB::table('histories')->select(DB::raw('DISTINCT products.id, products.name_en AS name'))
            ->join('codes','histories.code_id','=','codes.id')
            ->join('batches','codes.batch_id','=','batches.id')
            ->join('products','batches.product_id','=','products.id')
            ->whereRaw($whereRaw)
            ->orderBy('products.name_en', 'asc')
            ->get();

        $locations = DB::table('histories')->select(DB::raw('DISTINCT locations.id, locations.location'))
            ->join('codes','histories.code_id','=','codes.id')
            ->join('batches','codes.batch_id','=','batches.id')
            ->join('locations','histories.location_id','=','locations.id')
            ->whereRaw($whereRaw)
            ->orderBy('locations.location', 'asc')
            ->get();

        $resellers = DB::table('histories')->select(DB::raw('DISTINCT production_partners.id, production_partners.name_en AS name'))
            ->join('codes','histories.code_id','=','codes.id')
            ->join('batches','codes.batch_id','=','batches.id')
            ->join('production_partners','codes.reseller_id','=','production_partners.id')
            ->whereRaw($whereRaw)
            ->orderBy('production_partners.name_en', 'asc')
            ->get();

        return view( 'admin.histories.index', compact(['members', 'batches', 'products', 'locations', 'resellers']) );
    }

    public function getHistories(Request $request) {
        $pageSize = $request->input('pageSize');

        $sorts = $request->input('sort');
        $sort_field = '';
        $sort_dir = '';
        if(isset($sorts[0]))
        {
            $sort_field = $sorts[0]['field'];
            if($sort_field == 'id')
            {
                $sort_field = 'histories.id';
            }
            $sort_dir = $sorts[0]['dir'];
        }
        if(!$sort_field)
        {
            $sort_field = 'histories.id';
        }
        if(!$sort_dir)
        {
            $sort_dir = 'desc';
        }

        $whereRaw = " batches.deleted = 0 ";

        $filters = $request->input('filters');

        $binding_array = array();

        if($filters['search'])
        {
            $binding_array['search_string1'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string2'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string3'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string4'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string5'] = strtolower("%{$filters['search']}%");
            $binding_array['search_string6'] = strtolower("%{$filters['search']}%");
            $whereRaw .= " AND (LOWER(codes.full_code) LIKE :search_string1 ";
            $whereRaw .= "      OR LOWER(batches.batch_code) LIKE :search_string2 ";
            $whereRaw .= "      OR LOWER(products.name_en) LIKE :search_string3 ";
            $whereRaw .= "      OR LOWER(locations.location) LIKE :search_string4 ";
            $whereRaw .= "      OR LOWER(production_partners.name_en) LIKE :search_string5 ";
            $whereRaw .= "      OR LOWER(members.company_en) LIKE :search_string6 ) ";
        }
        if($filters['from_date'])
        {
            $binding_array['from_date'] = Utils::toMySQLDate($filters['from_date']);
            $whereRaw .= " AND DATE(histories.created_at) >= :from_date ";
        }
        if($filters['to_date'])
        {
            $binding_array['to_date'] = Utils::toMySQLDate($filters['to_date']);
            $whereRaw .= " AND DATE(histories.created_at) <= :to_date ";
        }
        if($filters['member_id'])
        {
            $binding_array['member_id'] = $filters['member_id'];
            $whereRaw .= " AND members.id = :member_id ";
        }
        if($filters['batch_id'])
        {
            $binding_array['batch_id'] = $filters['batch_id'];
            $whereRaw .= " AND codes.batch_id = :batch_id ";
        }        
        if($filters['product_id'])
        {
            $binding_array['product_id'] = $filters['product_id'];
            $whereRaw .= " AND batches.product_id = :product_id ";
        }
        if($filters['reseller_id'])
        {
            $binding_array['reseller_id'] = $filters['reseller_id'];
            $whereRaw .= " AND codes.reseller_id = :reseller_id ";
        }
        if($filters['location_id'])
        {
            $binding_array['location_id'] = $filters['location_id'];
            $whereRaw .= " AND histories.location_id = :location_id ";
        }
        if($filters['language'])
        {
            $binding_array['language'] = "{$filters['language']}";
            $whereRaw .= " AND histories.language = :language ";
        }
        if($filters['status'] || $filters['status'] === '0')
        {
            $binding_array['status'] = $filters['status'];
            $whereRaw .= " AND histories.status = :status ";
        }

        $codes = DB::table('histories')->select('codes.full_code', 'members.company_en', 'batches.batch_code', 'products.name_en AS name',
            'histories.created_at', 'histories.lat', 'histories.lng', 'histories.status',
            'production_partners.name_en AS reseller', 'locations.location',
            DB::raw("CASE histories.language
                        WHEN 'cn' THEN 'Simplified Chinese'
                        WHEN 'tr' THEN 'Traditional Chinese'
                        ELSE 'English'
                     END AS language"),
            DB::raw("CASE histories.status
                        WHEN 0 THEN 'Verified'
                        WHEN 1 THEN 'Authentic'
                        ELSE 'Failed'
                     END AS status"))
            ->join('codes','histories.code_id','=','codes.id')
            ->join('batches','codes.batch_id','=','batches.id')
            ->join('members','batches.member_id','=','members.id')
            ->join('products','batches.product_id','=','products.id')
            ->leftJoin('production_partners','codes.reseller_id','=','production_partners.id')
            ->leftJoin('locations','histories.location_id','=','locations.id')
            ->whereRaw($whereRaw, $binding_array)
            ->orderBy($sort_field, $sort_dir)->paginate($pageSize);

        return response()->json($codes);
    }
}