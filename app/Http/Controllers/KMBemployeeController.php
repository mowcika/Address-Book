<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class KMBemployeeController extends Controller
{
    public function getDistrictData()
    {
        $where = "state not in ('KARNATAKA','KERALA','PUDUCHERRY') OR state is null";
        $getData = DB::table('areas as ar')
            ->selectRaw("ar.dist_id as id, ar.district as name")
            ->where("ar.deleted", 0)
            ->whereRaw($where)
            ->groupBy("ar.dist_id")
            ->get();
        $result['distData'] = $getData;

        // city data
        $getData = DB::table('areas as ar')
            ->selectRaw("ar.id, ar.area_name as name, ar.dist_id")
            ->where("ar.deleted", 0)
            ->get();
        $result['cityData'] = $getData;

        return $result;
    }

    // get city district based
    public function getDistrictBasedCitys(Request $request)
    {
        $dist = $request->district;
        if ($dist) {
            $getData = DB::table('areas as ar')
                ->selectRaw("ar.id, ar.area_name as name")
                ->where("ar.dist_id", $dist)
                ->where("ar.deleted", 0)
                ->get();
            $result['cityData'] = $getData;
            return $result;
        } else { // empty
            $result['cityData'] = "";
            return $result;
        }
    }

    // get employees count city based
    public function getEmployeesCounts(Request $request)
    {
        $dist = $request->district;
        $city = $request->city;
        $kiloMeters = $request->kiloMeters;
        // $km_where = "km.id BETWEEN 1 AND $kiloMeters";
        $having = "KmSplit <= $kiloMeters";
        $getCityIds = DB::table('citygroupby_district as cd')
            ->leftJoin('km_master as km', 'cd.kilometer', '=', 'km.id')
//            ->selectRaw("group_concat(cd.city_id) as cityIds, SUBSTRING_INDEX(SUBSTRING_INDEX(`km`.`Kilometer`, ' ', 1), ' ', -1) AS `KmSplit`")
            ->selectRaw("cd.city_id as cityIds, SUBSTRING_INDEX(SUBSTRING_INDEX(`km`.`Kilometer`, ' ', 1), ' ', -1) AS `KmSplit`")
//            ->selectRaw("city_id,area_ids, kilometer")
            ->where("cd.area_ids", $city)
            ->havingRaw($having)
            ->get();

        $cid = array();

        foreach ($getCityIds as $index => $item) {
            $cid[] = $item->cityIds;
        }

        // employees count city based
//        $citys_object = $getCityIds[0]->cityIds;
        $citys_object = implode(',', $cid);
        if ($citys_object) {
            $result['empCounts'] = DB::table('employee as e')
                ->leftJoin('areas as ar', 'ar.id', '=', 'e.native_location')
                ->select('ar.area_name', 'e.native_location')
                ->selectRaw("count(e.id) as employeeCounts")
//            ->whereIn("e.native_location", [$city_ids])
                ->whereRaw("e.native_location in ($citys_object)")
                ->where('e.is_deleted', 0)
                ->groupBy('e.native_location')
                ->get();
            return $result;
        } else {
            return $result['empCounts'] = "empty";
        }

    }

    // view all employees list
    public function getAllCityEmployees(Request $request)
    {
//        print_r($request->native_location);
        $nL = $request->native_location;
//        if (Redis::exists('getAllCityEmployees')) {
//            $result = unserialize(Redis::get('getAllCityEmployees'));
//            $result['from'] = 'redis';
//        } else {
        if ($nL) {
            $result['empLists'] = DB::table('employee as e')
                ->leftJoin('areas as ar', 'ar.id', '=', 'e.native_location')
                ->select('e.id', 'e.name as employeeName', 'e.mobile1', 'e.mobile2', 'e.email', 'ar.area_name', 'e.native_location')
                //            ->whereIn("e.native_location", [$city_ids])
                //                ->whereRaw("e.native_location in ($nL)")
                ->where("e.native_location", $nL)
                ->where('e.is_deleted', 0)
                ->orderBy('e.id', 'desc')
                ->get();
            return $result;
        } else { // empty
            return $result['empLists'] = "";
        }
//            $result['from'] = 'database';
//            Redis::set('getAllCityEmployees', serialize($result));
//            Redis::expire('getAllCityEmployees', 86400);
//        }
    }

    // getCurrentCityBasedList
    public function getCurrentCityBasedList(Request $request)
    {
        $cityid = $request->city;
        $myArray = explode(',', $cityid);
//        print_r($myArray);exit;
//        if (Redis::exists('getCurrentCityBasedList')) {
//            $result = unserialize(Redis::get('getCurrentCityBasedList'));
//            $result['from'] = 'redis';
//        } else {
        if ($cityid) {
            $wherein = "cd.area_ids = '$cityid'";
            $orderby = "cast(`km`.`kilometer`  as unsigned) ASC";
            $result['empCityLists'] = DB::table('citygroupby_district as cd')
                ->leftJoin('areas as ar', 'ar.id', '=', 'cd.area_ids')
                ->leftJoin('areas as ct', 'ct.id', '=', 'cd.city_id')
                ->leftJoin('employee as ee', 'ee.native_location', '=', 'cd.city_id')
                ->leftJoin('km_master as km', 'km.id', '=', 'cd.kilometer')
                ->selectRaw("`cd`.`city_id`, `ar`.`area_name`, `cd`.`area_ids`, `cd`.`kilometer`, `ct`.`area_name` as `cityName`, count(`ee`.`native_location`) as empeCount, `km`.`kilometer` as `kilometerName`")
                ->whereRaw($wherein)
                ->where("ee.is_deleted", '=', '0')
                ->groupByRaw("`ee`.`native_location`")
                //                ->orderByRaw('`cd`.`kilometer`','asc')
                ->orderByRaw($orderby)
                ->get();
            $result['adStatus'] = true;
            return $result;
        } else { // empty
            $result['adStatus'] = false;
            return $result['empCityLists'] = "";
        }
//            $result['from'] = 'database';
//            Redis::set('getCurrentCityBasedList', serialize($result));
//            Redis::expire('getCurrentCityBasedList', 86400);
//        }
    }
}
