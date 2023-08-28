<?php

namespace App\Http\Controllers;

use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\FreeCallingModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class FreeCallingController extends Controller
{
    public function freeCallingMobileNumberTrim($str)
    {
        $str = trim($str ?? '');
        $stringLength = strlen($str);
        switch ($stringLength) {
            case ($stringLength == 13 && substr($str, 0, ($stringLength - 10)) == '+91'): // +91
                return substr($str, ($stringLength - 10), $stringLength);
                break;
            case ($stringLength == 12 && substr($str, 0, ($stringLength - 10)) == '91'): // 91
                return substr($str, ($stringLength - 10), $stringLength);
                break;
            case (substr($str, 0, 3) == '+91'): // +91
                return substr($str, 3, $stringLength);
                break;
            default:
                return ltrim($str, "+");
        }
    }

    public function getFreeCallingUploadExcel()
    {
        return DB::table('freecalling_details as fcd')
            ->selectRaw('fcd.`assigned_to`, u.`name`, COUNT(fcd.`assigned_to`) as `tCount`, SUM(fcd.`existStatus` = 0) as `notTaken`, SUM(fcd.`existStatus` = 2) as `duplicate`,
SUM(fcd.`existStatus` = 1) as `taken`, SUM(fcd.`LastfollowStatus` = 1) as `confirmed`')
            ->join('user as u', 'u.id', '=', 'fcd.assigned_to')
            ->where([['fcd.assigned_to', '!=', 0]])
            ->whereNotNull('fcd.assigned_to')
            ->groupBy('fcd.assigned_to')
            ->orderBy('u.name', 'DESC')
            ->get();
    }

    public function getFreeDistrict($table_name, $columns)
    {
        return DB::table($table_name)
            ->select($columns)
            ->where([['assigned_by', NULL], ['existStatus', 0]])
            ->where(function ($query) {
                $query->where('assigned_to', NULL);
                $query->orWhere('assigned_to', 0);
            })
            ->groupBy('district')
            ->orderBy(DB::raw('(name = "") DESC, district'))
            ->get();
    }

    public function getResource($table_name, $columns, $where = [], $whereInKey = '', $whereInValue = [])
    {
        return DB::table($table_name)
            ->select($columns)
            ->where($where)
            ->when($whereInKey !== '', function ($query) use ($whereInKey, $whereInValue) {
                return $query->whereIn($whereInKey, $whereInValue);
            })
//            ->whereIn($whereInKey, $whereInValue)
            ->get();
//            ->toSql();
    }

    public function getUsers($table_name, $columns)
    {
        return DB::table($table_name)
            ->select($columns)
            ->join('paymentTeamCompetitionMembers as ptcb', 'u.id', '=', 'ptcb.members')
            ->join('paymentTeamCompetitionMaster as ptcm', 'ptcm.id', '=', 'ptcb.teamName')
            ->where([['u.is_active', 0]])
            ->whereIn('ptcm.id', [6, 7, 8, 9, 10])
            ->get();
    }

    public function getFreeCallingAddDrop()
    {
        if (Redis::exists('getFreeCallingAddDrop')) {
            $result = unserialize(Redis::get('getFreeCallingAddDrop'));
            $result['from'] = 'redis';
        } else {
            $result['status'] = $this->getResource('freecalling_followup_master', ['id', 'status as name', 'mode']);
            $result['companySource'] = $this->getResource('company_source', ['id', 'source as name'], [['is_delete', 0]], 'id', [4, 25, 31]);
            $result['state'] = $this->getResource('state_master', ['id', 'name'], [], 'id', [1, 3, 18, 19, 28, 32]);
            $result['district'] = $this->getResource('free_calling_dist', ['id', 'english as name', 'state']);
            $result['city'] = $this->getResource('free_calling_city', ['id', 'area_name as name', 'district']);
            $result['from'] = 'database';
            Redis::set('getFreeCallingAddDrop', serialize($result));
            Redis::expire('getFreeCallingAddDrop', 86400);
        }
        return $result;
    }

    public function getCompanySource()
    {
        if (Redis::exists('getCompanySource')) {
            $result = unserialize(Redis::get('getCompanySource'));
            $result['from'] = 'redis';
        } else {
            $result['getFreeDistrict'] = $this->getFreeDistrict('freecalling_details', ['district as id', DB::raw('CONCAT(IF(district IS NULL OR district = "", "No District", district), " (",COUNT(*), ")") as name')]);
            $result['getUsers'] = $this->getUsers('user as u', ['u.id', 'u.name', 'u.role', 'u.usergroup']);
            $result['companySource'] = $this->getResource('company_source', ['id', 'source as name'], [['is_delete', 0]], 'id', [4, 25, 31]);
            $result['getFreeCallingUploadExcel'] = $this->getFreeCallingUploadExcel();
            $result['from'] = 'database';
            Redis::set('getCompanySource', serialize($result));
            Redis::expire('getCompanySource', 86400);
        }
        return $result;
    }

    public function freeCallingUpload(Request $request)
    {
        \DB::enableQueryLog();
        $indate = Carbon::now()->toDateString();
        $getSource = $request->source;
        $uid = $request->uid;
        $inip = $request->ip();
        $file = $request->file('fileUpload');
        $response = array('status' => false, 'message' => 'Failed');
        if (empty($file))
            return $response;
        if (Storage::disk('local')->put('public/freeCallingUpload/' . $file->getClientOriginalName(), file_get_contents($file))) {
//        $storagePath = Storage::disk('local')->path('public/freeCallingUpload/' . $file->getClientOriginalName());
            $filename = storage_path('app/public/freeCallingUpload/' . $file->getClientOriginalName());
            $new_file_name = $filename;
            $file = fopen($filename, "r");
            $getCSV = array();
            while (($data = fgetcsv($file, 200, ",")) !== FALSE) {
                $getCSV[] = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            }

            if (sizeof($getCSV))
                unset($getCSV[0]);
//            return $getCSV;
            $inserDataArray = array();
            foreach ($getCSV as $csvKey => $data) {
                if ($getSource == 25) {
                    $result1 = array( // b2b
                        'name' => $data[1], // name
                        'designation' => $data[2],
                        'companyName' => $data[3],
                        'district' => $data[4],
                        'city' => $data[5],
                        'mobile1' => $this->freeCallingMobileNumberTrim($data[6]),
                        'mobile2' => $this->freeCallingMobileNumberTrim($data[7]),
                        'email' => $data[8],
                        'excel_upload' => $new_file_name,
                        'source' => $getSource,
                        'inby' => $uid,
                        'inip' => $inip,
                        'indate' => $indate,
                    );
                } else if ($getSource == 31) {
                    $result2 = array( // just dial
                        'category' => $data[0], // category
                        'companyName' => $data[2],
                        'district' => $data[1],
                        'city' => $data[3],
                        'address' => $data[4],
                        'mobile1' => $this->freeCallingMobileNumberTrim($data[7]),
                        'mobile2' => $this->freeCallingMobileNumberTrim($data[6]),
                        'whatsapp' => $data[7],
                        'website' => $data[9],
                        'verified' => $data[10],
                        'paid' => $data[11],
                        'email' => $data[5],
                        'excel_upload' => $new_file_name,
                        'source' => $getSource,
                        'inby' => $uid,
                        'inip' => $inip,
                        'indate' => $indate,
                    );
                } else {
                    $result3 = array( // direct to app
                        'companyName' => $data[0], // category
                        'mobile1' => $this->freeCallingMobileNumberTrim($data[1]),
                        'excel_upload' => $new_file_name,
                        'source' => $getSource,
                        'inby' => $uid,
                        'inip' => $inip,
                        'indate' => $indate,
                    );
                }

                if (!empty($data[7]) || !empty($data[6]) || 1 == 1) { // 1 == 1 to allow all
                    if ($getSource == 25) { // b2b
//                        $result = $result1;
                        $inserDataArray[] = $result1;
                    } elseif ($getSource == 31) {// just dial
                        $inserDataArray[] = $result2;
                    } elseif ($getSource == 4) {// direct to app
                        $inserDataArray[] = $result3;
                    } else {
                        return array('status' => false, 'message' => 'Something went wrong!');
                    }
                }
            }
            if (sizeof($inserDataArray)) { //return $inserDataArray;
                $Details = DB::table('freecalling_details')->insert($inserDataArray);
//                dd(\DB::getQueryLog());
                \DB::getQueryLog();
                if ($Details) {
                    return array('status' => true, 'message' => 'Uploaded Successfully');
                } else {
                    return array('status' => false, 'message' => 'Something went wrong!');
                }
            } else {
                return array('status' => false, 'message' => 'Something went wrong!');
            }
        } else { // if file couldn't move
            return array('status' => false, 'message' => 'File Could not upload!');
        }
//        return $response;
    }

    public function freeCallingAssignTo(Request $request)
    {
        \DB::enableQueryLog();
//        $indate = Carbon::now()->toDateString(); // only date
        $indate = Carbon::now()->toDateTimeString(); // date with time
        $inip = $request->ip();

        $assignTo = $request->users;
        $fromNum = $request->count;
        $district = $request->district;
        $sourceData = $request->source;
        $uid = $request->uid;
        $Details = 0;
        $response = array('status' => false, 'message' => 'Failed');

        $userCount = sizeof($assignTo);
        $getModule = ($fromNum % $userCount);
        $getDivide = floor($fromNum / $userCount);
        $getDivide = ($getDivide == 0) ? 1 : $getDivide;
        foreach ($assignTo as $index => $a) {
            if ($index < $fromNum) {
                $isLimit = $getDivide;
                if ($index < $getModule)
                    $isLimit++;
                $assign = array(
                    "assigned_date" => $indate,
                    "assigned_by" => $uid,
                    "assigned_to" => $a,
                );

                $Details += DB::table('freecalling_details')
                    ->where([['inby', 199], ['assigned_by', NULL], ['existStatus', 0], ['district', $district], ['source', $sourceData]])
                    ->where(function ($query) {
                        $query->where('assigned_to', NULL);
                        $query->orWhere('assigned_to', 0);
                    })
                    ->orderBy(DB::raw('rand()'))
                    ->take($isLimit)
                    ->update($assign);
                \DB::getQueryLog();
            }
        }
        if ($Details > 0) {
            $response = array('status' => true, 'message' => 'Assigned Successfully');
        } else {
            $response = array('status' => false, 'message' => 'Something went wrong!');
        }
        return $response;
    }

    public function deleteJobs(Request $request)
    {
        \DB::enableQueryLog();

        DB::table($request->table)
            ->where('id', $request->id)
            ->update([
                'deleted' => 1,
            ]);
//        ddd(\DB::getQueryLog());

    }

    public function addFreeFollowup(Request $request)
    {
        $indate = Carbon::now()->toDateTimeString(); // date with time
        $inip = $request->ip();
        $in_array = array(
            'freecalling_id' => $request->id,
            'followStatus' => $request->status,
            'followRemarks' => $request->remarks,
            'nextFollowupDate' => $request->nextFollowupDate,
            'inby' => $request->uid,
            'inip' => $inip,
            'indate' => $indate
        );
        DB::table('freecalling_followup')
            ->insert([$in_array]);
        // update freecalling details
        $update_array = array(
            'LastfollowStatus' => $request->status,
            'LastFollowRemarks' => $request->remarks,
            'nextFollowupDate' => $request->nextFollowupDate,
            'LastFollowDate' => $indate
        );
        $ins = DB::table('freecalling_details')
            ->where('id', $request->id)
            ->update($update_array);
        // update freecalling details
        if ($ins > 0) {
//            $waWebMasterOnlyFor = array(3, 10, 11, 12, 13, 14, 15, 16);
//            if (in_array($request->status, $waWebMasterOnlyFor))
//                $responseWAInstance = $this->freeFollowUpWAInstance($request->id, $request->uid, 1);
            // update freecalling Auto-close when repeats same status 3 times except followup - 4
            $sqlAutoClose = "SELECT aa.`freecalling_id`, aa.`followStatus`, COUNT(*) FROM (SELECT `fcf`.* FROM `freecalling_followup` `fcf`
WHERE `fcf`.`freecalling_id` = $request->id AND $request->status != 4
ORDER BY `fcf`.`id` DESC LIMIT 3 ) aa GROUP BY aa.`followStatus` HAVING COUNT(*) > 2";
            $resAutoClose = DB::select($sqlAutoClose);
            $getData = json_decode(json_encode($resAutoClose), true);
            if (sizeof($getData)) {
                $in_array = array(
                    'freecalling_id' => $request->id,
                    'followStatus' => 16,
                    'followRemarks' => "Auto Close - " . $request->status . " : " . $request->remarks,
                    'nextFollowupDate' => $request->nextFollowupDate,
                    'inby' => $request->id,
                    'inip' => $inip,
                    'indate' => $indate
                );
                DB::table('freecalling_followup')
                    ->insert([$in_array]);

                // update freecalling details
                $update_array = array(
                    'LastfollowStatus' => 16,
                    'LastFollowRemarks' => "Auto Close - " . $request->status . " : " . $request->remarks,
                    'nextFollowupDate' => $request->nextFollowupDate,
                    'LastFollowDate' => $indate
                );
                DB::table('freecalling_details')
                    ->where('id', $request->id)
                    ->update($update_array);
            }
            // update freecalling Auto-close when repeats same status 3 times

//            $this->db->where('source', 1); // 1 is freecalling
//            $this->db->where('lastEventStatus', 0);
//            $this->db->where('sourceId', $request->id);
//            $this->db->delete('freePaidCallingToEmpr');

            $result['status'] = true;
            $result['message'] = 'Freecalling Followup added successfully';
        } else {
            $result['status'] = false;
            $result['message'] = 'Could not add Freecalling Details!';
        }
        return $result;
    }

    public function addCustomFreeFollowup(Request $request)
    {
        $indate = Carbon::now()->toDateTimeString(); // date with time
        $inip = $request->ip();
        // $phone, $newCompanyName, $notAppliciable, $status, $nextFollowupDate, $source, $remarks, $district, $city, $localUserid, $inip, $indate, $state
//        if ($newCompanyName == '' || $notAppliciable != '') {
//            $newCompanyName = $notAppliciable;
//        }
        // update telecalling details
        $update_array = array(
            'companyName' => $request->newCompanyName,
            'mobile2' => $request->phone,
            'source' => $request->source,
            'state' => $request->state,
            'district' => $request->district,
            'city' => $request->city,
            'LastfollowStatus' => $request->status,
            'LastFollowRemarks' => $request->remarks,
            'nextFollowupDate' => $request->nextFollowupDate,
            'existStatus' => 1,
            'existDate' => $indate,
            'assigned_to' => $request->uid,
            'assigned_date' => $indate,
            'inby' => $request->uid,
            'LastFollowDate' => $indate,
            'inip' => $inip,
            'indate' => $indate
        );
        $freecall_Id = DB::table('freecalling_details')->insertGetId($update_array);
//                dd(\DB::getQueryLog());
        \DB::getQueryLog();
//        $waWebMasterOnlyFor = array(3, 10, 11, 12, 13, 14, 15, 16);
//        if (in_array($status, $waWebMasterOnlyFor))
//            $responseWAInstance = $this->freeFollowUpWAInstance($freecall_Id, $localUserid, 2);
        $in_array = array(
            'freecalling_id' => $freecall_Id,
            'followStatus' => $request->status,
            'followRemarks' => $request->remarks,
            'nextFollowupDate' => $request->nextFollowupDate,
            'inby' => $request->uid,
            'inip' => $inip,
            'indate' => $indate
        );
        $ins = DB::table('freecalling_followup')->insert([$in_array]);
        if ($ins > 0) {
            $result['status'] = true;
            $result['message'] = 'Freecalling Followup added successfully';
            $result['last_insert_id'] = $freecall_Id;
        } else {
            $result['status'] = false;
            $result['message'] = 'Could not add Freecalling Details!';
        }
        return $result;
    }

    public function customfreetelecheck(Request $request)
    {
        $mobile1 = trim($request->mobile1 ?? '') ? $request->mobile1 : '';
        $mobile2 = trim($request->mobile2 ?? '') ? $request->mobile2 : '';
        $getMobileArray = array_filter(array($mobile1, $mobile2));
        $getMobileArrayString = '"' . implode('","', $getMobileArray) . '"';

        $cntRes = 'SELECT GROUP_CONCAT(CONCAT(id, " : ", tableName)) as tableResult, GROUP_CONCAT(tableId) as tableResultID FROM ((SELECT id, "employer" as tableName, 2 as tableId FROM employer_registration WHERE phonenumber IN (' . $getMobileArrayString . ') AND phonenumber != "" AND company_details != "")
							UNION (SELECT id, "telecalling_details_listed_new" as tableName, 3 as tableId FROM telecalling_details_listed_new WHERE phone IN (' . $getMobileArrayString . ') AND phone !="" AND status IN ("Ok", "Already Verified", "Verified Company", "Followup") AND status_date > (CURDATE() - INTERVAL 90 DAY) group BY `status`)
							UNION (SELECT id, "telecalling_details" as tableName, 4 as tableId FROM telecalling_details WHERE phone IN (' . $getMobileArrayString . ') AND phone !="" AND status IN ("Ok", "Already Verified", "Verified Company", "Followup") AND status_date > (CURDATE() - INTERVAL 90 DAY) group BY `status`)
							UNION (SELECT id, "freecalling_details" as tableName, 5 as tableId FROM freecalling_details WHERE (("' . $mobile1 . '" IN (mobile1, mobile2) AND "' . $mobile1 . '" != "") OR ("' . $mobile2 . '" IN (mobile1, mobile2) AND "' . $mobile2 . '" != "")) AND LastfollowStatus > 0)
							UNION (SELECT id, "jobs" as tableName, 6 as tableId FROM jobs WHERE (("' . $mobile1 . '" IN (phone, phone_new) AND "' . $mobile1 . '" != "") OR ("' . $mobile2 . '" IN (phone, phone_new) AND "' . $mobile2 . '" != "" )))) checkDuplicate';
        $getData1 = DB::select($cntRes);
        $getData = json_decode(json_encode($getData1), true);
        if (!$getData[0]['tableResultID'])
            unset($getData[0]);
//		print_r(count($getData));exit;
        $checkEmprNoJobCount = 0;
//		if (count($getData)) {
//			$getOnlyEmprNotJobArray = explode(",", $getData[0]['tableResultID']);
//			if (sizeof($getOnlyEmprNotJobArray) == 1 && in_array(2, $getOnlyEmprNotJobArray) && !in_array(6, $getOnlyEmprNotJobArray)) {
//				$getOnlyEmprNotJobID = explode(" : ", $getData[0]['tableResult'])[0];
//				$this->db->where('employer', $getOnlyEmprNotJobID);
//				$check = $this->db->get('jobs')->result(); //print_r($this->db->last_query());exit;
//				if (count($check)) {
//					$checkEmprNoJobCount = count($check);
//				}
//			}
//		}
        if (count($getData)) {
            if (!str_contains($getData[0]['tableResult'], 'freecalling_details')) {
                $result['status'] = true;
                $result['message'] = 'No result Matched for this number., You can make Follow';
            } else {
                foreach (explode(',', $getData[0]['tableResult']) as $item) {
                    if (str_contains($item, 'freecalling_details')) {
                        $split_id = explode(' : ', $item);
                        break;
                    }
                }
                $usernames = DB::table('freecalling_details as fd')
                    ->selectRaw("u.name as assignedUser, if(ffm.mode=1,'Spoken','Not Spoken') as SpokeStatus, ffm.status as FollowupName, DATE_FORMAT(fd.LastFollowDate,'%d-%m-%Y') as lastfollowupDate , ffm.status as followStatus, fd.LastFollowRemarks as followRemarks,date_format(fd.LastFollowDate, '%d-%m-%Y') as FollowupDates")
                    ->join('freecalling_followup_master as ffm', 'ffm.id', '=', 'fd.LastfollowStatus')
                    ->join('user as u', 'u.id', '=', 'fd.assigned_to')
                    ->where([['fd.id', '=', $split_id[0]]])
                    ->get();
                $result['status'] = false;
                $result['message'] = "This Number Already Exist in <br>" . $getData[0]['tableResult'] . "<br>--------------------------------------------------<br> Last Followup Date : " . $usernames[0]->lastfollowupDate . ", <br>Followed By : " . $usernames[0]->assignedUser . ", <br>Followup Status : " . $usernames[0]->SpokeStatus . ", <br>Followup Response : " . $usernames[0]->FollowupName . ", <br>Followup Remarks : " . $usernames[0]->followRemarks;
            }
        } else {
            $result['status'] = true;
            $result['message'] = 'No result Matched for this number., You can make Follow';
        }
        return $result;
    }

    public function freetelecheck(Request $request)
    {
        $id = $request->id;
        $mobile1 = $request->mobile1;
        $mobile2 = $request->mobile2;
        $localUserid = $request->localUserid;
        $indate = Carbon::now()->toDateTimeString(); // date with time
        $inip = $request->ip();
        $mobile1 = trim($mobile1 ?? '') ? $mobile1 : '';
        $mobile2 = trim($mobile2 ?? '') ? $mobile2 : '';
        $getMobileArray = array_filter(array($mobile1, $mobile2));
        $getMobileArrayString = '"' . implode('","', $getMobileArray) . '"';

        $cntRes = 'SELECT GROUP_CONCAT(CONCAT(id, " : ", tableName)) as tableResult, GROUP_CONCAT(tableId) as tableResultID FROM ((SELECT id, "employer" as tableName, 2 as tableId FROM employer_registration WHERE phonenumber IN (' . $getMobileArrayString . ') AND phonenumber != "" AND company_details != "")
							UNION (SELECT id, "telecalling_details_listed_new" as tableName, 3 as tableId FROM telecalling_details_listed_new WHERE phone IN (' . $getMobileArrayString . ') AND phone !="" AND status IN ("Ok", "Already Verified", "Verified Company", "Followup") AND status_date > (CURDATE() - INTERVAL 90 DAY) group BY `status`)
							UNION (SELECT id, "telecalling_details" as tableName, 4 as tableId FROM telecalling_details WHERE phone IN (' . $getMobileArrayString . ') AND phone !="" AND status IN ("Ok", "Already Verified", "Verified Company", "Followup") AND status_date > (CURDATE() - INTERVAL 90 DAY) group BY `status`)
							UNION (SELECT id, "freecalling_details" as tableName, 5 as tableId FROM freecalling_details WHERE id != "' . $id . '" AND (("' . $mobile1 . '" IN (mobile1, mobile2) AND "' . $mobile1 . '" != "") OR ("' . $mobile2 . '" IN (mobile1, mobile2) AND "' . $mobile2 . '" != "")) AND LastfollowStatus > 0)
							UNION (SELECT id, "jobs" as tableName, 6 as tableId FROM jobs WHERE (("' . $mobile1 . '" IN (phone, phone_new) AND "' . $mobile1 . '" != "") OR ("' . $mobile2 . '" IN (phone, phone_new) AND "' . $mobile2 . '" != "" )))) checkDuplicate';
        $getData1 = DB::select($cntRes);
        $getData = json_decode(json_encode($getData1), true);
        if (!$getData[0]['tableResultID'])
            unset($getData[0]);
        if (count($getData)) {
            $assign = array(
                "existStatus" => 2,
                "existDate" => $indate,
                "existRemarks" => $getData[0]['tableResult']
            );
            $result['status'] = false;
            $result['message'] = 'These Numbers Already Exist in ' . $getData[0]['tableResult'];
        } else {
            $assign = array(
                "existStatus" => 1,
                "existDate" => $indate,
                "existRemarks" => NULL
            );
            $result['status'] = true;
            $result['message'] = 'No result Matched for these numbers., You can make Follow';
        }
