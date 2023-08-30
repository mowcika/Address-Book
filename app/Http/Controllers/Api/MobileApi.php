<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\AddressModel;
use Illuminate\Support\Facades\DB;

class MobileApi extends Controller
{

    public function GetUserDetails($mobile){
        return AddressModel::where("mobile1", '=', $mobile)->get();
    }


    function SendOTP($mobile, $otp)
    {
        if ($mobile) {

            $msg = $otp . " is your OTP to verify your mobile number on Nithra app/website.";

            $msg = urlencode($msg);
            $url = "http://api.msg91.com/api/sendhttp.php?sender=NITHRA&route=4&mobiles=" . $mobile . "&authkey=221068AW6ROwfK5b2782c0&country=91&campaign=SOS&message=" . $msg . "&DLT_TE_ID=1307160853199181365";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            $response = curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
        }
    }

    public function dynamicServerJson(Request $request)
    {
        $indate = Carbon::now()->toDateTimeString();
        $inip = $request->ip();
        $action = $request->action;
        $mobile = $request->mobile;

        if ($action == 'getAddress') {
            $output = DB::table('address')
                ->selectRaw("id,name,address,dob,mobile1,mobile2,email,wnumber")
                ->where('is_delete', '=', '0')
                ->orderBy('id', 'ASC')->get();
        }elseif ($action == 'checkUser') {
            $otp = mt_rand(1111, 9999);

            $result_edit = $this->GetUserDetails($mobile);
//                        return $result_edit;
            if(count($result_edit) > 0){
                return $this->SendOTP($mobile,$otp);
                $update_array = array(
                    'otp' => $otp,
                    'otp_date' => $indate,

                );
                AddressModel::where("id", '=', $result_edit[0]->id)->update($update_array);
                $result_edit = $this->GetUserDetails($mobile);

                $output['status'] = 'success';
                $output['msg'] = 'Exiting User';
                $output['userid'] = $result_edit[0]->id;
                $output['otp'] = $result_edit[0]->otp;
                $output['mobile'] = $result_edit[0]->mobile1;

            }
            else{
                return $this->SendOTP($mobile,$otp);
//                return $result_edit[0]->name;
                $Userid = AddressModel::insertGetId([
                    'mobile1' => $mobile,
                    'otp' => $otp,
                    'otp_date' => $indate,
                    'cip' => $inip,
                    'cdate' => $indate,
                ]);
                $output['status'] = 'success';
                $output['msg'] = 'New User';
                $output['userid'] = $Userid;
                $output['otp'] = $otp;
                $output['mobile'] = $mobile;
//                return $Userid;
            }

//            return $result_edit;


        }
        elseif ($action=='pandiya'){
            $otp='0476';
            $mobile='6380161414';
            $msg = $otp . " is your OTP to verify your mobile number on Nithra app/website.";

            $msg = urlencode($msg);
            $url = "http://api.msg91.com/api/sendhttp.php?sender=NITHRA&route=4&mobiles=" . $mobile . "&authkey=221068AW6ROwfK5b2782c0&country=91&campaign=SOS&message=" . $msg . "&DLT_TE_ID=1307160853199181365";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            $response = curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
        }
        return $output;
    }
}
