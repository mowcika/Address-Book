<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;

class fcEmployerCreationController extends Controller
{
    // employer creation
    public function getEmployerCreationList(Request $request)
    {
        $date = $request->date;
        $sDate = date("Y-m-d", strtotime($date['startDate']));
        $eDate = date("Y-m-d", strtotime($date['endDate']));
        $dates = array();
        $whereInDate = 1;
        if ($date != '') {
            $dates[] = "date(fpct.indate) BETWEEN '$sDate' and '$eDate'";
        }
        if (count($dates)) {
            $whereInDate = implode(' AND ', $dates);
        }
        $groupWhere = "(fpct.tempJobId !=0 OR fpct.tempJobId != 0)";
        $getData['getData'] = DB::table('freePaidCallingToEmpr as fpct')
            ->selectRaw("fpct.id, fpct.employer_id, fpct.empName, fpct.designation, fpct.phone, fpct.nameEnglish, fpct.nameTamil, fpct.address, fpct.city, fpct.district, CAST(fpct.pincode as UNSIGNED) as pincode, fpct.companyType, fpct.paidType, fpct.paymentProof, fpct.selectedPlan, fpct.referenceNumber, fpct.state, '' as id_proof, '' as company_proof, u.uname as inby, DATE_FORMAT(fpct.indate, '%d-%m-%Y %r') as indate")
            ->leftJoin('user as u', 'u.id', '=', 'fpct.inby')
            ->where('fpct.employer_id', '=', 0)
            ->where('fpct.lastEventStatus', '=', 0)
            ->whereRaw($whereInDate)
            ->where('fpct.isHold', '=', 0)
            ->whereRaw($groupWhere)
            ->get();
        return $getData;
    }
}
