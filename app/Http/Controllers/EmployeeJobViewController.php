<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class EmployeeJobViewController extends Controller
{
    //get data
    public function getEjvCount(Request $request)
    {
        $where1 = "date(`ja`.`cdate`) = CURDATE() ";
        $orderby = "viewCounts desc";
        $result['getEmpJobCount'] = DB::table('employee as ee')
            ->leftJoin('job_apply as ja', 'ja.employee_id', '=', 'ee.id')
            ->selectRaw("`ee`.`id`, `ee`.`name` as `employeeName`, ja.job_id, count(`ja`.`view`) as `viewCounts`, date_format(`ja`.`cdate`, '%d-%m-%Y') as `jaCdate`")
            ->whereRaw('ja.view IS NOT NULL')
            ->where('ee.is_deleted', '=', '0')
            ->whereRaw($where1)
            ->groupBy('ee.id')
            ->orderByRaw($orderby)
//                                ->having('viewCounts','desc')
            ->limit(10)
            ->get();

        if (sizeof($result['getEmpJobCount'])) {
            $result['adStatus'] = true;
            $result['getEmpJobCount'] = $result['getEmpJobCount'];
        } else {
            $result['adStatus'] = false;
            $result['getEmpJobCount'] = '';
        }
        return $result;
    }

    public function getEmployeeViewCallTiming(Request $request)
    {
        $localUserId = $request->localUserId;
        $startDate = date("Y-m-d", strtotime($request->dateRange['startDate']));
        $endDate = date("Y-m-d", strtotime($request->dateRange['endDate']));
        $viewType = $request->viewType;
        $sourceType = $request->sourceType;
//        $sourceType = 'district';
        $groupByArray = array('date' => 'date', 'district' => 'dist_id', 'city' => 'id');
        $eventByArray = array('date' => 'event', 'district' => 'district', 'city' => 'event');
        $folderArray = array('date' => 'date', 'district' => 'city', 'city' => 'city');
        $regs = Storage::disk("public")->get("employeeTiming/$folderArray[$sourceType]/$viewType.json");
        $result = collect(json_decode($regs))
            ->whereBetween('date', [$startDate, $endDate]);
//            ->groupBy(function($reg){
//                return date('Y-m-d', strtotime($reg->dist_id));
//            })
        $groups = $result->groupBy($groupByArray[$sourceType]);

// we will use map to cumulate each group of rows into single row.
// $group is a collection of rows that has the same opposition_id.
        $groupwithcount = $groups->map(function ($group) use ($eventByArray, $sourceType) {
            return [
                'dist_id' => isset($group[0]->dist_id) ? $group[0]->dist_id : '', // opposition_id is constant inside the same group, so just take the first or whatever.
                'id' => isset($group[0]->id) ? $group[0]->id : '',
                'event' => $group[0]->{$eventByArray[$sourceType]},
                'district' => isset($group[0]->district) ? $group[0]->district : '',
                'date' => $group[0]->date,
                'total' => $group->sum('total'),
                '00:01 to 03:00' => $group->sum('00:01 to 03:00'),
                '03:01 to 06:00' => $group->sum('03:01 to 06:00'),
                '06:01 to 09:00' => $group->sum('06:01 to 09:00'),
                '09:01 to 12:00' => $group->sum('09:01 to 12:00'),
                '12:01 to 15:00' => $group->sum('12:01 to 15:00'),
                '15:01 to 18:00' => $group->sum('15:01 to 18:00'),
                '18:01 to 21:00' => $group->sum('18:01 to 21:00'),
                '21:01 to 23:59' => $group->sum('21:01 to 23:59'),
                'won' => $group->where('result', 'won')->count(),
                'lost' => $group->where('result', 'lost')->count(),
            ];
        })
            ->toArray();
        return array_values($groupwithcount);
    }
}
