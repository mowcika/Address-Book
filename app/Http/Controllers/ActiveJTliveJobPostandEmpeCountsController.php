<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActiveJTliveJobPostandEmpeCountsController extends Controller
{
    public function getlivepostandempeecounts(Request $request)
    {
        $where_jt = $where_pdate = array();
        $where_jt_in = $where_pdate_in = 1;
        $active_jt_id = $request->jt;
        $posted_date = $request->pdate;
        if ($posted_date['startDate']) {
            $fromdate = date("Y-m-d", strtotime($posted_date['startDate']));
            $todate = date("Y-m-d", strtotime($posted_date['endDate']));
            $where_pdate[] = "date(j.starting) between '$fromdate' and  '$todate' ";
            if (count($where_pdate)) {
                $where_pdate_in = implode(' AND', $where_pdate);
            }
        }


        if (!in_array('all', $active_jt_id)) {
            $jt_arr = implode(",", $active_jt_id);
            $where_jt[] = "j.jobtitle_id IN ($jt_arr) ";
        }
        if (count($where_jt)) {
            $where_jt_in = implode(' AND', $where_jt);
        }

        // jobtitle with employee counts
        $empeeCnt = DB::connection('read')->table('jobtitle as jt')
            ->leftJoin('user_job_category as ujc', 'ujc.sub_cat', '=', 'jt.id')
            ->leftJoin('employee as e', 'e.id', '=', 'ujc.user_id')
            ->selectRaw("ujc.sub_cat, count(e.id) as ct")
            ->where([['jt.active_title', 1], ['jt.deleted', 0], ['e.is_deleted', 0]])
            ->groupBy('ujc.sub_cat')
            ->get();

        $indate = date('Y-m-d');
        $livepost = DB::connection('read')->table('jobs as j')
            ->join('jobtitle as jt', 'jt.id', '=', 'j.jobtitle_id')
            ->leftJoin('employer as e', 'j.employerid', '=', 'e.id')
            ->leftJoin('employer_registration as er', 'er.company_details', '=', 'e.id')
            ->leftJoin('executive_employer as ee', 'e.id', '=', 'ee.employer_id')
            ->leftJoin("franchise_company_debits as fcd", function ($join) {
                $join->on("j.job_plan_id", "=", "fcd.debit_id")
                    ->on("fcd.status", "=", DB::raw("'TXN_SUCCESS'"));
            })
            ->selectRaw("j.jobtitle_id,
            concat(jt.tamil, ' ', jt.english) as jtName, count(`j`.`id`) as `livepost`")
//            ->whereBetween('j.ending', ['2023-04-01', '2023-04-06'])
            ->whereDate('j.ending', '>=', $indate)
            ->where([['jt.deleted', 0], ['jt.active_title', 1], ['j.deleted', 0], ['j.blocked', 0], ['j.jobtype', 1]])
            ->whereRaw($where_jt_in)
            ->whereRaw($where_pdate_in)
            ->whereIn('j.post_live_purpose', [1, 2, 3])
            ->groupBy('jt.id')
            ->orderBy('j.jobtitle_id', 'asc')
            ->get();
        $lp_jt = $ec_cnt = $cmm_mrg = array();
        $livepost = json_decode(json_encode($livepost), true);
        $empeeCnt = json_decode(json_encode($empeeCnt), true);

        foreach ($livepost as $lp_key => $lp_value) {
            foreach ($empeeCnt as $ec_key => $ec_value) {
                if ($lp_value['jobtitle_id'] == $ec_value['sub_cat']) {
                    $cmm_mrg[] = $lp_value + $ec_value;
                }
            }
        }
        $result['getData'] = $cmm_mrg;
        return $result;
    }
}
