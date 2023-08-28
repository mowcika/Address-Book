<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class adNotificationController extends Controller
{
    // get single Qualification controlle
    public function getsingleQualification()
    {
//        $where1 = "";
        $result['sQualification'] = DB::table('course as c')
            ->selectRaw("DISTINCT(c.`id`) as id,c.`course` as course , q.qualification")
            ->leftJoin('qualification as q', 'q.id', '=', 'c.qualification')
            ->where('c.status', '=', '0')
            ->whereRaw("c.`id` NOT IN (select `course` from `group_course`)")
            ->orderBy('position')
            ->get();
        return $result;
    }

    // get group qualification
    public function getgroupQualification()
    {
        $result['gQualification'] = DB::table('course as c')
            ->selectRaw("DISTINCT(c.`id`) as id,c.`course` as course,q.id as qid,q.qualification,q.position ")
            ->leftJoin('qualification as q', 'q.id', '=', 'c.qualification')
            ->crossJoin('group_course as gp', 'c.id', '=', 'gp.course')
            ->where('c.status', '=', '0')
            ->orderBy('position')
            ->get();
        return $result;
    }

    // get skill drop down list
    public function getskillsDrop()
    {
        $result['skillsDrop'] = DB::table('skills')->get();
        return $result;
    }

    // get district drop down list for job locations
    public function getdistDrop()
    {
        $result['distDrop'] = DB::table('dist')->get();
        return $result;
    }

    // get notification data
    public function getNotification()
    {
        $result['getNotifiData'] = DB::table('dynamic_notification_test as d')
            ->selectRaw('d.id,d.title,i.name as cby,DATE_FORMAT(d.cdate, "%d-%m-%Y %h:%i %p") as added_date')
            ->leftJoin('user as i', 'i.id', '=', 'd.cby')
            ->where('d.deleted', '=', '0')
            ->orderBy('d.id', 'desc')
            ->get();
        return $result;
    }

    // delete
    public function delNotification(Request $request)
    {
        // print_r($request->id);exit;
        $rid = $request->id;
        if ($rid != "") {
            $update = array(
                'deleted' => 1,
            );

            if (sizeof($update)) {
                $Details = DB::table('dynamic_notification_test')
                    ->where('id', $rid)
                    ->update($update);
                if ($Details) {
                    return array('adStatus' => true, 'adMessage' => 'Notification Deleted Successfully');
                } else {
                    return array('adStatus' => false, 'adMessage' => 'Notification Not Deleted!');
                }
            } else {
                return array('adStatus' => false, 'adMessage' => 'Something went wrong!');
            }
        }

    }

    // get employee counts
    public function addNotification_emplyee_count(Request $request)
    {
        $where = $wheresQ = $whereqQ = $wherejSk = $whereJl = $wheregender = $wheremarital = $wherework_status = $whereJT = array();
        $whereina = $whereinsQ = $whereinqQ = $whereinjSks = $whereinJl = $whereinGender = $whereinMarital = $whereinWorkStatus = $whereinJT = ' 1 ';
        $quli_condition = $gquli_condition = $skill_condition = $job_location_condition = $job_title_condition = "";

        $sQualification = isset($request->sQualification) != '' ? $request->sQualification : '';
        $gQualification = isset($request->gQualification) != '' ? $request->gQualification : '';
        $jobskills = isset($request->jobskills) != '' ? $request->jobskills : '';
        $joblocation = isset($request->joblocation) != '' ? $request->joblocation : '';
        $gender = isset($request->gender) != '' ? $request->gender : '';
        $marital = isset($request->marital) != '' ? $request->marital : '';
        $work_status = isset($request->work_status) != '' ? $request->work_status : '';
        $job_title = isset($request->job_title) != '' ? $request->job_title : '';

//        print_r($gQualification);exit;
        if ($sQualification) {
            $sq = implode(',', $sQualification);
            $wheresQ[] = "ue.single_qualification in($sq)";

            foreach ($sQualification as $key => $qulfi) {
                if ($qulfi == end($sQualification)) {
                    $quli_condition .= "`ue`.`single_qualification` in($qulfi)";
                } else {

                    $quli_condition .= "`ue`.`single_qualification` in($qulfi) OR ";
                }
            }
        }
        if (count($wheresQ)) {
            $whereinsQ = '(' . $quli_condition . ')';
        }

        // gq
        if ($gQualification) {
            $sq = implode(',', $gQualification);
            $whereqQ[] = "ue.single_qualification in($sq)";

            foreach ($gQualification as $key => $qulfi) {
                if ($qulfi == end($gQualification)) {
                    $gquli_condition .= "`ue`.`single_qualification` in($qulfi)";
                } else {

                    $gquli_condition .= "`ue`.`single_qualification` in($qulfi) OR ";
                }
            }
        }
        if (count($whereqQ)) {
            $whereinqQ = '(' . $gquli_condition . ')';
        }

        // job skills
        if ($jobskills) {
            $js = implode(',', $jobskills);
            $wherejSk[] = "e.skills in($js)";

            foreach ($jobskills as $key => $skill) {
                if ($skill == end($jobskills)) {
                    $skill_condition .= "`e`.`skills` in($skill)";
                } else {

                    $skill_condition .= "`e`.`skills` in($skill) OR ";
                }
            }
        }
        if (count($wherejSk)) {
            $whereinjSks = '(' . $skill_condition . ')';
        }

        // Job Locations
        if ($joblocation) {
            $jL = implode(',', $joblocation);
            $whereJl[] = "e.skills in($jL)";

            foreach ($joblocation as $key => $job_location) {
                if ($job_location == end($joblocation)) {
                    $job_location_condition .= "`a`.`dist_id` in($job_location)";
                } else {

                    $job_location_condition .= "`a`.`dist_id` in($job_location) OR ";
                }
            }
        }
        if (count($whereJl)) {
            $whereinJl = '(' . $job_location_condition . ')';
        }


        if ($job_title) {
            $jT = implode(',', $job_title);
            $whereJT[] = "e.skills in($jT)";

            foreach ($job_title as $key => $title) {
                if ($title == end($job_title)) {
                    $job_title_condition .= "`e`.`job_title` in($title)";
                } else {

                    $job_title_condition .= "`e`.`job_title` in($title) OR ";
                }
            }

        }
        if (count($whereJT)) {
            $whereinJT = '(' . $job_title_condition . ')';
        }

        if (($gender != 2) && ($gender != '')) {
            $wheregender[] = "e.gender = '$gender' ";
        }
        if (count($wheregender)) {
            $whereinGender = implode(' AND ', $wheregender);
        }

        if ($marital != '2' && $marital != '') {
            $wheremarital[] = "e.marital_status = '$marital' ";
        }
        if (count($wheremarital)) {
            $whereinMarital = implode(' AND ', $wheremarital);
        }
        if ($work_status != '2' && $work_status != '') {
            $wherework_status[] = "e.work_status = '$work_status' ";
        }
        if (count($wherework_status)) {
            $whereinWorkStatus = implode(' AND ', $wherework_status);
        }

        $employeesCounts = DB::table('employee as e')
            ->leftjoin('user_skills as s', 's.user_id', '=', 'e.id')
            ->leftjoin('user_education as ue', 'ue.user_id', '=', 'e.id')
            ->leftjoin('areas as a', 'a.id', '=', 'e.native_location')
            ->whereRaw($whereinsQ)
            ->whereRaw($whereinqQ)
            ->whereRaw($whereinjSks)
            ->whereRaw($whereinJl)
            ->whereRaw($whereinGender)
            ->whereRaw($whereinMarital)
            ->whereRaw($whereinWorkStatus)
            ->whereRaw($whereinJT)
            ->where('a.deleted', '=', '0')
            ->count();
        $return['eCounts'] = $employeesCounts;
//        print_r($return['eCounts']);exit;
        return $return;
    }

    // send notification
    public function sendNotification(Request $request)
    {
//         print_r($request);exit;
        $where = $wheresQ = $whereqQ = $wherejSk = $whereJl = $wheregender = $wheremarital = $wherework_status = $whereJT = array();
        $where = $wheresQ1 = $whereqQ1 = $wherejSk1 = $whereJl1 = $wheregender1 = $wheremarital1 = $wherework_status1 = $whereJT1 = array();

        $whereina = $whereinsQ = $whereinqQ = $whereinjSks = $whereinJl = $whereinGender = $whereinMarital = $whereinWorkStatus = $whereinJT = ' 1 ';
        $whereina1 = $whereinsQ1 = $whereinqQ1 = $whereinjSks1 = $whereinJl1 = $whereinGender1 = $whereinMarital1 = $whereinWorkStatus1 = $whereinJT1 = ' 1 ';

        $sQualification = $jobskills = $joblocation = $gender = $job_title = $marital = $work_status = $gQualification = $quli_condition = $gquli_condition = $skill_condition = $job_location_condition = $job_title_condition = '';
        $quli_condition1 = $gquli_condition1 = $skill_condition1 = $job_location_condition1 = $job_title_condition1 = $gender1 = $marital1 = $work_status1 = '';
//		$fcmData = array();

//        $date = new DateTime("now");
        date_default_timezone_set('Asia/Kolkata');
        $curr_date = date("Y-m-d");
        $time = date('H:i:s');
        // print_r($time);exit;
        $id = $request->id;
        $uid = $request->userid;
        $ip = $request->ip();

        $dynamic_notification = DB::table('dynamic_notification_test')
            ->where('id', '=', $id)
            ->get();
        // print_r($dynamic_notification);exit;

        if (count($dynamic_notification) > 0) {
            $result_emp = array();
            foreach ($dynamic_notification as $row) {
//				$result_emp['id'] = $row->id;
                $emp_filter = unserialize($row->emp_filter);
//				$result_emp['emp_filter'] = unserialize($row->emp_filter);
                $result_emp['job_filter'] = unserialize($row->job_filter);
                $title = $row->title;
            }
            // print_r($result_emp);exit;
            $msg = $result_emp;
            $job_title = $title1 = $bm = $title != '' ? $title : '';
            $sQualification = isset($emp_filter['sQualification']) ? $emp_filter['sQualification'] : '';
            $jobskills = isset($emp_filter['jobskills']) ? $emp_filter['jobskills'] : '';
            $joblocation = isset($emp_filter['joblocation']) ? $emp_filter['joblocation'] : '';
            $gender = isset($emp_filter['gender']) ? $emp_filter['gender'] : '';
            $job_title = isset($emp_filter['job_title']) ? $emp_filter['job_title'] : '';
            $marital = isset($emp_filter['marital']) ? $emp_filter['marital'] : '';
            $work_status = isset($emp_filter['work_status']) ? $emp_filter['work_status'] : '';
            $gQualification = isset($emp_filter['gQualification']) ? $emp_filter['gQualification'] : '';
            // print_r($sQualification);exit;

            if ($sQualification) {
                $sq = implode(',', $sQualification);
                $wheresQ[] = "ue.single_qualification in($sq)";

                foreach ($sQualification as $key => $qulfi) {
                    if ($qulfi == end($sQualification)) {
                        $quli_condition .= "`ue`.`single_qualification` in($qulfi)";
                    } else {

                        $quli_condition .= "`ue`.`single_qualification` in($qulfi) OR ";
                    }
                }
            }
            if (count($wheresQ)) {
                $whereinsQ = '(' . $quli_condition . ')';
            }

            // gq
            if ($gQualification) {
                $sq = implode(',', $gQualification);
                $whereqQ[] = "ue.single_qualification in($sq)";

                foreach ($gQualification as $key => $qulfi) {
                    if ($qulfi == end($gQualification)) {
                        $gquli_condition .= "`ue`.`single_qualification` in($qulfi)";
                    } else {

                        $gquli_condition .= "`ue`.`single_qualification` in($qulfi) OR ";
                    }
                }
            }
            if (count($whereqQ)) {
                $whereinqQ = '(' . $gquli_condition . ')';
            }

            // job skills
            if ($jobskills) {
//                print_r($jobskills);exit;
                $js = implode(',', $jobskills);
                $wherejSk[] = "e.skills in($js)";

                foreach ($jobskills as $key => $skill) {
                    if ($skill == end($jobskills)) {
                        $skill_condition .= "find_in_set('$skill',`e`.`skills`)";
                    } else {

                        $skill_condition .= "find_in_set('$skill',`e`.`skills`) OR ";
                    }
                }
            }
            if (count($wherejSk)) {
                $whereinjSks = '(' . $skill_condition . ')';
            }

            // Job Locations
            if ($joblocation) {
                $jL = implode(',', $joblocation);
                $whereJl[] = "e.skills in($jL)";

                foreach ($joblocation as $key => $job_location) {
                    if ($job_location == end($joblocation)) {
                        $job_location_condition .= "`a`.`dist_id` in($job_location)";
                    } else {

                        $job_location_condition .= "`a`.`dist_id` in($job_location) OR ";
                    }
                }
            }
            if (count($whereJl)) {
                $whereinJl = '(' . $job_location_condition . ')';
            }


            if ($job_title) {
                $jT = implode(',', $job_title);
                $whereJT[] = "e.skills in($jT)";

                foreach ($job_title as $key => $title) {
                    if ($title == end($job_title)) {
                        $job_title_condition .= "`e`.`job_title` in($title)";
                    } else {

                        $job_title_condition .= "`e`.`job_title` in($title) OR ";
                    }
                }

            }
            if (count($whereJT)) {
                $whereinJT = '(' . $job_title_condition . ')';
            }

            if ($gender) {
                $wheregender[] = "e.gender = '$gender' ";
            }
            if (count($wheregender)) {
                $whereinGender = implode(' AND ', $wheregender);
            }

            if ($marital) {
                $wheremarital[] = "e.marital_status = '$marital' ";
            }
            if (count($wheremarital)) {
                $whereinMarital = implode(' AND ', $wheremarital);
            }
            if ($work_status) {
                $wherework_status[] = "e.work_status = '$work_status' ";
            }
            if (count($wherework_status)) {
                $whereinWorkStatus = implode(' AND ', $wherework_status);
            }
            $total_fcm = DB::table('employee as e')
                ->selectRaw('distinct e.fcm_id')
                ->leftJoin('user_skills as s', 's.user_id', '=', 'e.id')
                ->leftJoin('user_education as ue', 'ue.user_id', '=', 'e.id')
                ->leftJoin('areas as a', 'a.id', '=', 'e.native_location')
                ->where('e.is_deleted', '=', '0')
                ->where('e.v_code', '>', '49')
                ->where('e.fcm_id', '!=', '')
                ->where('e.native_location', '!=', '')
                ->whereRaw($whereinsQ)
                ->whereRaw($whereinqQ)
                ->whereRaw($whereinjSks)
                ->whereRaw($whereinJl)
                ->whereRaw($whereinJT)
                ->whereRaw($whereinGender)
                ->whereRaw($whereinMarital)
                ->whereRaw($whereinWorkStatus)
                ->whereRaw('((DATE(NOW()) - INTERVAL 30 DAY) < DATE(e.last_login))')
                ->where('a.deleted', '=', '0')
//                ->groupBy('e.fcm_id')
                ->get()->count();
//             print_r($total_fcm);exit;
            $ntype = "bt";
            $url = "";
            $pac = "";
            $type = "dynamic";
            $json = array("title" => $title1, "message" => json_encode($msg, JSON_FORCE_OBJECT), "date" => date('d-m-Y'), "time" => $time, "type" => $type, "ntype" => $ntype, "url" => $url, "bm" => $bm, "pac" => $pac);

            if ($sQualification) {
                $sq = implode(',', $sQualification);
                $wheresQ1[] = "ue.single_qualification in($sq)";

                foreach ($sQualification as $key => $qulfi) {
                    if ($qulfi == end($sQualification)) {
                        $quli_condition1 .= "`ue`.`single_qualification` in($qulfi)";
                    } else {

                        $quli_condition1 .= "`ue`.`single_qualification` in($qulfi) OR ";
                    }
                }
            }
            if (count($wheresQ1)) {
                $whereinsQ1 = '(' . $quli_condition1 . ')';
            }

            // gq
            if ($gQualification) {
                $sq = implode(',', $gQualification);
                $whereqQ1[] = "ue.single_qualification in($sq)";

                foreach ($gQualification as $key => $qulfi) {
                    if ($qulfi == end($gQualification)) {
                        $gquli_condition1 .= "`ue`.`single_qualification` in($qulfi)";
                    } else {

                        $gquli_condition1 .= "`ue`.`single_qualification` in($qulfi) OR ";
                    }
                }
            }
            if (count($whereqQ1)) {
                $whereinqQ1 = '(' . $gquli_condition1 . ')';
            }

            // job skills
            if ($jobskills) {
                $js = implode(',', $jobskills);
                $wherejSk1[] = "e.skills in($js)";

                foreach ($jobskills as $key => $skill) {
                    if ($skill == end($jobskills)) {
                        $skill_condition1 .= "find_in_set('$skill',`e`.`skills`)";
                    } else {

                        $skill_condition1 .= "find_in_set('$skill',`e`.`skills`) OR ";
                    }
                }
            }
            if (count($wherejSk1)) {
                $whereinjSks1 = '(' . $skill_condition1 . ')';
            }

            // Job Locations
            if ($joblocation) {
                $jL = implode(',', $joblocation);
                $whereJl1[] = "e.skills in($jL)";

                foreach ($joblocation as $key => $job_location) {
                    if ($job_location == end($joblocation)) {
                        $job_location_condition1 .= "`a`.`dist_id` in($job_location)";
                    } else {

                        $job_location_condition1 .= "`a`.`dist_id` in($job_location) OR ";
                    }
                }
            }
            if (count($whereJl1)) {
                $whereinJl1 = '(' . $job_location_condition1 . ')';
            }


            if ($job_title) {
                $jT = implode(',', $job_title);
                $whereJT1[] = "e.skills in($jT)";

                foreach ($job_title as $key => $title) {
                    if ($title == end($job_title)) {
                        $job_title_condition1 .= "`e`.`job_title` in($title)";
                    } else {

                        $job_title_condition1 .= "`e`.`job_title` in($title) OR ";
                    }
                }

            }
            if (count($whereJT1)) {
                $whereinJT1 = '(' . $job_title_condition1 . ')';
            }


            if ($gender) {
                $gender1 = $gender;
                $wheregender1[] = "e.gender = '$gender1' ";
            }
            if (count($wheregender1)) {
                $whereinGender1 = implode(' AND ', $wheregender1);
            }

            if ($marital) {
                $marital1 = $marital;
                $wheremarital1[] = "e.marital_status = '$marital1' ";
            }
            if (count($wheremarital1)) {
                $whereinMarital1 = implode(' AND ', $wheremarital1);
            }


            if ($work_status) {
                $work_status1 = $work_status;
                $wherework_status1[] = "e.work_status = '$work_status1' ";
            }
            if (count($wherework_status1)) {
                $whereinWorkStatus1 = implode(' AND ', $wherework_status1);
            }
//            $total_fcm=10;//testing purpose
//            print($total_fcm);exit;

            if ($total_fcm > 0) {
                $current_number = 0;
                for ($i = 0; $i < ($total_fcm / 1000); $i++) {
                    $fcmData1 = [];
                    $a = 0;
                    $fcmData = DB::table('employee as e')
                        ->selectRaw('e.fcm_id')
                        ->leftJoin('user_skills as s', 's.user_id', '=', 'e.id')
                        ->leftJoin('user_education as ue', 'ue.user_id', '=', 'e.id')
                        ->leftJoin('areas as a', 'a.id', '=', 'e.native_location')
                        ->where('e.is_deleted', '=', '0')
                        ->where('e.v_code', '>', '49')
                        ->whereRaw("e.fcm_id !='' ")
                        ->whereRaw("e.native_location !='' ")
//                        ->where('e.mobile1','=','9842701007')
                        ->whereRaw($whereinsQ)
                        ->whereRaw($whereinqQ)
                        ->whereRaw($whereinjSks)
                        ->whereRaw($whereinJl)
                        ->whereRaw($whereinJT)
                        ->whereRaw($whereinGender)
                        ->whereRaw($whereinMarital)
                        ->whereRaw($whereinWorkStatus)
                        ->whereRaw('((DATE(NOW()) - INTERVAL 30 DAY) < DATE(e.last_login))')
                        ->where('a.deleted', '=', '0')
                        ->groupBy('e.fcm_id')
//                        ->limit(1000, $current_number)
                        ->limit(1000)->offset($current_number)->get();
//                        ->get();
                    foreach ($fcmData as $key_data => $fcm_value) {
                        $fcmData1[] = $fcm_value->fcm_id;
                        $fcmData2[] = $fcm_value->fcm_id;
                    }
//                    $fcmData1[]='d4JsO5-_SkKHqSeMVghNpZ:APA91bGtCMAfv7qHhyzTmi_zUjch9eu6CSk8salbx6x8jQazIEWdFy983TFoHcSD__-EtTHF8QRHyZnkj6zGdsq7JlAm_jvixD90r8VEtwf1brO1AR4QE84RA6suGYVL5BxvtBckNxjD';
//                    $fcmData1[]='f1wrqNAFS6uVKmuvAfdg3b:APA91bGEwrxyOwvrvk7ZHQf4BwmZZ-D6lXR2sB1q-cedgouveWmiyqbALnjaqzXm5w_HyiO8HzQ2AqQTLc0gml2GT_EnpYt-b4QWpeKU5M3cuUl4PvaWXQ-Nna3Ne1Beb_uuhWpQ9Fa3';
//                    $fcmData1[]='egAsyoWrRoOaLzktYRvW0J:APA91bGBHunwMRgOnAutK1E-clTsZswrzD6EoF8SgecW-lBAjNakXa8k1EUAVXttlk1iG4_yjp7yEmNGMdhySC8jN01vEjy5_ZzR275h9f_BNhXdFyfp0qJGozU0QN14JkH2Z0rFzg0Y';
//                    $fcmData1[]='eAHy-hC-QhG8RBYSYSU6uX:APA91bFi1BTrM5fxGKIsPRQ59JpAvsVAEJYnUMKvsBO4gYZpblPEuGJJHOhUEfIZ429ZoRDjIH36bk8P57eM1tBbHYkOxYi10aKoggyOfP3JKgbQZlsVneS7BtDtHikHIgCHfLhMeW7x';
//                    $fcmData1[]='eIr542pUS9K2PCcPsJiWi2:APA91bH4xv1B4M3uWJXCe1jHOVWJlQHM4jF-Gn0kEEC0HGcnQScuLGwOFD33kHb1-Tg3Mnox_2tfCiUcMG1Trl8Vx-Y8pSGXdEyaspKJX_Ep-Bmr5cI77rBi75y6lksZlhTcjx0rARZN';
//                    $fcmData1[]='ec12y8aTQWCT7iOhL9-mW6:APA91bGyOK3uOrC4W67BmacehGCE50PGSTmc2uDjZ5mr3xdwCgmit7DbbzsAIm6rW3lD_doCMS-rRM3FvtDuFWq6xGNeXD8jzcXysp88ELiiM8my5iUVlNIcyx6YpKI5XJ3UWshEG01V';
//                    $fcmData1[]='dBjvGjGcSsSe-C5ovQK2bZ:APA91bGPn3LhqyjXPey2dHbcN64b26XP-EdDg4pINGeQu5KLW_7_j8fCMgXh7U4-EyOsZbSsvytZpamr5O-3sHjw7n32fW57yML3J2aGFvtQTMhnYB3_LforcWghpyLD09QZSI3BJx7F';

                    $response = $this->sendMultiple($fcmData1, $json);
                    $current_number += 1000;

                    $response = $this->fcmCountInsert($json, $bm, json_encode($msg, JSON_FORCE_OBJECT), $current_number, 4, $uid, 'EMPLOYEE', "portalv4/#!/adNotification", $ip);


                }
                return array(
                    'check1' => $fcmData1,
                    'check2' => $fcmData2,
                    'check' => $response,
                    'addStatus' => true,
                    'message' => 'Send Notification Successfully'
                );
            } else {
                return array('adStatus' => false, 'adMessage' => 'No User!');
            }

        } else {
            return array('adStatus' => false, 'adMessage' => 'No Data!');
        }
    }

    // sendMultiple
    public function sendMultiple($registration_ids, $message)
    {
//		return $registration_ids;
        $fields = array(
            'registration_ids' => $registration_ids,
            'data' => $message,
        );
//		return $fields;
        $response = $this->sendPushNotification($fields);
        return $response;
    }

    // sendPushNotification
    public function sendPushNotification($fields)
    {
//	return json_encode($fields).'<br>';
        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Authorization:key = AAAABSOwP7Y:APA91bGY0YT0MQE9CsE-Oedflevnd6Y8FftVU9F812nDXu4q4k-slW2McGdpY2f46a-CZzdliPf4Gb_wQAnGi_EMXSCNz1iBrGOsBz5EYxtojju0xhSxDgU2jG4KS21ptN6p2re4Hi5y',
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        return $result;
    }

    // fcm insert
    public function fcmCountInsert($json, $bm, $msgg, $total_fcm, $getMode, $getEventBy, $userType, $fromPage, $ip)
    {
        $dateTime = date('Y-m-d H:i:s');
        $inip = $ip;
        $addData = array(
            'json' => json_encode($json),
            'bm' => $bm,
            'ids' => $msgg,
            'total_fcm' => $total_fcm,
            'getMode' => $getMode,
            'getEventBy' => $getEventBy,
            'userType' => $userType,
            'fromPage' => $fromPage,
            'indate' => $dateTime,
            'inip' => $inip
        );
        try {
//            $this->db->insert('fcmCountTable', $addData);
            return $insert = DB::table('fcmCountTable')->insert($addData);
        } catch (\Exception $e) {

        }
    }


}
