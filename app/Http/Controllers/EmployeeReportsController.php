<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class EmployeeReportsController extends Controller
{
    // get job titles
    public function getJobTitlesData()
    {
//        if (Redis::exists('getJobTitlesData')) {
//            $result = unserialize(Redis::get('getJobTitlesData'));
//            $result['from'] = 'redis';
//        } else {
        $result['getJobTitles'] = DB::table('jobtitle')
            ->selectRaw("id,tamil,english, concat(tamil,' (', english, ')') as jobtitles ")
            ->where("active_title", '=', 1)
            ->where("deleted", '=', 0)
            ->orderBy('english', 'Asc')
            ->get();
//            $result['from'] = 'database';
//            Redis::set('getJobTitlesData', serialize($result));
//            Redis::expire('getJobTitlesData', 86400);
//        }
        return $result;
    }

    // get search employees data
    public function getSearchEmployeesData(Request $request)
    {
        $where = $whereb = array();
        $whereina = $whereinb = ' 1 ';

        $jobtitleid_array = $request->jobtitle;
        $district = $request->district;
        $city = $request->city;
        if (!empty($jobtitleid_array)) {
            $jts = implode(',', $jobtitleid_array);
            $whereb[] = "ujc.final_id in ($jts)";
        }
//        if($district!=""){
//            $whereb[] = "ee.district = '$district' ";
//        }
        if ($city != "") {
            $whereb[] = "ee.native_location = '$city' ";
        }
        if (count($whereb)) {
            $whereinb = implode(' AND ', $whereb);
        }
        $km = "`ee`.`native_location` in (select city_id from `citygroupby_district` where `kilometer` BETWEEN 1 and 50)";
//        if (Redis::exists('getSearchEmployeesData')) {
//            $result = unserialize(Redis::get('getSearchEmployeesData'));
//            $result['from'] = 'redis';
//        } else {

        $result['getSearchData'] = DB::table('employee as ee')
            ->selectRaw("ee.id as empid, ee.name as employeeName, ee.mobile1,  ee.mobile2,ujc.final_id, jt.english as jobtitleName, ar.area_name as city, dt.district")
            ->leftJoin('user_job_category as ujc', 'ee.id', '=', 'ujc.user_id')
            ->leftJoin('jobtitle as jt', 'jt.id', '=', 'ujc.final_id')
            ->leftJoin('areas as ar', 'ar.id', '=', 'ee.native_location')
            ->leftJoin('areas as dt', 'dt.id', '=', 'ee.district')
            ->leftJoin('citygroupby_district as cd', 'cd.city_id', '=', 'ee.native_location')
            ->whereRaw($whereinb)
            ->where('ee.is_deleted', '=', 0)
            ->whereRaw($km)
            ->groupBy('ee.id')
            ->orderBy("ee.id", 'desc')
            ->get();
//            $result['from'] = 'database';
//            Redis::set('getSearchEmployeesData', serialize($result));
//            Redis::expire('getSearchEmployeesData', 86400);
//        }
        if (sizeof($result['getSearchData'])) {
            $result['getStatus'] = true;
            $result['getSearchData'] = $result['getSearchData'];
        } else {
            $result['getStatus'] = false;
            $result['getSearchData'] = "";
        }
        return $result;
    }
}
