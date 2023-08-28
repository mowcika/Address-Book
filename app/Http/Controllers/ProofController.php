<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProofMaster;
use Illuminate\Support\Facades\DB;

class ProofController extends Controller
{
    //
    public function getProof()
    {
        $result = array();
        $personProof = DB::connection('read')->table('proof_master')
            ->select('id as value', 'proof_name as text')
            ->whereIn('id', [11, 12, 13, 14, 15])
            ->get();
        $companyProof = DB::connection('read')->table('proof_master')
            ->select('id as value', 'proof_name as text')
            ->whereIn('id', [16])
            ->get();
        $industry_type = DB::connection('read')->table('industry_type_master')
            ->select('id as value', 'industry_type as text')
            ->orWhere(function ($query) {
                $query->where('industry_type', '<>', 'Person Proof')
                    ->where('industry_type', '<>', 'Company ID Proof');
            })
            ->get();
        $company_type = DB::connection('read')->table('company_type')
            ->select('id as value', 'name as text')
            ->get();
        $result['personProof'] = $personProof;
        $result['companyProof'] = $companyProof;
        $result['industry_type'] = $industry_type;
        $result['company_type'] = $company_type;
        return $result;
    }

    public function getState(Request $request)
    {

        $state = DB::connection('read')->table('state_master')
            ->select('id as value', 'name as text')
//            ->where('industry_type', '<>', 'Person Proof')
            ->get();

        $result['state'] = $state;
        return $result;
    }

    public function getDist(Request $request)
    {
        $dist = DB::connection('read')->table('dist')
            ->select('dist_id as value', 'dist as text')
            ->where('state', '=', $request->value)
            ->get();

        $result['dist'] = $dist;
        return $result;
    }

    public function getCity(Request $request)
    {
//        echo $request;exit;

        $areas = DB::connection('read')->table('areas')
            ->select('id as value', 'area_name as text')
            ->where('dist_id', '=', $request->value)
            ->where('deleted', '=', '0')
            ->get();

        $result['areas'] = $areas;
        return $result;
    }

    public function getAddressProof(Request $request)
    {
//       $val= $request->value;


        $proof_master = DB::connection('read')->table("proof_master")
            ->select('proof_master.id as value', 'proof_master.proof_name as text')
            ->leftjoin("industry_type_master", DB::raw("FIND_IN_SET(proof_master.id,industry_type_master.proof_id)"), ">",
                DB::raw("'0'"))
            ->where('industry_type_master.id', '=', $request->value)
            ->get();

        $result['addressProof'] = $proof_master;
        return $result;
    }
}
