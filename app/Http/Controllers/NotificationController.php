<?php

namespace App\Http\Controllers;

use App\Models\NotificationModel;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;
use App\Models\FcmModel;

class NotificationController extends Controller
{
    //
    public function saveNotification(Request $request)
    {
        $indate = Carbon::now()->toDateTimeString();
        if ($request->id) {
            NotificationModel::where('id', $request->id)->update([
                'app_name' => $request->appName,
                'notiType' => $request->notiType,
                'msgType' => $request->msgType,
                'title' => $request->title,
                'img' => $request->img,
                'msg' => $request->msg,
                'package_name' => $request->pname,
                'className' => $request->className,
                'sendType' => $request->sendType,
                'sendTypeValue' => $request->sendTypeValue,
                'typeCondition' => $request->typeCondition,
                'vcode' => $request->vcode,
                'weburl' => $request->weburl,
                'sendDate' => $request->sendDate,
                'mby' => $request->cby,

            ]);
            $result['status'] = 'success';
            $result['msg'] = 'The notification was Updated successfully';
        } else {

            DB::table('save_notification')->insertOrIgnore([
                'app_name' => $request->appName,
                'notiType' => $request->notiType,
                'msgType' => $request->msgType,
                'title' => $request->title,
                'img' => $request->img,
                'msg' => $request->msg,
                'package_name' => $request->pname,
                'className' => $request->className,
                'sendType' => $request->sendType,
                'sendTypeValue' => $request->sendTypeValue,
                'typeCondition' => $request->typeCondition,
                'vcode' => $request->vcode,
                'weburl' => $request->weburl,
                'sendDate' => $request->sendDate,
                'cby' => $request->cby,
                'cdate' => $indate,


            ]);
            $result['status'] = 'success';
            $result['msg'] = 'The notification was added successfully';
        }

        return $result;
//        return $request;
//        return 'pandiya';
    }

