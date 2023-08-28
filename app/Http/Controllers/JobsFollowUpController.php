<?php

namespace App\Http\Controllers;

use App\Models\FSEModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

//use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class JobsFollowUpController extends Controller
{

    public function executivePredictionFromRedis($startDate)
    {
        if (Redis::exists('executivePredictionFromRedis') && 1 == 2) {
            $result = unserialize(Redis::get('executivePredictionFromRedis'));
            $result['from'] = 'redis';
        } else {
            $getFollowUp = DB::select("SELECT `inby` as `id`, `executive`, COUNT(DISTINCT if(`PaidStatus` = 'Paid', `employerid`, NULL)) as `followUpPaid`,
COUNT(DISTINCT if(`PaidStatus` = 'Free', `employerid`, NULL)) as `followUpFree`
FROM (SELECT `pd`.`inby`, `i`.`name` as `executive`, `pd`.`employerid`, `pd`.`employer_name`, `ac`.`adextend`, pd.`payment_remarks`,
CASE
WHEN SUM(fcd.`txn_amt`) > 0 THEN 'Paid'
ELSE 'Free'
END AS `PaidStatus`,
`pd`.`status`
FROM `payment_details` `pd` JOIN `employer` `e` ON `e`.`id` = `pd`.`employerid`
AND `pd`.`deleted` = 0 AND CURRENT_DATE = DATE_SUB(pd.`followup_date`, INTERVAL 1 DAY)
JOIN `franchise_company_debits` `fcd` ON `fcd`.`employer_id` = `pd`.`employerid` AND fcd.`status` = 'TXN_SUCCESS'
JOIN `user` `i` ON `i`.`id` = `pd`.`inby`
JOIN `master_adextend` `ac` ON `ac`.`id` = `pd`.`extendReason`
WHERE 1
GROUP BY `pd`.`inby`, `pd`.`employerid`) qqq GROUP BY `inby` ORDER BY `executive`");
            $getExpiry = DB::select("SELECT `user_id` as `id`, `executive`, COUNT(DISTINCT if(`PaidStatus` = 'Paid', `employerid`, NULL)) as `expiryPaid`,
COUNT(DISTINCT if(`PaidStatus` = 'Free', `employerid`, NULL)) as `expiryFree` FROM (SELECT `ee`.`user_id`, `i`.`name` as `executive`, `j`.`employerid`,
CASE
WHEN SUM(fcd.`txn_amt`) > 0 THEN 'Paid'
ELSE 'Free'
END AS `PaidStatus`
FROM `jobs` `j` JOIN `executive_employer` `ee` ON `ee`.`employer_id` = `j`.`employerid`
AND `j`.`deleted` = 0 AND CURRENT_DATE = DATE_SUB(j.`ending`, INTERVAL 1 DAY) AND j.`jobtype` = 1 AND j.`post_live_purpose` IN (1,2,3)
JOIN `franchise_company_debits` `fcd` ON `fcd`.`employer_id` = `j`.`employerid` AND fcd.`status` = 'TXN_SUCCESS'
JOIN `user` `i` ON `i`.`id` = `ee`.`user_id`
WHERE 1
GROUP BY `ee`.`user_id`, `j`.`employerid`) qqq GROUP BY `user_id` ORDER BY `executive`");
            $result['getFollowUp'] = $getFollowUp;
            $result['getExpiry'] = $getExpiry;
            $result['from'] = 'database';
            Redis::set('executivePredictionFromRedis', serialize($result));
            Redis::expire('executivePredictionFromRedis', 86400);
        }
        return $result;
    }

    public function getFollowUpStatus(Request $request)
    {
        $localUserId = $request->localUserId;
        $localUserId = ($localUserId == 2) ? 1 : $localUserId;
        $segment = $request->segment;
        $limit = ($request->limit) ? $request->limit : 0;
        $getEmployer = DB::select("SELECT j.`id` as `jobId`, j.`views`, j.`call_count`, j.`employerid` as `id`
FROM `jobs` j
JOIN `franchise_company_debits` fcs ON j.`job_plan_id` = fcs.`debit_id` AND fcs.`status` = 'TXN_SUCCESS' AND fcs.`txn_amt` > 0
AND j.`deleted` = 0 AND j.`post_live_purpose` IN (1,2,3) AND j.`jobtype` = 1 AND fcs.`incentive_userid` = $localUserId AND DATE(j.`posteddate`) < DATE_SUB(CURRENT_DATE, INTERVAL 5 DAY)
AND j.`call_count` < 15 AND DATE(j.`ending`) >= CURRENT_DATE
LEFT JOIN `jobSixthDayFollowUp` jsdf ON j.`id` = jsdf.`job_id`
WHERE 1
GROUP BY j.`employerid`, j.`id`
HAVING SUM(IF(jsdf.`status` = 1, 1, 0)) = 0
ORDER BY jsdf.`cdate` ASC, j.`id` DESC
");
        $getSizeOf = sizeof($getEmployer);
        if (sizeof($getEmployer) == 0) {
            return response()->json([
                'status' => false,
                'phonenumber' => "No FollowUp Found",
                'event' => "",
//                'eventClass' => ($searchEvent == 1) ? "success" : "danger",
                'eventClass' => "danger",
                'executive' => " - ",
                'message' => 'No data found with our record.',
                'list' => [],
                'payment' => [],
                'count' => '',
                'jobId' => '',
                'getSizeOf' => $getSizeOf
            ], 200);
        }
        $employerID = $getEmployer[$limit]->id;
        $searchText = $getEmployer[$limit]->jobId;
        $views = $getEmployer[$limit]->views;
        $call_count = $getEmployer[$limit]->call_count;
        $getEmployer = DB::table('employer_registration as er')
            ->leftJoin('employer as e', 'er.id', '=', 'e.employer')
            ->selectRaw("e.`id`, er.`firstname`, e.`company-name` as `companyName`, `er`.`phonenumber`, er.`email`, e.`image`, e.`address`, SUBSTRING_INDEX(e.`address`, '#@#', -1) as `pincode`,
SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(e.`address`, '#@#', -3), '#@#', 2), '#@#', IF(er.`vcode` = 53, 1, -1)) as `dist`")
            ->where([['e.deleted', 0], ['e.is_block', 0], ['e.id', $employerID]])
            ->groupBy('e.id')
            ->orderBy('e.company-name', 'ASC')
            ->get();
        if (sizeof($getEmployer) == 0) {
            return response()->json([
                'status' => false,
                'phonenumber' => "No Data Found ",
                'event' => "",
//                'eventClass' => ($searchEvent == 1) ? "success" : "danger",
                'eventClass' => "success",
                'executive' => " - ",
                'message' => 'No data found with our record.',
                'list' => $getEmployer,
                'payment' => [],
                'count' => '',
                'jobId' => '',
                'getSizeOf' => $getSizeOf
            ], 200);
        }

        $countArray = collect([
            (object)[
                'id' => 1,
                'key' => 'jobs',
                'count' => DB::table('jobs')->where('employerid', $employerID)->count()
            ],
            (object)[
                'id' => 2,
                'key' => 'draft_jobs',
                'count' => DB::table('draft_jobs')->where('employerid', $employerID)->count()
            ],
            (object)[
                'id' => 3,
                'key' => 'franchise_company_debits',
                'count' => DB::table('franchise_company_debits')->where('employer_id', $employerID)->count()
            ]
        ]);
        return response()->json([
            'status' => true,
            'message' => '',
            'phonenumber' => "JOB ID : " . $searchText,
            'event' => " [Views - " . $views . ", Calls - " . $call_count . "]",
//                'eventClass' => ($searchEvent == 1) ? "success" : "danger",
            'eventClass' => "danger",
            'executive' => DB::table('executive_employer as ex')
                ->leftJoin('user as u', 'u.id', '=', 'ex.user_id')->selectRaw('COALESCE(u.name, " - ") as name')->where([['ex.employer_id', $employerID]])->get()[0]->name,
            'list' => $getEmployer[0],
            'payment' => DB::table('franchise_company_debits')->selectRaw('COUNT(*) as count, SUM(`txn_amt`) as amount')->where([['employer_id', $employerID], ['status', 'TXN_SUCCESS']])->get()[0],
            'count' => $countArray,
            'jobId' => $searchText,
            'getSizeOf' => $getSizeOf
        ], 200);
    }

    public function getEmployerSegment(Request $request)
    {
        $localUserId = $request->localUserId;
        $segment = $request->segment;
        $employerID = $request->employerID;
        $segmentArray = array('load' => 0, 'jobs' => 0, 'draft_jobs' => 1, 'franchise_company_debits' => 2, 'employee_shortlist' => 3);
        $countArray = collect([
            (object)[
                'id' => 1,
                'key' => 'jobs',
                'column' => "id, jobtitle as subject, CONCAT(DATE_FORMAT(`starting`, '%d-%m-%Y'), ' to ', DATE_FORMAT(`ending`, '%d-%m-%Y')) as message, DATE_FORMAT(`posteddate`, '%d-%m-%Y') as time, IF(`ending` >= CURRENT_DATE, 'Live', 'Expired') as status",
                'where' => array('employerid' => $employerID),
                'orderBy' => 'id', 'DESC'
            ],
            (object)[
                'id' => 2,
                'key' => 'draft_jobs',
                'column' => "id, jobtitle as subject, CONCAT(DATE_FORMAT(`starting`, '%d-%m-%Y'), ' to ', DATE_FORMAT(`ending`, '%d-%m-%Y')) as message, DATE_FORMAT(`posteddate`, '%d-%m-%Y') as time, IF(`ending` >= CURRENT_DATE, 'Live', 'Expired') as status",
                'where' => array('employerid' => $employerID),
                'orderBy' => 'id', 'DESC'
            ],
            (object)[
                'id' => 3,
                'key' => 'franchise_company_debits',
                'column' => "CONCAT(debit_id, ' [', inv_prefix, inv_no, ']') as id, CONCAT(`plan_name`, ' - Rs.', `txn_amt`, ' (', `status`, ')') as subject, CONCAT(DATE_FORMAT(`starting_date`, '%d-%m-%Y'), ' to ', DATE_FORMAT(`expired_date`, '%d-%m-%Y')) as message, DATE_FORMAT(`cdate`, '%d-%m-%Y') as time, '' as status",
                'where' => array('employer_id' => $employerID),
                'orderBy' => 'debit_id', 'DESC'
            ],
            (object)[
                'id' => 4,
                'key' => 'employee_shortlist',
                'column' => 'debit_id as id, plan_name as subject, CONCAT(starting, " - ",ending) as message, posted_date as time',
                'where' => array('employer_id' => $employerID),
                'orderBy' => 'id', 'DESC'
            ]
        ]);
        $Details = DB::table($countArray[$segmentArray[$segment]]->key)->selectRaw($countArray[$segmentArray[$segment]]->column)->where($countArray[$segmentArray[$segment]]->where)->orderBy($countArray[$segmentArray[$segment]]->orderBy)->get();
        return $Details;
    }

    public function getEmployerStatistic(Request $request)
    {
        $localUserId = $request->localUserId;
        $segment = $request->segment;
        $employerID = $request->employerID;
        $Details = DB::table('franchise_company_debits')->selectRaw('SUM(`txn_amt`) as `y`, DATE_FORMAT(`txn_date`, "%M-%Y") as `x`')->where([['txn_amt', '>', 0], ['employer_id', $employerID], ['status', 'TXN_SUCCESS']])->groupByRaw('DATE_FORMAT(`txn_date`, "%m%Y")')->orderBy('txn_date', 'DESC')->take(5)->get();
        return $Details;
    }

    public function addJobsFollowUpStatus(Request $request)
    {
        $indate = Carbon::now()->toDateTimeString();
        $inip = $request->ip();
        $localUserId = $request->localUserId;
        $Details = DB::table('jobSixthDayFollowUp')->insert([
            'job_id' => $request->jobId,
            'status' => $request->callType,
            'mark' => $request->emprMark,
            'cby' => $request->localUserId,
            'cdate' => $indate,
            'cip' => $inip
        ]);
        $getEmployer = DB::select("SELECT j.`id` as `jobId`, j.`views`, j.`call_count`, j.`employerid` as `id`
FROM `jobs` j
JOIN `franchise_company_debits` fcs ON j.`job_plan_id` = fcs.`debit_id` AND fcs.`status` = 'TXN_SUCCESS' AND fcs.`txn_amt` > 0
AND j.`deleted` = 0 AND j.`post_live_purpose` IN (1,2,3) AND j.`jobtype` = 1 AND fcs.`incentive_userid` = $localUserId AND DATE(j.`posteddate`) < DATE_SUB(CURRENT_DATE, INTERVAL 5 DAY)
AND j.`call_count` < 15 AND DATE(j.`ending`) >= CURRENT_DATE
LEFT JOIN `jobSixthDayFollowUp` jsdf ON j.`id` = jsdf.`job_id`
WHERE 1
GROUP BY j.`employerid`, j.`id`
HAVING SUM(IF(jsdf.`status` = 1, 1, 0)) = 0
ORDER BY jsdf.`cdate` ASC, j.`id` DESC");
        if ($Details) {
            return response()->json([
                'status' => true,
                'getSizeOf' => sizeof($getEmployer),
                'message' => 'Feedback Added successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'getSizeOf' => sizeof($getEmployer),
                'message' => 'Something went wrong!',
            ], 200);
        }
    }

    public function msg91FreeExpiry(Request $request)
    {
        $getFreeExpiryCount = DB::select("SELECT j.`id`, j.`employerid`, er.`phonenumber`, j.`phone_new`, j.`phone`, SUM(COALESCE(fcs.`txn_amt`, 0)) as `totalAmount`, COUNT(DISTINCT j.`employerid`) as `jobCount`, DATE_FORMAT(j.`ending`, '%d-%m-%Y') as `ending` FROM `jobs` j
                                                JOIN `employer` e ON e.`id` = j.`employerid` AND `e`.`type` = 1
                                                JOIN `employer_registration` `er` ON `er`.`company_details` = `e`.`id`
                                                JOIN `franchise_company_debits` fcs ON j.`employerid` = fcs.`employer_id` AND fcs.`status` = 'TXN_SUCCESS'
                                                WHERE j.`deleted` = 0 AND j.`jobtype` = 1 AND j.`post_live_purpose` IN (1,2,3) AND DATE(j.`ending`) >= CURRENT_DATE AND CURRENT_DATE = DATE_SUB(j.`ending`, INTERVAL 1 DAY)
                                                GROUP BY j.`employerid`
                                                HAVING `totalAmount` = 0
                                                ORDER BY j.`id` DESC");
        $checkMsgSent = DB::table('msg91')->whereRaw('DATE(cdate) = CURRENT_DATE')->count();
        return response()->json([
            'status' => ($checkMsgSent > 0) ? false : true,
            'getSizeOf' => sizeof($getFreeExpiryCount),
            'message' => ($checkMsgSent > 0) ? [] : $getFreeExpiryCount
        ], 200);
    }

    public function msg91FreeExpirySend(Request $request)
    {
        $indate = Carbon::now()->toDateTimeString();
        $inip = $request->ip();
        $localUserId = $request->localUserId;
        $getFreeExpiryCount = DB::select("SELECT GROUP_CONCAT(DISTINCT j.`id`) as `id`, j.`employerid`, er.`phonenumber`, j.`phone_new`, j.`phone`, SUM(COALESCE(fcs.`txn_amt`, 0)) as `totalAmount`, COUNT(DISTINCT j.`employerid`) as `jobCount`, j.`ending` FROM `jobs` j
                                                JOIN `employer` e ON e.`id` = j.`employerid` AND `e`.`type` = 1
                                                JOIN `employer_registration` `er` ON `er`.`company_details` = `e`.`id`
                                                JOIN `franchise_company_debits` fcs ON j.`employerid` = fcs.`employer_id` AND fcs.`status` = 'TXN_SUCCESS'
                                                WHERE j.`deleted` = 0 AND j.`jobtype` = 1 AND j.`post_live_purpose` IN (1,2,3) AND DATE(j.`ending`) >= CURRENT_DATE AND CURRENT_DATE = DATE_SUB(j.`ending`, INTERVAL 1 DAY)
                                                GROUP BY j.`employerid`
                                                HAVING `totalAmount` = 0
                                                ORDER BY j.`id` DESC");
        $getRequestString = array();
        $getRequestStringUnique = array();
        $getRequestJobIDUnique = $getRequestJobIDUnique1 = array();
        $integrated_number = '8925906178';
        $template = 'jobs_expiry_update';
        $imageLink = 'https://d2u1fav05r2331.cloudfront.net/apppromo/job_ad_new.png';
//        foreach ($getFreeExpiryCount as $key => $value) {
//            $getRequestString[] = $value->phonenumber;
//            $getRequestString = array_merge($getRequestString, explode(",", $value->phone));
//            $getRequestString = array_merge($getRequestString, explode(",", $value->phone_new));
//            $getRequestJobIDUnique = array_merge($getRequestJobIDUnique, explode(",", $value->id));
//        }
//        $getRequestString = array_filter($getRequestString);
//        $getRequestString = array_unique($getRequestString);
//        $getRequestStringUnique = array_values($getRequestString);
//
//        $getRequestJobIDUnique1 = array_values($getRequestJobIDUnique);
///         $getRequestJobIDUnique1 = array_filter($getRequestJobIDUnique1);
//        $getRequestJobIDUnique1 = array_unique($getRequestJobIDUnique1);
        $getRequestStringUnique[] = 9842701007;
        $sentCount = 0;
        $curl = curl_init();
        foreach ($getRequestStringUnique as $key => $value) {
            $value = trim($value);
            $value = (strlen($value) == 12) ? $value : ((strlen($value) == 10) ? "91" . $value : $value);
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
    "integrated_number": "91' . $integrated_number . '",
    "content_type": "template",
    "payload": {
        "to": "' . $value . '",
        "type": "template",
        "template": {
            "name": "' . $template . '",
            "language": {
                "code": "ta",
                "policy": "deterministic"
            },
            "components": []
        },
        "messaging_product": "whatsapp"
    }
}',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'authkey: 221068AW6ROwfK5b2782c0',
                ),
            ));
            $response = curl_exec($curl);
            $tempArray = json_decode($response, true);
            if ($tempArray['status'] == 'success')
                $sentCount++;
        }
        curl_close($curl);
        if ($sentCount > 0) {
            $checkMsgSentSave = DB::table('msg91')->insert([
                'type' => 'free',
                'count' => $sentCount,
                'json' => serialize($getRequestStringUnique),
                'jobID' => serialize($getRequestJobIDUnique1),
                'cby' => $localUserId,
                'cdate' => $indate,
                'cip' => $inip
            ]);
        }
        return response()->json([
            'status' => ($sentCount > 0) ? false : true,
            'getSizeOf' => sizeof($getFreeExpiryCount),
            'message' => ($sentCount > 0) ? [] : $getFreeExpiryCount,
            'test' => ($sentCount > 0) ? $sentCount . " Sent Seccessfully!" : "Could not send!"
        ], 200);
    }

    public function executivePrediction(Request $request)
    {
        $indate = Carbon::now()->toDateTimeString();
        $inip = $request->ip();
        $localUserId = $request->localUserId;
        $startDate = date("Y-m-d", strtotime($request->startDate));
        $sourceFilter = $request->sourceFilter;
        $membersArray = array_filter(explode(",", $sourceFilter));
        $FieldsFromRedis = $this->executivePredictionFromRedis($startDate);
        $getFollowUp = $FieldsFromRedis['getFollowUp'];
        $getExpiry = $FieldsFromRedis['getExpiry'];
        $executives = array_unique(array_merge(array_column($getFollowUp, 'id'), array_column($getExpiry, 'id')));
        $getFollowUpTemp = collect($getFollowUp)->keyBy('id');
        $getExpiryTemp = collect($getExpiry)->keyBy('id');
        $getFinalArray = array();
        foreach ($executives as $key => $value) {
            if ((sizeof($membersArray) && in_array($value, $membersArray)) || sizeof($membersArray) < 1) {
                $getFinalArray[$value] = array(
                    'executive' => '',
                    'followUpPaid' => 0,
                    'followUpFree' => 0,
                    'expiryPaid' => 0,
                    'expiryFree' => 0,
                    'Total' => 0
                );
            }
        }
        foreach ($getFinalArray as $key => $value) {
            $getFinalArray[$key] = array(
                'executive' => isset($getFollowUpTemp[$key]) ? $getFollowUpTemp[$key]->executive : $getExpiryTemp[$key]->executive,
                'followUpPaid' => isset($getFollowUpTemp[$key]) ? $getFollowUpTemp[$key]->followUpPaid : 0,
                'followUpFree' => isset($getFollowUpTemp[$key]) ? $getFollowUpTemp[$key]->followUpFree : 0,
                'expiryPaid' => isset($getExpiryTemp[$key]) ? $getExpiryTemp[$key]->expiryPaid : 0,
                'expiryFree' => isset($getExpiryTemp[$key]) ? $getExpiryTemp[$key]->expiryFree : 0
            );
            $getFinalArray[$key]['Total'] = array_sum($getFinalArray[$key]);
        }

        return response()->json([
            'status' => true,
            'message' => array_values($getFinalArray),
            'ids' => $executives
        ], 200);
    }
}
