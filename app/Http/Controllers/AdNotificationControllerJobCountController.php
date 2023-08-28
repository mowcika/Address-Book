<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class AdNotificationControllerJobCountController extends Controller
{
    // job counts
    public function addNotification_job_count(Request $request)
    {
        // print_r($request->gQualification);exit;
        $where = $wheresQ = $whereqQ = $wherejSk = $whereJl = $wheregender = $wheremarital = $wherework_status = $whereJT = array();
        $whereina = $whereinsQ = $whereinqQ = $whereinjSks = $whereinJl = $whereinGender = $whereinMarital = $whereinWorkStatus = $whereinJT = $whereinDG = ' 1 ';
        $quli_condition = $gquli_condition = $skill_condition = $job_location_condition = $job_title_condition = "";

        $district = isset($request->district) != '' ? implode(',', $request->district) : '';
        $sQualification = isset($request->sQualification) != '' ? $request->sQualification : '';
        $gQualification = isset($request->gQualification) != '' ? $request->gQualification : '';
        $job_title = isset($request->job_title) != '' ? $request->job_title : '';

        if ($district) {
//            $whereinRaw = "`dist_id` in ('$district')";
            $whereinRaw = "`dist_id` in ($district)";
            $district_string = DB::table('areas')
                ->selectRaw("GROUP_CONCAT(id SEPARATOR ',') as areaid")
                ->whereRaw($whereinRaw)
                ->get();
            $check_ara = json_decode(json_encode($district_string), true);
            $check_ara = json_decode(json_encode($check_ara[0]), true);
            $dis_group = $check_ara['areaid'];
            $joblocation = explode(',', $dis_group);

        } else {
            $dis_group = '';
        }

        if ($dis_group) {
            $jL = implode(',', $joblocation);
            $whereJl[] = "joblocation in($jL)";

            foreach ($joblocation as $key => $job_location) {
                if ($job_location == end($joblocation)) {
                    $job_location_condition .= "`joblocation` in($job_location)";
                } else {

                    $job_location_condition .= "`joblocation` in($job_location) OR ";
                }
            }

            if (count($whereJl)) {
                $whereinJl = '(' . $job_location_condition . ')';
            }
        }

        if ($dis_group) {
            $whereinDG = '(' . $job_location_condition . ')';
        }

        // single qualification
        if ($sQualification) {
            $sq = implode(',', $sQualification);
            $wheresQ[] = "singlequalification in($sq)";

            foreach ($sQualification as $key => $qulfi) {
                if ($qulfi == end($sQualification)) {
                    $quli_condition .= "FIND_IN_SET('" . $qulfi . "', `singlequalification`)";
                } else {

                    $quli_condition .= "FIND_IN_SET('" . $qulfi . "', `singlequalification`) OR ";
                }
            }
        }
        if (count($wheresQ)) {
            $whereinsQ = '(' . $quli_condition . ')';
        }


//         employer (group qualification)
        if ($gQualification) {
//            $empIds = array();
//            foreach ($gQualification as $index => $item) {
//                $empIds[] = $item['id'];
//            }
            // print_r($empIds);exit;
            $sq = implode(',', $gQualification);
            $whereqQ[] = "employerid in($sq)";

            foreach ($gQualification as $key => $qulfi) {
                if ($qulfi == end($gQualification)) {
                    $gquli_condition .= "`employerid` in($qulfi)";
                } else {

                    $gquli_condition .= "`employerid` in($qulfi) OR ";
                }
            }
        }
        if (count($whereqQ)) {
            $whereinqQ = '(' . $gquli_condition . ')';
        }

//         group qualification
//        if($gQualification){
//            $sq = implode(',', $gQualification);
//            $whereqQ[] = "ue.single_qualification in($sq)";
//
//            foreach ($gQualification as $key => $qulfi) {
//                if ($qulfi == end($gQualification)) {
//                    $gquli_condition .= "`groupqualification` in($qulfi)";
//                } else {
//
//                    $gquli_condition .= "`groupqualification` in($qulfi) OR ";
//                }
//            }
//        }
//        if (count($whereqQ)) {
//            $whereinqQ = '('.$gquli_condition.')';
//        }

        // job title
        if ($job_title) {
            $jT = implode(',', $job_title);
            $whereJT[] = "e.skills in($jT)";

            foreach ($job_title as $key => $title) {
                if ($title == end($job_title)) {
                    $job_title_condition .= "jobtitle_id in($title)";
                } else {
                    $job_title_condition .= "jobtitle_id in($title) OR ";
                }
            }

        }
        if (count($whereJT)) {
            $whereinJT = '(' . $job_title_condition . ')';
        }

        $current_date = date('Y-m-d');
        $where1 = "date(ending) >= '$current_date' ";
        $check = DB::table('jobs')
            ->whereRaw($where1)
            ->whereRaw($whereinJl)
            ->whereRaw($whereinDG)
            ->whereRaw($whereinsQ)
            ->whereRaw($whereinqQ)
            ->whereRaw($whereinJT)
            ->where('deleted', '=', '0')
            ->where('blocked', '=', '0')
            ->count();
//        print_r($check);exit;
        $result['jCounts'] = $check;
        return $result;

    }
}
