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

class IOTrackController extends Controller
{

    public function getEmployerStatus(Request $request)
    {
        $localUserId = $request->localUserId;
        $segment = $request->segment;
        $Details = DB::table('jobsUserIOTrack')->where([['user_id', $localUserId]])->orderBy('id', 'desc')->first();
        if (!$Details) {
            return response()->json([
                'status' => false,
                'phonenumber' => "No Numbers",
                'event' => "",
//                'eventClass' => ($searchEvent == 1) ? "success" : "danger",
                'eventClass' => "danger",
                'executive' => " - ",
                'message' => 'No data found with our record.',
                'list' => [],
                'payment' => [],
                'count' => ''
            ], 200);
        }
        $searchText = trim($Details->phonenumber);
        $searchEvent = trim($Details->event);
        $getEmployer = DB::table('employer_registration as er')
            ->leftJoin('employer as e', 'er.id', '=', 'e.employer')
            ->selectRaw("e.`id`, er.`firstname`, e.`company-name` as `companyName`, `er`.`phonenumber`, er.`email`, e.`image`, e.`address`, SUBSTRING_INDEX(e.`address`, '#@#', -1) as `pincode`,
SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(e.`address`, '#@#', -3), '#@#', 2), '#@#', IF(er.`vcode` = 53, 1, -1)) as `dist`")
            ->where([['e.deleted', 0], ['e.is_block', 0], ['er.phonenumber', $searchText]])
//            ->when($searchText, function ($query) use ($searchText) {
//                return $query->where(function ($query) use ($searchText) {
//                    $query->where('er.phonenumber', 'LIKE', '%' . $searchText . '%');
//                    $query->orWhere('e.company-name', 'LIKE', '%' . $searchText . '%');
//                });
//            })
            ->groupBy('e.id')
            ->orderBy('e.company-name', 'ASC')
            ->get();
        if (sizeof($getEmployer) == 0) {
            $getEmployer = DB::table('jobs')
                ->select('employerid as id')
                ->where('phone', $searchText)
                ->orWhereRaw("FIND_IN_SET($searchText, phone_new)")
                ->get();
        }
        if (sizeof($getEmployer) == 0) {
            return response()->json([
                'status' => false,
                'phonenumber' => $searchText,
                'event' => ($searchEvent == 1) ? "Calling" : "Calling",
//                'eventClass' => ($searchEvent == 1) ? "success" : "danger",
                'eventClass' => "danger",
                'executive' => " - ",
                'message' => 'No data found with our record.',
                'list' => $getEmployer,
                'payment' => [],
                'count' => ''
            ], 200);
        }
        $employerID = $getEmployer[0]->id;
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
            'phonenumber' => $searchText,
            'event' => ($searchEvent == 1) ? "Calling" : "Calling",
//                'eventClass' => ($searchEvent == 1) ? "success" : "danger",
            'eventClass' => "danger",
            'executive' => DB::table('executive_employer as ex')
                ->leftJoin('user as u', 'u.id', '=', 'ex.user_id')->selectRaw('COALESCE(u.name, " - ") as name')->where([['ex.employer_id', $employerID]])->get()[0]->name,
            'list' => $getEmployer[0],
            'payment' => DB::table('franchise_company_debits')->selectRaw('COUNT(*) as count, COALESCE(SUM(`txn_amt`), 0) as amount')->where([['employer_id', $employerID], ['status', 'TXN_SUCCESS']])->get()[0],
            'count' => $countArray
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
                'orderBy' => 'id DESC'
            ],
            (object)[
                'id' => 2,
                'key' => 'draft_jobs',
                'column' => "id, jobtitle as subject, CONCAT(DATE_FORMAT(`starting`, '%d-%m-%Y'), ' to ', DATE_FORMAT(`ending`, '%d-%m-%Y')) as message, DATE_FORMAT(`posteddate`, '%d-%m-%Y') as time, IF(`ending` >= CURRENT_DATE, 'Live', 'Expired') as status",
                'where' => array('employerid' => $employerID),
                'orderBy' => 'id DESC'
            ],
            (object)[
                'id' => 3,
                'key' => 'franchise_company_debits',
                'column' => "CONCAT(debit_id, ' [', inv_prefix, inv_no, ']', IF(`txn_amt` > 0 AND `status` = 'TXN_SUCCESS', '" . '&nbsp;<span class="badge badge-light-success badge-pill">Paid</span>' . "', IF(`status` != 'TXN_SUCCESS', '" . '&nbsp;<span class="badge badge-light-danger badge-pill">Failed</span>' . "', '" . '&nbsp;<span class="badge badge-light-warning badge-pill">Free</span>' . "'))) as id, CONCAT(`plan_name`, ' - Rs.', `txn_amt`, ' (', `status`, ')') as subject, CONCAT(DATE_FORMAT(`starting_date`, '%d-%m-%Y'), ' to ', DATE_FORMAT(`expired_date`, '%d-%m-%Y')) as message, DATE_FORMAT(`cdate`, '%d-%m-%Y') as time, '' as status",
                'where' => array('employer_id' => $employerID),
                'orderBy' => 'debit_id DESC'
            ],
            (object)[
                'id' => 4,
                'key' => 'employee_shortlist',
                'column' => 'debit_id as id, plan_name as subject, CONCAT(starting, " - ",ending) as message, posted_date as time',
                'where' => array('employer_id' => $employerID),
                'orderBy' => 'id DESC'
            ]
        ]);
        $Details = DB::table($countArray[$segmentArray[$segment]]->key)->selectRaw($countArray[$segmentArray[$segment]]->column)->where($countArray[$segmentArray[$segment]]->where)->orderByRaw($countArray[$segmentArray[$segment]]->orderBy)->get();
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

    public function getCallHistory(Request $request)
    {
        $localUserId = $request->localUserId;
        if (isset($request->dateRange)) {
            $startDate = date("Y-m-d", strtotime($request->dateRange['startDate']));
            $endDate = date("Y-m-d", strtotime($request->dateRange['endDate']));
        } else {
            $startDate = '';
            $endDate = '';
        }
        $Details = DB::table('jobsUserIOTrack')->selectRaw('event, phonenumber, duration, DATE_FORMAT(`cdate`, "%d-%m-%Y %r") as callTime')
            ->where([['user_id', $localUserId], ['event', '!=', 100], ['event', '>', 0]])
            ->when($startDate !== '', function ($query) use ($startDate, $endDate) {
                return $query->whereRaw("DATE(cdate) BETWEEN '$startDate' AND '$endDate'");
            })
            ->when($startDate == '', function ($query) {
                return $query->whereRaw("DATE(cdate) = CURRENT_DATE");
            })
            ->orderBy('id', 'DESC')
//            ->take(100)
            ->get();
        return $Details;
    }
}
