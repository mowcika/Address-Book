<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class invoiceRequestController extends Controller
{
    // check invoice request already verify or not
    public function check_ir_approved(Request $request)
    {
        $les = $request->lastEventStatus;
        $count = DB::table('freePaidCallingToEmpr')
            ->where('id', '=', $request->rowid)
            ->where('lastEventStatus', '=', '4')
            ->count();

        if ($count > 0) {
            $result['adStatus'] = false;
            $result['message'] = "Already Approved! Please refresh ";
            $result['count'] = $count;
        } else {
            $result['adStatus'] = true;
            $result['rowid'] = $request->rowid;
            $result['count'] = $count;
        }

        return $result;
    }

    // save invoice request data
    public function invoiceRequest_save(Request $request)
    {
        $request->all(); // Returns array with all elements.
        $request->only(['key1', 'key2']); // Returns array with selected items
        $k1 = $request->except(['key1']); // Returns array with everything except key1.
//        print_r($k1);exit;
        $featureArray = array();
        $featureArray_key = array();
        if (array_key_exists("1", $k1) || is_array($k1)) {
            // echo "Key exists!".$k1;die();

            if (is_array($k1)) {
                foreach ($k1 as $ky => $vl) {
                    $keyInt = (int)$ky;
                    if ($keyInt > 0 && is_numeric($keyInt)) {
                        $featureArray[$keyInt] = $vl;
//                        $featureArray_key[]= $keyInt;
                    }
                }
                $formDetails = array(
                    'featureDetails' => $featureArray,
                );
            } else {
                $featureArray = array();
            }
        } else {
            $featureArray = array();
            // echo "Key does not exist!";die();
        }


        $cloudFrontUrl = "https://d2hy6ree306xec.cloudfront.net/";
        $jobsTicketRowId = ""; // waiting for response 04-02-23


        $userid = $request->localUserId;
        $editId = isset($request->editId) ? $request->editId : '';
        $order_id = isset($request->order_id) ? $request->order_id : '';
        $txn_id = isset($request->txn_id) ? $request->txn_id : '';
        $status = isset($request->status) ? $request->status : '';
        $currency = isset($request->currency) ? $request->currency : '';

        $lastEventStatus = isset($request->last_event_status) ? $request->last_event_status : '';
        // echo $order_id." - ".$txn_id." - ".$status." - ".$currency; exit;

        if ($lastEventStatus == 3) {
            $updateReject_remarks = isset($request->updatedRejectedRemarks) ? $request->updatedRejectedRemarks : '';
            $lastEventStatus = 0;
        } else {
            $updateReject_remarks = "";
        }

        $jobsource = isset($request->jobsource) ? $request->jobsource : '';
        $empId = isset($request->empName) ? $request->empName : '';
        if (isset($request->plan)) {
            // echo "if";exit;
            if ($request->plan == 'custom') {
                $planName = $request->planName;
                $planID = '';
            } else {
                $plan_array = explode('#@#', $request->plan);
                $planName = $plan_array[1];
                $planID = $plan_array[0];
                $feature_details = DB::table('feature_details')->where('plan_id', '=', $planID)->get();
                // print_r($feature_details);die();
                if (count($feature_details) > 0) {
                    $formDetails['featureDetails'] = $feature_details;
                }

            }
        } else {
            //echo "if else";exit;
            $planName = '';
            $planID = '';
        }
        //print_r($formDetails['featureDetails']);exit;

        $jvalidity = isset($request->jvalidity) ? $request->jvalidity : '';
        $pvalidity = isset($request->pvalidity) ? $request->pvalidity : '';
        $jcount = isset($request->jcount) ? $request->jcount : '';
        $paydate = isset($request->payDate) ? date("Y-m-d", strtotime($request->payDate)) : '';

        if (isset($request->payAmount)) {
            if ($request->payAmount == "") {
                $payAmount = 0;
            } else {
                $payAmount = $request->payAmount;
            }
        } else {
            $payAmount = '';
        }
        $mode = isset($request->mode) ? $request->mode : '';
        if ($request->invDate == null) {
            if ($request->invDate == null || $request->invDate == "" || $request->invDate == "1970-01-01") {
//                $invDate = $paydate;
                $invDate = '';
            } else {
//                $invDate = date("Y-m-d", strtotime($request->invDate));
                $invDate = '';
            }
        } else {
            $invDate = '';
        }
//return $request->invDate;
        if ($request->invDate != "") {
            $invDate = date("Y-m-d", strtotime($request->invDate));
        }
        $igst = isset($request->igst) ? $request->igst : '';
        $discountIncluded = isset($request->discountIncluded) ? $request->discountIncluded : '';
        $dicountOffers = isset($request->dicountOffers) ? $request->dicountOffers : '';
        if (isset($request->payStatus)) {
            $payStatus = $request->payStatus;
            if ($payStatus == 1) {
                $mode = "Offer";
            }
        } else {
            $payStatus = '';
        }


        if ($request->Description == "" || $request->Description == null) {
            $plan_Description = "Free";
        } else {
            $plan_Description = $request->Description;
        }

        // print_r($proof_path);exit;
        $fileNameArray = array();
        $files = $request->file('payProof'); // payment_proofs


        $uploadFiles = $pay_proof = '';
        if ($request->file('images')) { // return "if";
            foreach ($request->file('images') as $file) {
//                $product_img = $file->store(
//                    '/payment_proofs',
//                    's3'
//                );
                $extension = $file->getClientOriginalExtension();
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $fileName = $originalName . '-' . date("Ymd-Hms") . '.' . $extension;
                Storage::disk('s3')->put($fileName, file_get_contents($file));
                $uploadFiles .= $cloudFrontUrl . $fileName . ',';
            }
            $uploadFiles .= $request->editimg;
            $pay_proof = rtrim($uploadFiles, ',');
        } else if ($request->editimg != "") {
            $pay_proof = $request->editimg;
        } else { // return "else";
            $pay_proof = "";
        }

        //print_r($pay_proof);exit;
        $pay_proof_text = isset($request->pay_proof_text) ? $request->pay_proof_text : '';
        $state = isset($request->state) ? $request->state : '';
        //echo $request->editId;exit;


        if ($request->editId != "" || $request->editId != NULL) {
            // edit update area
            $edit_data = DB::table('freePaidCallingToEmpr')->where('id', $request->editId)->get();
            $gst_percent = 18;
            $actualamt = ($payAmount * 100) / (100 + $gst_percent);
            $actualamt1 = round($actualamt, 2);
            $gstamt = $payAmount - $actualamt;
            $gstamt = round($gstamt, 2);
            $inip = $request->ip();
            $indate = date('Y-m-d H:i:s');
            // $activated_date = date("Y-m-d");
            $activated_date = $edit_data[0]->ir_txn_date;
            $date = date_create($activated_date);
            date_add($date, date_interval_create_from_date_string("+" . ($pvalidity - 1) . " days"));
            $expiry_date = date_format($date, "Y-m-d");
            $update_array = array(
                "employer_id" => $empId,
                'state' => $state,
                "ir_plan_id" => $planID,
                "ir_plan_name" => $planName,
                "ir_plan_type" => 'paid',
                "ir_validity" => $pvalidity,
                "ir_job_validity" => $jvalidity,
                "ir_starting_date" => $activated_date,
                "ir_expired_date" => $expiry_date,
                "ir_post_count" => $jcount,
//                "ir_txn_date" => $paydate,
                "ir_payment_date" => $paydate,
                "ir_txn_amt" => $payAmount,
                "ir_actual_amount" => $payAmount,
                "ir_debited_amount" => $payAmount,
                "ir_actual_amount1" => $actualamt1,
                "ir_paymentmode" => $mode,
                "ir_update" => $indate,
                "ir_upip" => $inip,
                "ir_upby" => $userid,
//                "ir_invoice_date" => $invDate,
                "ir_plan_description" => $plan_Description,
                "ir_is_igst" => $igst,
                "ir_discountIncluded" => $discountIncluded,
                "ir_dicountMaster" => $dicountOffers,
                "ir_is_credit" => $payStatus,
                "ir_pay_proof" => $pay_proof,
                "ir_pay_proof_text" => $pay_proof_text,
                "ir_jobsource" => $jobsource,
                "rejected_updated_remarks" => $updateReject_remarks,
                "lastEventStatus" => $lastEventStatus,
            );
//                print_r($update_array);exit;
            $upd = DB::table('freePaidCallingToEmpr')->where('id', '=', $request->editId)->update($update_array);

            $last_id_francis = DB::table('freePaidCallingToEmpr')->where('id', '=', $request->editId)->get();

            $where_1 = $where_2 = array();
            $where_1_in = 1;

            if (isset($formDetails['featureDetails'])) {
                // echo "if in";exit;
                foreach ($formDetails['featureDetails'] as $key => $value) {
                    $addFeature = array(
                        "plan_id" => $planID,
                        "feature_id" => $key,
                        "values" => $value,
                        "indate" => $indate,
                        "inip" => $inip,
                        "inby" => $userid,
                        "company_id" => $empId,
                        "last_insert_id" => $last_id_francis[0]->id
                    );
//                        if($formDetails['plan'] != 'custom'){
                    if ($request->plan != 'custom') {
                        // print_r('if'.$value->values);
                        $addFeature['feature_id'] = $value->feature_id;
                        $addFeature['values'] = $value->values;
                        if ($value->feature_id == "7") {
                            $addFeature['remaining_profile_view'] = $value->values;
                        }
                        $where_2 = "feature_id = '$value->feature_id' ";
                    } else {
                        if ($key == "7") {
                            $addFeature['remaining_profile_view'] = $value;
                        }
                        $where_2 = "feature_id = '$key' ";
                    }


                    DB::table('feature_details_plan_ir')
                        ->where('last_insert_id', '=', $request->editId)
                        ->whereRaw($where_2)
                        ->update($addFeature);
                }
            }
            if ($upd > 0) {
                $result['addStatus'] = true;
                $result['message'] = 'Invoice Request details updated successfully';
            } else {
                $result['addStatus'] = false;
                $result['message'] = 'Could not update Invoice Request details!';
            }
        } else {
            // echo "add";exit;
            $gst_percent = 18;
            $actualamt = ($payAmount * 100) / (100 + $gst_percent);
            $actualamt1 = round($actualamt, 2);
            $gstamt = $payAmount - $actualamt;
            $gstamt = round($gstamt, 2);
            // print_r($gstamt);exit;
            $inip = $request->ip();
            $indate = date('Y-m-d H:i:s');
            $activated_date = $paydate;
            $date = date_create($activated_date);
            date_add($date, date_interval_create_from_date_string("+" . ($pvalidity - 1) . " days"));
            $expiry_date = date_format($date, "Y-m-d");

            $insert_array = array(
                "employer_id" => $empId,
                'state' => $state,
                "ir_plan_id" => $planID,
                "ir_plan_name" => $planName,
                "ir_plan_type" => 'paid',
                "ir_validity" => $pvalidity,
                "ir_job_validity" => $jvalidity,
                "ir_starting_date" => $activated_date,
                "ir_expired_date" => $expiry_date,
                "ir_post_count" => $jcount,
//                "ir_txn_date" => $paydate,
                "ir_payment_date" => $paydate,
                "ir_txn_amt" => $payAmount,
                "ir_actual_amount" => $payAmount,
                "ir_debited_amount" => $payAmount,
                "ir_actual_amount1" => $actualamt1,
                "ir_paymentmode" => $mode,
                "ir_cdate" => $indate,
                "ir_cip" => $inip,
                "ir_cby" => $userid,
//                "ir_invoice_date" => $invDate,
                "ir_plan_description" => $plan_Description,
                "ir_is_igst" => $igst,
                "ir_discountIncluded" => $discountIncluded,
                "ir_dicountMaster" => $dicountOffers,
                "ir_is_credit" => $payStatus,
                "ir_pay_proof" => $pay_proof,
                "ir_pay_proof_text" => $pay_proof_text,
                "ir_jobsource" => $jobsource,
            );

            $insr = DB::table('freePaidCallingToEmpr')->insert($insert_array);
            $last_id = DB::getPdo()->lastInsertId();

            if (isset($formDetails['featureDetails'])) {
                // echo "if in";exit;
                foreach ($formDetails['featureDetails'] as $key => $value) {
                    $addFeature = array(
                        "plan_id" => $planID,
                        "feature_id" => $key,
                        "values" => $value,
                        "indate" => $indate,
                        "inip" => $inip,
                        "inby" => $userid,
                        "company_id" => $empId,
                        "last_insert_id" => $last_id
                    );
//                        if($formDetails['plan'] != 'custom'){
                    if ($request->plan != 'custom') {
                        // print_r('if'.$value->values);
                        $addFeature['feature_id'] = $value->feature_id;
                        $addFeature['values'] = $value->values;
                        if ($value->feature_id == "7") {
                            $addFeature['remaining_profile_view'] = $value->values;
                        }
                    } else {
                        if ($key == "7") {
                            $addFeature['remaining_profile_view'] = $value;
                        }
                    }
                    // print_r($value);exit;
                    DB::table('feature_details_plan_ir')->insert($addFeature);
                }
            }

            if ($insr) {
                $result['addStatus'] = true;
                $result['message'] = 'Invoice Request added successfully';
            } else {
                $result['addStatus'] = false;
                $result['message'] = 'Could not add Invoice Request details!';
            }
        }
        return $result;
    }

    // get invoice request data
    public function getPlan_new_ir(Request $request)
    {
        $rid = $request->fpid;
        $editId = $request->editId;
        $le_status = $request->le_status;
        $uid = $request->localUserId;


        if ($rid != "") {
            $editData = DB::connection('read')->table('freePaidCallingToEmpr as fp')
                ->selectRaw('fp.*,e.`company-name` as companyName, CONCAT(ir_plan_id,"#@#",ir_plan_name,"#@#",trim(ir_txn_amt)+0) as  selectplan, fp.id as fpId, fp.employer_id as empid, e.gst')
                ->leftJoin('employer as e', 'e.id', '=', 'fp.employer_id')
                ->where('fp.id', '=', $rid)
                ->first();

            // feature_details_plan
            $featureData = DB::connection('read')->table('feature_details_plan_ir')->where('last_insert_id', '=', $rid)->get();

            $result['editData'] = $editData;
            $result['featureData'] = $featureData;
            return $result;
        } else {

            if ($editId != "") {
                $editData = DB::connection('read')->table('freePaidCallingToEmpr as fp')
                    ->selectRaw('fp.*,e.`company-name` as companyName, CONCAT(ir_plan_id,"#@#",ir_plan_name,"#@#",trim(ir_txn_amt)+0) as  selectplan, fp.id as fpId, fp.employer_id as empid, fp.ir_payment_date, e.gst')
                    ->leftJoin('employer as e', 'e.id', '=', 'fp.employer_id')
                    ->where('fp.id', '=', $editId)
                    ->first();

                // feature_details_plan
                $featureData = DB::table('feature_details_plan_ir')->where('last_insert_id', '=', $editId)->get();

                $result['editData'] = $editData;
                $result['featureData'] = $featureData;
                return $result;
            } else {
                $le = $user = $dates = array();
                $le_in = $user_in = $whereInDate = 1;

                $date = $request->date;
                $fromdate = date("Y-m-d", strtotime($date['startDate']));
                $todate = date("Y-m-d", strtotime($date['endDate']));

                if ($date != "") {
                    $fr = $fromdate;
                    $t = $todate;
                    $from = date('Y-m-d', strtotime($fr));
                    $to = date('Y-m-d', strtotime($t));
                    $dates[] = "date(fp.ir_cdate) BETWEEN '$from' and '$to'";
                    $date_from = strtotime($from);
                    $date_to = strtotime($to);
                }
                if (count($dates)) {
                    $whereInDate = implode(' AND ', $dates);
                }

                if ($le_status != "") {
                    $le[] = "lastEventStatus = '$le_status'";
                }
                if (count($le)) {
                    $le_in = implode(' AND', $le);
                }
                if ($uid != '') {
                    if ($uid != '1' && $uid != '2' && $uid != '24' && $uid != '163' && $uid != '110' && $uid != '102' && $uid != '165' && $uid != '196' && $uid != '205' && $uid != '197' && $uid != '123') {
                        $user[] = "fp.ir_cby = '$uid' ";
                    }
                    if (count($user)) {
                        $user_in = implode(' AND', $user);
                    }
                }

                return $getIR['getIR'] = DB::connection('read')->table('freePaidCallingToEmpr as fp')
                    ->selectRaw("DISTINCT(e.`company-name`) as companyName, REPLACE(e.address, '#@#',',') as address, er.phonenumber, fp.employer_id, fp.id as fpId, concat(er.firstname,' ', er.lastname) as empName, u.name as irCby, date_format(fp.ir_cdate,'%d-%m-%Y %H:%m:%s') as irCDate, case when fp.lastEventStatus = 0 then 'No Action' when fp.lastEventStatus = 1 then 'Employer Rejected' when fp.lastEventStatus = 3 then 'Invoice Rejected' else 'Invoice Generated' END AS Lasteventstatus, fp.lastEventStatus as lestatus, fp.lastEventRemarks, ig.name as invoiceeventBy, if(fp.invoiceEventDate = '0000-00-00 00:00:00', '', date_format(fp.invoiceEventDate,'%d-%m-%Y %H:%m:%s')) as invoiceEventDate, fp.invoiceEventIp, irej.name as invoiceRejectby, if(fp.lastEventDate = '0000-00-00 00:00:00', '', date_format(fp.lastEventDate,'%d-%m-%Y %H:%m:%s')) as irejDate, fp.lastEventIp as irejIP, IFNULL(fcd.inv_no,'---') as invoice_no,fp.rejected_updated_remarks as updatedRemarks,e.gst ")
                    ->leftJoin('employer as e', 'e.id', '=', 'fp.employer_id')
                    ->leftJoin('employer_registration as er', 'er.company_details', '=', 'fp.employer_id')
                    ->leftJoin('franchise_company_debits as fcd', 'fcd.debit_id', '=', 'fp.invoiceNo')
                    ->leftJoin('user as u', 'u.id', '=', 'fp.ir_cby')
                    ->leftJoin('user as ig', 'ig.id', '=', 'fp.invoiceEventBy')
                    ->leftJoin('user as irej', 'irej.id', '=', 'fp.lastEventBy')
                    ->whereRaw($le_in)
                    ->whereRaw($user_in)
                    ->whereRaw($whereInDate)
                    ->whereIn('lastEventStatus', array(0, 3))
                    ->groupBy('fp.id')
                    ->orderBy('lestatus', 'ASC')
                    ->orderBy('fp.id', 'DESC')
                    ->get();
            }

        }

    }


}
