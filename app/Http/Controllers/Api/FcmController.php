<?php

namespace App\Http\Controllers\Api;

use App\Models\FSEModel;
use App\Models\FcmModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FcmController extends Controller
{
    public function FcmPro(Request $request)
    {
        $indate = Carbon::now()->toDateTimeString();
        $inip = $request->ip();

        $action = $request->action;

        if ($action == 'saveFcm') {
            $device_id = $request->device_id;
            $fcm_id = $request->fcm_id;
            $device_type = $request->device_type;
            $vcode = $request->vcode;
            $device_version = $request->device_version;
            $app_name = $request->app_name;
//        $checking= FcmModel::where([['app_name', $app_name],['device_id', $device_id]])->get(['id']);
            $condition = DB::table('gcm_register')->select('id')
                ->where('app_name', $app_name)
                ->where('device_id', $device_id)
                ->first();
//        echo $condition->id;exit;
//        print_r($condition);exit;
            if ($condition) {
                $id = $condition->id;

                FcmModel::where('id', $id)->update([
                    'device_id' => $request->device_id,
                    'fcm_id' => $request->fcm_id,
                    'device_type' => $request->device_type,
                    'vcode' => $request->vcode,
                    'device_version' => $request->device_version,
                    'app_name' => $request->app_name,
                ]);
                $result['status'] = 'success';
                $result['msg'] = 'The information was updated successfully';
                return $result;
            } else {

                DB::table('gcm_register')->insertOrIgnore([
                    'device_id' => $request->device_id,
                    'fcm_id' => $request->fcm_id,
                    'device_type' => $request->device_type,
                    'vcode' => $request->vcode,
                    'device_version' => $request->device_version,
                    'app_name' => $request->app_name,
                    'cip' => $inip,
                    'cdate' => $indate,

                ]);

                $result['status'] = 'success';
                $result['msg'] = 'The information was inserted successfully';
                return $result;
            }
        } elseif ($action == 'saveReferral') {
            $app = $request->input("app");
            $ref = $request->input("ref");
            $m = $request->input("m");
            $c = $request->input("c");
            $device_id = $request->input("device_id");
            DB::table('referral_table')->insertOrIgnore([
                'device_id' => $device_id,
                'app_name' => $app,
                'ref' => $ref,
                'c' => $c,
                'm' => $m,
                'cdate' => $indate,

            ]);

            $result['status'] = 'success';
            $result['msg'] = 'The information was inserted successfully';
            return $result;
        } elseif ($action == 'saveFeedback') {
            $device_id = $request->input("device_id");
            $app_name = $request->input("app_name");
            $feedback_type = $request->input("feedback_type");
            $mobile = $request->input("mobile");
            $feedback = $request->input("feedback");
            $email = $request->input("email");
            $vcode = $request->input("vcode");
            $device_type = $request->input("device_type");
            DB::table('feedback_table')->insertOrIgnore([
                "device_id" => $device_id,
                "app_name" => $app_name,
                "feedback_type" => $feedback_type,
                "mobile" => $mobile,
                "feedback" => $feedback,
                "email" => $email,
                "vcode" => $vcode,
                "device_type" => $device_type,
                'cdate' => $indate,

            ]);

            $result['status'] = 'success';
            $result['msg'] = 'The information was inserted successfully';
            return $result;
        } else {
            $result['status'] = 'failure';
            $result['msg'] = 'Need Action Key';
            return $result;
        }

    }
}
