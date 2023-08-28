<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function loadJSON(Request $request)
    {
        $segment = $request->segment;
        $viewType = $request->viewType;
        $fileName = ($viewType) ? 'post' : 'revenue';
        $path = storage_path("app/public/revenueJson/$fileName.json"); // ie: /var/www/laravel/app/storage/json/filename.json

//        $json = json_decode(file_get_contents($path), true);
        $json = file_get_contents($path);
        return $json[$segment];
    }

    public function getYHQSegment(Request $request)
    {
        $localUserId = $request->localUserId;
        $segment = $request->segment;
        $viewType = $request->viewType;
        $now = time(); // or your date as well
        $your_date = strtotime(date('Y') . "-01-01");
        $datediff = $now - $your_date;
        $currentYearDays = round($datediff / (60 * 60 * 24));
// for temp redirect begin
        $fileName = ($viewType) ? 'post' : 'revenue';
        $path = storage_path("app/public/revenueJson/$fileName/$segment.json"); // ie: /var/www/laravel/app/storage/json/filename.json

        $json = json_decode(file_get_contents($path), true);
//        $json = file_get_contents($path);
        return $json;
        exit;
// for temp redirect end
        $segmentArray = array('load' => 0, 'y' => 0, 'h' => 1, 'q' => 2, 'm' => 3);
        $segmentPerDayArray = array('load' => 360, 'y' => 360, 'h' => 180, 'q' => 90, 'm' => 30);
        $countArray = collect([
            (object)[
                'id' => 1,
                'key' => 'franchise_company_debits',
                'column' => "YEAR(`txn_date`) as 'year', SUM(ROUND(`txn_amt`/1.18)) as `revenue`",
                'where' => array(['status', 'TXN_SUCCESS'], ['txn_amt', '>', 0]),
                'orderBy' => 'debit_id', 'DESC',
                'groupBy' => 'YEAR(`txn_date`)',
                'post' => (object)[
                    'id' => 1,
                    'key' => 'franchise_company_debits',
                    'column' => "YEAR(j.`posteddate`) as 'year', '' as `period`, COUNT(j.`id`) as `revenue`",
                    'where' => array(['status', 'TXN_SUCCESS'], ['txn_amt', '>', 0]),
                    'orderBy' => 'j.`posteddate`', 'ASC',
                    'groupBy' => 'YEAR(j.`posteddate`)',
                ]
            ],
            (object)[
                'id' => 2,
                'key' => 'franchise_company_debits',
                'column' => "YEAR(`txn_date`) as 'year', '' as `period`, SUM(ROUND(`txn_amt`/1.18)) as `revenue`,
                            sum(case when MONTH(`txn_date`) in (1,2,3,4,5,6) then ROUND(`txn_amt`/1.18) else 0 end) as 'h1',
                            sum(case when MONTH(`txn_date`) in (7,8,9,10,11,12) then ROUND(`txn_amt`/1.18) else 0 end) as 'h2'",
                'where' => array(['status', 'TXN_SUCCESS'], ['txn_amt', '>', 0]),
                'orderBy' => 'debit_id', 'DESC',
                'groupBy' => 'YEAR(`txn_date`)',
                'post' => (object)[
                    'id' => 1,
                    'key' => 'franchise_company_debits',
                    'column' => "YEAR(j.`posteddate`) as 'year', '' as `period`, COUNT(j.`id`) as `revenue`,
                                sum(case when MONTH(j.`posteddate`) in (1,2,3,4,5,6) then 1 else 0 end) as 'h1',
                                sum(case when MONTH(j.`posteddate`) in (7,8,9,10,11,12) then 1 else 0 end) as 'h2'",
                    'where' => array(['status', 'TXN_SUCCESS'], ['txn_amt', '>', 0]),
                    'orderBy' => 'j.`posteddate`', 'ASC',
                    'groupBy' => 'YEAR(j.`posteddate`)',
                ]
            ],
            (object)[
                'id' => 3,
                'key' => 'franchise_company_debits',
                'column' => "YEAR(`txn_date`) as 'year', '' as `period`, SUM(ROUND(`txn_amt`/1.18)) as `revenue`,
                            sum(case when MONTH(`txn_date`) in (1,2,3) then ROUND(`txn_amt`/1.18) else 0 end) as 'q1',
                            sum(case when MONTH(`txn_date`) in (4,5,6) then ROUND(`txn_amt`/1.18) else 0 end) as 'q2',
                            sum(case when MONTH(`txn_date`) in (7,8,9) then ROUND(`txn_amt`/1.18) else 0 end) as 'q3',
                            sum(case when MONTH(`txn_date`) in (10,11,12) then ROUND(`txn_amt`/1.18) else 0 end) as 'q4'",
                'where' => array(['status', 'TXN_SUCCESS'], ['txn_amt', '>', 0]),
                'orderBy' => 'debit_id', 'DESC',
                'groupBy' => 'YEAR(`txn_date`)',
                'post' => (object)[
                    'id' => 1,
                    'key' => 'franchise_company_debits',
                    'column' => "YEAR(j.`posteddate`) as 'year', '' as `period`, COUNT(j.`id`) as `revenue`,
                                sum(case when MONTH(j.`posteddate`) in (1,2,3) then 1 else 0 end) as 'q1',
                                sum(case when MONTH(j.`posteddate`) in (4,5,6) then 1 else 0 end) as 'q2',
                                sum(case when MONTH(j.`posteddate`) in (7,8,9) then 1 else 0 end) as 'q3',
                                sum(case when MONTH(j.`posteddate`) in (10,11,12) then 1 else 0 end) as 'q4'",
                    'where' => array(['status', 'TXN_SUCCESS'], ['txn_amt', '>', 0]),
                    'orderBy' => 'j.`posteddate`', 'ASC',
                    'groupBy' => 'YEAR(j.`posteddate`)',
                ]
            ],
            (object)[
                'id' => 4,
                'key' => 'franchise_company_debits',
                'column' => "YEAR(`txn_date`) as 'year', MONTHNAME(`txn_date`) as `period`, SUM(ROUND(`txn_amt`/1.18)) as `revenue`",
                'where' => array(['status', 'TXN_SUCCESS'], ['txn_amt', '>', 0]),
                'orderBy' => 'txn_date', 'DESC',
                'groupBy' => 'YEAR(`txn_date`), MONTH(`txn_date`)',
                'post' => (object)[
                    'id' => 1,
                    'key' => 'franchise_company_debits',
                    'column' => "YEAR(j.`posteddate`) as 'year', MONTHNAME(j.`posteddate`) as `period`, COUNT(j.`id`) as `revenue`",
                    'where' => array(['status', 'TXN_SUCCESS'], ['txn_amt', '>', 0]),
                    'orderBy' => 'j.`posteddate`', 'ASC',
                    'groupBy' => 'YEAR(j.`posteddate`), MONTH(j.`posteddate`)',
                ]
            ]
        ]);
        if ($viewType == 0) {
            $Details = DB::table($countArray[$segmentArray[$segment]]->key)->selectRaw($countArray[$segmentArray[$segment]]->column)->where($countArray[$segmentArray[$segment]]->where)->orderBy($countArray[$segmentArray[$segment]]->orderBy)->groupByRaw($countArray[$segmentArray[$segment]]->groupBy)->get();
        }
        $postDetails = DB::table('jobs as j')
            ->join("franchise_company_debits as fcs", function ($join) {
                $join->on("j.job_plan_id", "=", "fcs.debit_id")
                    ->on("fcs.status", "=", DB::raw("'TXN_SUCCESS'"))
                    ->on("fcs.txn_amt", ">", DB::raw(0))
                    ->on("j.deleted", DB::raw(0));
            })
            ->selectRaw($countArray[$segmentArray[$segment]]->post->column)->orderByRaw($countArray[$segmentArray[$segment]]->post->orderBy)->groupByRaw($countArray[$segmentArray[$segment]]->post->groupBy)->get();
        if ($viewType == 1)
            $Details = $postDetails;
        $finalArray = collect();
        $postArray = array();
        $finalCalculationArray = collect();
        switch ($segment) {
            case 'y':
                foreach ($postDetails as $key => $value) {
                    $postArray[$value->year] = $value->revenue;
                }
                foreach ($Details as $key => $value) {
                    $revenueArray = collect(
                        (object)[
                            'year' => $value->year,
                            'period' => "",
                            'revenue' => round($value->revenue),
                            'difference' => "",
                            'increasePercentage' => "",
                            'perDayRevenue' => "",
                            'jobPostingCount' => (isset($postArray[$value->year])) ? $postArray[$value->year] : 0,
                            'averageCostPerPosting' => ""
                        ]);
                    $finalArray->push($revenueArray);
                }
                break;
            case 'h':
                foreach ($postDetails as $key => $value) {
                    for ($i = 1; $i <= 2; $i++) {
                        $dynamicPeriod = "h$i";
                        $postArray[$value->year][$dynamicPeriod] = $value->$dynamicPeriod;
                    }
                }
                foreach ($Details as $key => $value) {
                    for ($i = 1; $i <= 2; $i++) {
                        $dynamicPeriod = "h$i";
                        $revenueArray = collect(
                            (object)[
                                'year' => $value->year,
                                'period' => strtoupper($dynamicPeriod),
                                'revenue' => round($value->$dynamicPeriod),
                                'difference' => "",
                                'increasePercentage' => "",
                                'perDayRevenue' => "",
                                'jobPostingCount' => (isset($postArray[$value->year][$dynamicPeriod])) ? $postArray[$value->year][$dynamicPeriod] : 0,
                                'averageCostPerPosting' => ""
                            ]);
                        $finalArray->push($revenueArray);
                    }
                }
                break;
            case 'q':
                foreach ($postDetails as $key => $value) {
                    for ($i = 1; $i <= 4; $i++) {
                        $dynamicPeriod = "q$i";
                        $postArray[$value->year][$dynamicPeriod] = $value->$dynamicPeriod;
                    }
                }
                foreach ($Details as $key => $value) {
                    for ($i = 1; $i <= 4; $i++) {
                        $dynamicPeriod = "q$i";
                        $revenueArray = collect(
                            (object)[
                                'year' => $value->year,
                                'period' => strtoupper($dynamicPeriod),
                                'revenue' => round($value->$dynamicPeriod),
                                'difference' => "",
                                'increasePercentage' => "",
                                'perDayRevenue' => "",
                                'jobPostingCount' => (isset($postArray[$value->year][$dynamicPeriod])) ? $postArray[$value->year][$dynamicPeriod] : 0,
                                'averageCostPerPosting' => ""
                            ]);
                        $finalArray->push($revenueArray);
                    }
                }
                break;
            case 'm':
                foreach ($postDetails as $key => $value) {
                    $postArray[$value->year][$value->period] = $value->revenue;
                }
                foreach ($Details as $key => $value) {
                    $revenueArray = collect(
                        (object)[
                            'year' => $value->year,
                            'period' => strtoupper($value->period),
                            'revenue' => round($value->revenue),
                            'difference' => "",
                            'increasePercentage' => "",
                            'perDayRevenue' => "",
                            'jobPostingCount' => (isset($postArray[$value->year][$value->period])) ? $postArray[$value->year][$value->period] : 0,
                            'averageCostPerPosting' => ""
                        ]);
                    $finalArray->push($revenueArray);
                }
                break;
        }
        foreach ($finalArray as $key => $value) {
            $dynamicDifference = ($key == 0) ? 0 : $value['revenue'] - $finalArray[$key - 1]['revenue'];
            $dynamicIncPercentage = ($key == 0) ? 0 : (($finalArray[$key - 1]['revenue'] == 0) ? 0 : ($dynamicDifference / $finalArray[$key - 1]['revenue']) * 100);
            $currentYearDays1 = ($value['year'] == date('Y') && $segment == 'y' && $currentYearDays < 360) ? $currentYearDays : $segmentPerDayArray[$segment];
            $revenueArray = collect(
                (object)[
                    'year' => $value['year'],
                    'period' => $value['period'],
                    'revenue' => $value['revenue'],
                    'difference' => $dynamicDifference,
                    'increasePercentage' => round($dynamicIncPercentage, 2),
                    'perDayRevenue' => round($value['revenue'] / $currentYearDays1),
                    'jobPostingCount' => $value['jobPostingCount'],
//                    'averageCostPerPosting' => ""
                ]);
            $finalCalculationArray->push($revenueArray);
        }
        return $finalCalculationArray;
    }

    public function employerFreeToPaid(Request $request)
    {
        $localUserId = $request->localUserId;
        $segment = $request->segment;
//        $dateRange = $request->dateRange;
//        $startDate = Carbon::parse($dateRange['startDate'])->format('Y-m-d');
//        $endDate = Carbon::parse($dateRange['endDate'])->format('Y-m-d');

        $startDate = date("Y-m-d", strtotime($request->dateRange['startDate']));
        $endDate = date("Y-m-d", strtotime($request->dateRange['endDate']));

//        $Details = DB::select('SELECT COUNT(*) as `total`, SUM(qq.`sum` = 0) as `free`, SUM(qq.`sum` > 0) as `paid`
//FROM (SELECT fcs.`employer_id`, SUM(fcs.`txn_amt`) as `sum`
//FROM `employer` e
//    JOIN `employer_registration` `er` ON `er`.`company_details` = `e`.`id` AND `er`.`phonenumber` NOT LIKE "%!_0%" ESCAPE "!"
//    LEFT JOIN `franchise_company_debits` fcs ON e.`id` = fcs.`employer_id` AND fcs.`status` = "TXN_SUCCESS" WHERE e.`deleted` = 0 AND e.`is_block` = 0 AND `e`.`type` = 1
//GROUP BY e.`id`) qq');
        $Details = DB::select('SELECT e.`id`
FROM `employer` e
    JOIN `employer_registration` `er` ON `er`.`company_details` = `e`.`id` AND `er`.`phonenumber` NOT LIKE "%!_0%" ESCAPE "!"
AND e.`deleted` = 0 AND e.`is_block` = 0 AND `e`.`type` = 1
GROUP BY e.`id`');
        $ConvertPaidOnly = DB::select("SELECT fcs.`employer_id` FROM `franchise_company_debits` fcs
                         JOIN `employer` e ON e.`id` = fcs.`employer_id` AND e.`deleted` = 0 AND e.`is_block` = 0 AND `e`.`type` = 1
                         JOIN `employer_registration` `er` ON `er`.`company_details` = `e`.`id` AND `er`.`phonenumber` NOT LIKE '%!_0%' ESCAPE '!'
WHERE fcs.`status` = 'TXN_SUCCESS'
GROUP BY fcs.`employer_id` HAVING SUM(fcs.`txn_amt`) > 0");
        $ConvertFreeOnly = DB::select("SELECT COUNT(DISTINCT j.`employerid`) as `jobCount` FROM `jobs` j
    JOIN `employer` e ON e.`id` = j.`employerid` AND e.`deleted` = 0 AND e.`is_block` = 0 AND `e`.`type` = 1
    JOIN `employer_registration` `er` ON `er`.`company_details` = `e`.`id` AND `er`.`phonenumber` NOT LIKE '%!_0%' ESCAPE '!'
LEFT JOIN `franchise_company_debits` fcs ON j.`employerid` = fcs.`employer_id` AND fcs.`status` = 'TXN_SUCCESS'
WHERE j.`deleted` = 0 AND j.`jobtype` = 1
GROUP BY j.`employerid`
HAVING SUM(COALESCE(fcs.`txn_amt`, 0)) = 0
ORDER BY j.`id` DESC");
        $ConvertPaid = DB::select("SELECT fcs.`employer_id` FROM `franchise_company_debits` fcs
                     JOIN `employer` e ON e.`id` = fcs.`employer_id` AND e.`deleted` = 0 AND e.`is_block` = 0 AND `e`.`type` = 1
                         JOIN `employer_registration` `er` ON `er`.`company_details` = `e`.`id` AND `er`.`phonenumber` NOT LIKE '%!_0%' ESCAPE '!'
WHERE fcs.`status` = 'TXN_SUCCESS' AND fcs.`txn_amt` > 0
GROUP BY fcs.`employer_id` HAVING SUM(fcs.`txn_amt`) > 0 AND min(DATE(fcs.`txn_date`)) BETWEEN '$startDate' AND '$endDate'");
        $ConvertFree = DB::select("SELECT fcs.`employer_id` FROM `franchise_company_debits` fcs
                     JOIN `employer` e ON e.`id` = fcs.`employer_id` AND e.`deleted` = 0 AND e.`is_block` = 0 AND `e`.`type` = 1
                         JOIN `employer_registration` `er` ON `er`.`company_details` = `e`.`id` AND `er`.`phonenumber` NOT LIKE '%!_0%' ESCAPE '!'
WHERE fcs.`status` = 'TXN_SUCCESS'
GROUP BY fcs.`employer_id` HAVING SUM(fcs.`txn_amt`) = 0 AND min(DATE(fcs.`txn_date`)) BETWEEN '$startDate' AND '$endDate'");
        $ConvertTotal = DB::select("SELECT fcs.`employer_id` FROM `franchise_company_debits` fcs
                     JOIN `employer` e ON e.`id` = fcs.`employer_id` AND e.`deleted` = 0 AND e.`is_block` = 0 AND `e`.`type` = 1
                         JOIN `employer_registration` `er` ON `er`.`company_details` = `e`.`id` AND `er`.`phonenumber` NOT LIKE '%!_0%' ESCAPE '!'
WHERE fcs.`status` = 'TXN_SUCCESS' AND fcs.`txn_amt` > 0 AND DATE(fcs.`txn_date`) BETWEEN '$startDate' AND '$endDate'
GROUP BY fcs.`employer_id`");
        $finalArray = collect([
            (object)[
                'x' => 'Total',
                'y' => count($Details)
            ],
            (object)[
                'x' => "Free Only",
                'y' => count($ConvertFreeOnly)
            ],
            (object)[
                'x' => "Paid Only",
                'y' => count($ConvertPaidOnly)
            ],
            (object)[
                'x' => "New Free Convert",
                'y' => count($ConvertFree)
            ],
            (object)[
                'x' => "New Paid Convert",
                'y' => count($ConvertPaid)
            ],
            (object)[
                'x' => "Total Invoice (Paid)",
                'y' => count($ConvertTotal)
            ]
        ]);
        return $finalArray;
    }

    public function executivesCallReport(Request $request)
    {
        $localUserId = $request->localUserId;
        $startDate = date("Y-m-d", strtotime($request->dateRange['startDate']));
        $endDate = date("Y-m-d", strtotime($request->dateRange['endDate']));
        $Details = DB::table('jobsUserIOTrack as jut')
            ->selectRaw('u.id, u.name, SUM(IF(jut.event = 1, 1, 0)) as incoming, SUM(IF(jut.event = 2, 1, 0)) as outgoing, SUM(IF(jut.event = 3, 1, 0)) as missed,
                                   SEC_TO_TIME(SUM(IF(jut.event = 1, TIME_TO_SEC(jut.duration), 0))) as incomingTime, SEC_TO_TIME(SUM(IF(jut.event = 2, TIME_TO_SEC(jut.duration), 0))) as outgoingTime, SEC_TO_TIME(SUM(IF(jut.event = 3, 0, 0))) as missedTime,
                                    SEC_TO_TIME(SUM(IF(jut.event = 1 OR jut.event = 2, TIME_TO_SEC(jut.duration), 0))) as totalTime')
            ->join('user as u', 'u.id', '=', 'jut.user_id')
//            ->where([['user_id', $localUserId], ['event', '!=', 100]])
            ->whereRaw("DATE(cdate) BETWEEN '$startDate' AND '$endDate' AND event != 100 AND event > 0")
            ->orderBy('id', 'DESC')
            ->take(50)
            ->groupBy('jut.user_id')
            ->get();
        foreach ($Details as $key => $value) {
            $Details[$key]->total = $value->incoming + $value->outgoing + $value->missed;
            if ($Details[$key]->total > 0)
                $Details[$key]->total = $Details[$key]->total . " [" . $value->totalTime . "]";
            $Details[$key]->incoming = $value->incoming . " [" . $value->incomingTime . "]";
            $Details[$key]->outgoing = $value->outgoing . " [" . $value->outgoingTime . "]";
        }
        return $Details;
    }

    public function employeeViewsTiming(Request $request)
    {
        return $request;
    }
}