//        DB::table('freecalling_details')
//        ->where('id', $id)
//        ->update($assign);

//        DB::statement('UPDATE `freecalling_details` SET `existStatus` = 2, `existDate` = "' . $indate . '", `existRemarks` = "this table duplicate ID : ' . $id . '"
//					WHERE `id` != "' . $id . '" AND (("' . $mobile1 . '" IN(`mobile1`, `mobile2`) AND "' . $mobile1 . '" != "") OR ("' . $mobile2 . '" IN(`mobile1`, `mobile2`) AND "' . $mobile2 . '" != ""))
//					AND (`LastfollowStatus` IS NULL OR `LastfollowStatus` = 0)');
        return $result;
    }

    public function getFreeCallingDistrict(Request $request)
    {
        $localUserid = $request->localUserid;
        $getData['district'] = DB::table('freecalling_details as fcd')
            ->selectRaw("fcd.district, COUNT(*) as count")
            ->whereIn('fcd.LastfollowStatus', [0, 2, 3, 4, 10, 12, 14])
            ->where([['fcd.assigned_to', $localUserid]])
            ->whereRaw('fcd.existStatus < 2')
            ->whereRaw('((fcd.nextFollowupDate <= CURDATE() AND fcd.LastfollowStatus = 4) OR fcd.LastfollowStatus != 4)')
            ->whereRaw('((DATE(fcd.LastFollowDate) + INTERVAL 30 DAY <= CURDATE() AND fcd.LastfollowStatus = 2) OR fcd.LastfollowStatus != 2)')
            ->whereRaw('((DATE(fcd.LastFollowDate) != CURDATE() AND fcd.LastfollowStatus > 0 ) OR fcd.LastfollowStatus = 0)')
            ->groupBy('fcd.district')
            ->orderBy('fcd.district', 'asc')
            ->get();

        $getData['source'] = DB::table('freecalling_details as fcd')
            ->selectRaw("fcd.source as sid, cs.source, COUNT(*) as count")
            ->leftJoin('company_source as cs', 'cs.id', '=', 'fcd.source')
            ->whereIn('fcd.LastfollowStatus', [0, 2, 3, 4, 10, 12, 14])
            ->where([['fcd.assigned_to', $localUserid]])
            ->whereRaw('fcd.existStatus < 2')
            ->whereRaw('((fcd.nextFollowupDate <= CURDATE() AND fcd.LastfollowStatus = 4) OR fcd.LastfollowStatus != 4)')
            ->whereRaw('((DATE(fcd.LastFollowDate) + INTERVAL 30 DAY <= CURDATE() AND fcd.LastfollowStatus = 2) OR fcd.LastfollowStatus != 2)')
            ->whereRaw('((DATE(fcd.LastFollowDate) != CURDATE() AND fcd.LastfollowStatus > 0 ) OR fcd.LastfollowStatus = 0)')
            ->groupBy('fcd.source')
            ->orderBy('cs.source', 'asc')
            ->get();

        $result['getData'] = $getData;
        return $result;
    }

    public function viewFreeCallingDetails(Request $request)
    {
        $indate = Carbon::now()->toDateTimeString();
        $jayParsedAry = [
            [
                "make" => $indate,
                "model" => "Boxter",
                "price" => 72000
            ],
            [
                "make" => "Ford",
                "model" => "Mondeo",
                "price" => 32000
            ],
            [
                "make" => "Ford",
                "model" => "Mondeo",
                "price" => 32000
            ],
            [
                "make" => "Toyota",
                "model" => "Celica",
                "price" => 35000
            ],
            [
                "make" => "Toyota",
                "model" => "Celica",
                "price" => 35000
            ],
            [
                "make" => "Porsche",
                "model" => "Boxter",
                "price" => 72000
            ],
            [
                "make" => "Toyota",
                "model" => "Celica",
                "price" => 35000
            ],
            [
                "make" => "Toyota",
                "model" => "Celica",
                "price" => 35000
            ]
        ];
//        return $jayParsedAry;
        $localUserid = $request->localUserid;
        $districtFilter = $request->districtFilter;
        $sourceFilter = $request->sourceFilter;
        $followFilter = $request->followFilter;
        $localUserid = 26;
        $seventhdate = date('Y-m-d', strtotime('-6 days'));
        $todate = date('Y-m-d', strtotime('-0 days'));
        switch ($followFilter) {
            case 1: // fresh
                $followFilter = "fcd.`LastfollowStatus` = 0";
                break;
            case 2: // all followUp
                $followFilter = "fcd.`LastfollowStatus` > 0";
                break;
            case 3: // No Vacancy
                $followFilter = "fcd.`LastfollowStatus` = 2";
                break;
            case 4: // Call Not Attend
                $followFilter = "fcd.`LastfollowStatus` = 3";
                break;
            case 5: // Switched Off
                $followFilter = "fcd.`LastfollowStatus` = 10";
                break;
            case 6: // Not Reachable
                $followFilter = "fcd.`LastfollowStatus` = 12";
                break;
            case 7: // Incoming Barred
                $followFilter = "fcd.`LastfollowStatus` = 13";
                break;
            case 8: // Busy
                $followFilter = "fcd.`LastfollowStatus` = 14";
                break;
            case 9: // Auto Close
                $followFilter = "fcd.`LastfollowStatus` = 16";
                break;
            case 10: // Followup
                $followFilter = "fcd.`LastfollowStatus` = 4";
                break;
            default:
                $followFilter = "1 = 1";
        }
        \DB::getQueryLog();
        $getData = DB::table('freecalling_details as fcd')
            ->selectRaw('"" as Sno, fcd.id, fcd.name, fcd.designation, fcd.companyName, fcd.district, CONCAT(fcd.city, ", ", fcd.district) as city, fcd.mobile1, fcd.mobile2, fcd.email, fcd.existStatus, IF(fcd.LastfollowStatus = 0, "-", ffm.status) as LastfollowStatus, date_format(fcd.LastFollowDate,"%d-%m-%Y %r") as LastFollowDate, fcd.LastFollowRemarks, date_format(fcd.assigned_date,"%d-%m-%Y") as assigned_date')
            ->leftjoin('freecalling_followup_master as ffm', 'ffm.id', '=', 'fcd.LastfollowStatus')
            ->where('fcd.assigned_to', $localUserid)
            ->whereRaw('fcd.existStatus < 2')
            ->when($districtFilter != '', function ($query) use ($districtFilter) {
                return $query->where('fcd.district', $districtFilter);
            })
            ->when($sourceFilter != '', function ($query) use ($sourceFilter) {
                return $query->where('fcd.source', $sourceFilter);
            })
            ->whereIn('fcd.LastfollowStatus', [0, 2, 3, 4, 10, 12, 14])
            ->whereRaw('((fcd.nextFollowupDate <= CURDATE() AND fcd.LastfollowStatus = 4) OR fcd.LastfollowStatus != 4)')
            ->whereRaw('((DATE(fcd.LastFollowDate) + INTERVAL 30 DAY <= CURDATE() AND fcd.LastfollowStatus = 2) OR fcd.LastfollowStatus != 2)')
            ->whereRaw('(((DATE(fcd.LastFollowDate) != CURDATE() AND fcd.LastfollowStatus > 0 ) OR fcd.LastfollowStatus = 0) OR ((DATE(fcd.LastFollowDate) = CURDATE() AND fcd.LastfollowStatus > 0 ) OR fcd.LastfollowStatus = 0))')
            ->whereRaw($followFilter)
            ->orderBy('fcd.existStatus', 'DESC')
            ->orderBy('fcd.LastFollowDate', 'DESC')
            ->get();
//                        dd(\DB::getQueryLog());
        $result['getData'] = $getData;
        return $result;
    }

    public function freeteleHistory(Request $request)
    {
        $id = $request->id;
        $getData = DB::table('freecalling_followup as ff')
            ->selectRaw("ff.id, ffm.status as followStatus, ff.followRemarks, ff.ok_job_id, date_format(ff.indate, '%d-%m-%Y %H:%i:%s') as indate")
            ->join('freecalling_followup_master as ffm', 'ff.followStatus', '=', 'ffm.id')
            ->where('ff.freecalling_id', $id)
            ->orderBy('ff.indate', 'DESC')
            ->get();
        $result['HistoryData'] = $getData;
        return $result;
    }
}
