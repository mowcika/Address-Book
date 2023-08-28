<?php

namespace App\Http\Controllers;

use App\Models\ExecutiveEmployerModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

//use Illuminate\Support\Facades\Cache;

class ExecutiveMasterCtrl extends Controller
{
    public function getExecutiveEmployer(Request $request)
    {
//        ->where([
//        ['e.type', 1],
//        ['e.deleted', 0],
//        ['e.is_block', 0],
//        ['ex.user_id', 19],
//    ])
        $where[] = ['e.type', 1];
        $where[] = ['e.deleted', 0];
        $where[] = ['e.is_block', 0];
        $userid = $request->inby;
        $filterTableUser = $request->filterTableUser;
        $assignFilter = $request->assignFilter;
//        print_r($assignFilter);exit;
//        echo $userid;
//        $userid = 19;
//        $userid = 146;
        $admin_array = array("2", '156', '203', '228');
        if (in_array($userid, $admin_array)) {

        } else {
            $where[] = ['ex.user_id', $userid];
        }
        $userconditon = '1';
        if ($filterTableUser || $assignFilter) {
//            print_r($assignFilter);exit;
            if ($filterTableUser) {
                $userFilter = implode(',', $filterTableUser);
                $userconditon = "ex.user_id in ($userFilter)";
            } elseif ($assignFilter) {
                $assignFilter = $assignFilter == 2 ? '0' : '1';
                $where[] = ['ex.assigned', $assignFilter];
            }

//            foreach ($filterTableUser as $keyvalue => $userIds) {
//                $where[] = ['ex.user_idd', $userIds];
//
//            }
            $redies_key = "executive_employer_$userid";

            $data = DB::connection('read')->table('employer as e')
                ->leftJoin('employer_registration as er', 'e.employer', '=', 'er.id')
                ->leftJoin('executive_employer_laravel as ex', 'e.id', '=', 'ex.employer_id')
                ->leftJoin('jobs as j', 'e.id', '=', 'j.employerid')
                ->leftJoin('franchise_company_debits as fcds', 'ex.employer_id', '=', 'fcds.employer_id')
                ->select('ex.id', 'e.whatsapp', 'company-name as cname', 'ex.employer_id', 'er.phonenumber', 'j.phone_new', 'e.company_type', 'ex.user_id as userid', 'ex.user_id as user_name', 'ex.last_followup_status', 'ex.last_feedback', 'ex.last_target_source', 'ex.last_mindset', 'e.city as district', 'ex.assigned', 'e.is_block as companyStatus')
                ->selectRaw("count(distinct j.id) as overallJobPost")
                ->selectRaw("count(distinct fcds.debit_id) as overallpaymentCount")
                ->selectRaw("sum(fcds.txn_amt)*count(DISTINCT fcds.debit_id)/count(*) as overallpaymentAMt")
                ->selectRaw("DATE_FORMAT(max(date(j.ending)), '%d-%m-%Y') as jobExpiryDate")
                ->selectRaw("MAX(j.posteddate) as last")
                ->selectRaw(" CASE
           WHEN max(date(j.ending)) >= CURRENT_DATE() THEN 'Live'
           WHEN max(date(j.ending)) <= CURRENT_DATE() THEN 'Expiry'
           ELSE 'Not Post' END   as jobstatus")
                ->selectRaw(" CASE
             WHEN sum(fcds.txn_amt) = '0' THEN 'Free'
            WHEN sum(fcds.txn_amt) > '1' THEN 'Paid'
             WHEN max(date(j.ending)) <= CURRENT_DATE() THEN 'Free'
            ELSE 'Not Service Use' END  as paymentstatus")
                ->selectRaw(" DATE_FORMAT(er.indate, '%d-%m-%Y')   as empregDate")
                ->selectRaw(" DATE_FORMAT(MAX(j.posteddate), '%d-%m-%Y')    as lastpostdate")
                ->selectRaw(" date_format( MAX(j.ending), '%d-%m-%Y')   as lastexpiry")
                ->selectRaw(" date_format(`e`.`indate`, '%d-%m-%Y')   as employerindate")
                ->where($where)
                ->whereRaw(" er.`phonenumber` NOT LIKE '%!_0%' ESCAPE '!'")
                ->whereRaw($userconditon)
//            ->whereRaw(" ex.last_feedback > 0")
                ->groupBy('e.id')
                ->get();
            $result['executive'] = $data;

            $result['from'] = 'database';
            $result['key'] = $redies_key;
//            Redis::set($redies_key, serialize($result));
//            Redis::expire($redies_key, 86400);

        } else {


            $redies_key = "executive_employer_$userid";
            if (Redis::exists($redies_key)) {
                $result = unserialize(Redis::get($redies_key));
                $result['from'] = 'redis';
                $result['key'] = $redies_key;
            } else {
                $data = DB::connection('read')->table('employer as e')
                    ->leftJoin('employer_registration as er', 'e.employer', '=', 'er.id')
                    ->leftJoin('executive_employer_laravel as ex', 'e.id', '=', 'ex.employer_id')
                    ->leftJoin('jobs as j', 'e.id', '=', 'j.employerid')
                    ->leftJoin('franchise_company_debits as fcds', 'ex.employer_id', '=', 'fcds.employer_id')
                    ->select('ex.id', 'e.whatsapp', 'company-name as cname', 'ex.employer_id', 'er.phonenumber', 'j.phone_new', 'e.company_type', 'ex.user_id as userid', 'ex.user_id as user_name', 'ex.last_followup_status', 'ex.last_feedback', 'ex.last_target_source', 'ex.last_mindset', 'e.city as district', 'ex.assigned', 'e.is_block as companyStatus')
                    ->selectRaw("count(distinct j.id) as overallJobPost")
                    ->selectRaw("count(distinct fcds.debit_id) as overallpaymentCount")
                    ->selectRaw("sum(fcds.txn_amt)*count(DISTINCT fcds.debit_id)/count(*) as overallpaymentAMt")
                    ->selectRaw("DATE_FORMAT(max(date(j.ending)), '%d-%m-%Y') as jobExpiryDate")
                    ->selectRaw("MAX(j.posteddate) as last")
                    ->selectRaw(" CASE
           WHEN max(date(j.ending)) >= CURRENT_DATE() THEN 'Live'
           WHEN max(date(j.ending)) <= CURRENT_DATE() THEN 'Expiry'
           ELSE 'Not Post' END   as jobstatus")
                    ->selectRaw(" CASE
             WHEN sum(fcds.txn_amt) = '0' THEN 'Free'
            WHEN sum(fcds.txn_amt) > '1' THEN 'Paid'
             WHEN max(date(j.ending)) <= CURRENT_DATE() THEN 'Free'
            ELSE 'Not Service Use' END  as paymentstatus")
                    ->selectRaw(" DATE_FORMAT(er.indate, '%d-%m-%Y')   as empregDate")
                    ->selectRaw(" DATE_FORMAT(MAX(j.posteddate), '%d-%m-%Y')    as lastpostdate")
                    ->selectRaw(" date_format( MAX(j.ending), '%d-%m-%Y')   as lastexpiry")
                    ->selectRaw(" date_format(`e`.`indate`, '%d-%m-%Y')   as employerindate")
                    ->where($where)
                    ->whereRaw(" er.`phonenumber` NOT LIKE '%!_0%' ESCAPE '!'")
//            ->whereRaw(" ex.last_feedback > 0")
                    ->groupBy('e.id')
                    ->get();
                $result['executive'] = $data;

                $result['from'] = 'database';
                $result['key'] = $redies_key;
                Redis::set($redies_key, serialize($result));
                Redis::expire($redies_key, 86400);
            }
        }
        return $result;


    }

    public function assignExecutiveEmployer(Request $request)
    {
//        print_r($request->employerIds);
        $employer_id = array();
        $user_id = $request->assignUser;
        foreach ($request->employerIds as $index => $user) {
            foreach ($user as $key => $value) {
                if ($key == 'employer_id') {
                    $employer_id[] = $value;
                }
            }
        }
        $curr_date = date("Y-m-d");
        $ip = $_SERVER['REMOTE_ADDR'];
        if ((count($employer_id) > 0) && (count($user_id) <= count($employer_id))) {
            $countAssTo = count($user_id) - 1;
            foreach ($employer_id as $key => $tid) {
                if ($tid > 0 && $tid != '' && $tid != NULL) {
                    $as_id = $user_id[$countAssTo];
                    $addData = array(
                        "user_id" => $as_id,
                        "assigned_by" => $request->loginId,
                        "assigned_date" => $curr_date,
                        "up_date" => $curr_date,
                        "upby" => $request->loginId,
                        "upip" => $ip,
                        "assigned" => 1,
                        "check" => 1,
                    );
//                    DB::enableQueryLog();
                    $affected = ExecutiveEmployerModel::where('employer_id', $tid)->update($addData);
//                    $affected_check[] = DB::getQueryLog();

                }


                $countAssTo--;
                if ($countAssTo < 0) {
                    $countAssTo = count($user_id) - 1;
                }
            }


        }

//        $redis = Cache::getRedis();

        foreach ($user_id as $keyvalue => $userIds) {
            Redis::DEL("executive_employer_$userIds");

        }
//        $readiesKeyLogin = "executive_employer_$request->loginId";
////    Redis::DEL($readiesKeyLogin);
        $readiesKeyLogin = "executive_employer_19";
        Redis::DEL($readiesKeyLogin);
        if ($affected > 0) {
            $result['addStatus'] = true;
//            $result['results'] = $affected_check;
            $result['message'] = 'Assigned successfully';
        } else {
            $result['addStatus'] = false;
            $result['message'] = 'Could not assign!';
//            $result['query'] = $affected_check;
        }

        return $result;
    }


}