    public function getNotification(Request $request)
    {
        $page = $request->page;
        $shown = $request->shown;
        $id = $request->id;
        if ($id) {
            $where[] = ['n.id', $id];
        } else {
            if ($shown == 2) {
                $where[] = ['n.is_send', 1];
            } else {
                $where[] = ['n.is_send', 0];
            }
        }

        $where[] = ['n.is_delete', 0];

        $data = DB::table('save_notification as n')
            ->join('app_master as a', 'n.app_name', '=', 'a.id')
            ->join('noti_type as nt', 'n.notiType', '=', 'nt.id')
            ->join('msg_type as m', 'n.msgType', '=', 'm.id')
            ->select('n.id', 'n.title', 'n.package_name', 'n.className', 'n.weburl', 'm.type as msgType', 'n.is_send', 'n.img', 'a.app_name', 'nt.noti', 'n.msg',
                DB::raw("CASE sendType
WHEN '1' THEN 'EMAIL'
WHEN '2' THEN 'VERSION'
WHEN '3' THEN 'ALL'
ELSE 'VCODES'
END as sendType,date_format(n.sendDate,'%d-%m-%Y %h:%i:%s %p') as sendDate,date_format(n.cdate,'%d-%m-%Y %h:%i:%s %p') as addedDate"),
            )
            ->where($where)
            ->orderBy('n.sendDate', 'DESC')->paginate(3000);
//        return dd(\DB::getQueryLog());
        return $data;

    }


    public function getSingleNotification(Request $request)
    {


        $where[] = ['n.is_delete', 0];
        $where[] = ['n.id', $request->id];
        $data = DB::table('save_notification as n')
            ->where($where)
            ->orderBy('id', 'DESC')->get();
//        return dd(\DB::getQueryLog());
        return $data;

    }


    public function getSingleUser($request)
    {
        $id = $request['id'];
        $where[] = ['n.is_delete', 0];
        $where[] = ['n.device_id', $id];
        $data = DB::table('gcm_register as n')
            ->where($where)
            ->orderBy('id', 'DESC')->get();
//        return dd(\DB::getQueryLog());
        return $data;

    }

    public function deleteNotification(Request $request)
    {
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();
        if ($request->id) {

            NotificationModel::where('id', $request->id)->update([
                'is_delete' => 1,
            ]);

        }
        $request['page'] = 1;
        return $this->getNotification($request);
    }

    public function sendNotification(Request $request)
    {
//        $title = $request->input('title');
//        $message = $request->input('message');
//        $token = $request->input('token');
//        $date = $request->input('date');
//        $content_available = $request->input('content_available');
//        $pac = $request->input('pac');
//        $topic = $request->input('topic');
//        $bm = $request->input('bm');
//        $time = $request->input('time');
//        $type = $request->input('type');
//        $ntype = $request->input('ntype');
//        $title = $request->input('title');
//        $message = $request->input('message');
//        $url = $request->input('url');

        $notiDetails = $this->getSingleNotification($request);
        $notiarray = json_decode(json_encode($notiDetails), true);
        $notiType = $notiarray[0]['notiType'];
        $msgType = $notiarray[0]['msgType'];
        $title = $notiarray[0]['title'];
        $img = $notiarray[0]['img'];
        $msg = $notiarray[0]['msg'];
        $package_name = $notiarray[0]['package_name'];
        $className = $notiarray[0]['className'];
        $weburl = $notiarray[0]['weburl'];
        $sendDate = $notiarray[0]['sendDate'];

        $timestamp = (strtotime($sendDate));
        $notidate = date('d-m-Y', $timestamp);
        $notitime = date('h:i:s A', $timestamp);
        $getdate = array(
            'id' => '846af96817747911',
        );
        $sendNotiUser = $this->getSingleUser($getdate);
        $Userarray = json_decode(json_encode($sendNotiUser), true);
        $token = $Userarray[0]['fcm_id'];
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'user not found']);
        }
        // Load the service account JSON file
        $serviceAccount = json_decode(file_get_contents('https://diyastore.in/server1.json'), true);

        // Create the JWT token for authentication
        $now = time();
        $payload = [
            'iss' => $serviceAccount['client_email'],
            'sub' => $serviceAccount['client_email'],
            'aud' => 'https://fcm.googleapis.com/',
            'iat' => $now,
            'exp' => $now + 3600, // Token expires in 1 hour
        ];
        $jwt = JWT::encode($payload, $serviceAccount['private_key'], 'RS256');

        // Create the Guzzle HTTP client
        $client = new Client();

        // Prepare the FCM endpoint URL
        $url = 'https://fcm.googleapis.com/v1/projects/api-project-22073589686/messages:send';

        // Prepare the request headers
        $headers = [
            'Authorization' => 'Bearer ' . $jwt,
            'Content-Type' => 'application/json',
        ];

        // Prepare the request payload
        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,


                ],
                'data' => [
                    'Type' => "$notiType",
                    'msgType' => "$msgType",

                    'title' => "$title",
                    'img' => "$img",
                    'msg' => "$msg",
                    'package_name' => "$package_name",
                    'className' => "$className",
                    'web' => "$weburl",
                    'sendDate' => "$sendDate",
                    'date' => "$notidate",
                    'time' => "$notitime",

                ],
            ],
        ];

//        print_r($payload);exit;
        // Send the request
        try {
            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $payload,
            ]);

            // Handle the response
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();

            NotificationModel::where('id', $request->id)->update([
                'is_send' => 1,
                'send_by' => $request->userid,
                'send_date' => Carbon::now()->toDateTimeString(),
            ]);

            if ($request->fromArea) {
                $request['shown'] = '1';
            } else {
                $request['shown'] = '2';
            }

            unset($request['id']);


            if ($statusCode == 200) {
                // Successful request

                return $this->getNotification($request);
//                return response()->json(['success' => true, 'message' => 'Push notification sent successfully']);
            } else {
                // Error in the request
                return $this->getNotification($request);
            }
        } catch (\Exception $e) {
            // Exception occurred during the request
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

    }
}
