<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class FseReportController extends Controller
{
    // getFseReportData
    public function getFseReportData(Request $request)
    {
//        print_r($request->fromDate);exit;
        $indate = Carbon::now()->toDateString();
        $current_date = date('Y-m-d');
        $last_seven_dates = date('Y-m-d', strtotime('-6 days'));

        $fdate = $request->fromDate;
        $tdate = $request->toDate;
        if (!empty((int)$fdate) && !empty((int)$tdate)) {
            $where_date = "date(fs.lastFollowDate) BETWEEN '$fdate' AND '$tdate' ";
        } else {
            $where_date = "date(fs.lastFollowDate) BETWEEN '$last_seven_dates' AND '$current_date' ";
        }
        $gBY = "date(fs.lastFollowDate), user_id";
        $orderBY = "date(fs.lastFollowDate) desc";
//        if (Redis::exists('getArticledata')) {
//            $result = unserialize(Redis::get('getArticledata'));
//            $result['from'] = 'redis';
//        } else {
        $result['fseReportData'] = DB::table("fseDetails as fs")
            ->selectRaw("fs.id as fsid,date_format(fs.lastFollowDate, '%Y-%m-%d') as lastFollowDat1, date_format(fs.lastFollowDate, '%d-%m-%Y') as lastFollowDate, count(fs.id) as totalCount,   sum( case when fs.lastFollowStatus = 1 then 1 else 0 end ) as Novacancy, sum( case when fs.lastFollowStatus = 2 then 1 else 0 end ) as Notinterested, sum( case when fs.lastFollowStatus = 3 then 1 else 0 end ) as Followup, sum( case when fs.lastFollowStatus = 4 then 1 else 0 end ) as InterestedtoPost, u.name as username,fs.user_id, fs.imageField")
            ->leftJoin('user as u', 'u.id', '=', 'fs.user_id')
            ->whereRaw($where_date)
            ->groupByRaw($gBY)
            ->orderByRaw($orderBY)
            ->get();
//            $result['from'] = 'database';
//            Redis::set('getArticledata', serialize($result));
//            Redis::expire('getArticledata', 86400);
//        }
        return $result;
    }


    // get fsc followup reports
    public function getFollowupData(Request $request)
    {
//        print_r($request->lastFollowDat1);exit;
        $userid = $request->userid;
        $lastFollowupDate = $request->lastFollowDat1;
        if ($userid && $lastFollowupDate) {
            $where = "`fsd`.`lastFollowBy` = '$userid' AND date(`ff`.`lastFollowDate`) = '$lastFollowupDate' ";
//            if (Redis::exists('getFollowupData')) {
//                $result = unserialize(Redis::get('getFollowupData'));
//                $result['from'] = 'redis';
//            } else {
            $result['fseFollowupHistory'] = DB::table("fseDetails as fsd")
                ->selectRaw("`fsd`.`id`,`ff`.`fse_id`, `ff`.`lastFollowRemarks`, `fsd`.`companyName`, date_format(`ff`.`lastFollowDate`, '%d-%m-%Y %H:%i:%s') as `lastFollowDate`,
                case
                    when fsd.lastFollowStatus = 1
                        then 'No vacancy'
                    when fsd.lastFollowStatus = 2
                        then 'Not interested'
                    when fsd.lastFollowStatus = 3
                        then 'Follow up'
                    when fsd.lastFollowStatus = 3
                        then 'Interested to Post'
                end as `lastFollowupstatus`, fsd.companyGeoLat, fsd.companyGeoLang,
                concat(fsd.companyGeoLat,',',fsd.companyGeoLang) as latlongs
                ")
                ->leftJoin('fseFollowUp as ff', 'ff.fse_id', '=', 'fsd.id')
                ->whereRaw($where)
                ->orderBy('ff.id', "desc")
                ->get();
            //            $result['from'] = 'database';
            //            Redis::set('getFollowupData', serialize($result));
            //            Redis::expire('getFollowupData', 86400);
//            }
            return $result;
        } else {
            return $result['fseFollowupHistory'] = "";
        }

    }
}
