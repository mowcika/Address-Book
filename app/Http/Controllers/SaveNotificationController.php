<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SaveNotificationController extends Controller
{
    // save notification
    public function saveNotificationPart(Request $request)
    {
//        print_r($request) ;
        $userid = $request->localUserid;
        $inip = $request->ip();
        $indate = date('Y-m-d H:i:s');
        $formDetails_emp = $request->formDetails_emp;
        $formDetails_job = $request->formDetails_job;
        // echo "bala : ".serialize($formDetails_job);
//        print_r($formDetails_job['gQualification']);exit;
        $empIds = array();
//        foreach ($formDetails_job['gQualification'] as $index => $item) {
//            $empIds[] = $item['id'];
//        }
        $gQ = implode(",", $formDetails_job['gQualification']);
        $qroup_quali = array(
            '@#@' . $gQ
        );
        $add_data = array(
            'district' => $request->formDetails_job['district'],
            'sQualification' => $request->formDetails_job['sQualification'],
            'gQualification' => $qroup_quali,
            'job_title' => $request->formDetails_job['job_title'],
        );
//        return $add_data;
        // print_r(serialize($add_data));exit;
        //print_r(implode('@#@',$empIds));exit;
//        $formDetails_job['gQualification'] = implode('@#@',$empIds);
//        $formDetails_job = $formDetails_job['gQualification'];
//        print_r($formDetails_job);exit;

        $fields = array(
            'cby' => $userid,
            'cip' => $inip,
            'cdate' => $indate,
            'job_filter' => serialize($add_data),
            'emp_filter' => serialize($formDetails_emp),
            'title' => $request->formDetails_emp['msg_title'],
        );
        //echo json_encode($fields);exit;
        if (sizeof($fields)) { //return $inserDataArray;
            $Details = DB::table('dynamic_notification_test')->insertOrIgnore([
                $fields
            ]);
//            $Details = DB::table('dynamic_notification_test')->insert($fields);
//            $Details = DB::table('dynamic_notification')->insert($fields);
//                dd(\DB::getQueryLog());
//            DB::getQueryLog();
            if ($Details) {
                return array('adStatus' => true, 'adMessage' => 'Notification Added Successfully');
            } else {
                return array('adStatus' => false, 'adMessage' => 'Notification Not Insert!');
            }
        } else {
            return array('adStatus' => false, 'adMessage' => 'Something went wrong!');
        }

    }
}
