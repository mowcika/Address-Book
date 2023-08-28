<?php

namespace App\Http\Controllers;

// use http\Env\Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

// use PDF;
use Dompdf\Dompdf;

// use Barryvdh\DomPDF\Facade\Pdf;

class FeatureController extends Controller
{
    public function getFeatureDrop(Request $request)
    {
        $getData = DB::table('franchise_plan_master')
            ->selectRaw('franchise_plan_master.*,date_format(franchise_plan_master.plan_start_date,"%d-%m-%Y %h:%i %p") as start,date_format(franchise_plan_master.plan_end_date,"%d-%m-%Y %h:%i %p") as end')
            ->where('status', '=', 3)
            ->orderBy('plan_id', 'desc')
            ->get();
        $result_plan = array();
        foreach ($getData as $row) {
            $result_plan['id'] = $row->plan_id;
            $result_plan['name'] = $row->name;
            $result_plan['amount'] = $row->amount;
            $result_plan['feature'] = $this->getFeatureDetails($row->plan_id);
            $result_plan_array[] = $result_plan;
        }

        $featureData = DB::table('feature_details as fd')
            ->leftJoin('master_feature as mf', 'mf.id', '=', 'fd.feature_id')
            ->selectRaw('fd.*,mf.feature as feature,mf.id as fid,mf.additional_text')
            ->get();

        $FeatureMaster = DB::table('master_feature')
            ->orderBy('id', 'desc')
            ->get();

        $state = DB::table('state_master')
            ->orderBy('name', 'asc')
            ->get();

        $dMaster = DB::table('discount_master')
            ->orderByRaw("id asc, name asc")
            ->get();

        $result['planData'] = $getData;
        $result['featureData'] = $featureData;
        $result['FeatureMaster'] = $FeatureMaster;
        $result['getData'] = $result_plan_array;
        $result['stateData'] = $state;
        $result['discountMasterData'] = $dMaster;

        // print_r($dMaster);exit;
        return $result;
    }

    public function getFeatureDetails($pid)
    {
        $result = DB::table('feature_details')
            ->where('plan_id', '=', $pid)
            ->get();
        return $result;
    }

    public function getjobSourceDrop()
    {
        $jobsourceDrop = DB::table('company_source')
            ->selectRaw('id,source')
            ->where('is_delete', '=', '0')
            ->get();
        $result['jobsourceDrop'] = $jobsourceDrop;

        return $result;
    }

    public function getEmpPlan_Details(Request $request)
    {
        $id = $request->empid;

        $data = DB::table('franchise_company_debits as fcs')
            ->selectRaw('debit_id,concat(inv_prefix,inv_no) as inv,plan_name,date_format(invoice_date,"%d-%m-%Y") as idate,e.state as state')
            ->leftJoin('employer as e', 'e.id', '=', 'fcs.employer_id')
            ->where('employer_id', '=', $id)
            ->orderBy('debit_id', 'desc')
            ->get();

        $gst = DB::connection('read')->table('employer as e')->selectRaw('gst')->where('id', '=', $id)->first();

        $result['empData'] = $data;
        $result['gst'] = $gst;

        return $result;
    }

    public function checkExistUtrnNo(Request $request)
    {
        $pProof = $request->pProof;
        // print_r($pProof);exit;
        if ($pProof != "") {
            $personProof_check = DB::table('franchise_company_debits')
                ->whereRaw("pay_proof_text = '$pProof'")
                ->get();
            // print_r($personProof_check );exit;
            if (sizeof($personProof_check) > 0) {
                $result['addStatus'] = false;
                $result['message'] = 'UTR Number already exists!';
            }

        } else {
            $result['addStatus'] = true;
            $personProof_check = "";
        }
        $result['personProof_check'] = $personProof_check;
        return $result;
    }


    public function updateFeature_plan(Request $request)
    {
//        return $request->fp_key;
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
        $fp_key = isset($request->fp_key) ? $request->fp_key : ''; // invoice request key
        $order_id = isset($request->order_id) ? $request->order_id : '';
        $txn_id = isset($request->txn_id) ? $request->txn_id : '';
        $status = isset($request->status) ? $request->status : '';
        $currency = isset($request->currency) ? $request->currency : '';
        // echo $order_id." - ".$txn_id." - ".$status." - ".$currency; exit;

        $jobsource = isset($request->jobsource) ? $request->jobsource : '';
        $empId = isset($request->empName) ? $request->empName : '';
        // get incentive user id
        $incentive_user = DB::connection('read')->table('executive_employer')
            ->selectRaw("user_id as incentive_userid")
            ->where('employer_id', '=', $empId)
            ->where('user_id', '!=', 0)
            ->get();
        // return $incentive_user->incentive_user_id;
        if (sizeof($incentive_user) > 0) {
            $in_user = $incentive_user[0]->incentive_userid;
        } else {
            $in_user = 0;
        }
        // get old or new company
//        $cs_newOrOld = DB::connection('read')->table('franchise_company_debits')
//            ->where('employer_id', '=', $empId)
//            ->where('txn_amt','>',0)
//            ->where('status', '=', 'TXN_SUCCESS')
//            ->get();
//        // return sizeof($cs_newOrOld);
//        if (sizeof($cs_newOrOld) > 0) {
//            $css = 'Old';
//        } else {
//            $css = 'New';
//        }
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
                $invDate = $paydate;
            } else {
                $invDate = date("Y-m-d", strtotime($request->invDate));
            }
        } else {
            $invDate = '';
        }

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
        // $pay_proof          = isset($request->pay_proof) ? $request->pay_proof : '';
        // print_r($_FILES);exit;
        if ($_SERVER['SERVER_NAME'] == 'localhost') {
            $proof_path = $_SERVER['DOCUMENT_ROOT'] . '/images/payment_proof/';
        } else {
            $proof_path = $_SERVER['DOCUMENT_ROOT'] . '/images/payment_proof/';
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
        $lasteventStatus = isset($request->lasteventStatus) ? $request->lasteventStatus : '';
        $lastEventRemarks = isset($request->lastEventRemarks) ? $request->lastEventRemarks : '';
        //echo $request->editId;exit;

        // ----------------- add / edit ----------------- //
        if ($request->editId != "" || $request->editId != NULL) {
            // edit area
//            echo "edit";exit;
            //invoice number
            $edit_data = DB::table('franchise_company_debits')->where('debit_id', $request->editId)->get();
            //end of inv number
            $gst_percent = 18;
            $actualamt = ($payAmount * 100) / (100 + $gst_percent);
            $actualamt1 = round($actualamt, 2);
            $gstamt = $payAmount - $actualamt;
            $gstamt = round($gstamt, 2);
            $inip = $request->ip();
            $indate = date('Y-m-d H:i:s');
            // $activated_date = date("Y-m-d");
            $activated_date = $edit_data[0]->txn_date;
            $date = date_create($activated_date);
            date_add($date, date_interval_create_from_date_string("+" . ($pvalidity - 1) . " days"));
            $expiry_date = date_format($date, "Y-m-d");
            $update_array = array(
                // "inv_prefix" => $inv_prefix,
                // "inv_no" => $new_inv_no,
                "employer_id" => $empId,
                "plan_id" => $planID,
                "plan_name" => $planName,
                "plan_type" => 'paid',
                "job_validity" => $jvalidity,
                "validity" => $pvalidity,
                "starting_date" => $paydate,
                "expired_date" => $expiry_date,
                "post_count" => $jcount,
                "txn_date" => $paydate,
                "txn_amt" => $payAmount,
                "order_id" => $order_id,
                "txn_id" => $txn_id,
                "status" => $status,
                "currency" => $currency,
                "gst_percent" => $gst_percent,
                "actual_amount" => $payAmount,
                "debited_amount" => $payAmount,
                "actual_amount1" => $actualamt1,
                "paymentmode" => $mode,
                "gst_amount" => $gstamt,
                "up_date" => $indate,
                "upip" => $inip,
                "upby" => $userid,
                "invoice_date" => $invDate,
                "plan_description" => $plan_Description,
                // "incentive_userid" => $users,
                "is_igst" => $igst,
                "discountIncluded" => $discountIncluded,
                "dicountMaster" => $dicountOffers,
                "is_credit" => $payStatus,
                "pay_proof" => $pay_proof,
                "pay_proof_text" => $pay_proof_text,
                "jobsource" => $jobsource,

            );

            $emp_pdf_dtls = DB::table('employer_registration as er')
                ->selectRaw('er.firstname,er.phonenumber,er.email,e.gst,e.address,`company-name` as company,e.employer')
                ->leftJoin('employer as e', 'e.employer', '=', 'er.id')
                ->where('er.company_details', '=', $empId)->get();

            if (sizeof($emp_pdf_dtls) > 0) {
                $invoice_no = $edit_data[0]->inv_prefix . $edit_data[0]->inv_no;
                $invoice_date = $invDate;
                $plan_name = $planName;
                $plan_validity = $pvalidity;
                $actual_amt = $actualamt1;
                $gst_percnt = $gst_percent;
                $sgst = 9;
                $cgst = 9;
                $gst_amt = $gstamt;
                $total = number_format((float)$payAmount, 2, '.', '');
                if ($emp_pdf_dtls[0]->company) {
                    $company_name = $emp_pdf_dtls[0]->company;
                    $company_address = explode('#@#', $emp_pdf_dtls[0]->address);
                    $company_mobile = $emp_pdf_dtls[0]->phonenumber;
                    $user_gstno = $emp_pdf_dtls[0]->gst;
                } else {
                    $company_name = $emp_pdf_dtls[1]->company;
                    $company_address = explode('#@#', $emp_pdf_dtls[1]->address);
                    $company_mobile = $emp_pdf_dtls[1]->phonenumber;
                    $user_gstno = $emp_pdf_dtls[1]->gst;
                }
                $address_0 = $company_address[0];
                $address_1 = $company_address[1]; // city
                $address_2 = $company_address[2];
                $address_3 = $company_address[3];

                // get city name
                $cityvalue = DB::table('areas')->selectRaw('id, area_name')->where('id', $company_address[1])->get();
                if (sizeof($cityvalue) > 0) {
                    $cty = !empty($cityvalue) ? $cityvalue[0]->area_name : '';
                } else {
                    $cty = "";
                }

                $find_gst = substr($user_gstno, 0, 2);

                $preparedby = "Nithra Apps India Pvt Ltd,<br>4th Floor, AV Plaza,<br>South Car Street, Tiruchengode,<br>Namakkal,<br>637211.";
                $preparedby_gst = "33AAECN4289M1Z0";
                // echo $preparedby." gst - ".$preparedby_gst;exit;
                $pdf_no = "EMPLOYER_POSTING_PLAN_" . $edit_data[0]->inv_no;
                $filename = "invoice_employer/" . $pdf_no . ".pdf";
//                $filename1 = "invoice_employer/" . $pdf_no;
                $filename1 = "invoices/" . $pdf_no . date('His');
// echo $pdf_no;exit;
                $html_Ad_gst = $html_Ad_gst_row = "";
                if ($user_gstno != 'undefined' || $user_gstno != '') {
                    $html_Ad_gst = ' <br> <span style="padding: 0px"> GST No: ' . $user_gstno . '</span>';
                } else {
                    $html_Ad_gst = '<br> <span style="padding: 0px"> GST No: </span>';
                }

                // gst igst rows
                if ($find_gst == '') {
                    if ($state != 1) {
                        $html_Ad_gst_row .= '<tr>
                            <td align ="right" style="padding: 5px"></td>
                            <td align ="right" colspan="2" style="padding-right: 0px;">IGST(18%) (<span class="curFont" style="padding: 0">₹</span>)&nbsp;
                            ' . $gst_amt . '&nbsp;&nbsp;&nbsp;</td>
                            </tr>';
                    } else {
                        $html_Ad_gst_row .= '<tr>
                    <td align ="right" style="padding: 5px"></td>
                    <td align ="right" colspan="2" style="padding-right: 0px;">SGST(9%) ( <span class="curFont" style="padding: 0">₹</span> )&nbsp;
                    ' . $gst_amt / 2 . '&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                    <td align ="right" style="padding: 5px"></td>
                    <td align ="right" colspan="2" style="padding-right: 0px;">CGST(9%) ( <span class="curFont" style="padding: 0">₹</span> )&nbsp;
                    ' . $gst_amt / 2 . '&nbsp;&nbsp;&nbsp;</td>
                    </tr>';
                    }
                } else {
                    if ($find_gst != '33') {
                        $html_Ad_gst_row .= '<tr>
                    <td align ="right"></td>
                    <td align ="right" colspan="2" style="padding-right: 0px;">IGST(18%) ( <span class="curFont" style="padding: 0">₹</span> )&nbsp;
                    ' . $gst_amt . '&nbsp;&nbsp;&nbsp;</td>
                    </tr>';
                    } else {
                        $html_Ad_gst_row .= '<tr>
                    <td align ="right" style="padding: 5px"></td>
                    <td align ="right" colspan="2" style="padding-right: 0px;">SGST(9%) ( <span class="curFont" style="padding: 0">₹</span> )&nbsp;' . $gst_amt / 2 . '&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                    <td align ="right" style="padding: 5px"></td>
                    <td align ="right" colspan="2" style="padding-right: 0px;">CGST(9%) ( <span class="curFont" style="padding: 0">₹</span> )&nbsp;
                    ' . $gst_amt / 2 . '&nbsp;&nbsp;&nbsp;</td>
                    </tr>';
                    }
                }
//return $address_0;
                $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><style>*{margin:0px; padding:20px;}body { font-family:freeserif;font-size : 12pt;background-color : #E8FAFB; } .curFont{font-family:"DeJaVu Sans Mono";} </style><body><h1 class="text-light"><span style = "margin-top : 50px;padding-left: 0px">&nbsp;NITHRA JOBS </span><br /></h1>';
                // Prepared for table
                $html .= '<table height="100%" width="100%" style="margin-top: -10px;margin-left: -10px; border-collapse: collapse;"><tr>
                        <td colspan="2">
                            <h2 style="color: #000;text-align: left;margin-left: 0px;padding-left: 0px" >Prepared for </h2>
                            <span style="line-height: 24px;padding: 0px">
                            ' . $company_name . ' <br>
                            ' . $address_0 . ' <br>
                            ' . (!empty($cty) ? $cty . ".<br>" : '') . '
                            ' . $address_2 . '.<br>
                            ' . $company_mobile . '
                        </span>' . $html_Ad_gst;
                $html .= '</td><td style="text-align: center;font-weight: bold;font-size: 22px">INVOICE</td><tr></table>';
                // Prepared by table
                $html .= '<table height="100%" width="100%" style="margin-top: -40px;margin-left: -10px; border-collapse: collapse;"><tr>
                    <td>
                        <h2 style="color: #000;text-align: left;margin-left: 0px;padding-left: 0px" >Prepared by </h2>
                        <span style="line-height: 24px;padding: 0px">
                        ' . nl2br($preparedby) . '
                            <br>GST No: 33AAECN4289M1Z0 <br>HSN Code: 998365
                        </span>
                    </td>
                    <td>Invoice No <br><br>Invoice Date</td>
                    <td>' . $invoice_no . '<br><br>' . date("F j, Y", strtotime($invoice_date)) . '</td><tr>';
                $html .= '</table>';
                $html .= '<hr style="border:1px solid #333;height: 0.5px;border-bottom: none;border-left: none;border-right: none">';

                // content table
                $html .= '<table height="100%" width="100%" style="margin-top: -40px;margin-left: -10px; border-collapse: collapse;">
                    <tr style="background: #02671c;color: #fff;">
                        <th style="padding: 3px;padding-left:0px;width: 10%;">#</th>
                        <th style="padding: 3px;padding-left:0px;width: 75%;">Description</th>
                        <th style="padding: 3px;padding-left:0px;padding-right:10px;width: 15%;" align="right">Total (<span class="curFont" style="padding: 0px">₹</span>)</th>
                    </tr>
                    <tr style="border-bottom: 1px solid #333">
                        <td style="padding: 15px;padding-left:0px;width: 10%;" align="center">1</td>
                        <td style="padding: 15px;padding-left:0px;width: 75%;">' . $plan_Description . '</td>
                        <td style="padding: 15px;padding-left:0px;width: 15%;" align="right">' . $actual_amt . '</td>
                    </tr>
                     // gst rows
                     ' . $html_Ad_gst_row . '
                    // total
                    <tr>
                        <td  style="padding: 5px"></td>
                        <td align="right" style="padding-right: 0px" colspan="2">Total ( <span class="curFont" style="padding: 0">₹</span> )&nbsp; ' . $total . '</td>
                    </tr>

                    // Note
                    <tr>
                        <td colspan="3" style="padding: 5px">
                            <h3 class="text-light opacity-50">Notes</h3>
                            <p style="padding: 0"><strong><em>** This is System Generated Invoice No signature required.</em></strong></p>
                        </td>
                    </tr>
                    ';
                $html .= '</table>';
                $dompdf = new DOMPDF();
                $dompdf->load_html($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                $pathToSaveOn = 'invoices/' . $filename1 . '.pdf';
                $path = \Storage::disk('s3')->put($pathToSaveOn, $dompdf->output());


                // echo $cloudFrontUrl . $pathToSaveOn;exit;
                // echo $path;exit;


                $update_array['invoice_pdf'] = $cloudFrontUrl . $pathToSaveOn;
                $update_array['mob_number'] = $company_mobile;
//                print_r($update_array);exit;
                $upd = DB::table('franchise_company_debits')->where('debit_id', '=', $request->editId)->update($update_array);

                $last_id_francis = DB::table('franchise_company_debits')->where('debit_id', '=', $request->editId)->get();
                //to update on allot company source
                $upsourceData = array(
                    "employerid" => $empId,
                    // "debit_id" => $edit_id,
                    // "category" => 'Paid',
                    "upip" => $inip,
                    "up_date" => $indate,
                    "upby" => $userid,
                );
                DB::table('allot_company_source')->where('debit_id', '=', $request->editId)->update($upsourceData);
                $where_1 = array();
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
                            "plan_purchace_id" => $last_id_francis[0]->debit_id,
                            "employer_id" => $emp_pdf_dtls[0]->employer,
                            "company_id" => $last_id_francis[0]->employer_id,
                            "mobile1" => $last_id_francis[0]->mob_number,
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


                        DB::table('feature_details_plan')
                            ->where('plan_purchace_id', '=', $request->editId)
                            ->whereRaw($where_2)
                            ->update($addFeature);
                    }
                }
                if ($upd > 0) {
                    $result['addStatus'] = true;
                    $result['message'] = 'Plan details updated successfully';
                } else {
                    $result['addStatus'] = false;
                    $result['message'] = 'Could not update plan details!';
                }
            } else {
                $result['addStatus'] = false;
                $result['message'] = 'Employer registration incorrect!';
            }
        } else { // adding area
//            echo "add";exit;
            //invoice number
            $cur_month = date('m');
            if ($cur_month > 3) {
                $year = date('Y') . "-" . date('y', strtotime('+1 year'));
            } else {
                $year = date('Y', strtotime('-1 year')) . "-" . date('y');
            }
            if ($year == '2021-22') {
                $inv_prefix = $year .= "/JP";
            } else {
                $inv_prefix = $year .= "/JTA/";
            }

            $followData = DB::table('franchise_company_debits')
                ->selectRaw('inv_prefix,Max(inv_no) as inv_no')->where('inv_prefix', '=', $inv_prefix)->get();

            if (count($followData)) {
                $new_inv_no = $followData[0]->inv_no + 1;
            } else {
                $new_inv_no = 1;
            }
            $new_inv_no = str_pad($new_inv_no, 5, "0", STR_PAD_LEFT);
            // print_r($new_inv_no);exit;
            //end of inv number

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
                "inv_prefix" => $inv_prefix,
                "inv_no" => $new_inv_no,
                "employer_id" => $empId,
                "plan_id" => $planID,
                "plan_name" => $planName,
                "plan_type" => 'paid',
                "job_validity" => $jvalidity,
                "validity" => $pvalidity,
                "starting_date" => $activated_date,
                "expired_date" => $expiry_date,
                "post_count" => $jcount,
                "txn_date" => $paydate,
                "txn_amt" => $payAmount,
                "order_id" => $order_id,
                "txn_id" => $txn_id,
                "status" => $status,
                "currency" => $currency,
                "gst_percent" => $gst_percent,
                "actual_amount" => $payAmount,
                "debited_amount" => $payAmount,
                "actual_amount1" => $actualamt1,
                "paymentmode" => $mode,
                "gst_amount" => $gstamt,
                "cdate" => $indate,
                "cip" => $inip,
                "cby" => $userid,
                "invoice_date" => $invDate,
                "plan_description" => $plan_Description,
//                "incentive_userid" => $in_user,
//                "company_status" => $css,
                "is_igst" => $igst,
                "discountIncluded" => $discountIncluded,
                "dicountMaster" => $dicountOffers,
                "is_credit" => $payStatus,
                "pay_proof" => $pay_proof,
                "pay_proof_text" => $pay_proof_text,
                "jobsource" => $jobsource,
            );
            // print_r($insert_array);exit;
            $emp_pdf_dtls = DB::table('employer_registration as er')
                ->selectRaw('er.firstname,er.phonenumber,er.email,er.gst,e.address,`company-name` as company,e.employer,e.state')
                ->leftJoin('employer as e', 'e.employer', '=', 'er.id')
                ->where('er.company_details', '=', $empId)
                ->get();
            // print_r($emp_pdf_dtls);exit;
            if (count($emp_pdf_dtls) > 0) {
                // echo "if";exit;
                $invoice_no = $inv_prefix . $new_inv_no;
                $invoice_date = $invDate;
                $plan_name = $planName;
                $plan_validity = $pvalidity;
                $actual_amt = $actualamt1;
                $gst_percnt = $gst_percent;
                $sgst = 9;
                $cgst = 9;
                $gst_amt = $gstamt;
                $total = number_format((float)$payAmount, 2, '.', '');
                if ($emp_pdf_dtls[0]->company) {
                    $company_name = $emp_pdf_dtls[0]->company;
                    $company_address = explode('#@#', $emp_pdf_dtls[0]->address);
                    $company_mobile = $emp_pdf_dtls[0]->phonenumber;
                    $user_gstno = $emp_pdf_dtls[0]->gst;
                } else {
                    $company_name = $emp_pdf_dtls[1]->company;
                    $company_address = explode('#@#', $emp_pdf_dtls[1]->address);
                    $company_mobile = $emp_pdf_dtls[1]->phonenumber;
                    $user_gstno = $emp_pdf_dtls[1]->gst;
                }
                $address_0 = $company_address[0];
                $address_1 = $company_address[1]; // city
                $address_2 = $company_address[2];
                $address_3 = $company_address[3];
                // print_r($company_address[1]);exit;

                // get city name
                $cityvalue = DB::table('areas')->selectRaw('id, area_name')->where('id', '=', $company_address[1])->get();
                if (sizeof($cityvalue) > 0) {
                    $cty = $cityvalue[0]->area_name;
                } else {
                    $cty = "";
                }
                $find_gst = substr($user_gstno, 0, 2);

                if ($emp_pdf_dtls[0]->state == null && $state != '') {
                    $update_state = array(
                        "state" => $state
                    );
                    DB::table('employer')->where('id', '=', $empId)->update($update_state);
                } else {
                    // print_r('do not');
                }
//                return $company_address;
                $preparedby = "Nithra Apps India Pvt Ltd,<br>4th Floor, AV Plaza,<br>South Car Street, Tiruchengode,<br>Namakkal,<br>637211.";
                $preparedby_gst = "33AAECN4289M1Z0";
                // echo $preparedby." gst - ".$preparedby_gst;exit;
                $pdf_no = "EMPLOYER_POSTING_PLAN_" . $new_inv_no;
                $filename = "invoice_employer/" . $pdf_no . ".pdf";
//                $filename1 = "invoice_employer/" . $pdf_no;
                $filename1 = "invoices/" . $pdf_no . date('His');
// echo $pdf_no;exit;
                $html_Ad_gst = $html_Ad_gst_row = "";
                if ($user_gstno != 'undefined' || $user_gstno != '') {
                    $html_Ad_gst = ' <br> <span style="padding: 0px"> GST No: ' . $user_gstno . '</span>';
                } else {
                    $html_Ad_gst = '<br> <span style="padding: 0px"> GST No: </span>';
                }

                // gst igst rows
                if ($find_gst == '') {
                    if ($state != 1) {
                        $html_Ad_gst_row .= '<tr>
                            <td align ="right" style="padding: 5px"></td>
                            <td align ="right" colspan="2" style="padding-right: 0px;">IGST(18%) (<span class="curFont" style="padding: 0">₹</span>)&nbsp;
                            ' . $gst_amt . '&nbsp;&nbsp;&nbsp;</td>
                            </tr>';
                    } else {
                        $html_Ad_gst_row .= '<tr>
                    <td align ="right" style="padding: 5px"></td>
                    <td align ="right" colspan="2" style="padding-right: 0px;">SGST(9%) ( <span class="curFont" style="padding: 0">₹</span> )&nbsp;
                    ' . $gst_amt / 2 . '&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                    <td align ="right" style="padding: 5px"></td>
                    <td align ="right" colspan="2" style="padding-right: 0px;">CGST(9%) ( <span class="curFont" style="padding: 0">₹</span> )&nbsp;
                    ' . $gst_amt / 2 . '&nbsp;&nbsp;&nbsp;</td>
                    </tr>';
                    }
                } else {
                    if ($find_gst != '33') {
                        $html_Ad_gst_row .= '<tr>
                    <td align ="right"></td>
                    <td align ="right" colspan="2" style="padding-right: 0px;">IGST(18%) ( <span class="curFont" style="padding: 0">₹</span> )&nbsp;
                    ' . $gst_amt . '&nbsp;&nbsp;&nbsp;</td>
                    </tr>';
                    } else {
                        $html_Ad_gst_row .= '<tr>
                    <td align ="right" style="padding: 5px"></td>
                    <td align ="right" colspan="2" style="padding-right: 0px;">SGST(9%) ( <span class="curFont" style="padding: 0">₹</span> )&nbsp;' . $gst_amt / 2 . '&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                    <td align ="right" style="padding: 5px"></td>
                    <td align ="right" colspan="2" style="padding-right: 0px;">CGST(9%) ( <span class="curFont" style="padding: 0">₹</span> )&nbsp;
                    ' . $gst_amt / 2 . '&nbsp;&nbsp;&nbsp;</td>
                    </tr>';
                    }
                }
//return $address_0;
                $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><style>*{margin:0px; padding:20px;}body { font-family:freeserif;font-size : 12pt;background-color : #E8FAFB; } .curFont{font-family:"DeJaVu Sans Mono";} </style><body><h1 class="text-light"><span style = "margin-top : 50px;padding-left: 0px">&nbsp;NITHRA JOBS </span><br /></h1>';
                // Prepared for table
                $html .= '<table height="100%" width="100%" style="margin-top: -10px;margin-left: -10px; border-collapse: collapse;"><tr>
                        <td colspan="2">
                            <h2 style="color: #000;text-align: left;margin-left: 0px;padding-left: 0px" >Prepared for </h2>
                            <span style="line-height: 24px;padding: 0px">
                            ' . $company_name . ' <br>
                            ' . $address_0 . ' <br>
                            ' . (!empty($cty) ? $cty . ".<br>" : '') . '
                            ' . $address_2 . '.<br>
                            ' . $company_mobile . '
                        </span>' . $html_Ad_gst;
                $html .= '</td><td style="text-align: center;font-weight: bold;font-size: 22px">INVOICE</td><tr></table>';
                // Prepared by table
                $html .= '<table height="100%" width="100%" style="margin-top: -40px;margin-left: -10px; border-collapse: collapse;"><tr>
                    <td>
                        <h2 style="color: #000;text-align: left;margin-left: 0px;padding-left: 0px" >Prepared by </h2>
                        <span style="line-height: 24px;padding: 0px">
                        ' . nl2br($preparedby) . '
                            <br>GST No: 33AAECN4289M1Z0 <br>HSN Code: 998365
                        </span>
                    </td>
                    <td>Invoice No <br><br>Invoice Date</td>
                    <td>' . $invoice_no . '<br><br>' . date("F j, Y", strtotime($invoice_date)) . '</td><tr>';
                $html .= '</table>';
                $html .= '<hr style="border:1px solid #333;height: 0.5px;border-bottom: none;border-left: none;border-right: none">';

                // content table
                $html .= '<table height="100%" width="100%" style="margin-top: -40px;margin-left: -10px; border-collapse: collapse;">
                    <tr style="background: #02671c;color: #fff;">
                        <th style="padding: 3px;padding-left:0px;width: 10%;">#</th>
                        <th style="padding: 3px;padding-left:0px;width: 75%;">Description</th>
                        <th style="padding: 3px;padding-left:0px;padding-right:10px;width: 15%;" align="right">Total (<span class="curFont" style="padding: 0px">₹</span>)</th>
                    </tr>
                    <tr style="border-bottom: 1px solid #333">
                        <td style="padding: 15px;padding-left:0px;width: 10%;" align="center">1</td>
                        <td style="padding: 15px;padding-left:0px;width: 75%;">' . $plan_Description . '</td>
                        <td style="padding: 15px;padding-left:0px;width: 15%;" align="right">' . $actual_amt . '</td>
                    </tr>
                     // gst rows
                     ' . $html_Ad_gst_row . '
                    // total
                    <tr>
                        <td  style="padding: 5px"></td>
                        <td align="right" style="padding-right: 0px" colspan="2">Total ( <span class="curFont" style="padding: 0">₹</span> )&nbsp; ' . $total . '</td>
                    </tr>

                    // Note
                    <tr>
                        <td colspan="3" style="padding: 5px">
                            <h3 class="text-light opacity-50">Notes</h3>
                            <p style="padding: 0"><strong><em>** This is System Generated Invoice No signature required.</em></strong></p>
                        </td>
                    </tr>
                    ';
                $html .= '</table>';

                if ($fp_key == '') { // fresh invoice
                    $dompdf = new DOMPDF();
                    $dompdf->load_html($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();

                    $pathToSaveOn = 'invoices/' . $filename1 . '.pdf';
                    $path = \Storage::disk('s3')->put($pathToSaveOn, $dompdf->output());


                    $insert_array['invoice_pdf'] = $cloudFrontUrl . $pathToSaveOn;
                    $insert_array['mob_number'] = $company_mobile;
                    $insr = DB::table('franchise_company_debits')->insert($insert_array);
                    if ($insr) {
                        $gtEmp_ct = DB::table('franchise_company_debits')->selectRaw("debit_id, employer_id")->where("employer_id", '=', $empId)->orderBy('debit_id', 'desc')->first();

                        if ($insert_array['txn_amt'] > 0 && $insert_array['status'] == 'TXN_SUCCESS') {
                            app('App\Http\Controllers\Zoho\APIController')->zohoBooksInvoice($empId, $gtEmp_ct->debit_id);
                        }
                    }
//                    return $gtEmp_ct;

                }


                if ($fp_key != '' && $lasteventStatus == '') { // invoice request with verified
                    $dompdf = new DOMPDF();
                    $dompdf->load_html($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();

                    $pathToSaveOn = 'invoices/' . $filename1 . '.pdf';
                    $path = \Storage::disk('s3')->put($pathToSaveOn, $dompdf->output());
                    $insert_array['invoice_pdf'] = $cloudFrontUrl . $pathToSaveOn;
                    $insert_array['mob_number'] = $company_mobile;

                    $insert_array['fp_key'] = $fp_key;
                    $chkExist = DB::table('franchise_company_debits')
                        ->where('fp_key', '=', $fp_key)
                        ->get();
                    if (sizeof($chkExist) > 0) { // exist
                        $result['addStatus'] = false;
                        $result['message'] = 'Already Invoice Approved';
                        return $result;
                    } else {
                        $insr = DB::table('franchise_company_debits')->insert($insert_array);
                        if ($insr) {
                            $gtEmp_ct = DB::table('franchise_company_debits')->selectRaw("debit_id, employer_id")->where("employer_id", '=', $empId)->orderBy('debit_id', 'desc')->first();

                            if ($insert_array['txn_amt'] > 0 && $insert_array['status'] == 'TXN_SUCCESS') {
                                app('App\Http\Controllers\Zoho\APIController')->zohoBooksInvoice($empId, $gtEmp_ct->debit_id);
                            }
                        }

                        $last_id = DB::getPdo()->lastInsertId();
                        $lasteventStatus = $lasteventStatus == 3 ? $lasteventStatus : 4; // 3- invoice rejected 4- invoice reg
                        $update_invoice_no = array(
                            'invoiceNo' => $last_id,
                            'invoiceEventDate' => $indate,
                            'invoiceEventIp' => $inip,
                            'invoiceEventBy' => $userid,
                            'lastEventStatus' => 4,
                            'lastEventRemarks' => "",
                            'lastEventDate' => "",
                            'lastEventIp' => "",
                            'lastEventBy' => "",
                        );
                        $fpcte_in = DB::table('freePaidCallingToEmpr')->where('id', '=', $fp_key)->update($update_invoice_no);
                    }
                }
                if ($fp_key != '' && $lasteventStatus == 3) { // invoice request with rejected
                    $lasteventStatus = $lasteventStatus == 3 ? $lasteventStatus : 4; // 3- invoice rejected 4- invoice reg
                    $update_invoice_no = array(
                        'lastEventStatus' => $lasteventStatus,
                        'lastEventRemarks' => $lastEventRemarks,
                        'lastEventDate' => $indate,
                        'lastEventIp' => $inip,
                        'lastEventBy' => $userid,
                    );
                    $fpcte = DB::table('freePaidCallingToEmpr')->where('id', '=', $fp_key)->update($update_invoice_no);
                    if ($fpcte > 0) {
                        $result['addStatus'] = false;
                        $result['message'] = 'Invoice Rejected!';
                    }
                    return $result;
                }
                if ($insr > 0) {
                    $last_id_francis = DB::table('franchise_company_debits')
                        ->where('employer_id', '=', $empId)->limit('1')
                        ->orderBy('debit_id', 'desc')->get();
                    //  print_r($last_id_francis);exit;


                    $sourceData = array(
                        "employerid" => $empId,
                        "debit_id" => $last_id_francis[0]->debit_id,
                        "category" => 'Paid',
                        "inip" => $inip,
                        "indate" => $paydate,
                        "inby" => $userid,
                    );
                    DB::table('allot_company_source')->insert($sourceData);
//                  print_r($formDetails['featureDetails']);
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
                                "plan_purchace_id" => $last_id_francis[0]->debit_id,
                                "employer_id" => $emp_pdf_dtls[0]->employer,
                                "company_id" => $last_id_francis[0]->employer_id,
                                "mobile1" => $last_id_francis[0]->mob_number,
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
                            DB::table('feature_details_plan')->insert($addFeature);
                        }
                    }
                    if ($payStatus != '') {
                        $update_exEmployer = array(
                            'status' => 'Paid',
                        );
                        DB::table('executive_employer')
                            ->where('employer_id', '=', $empId)
                            ->update($update_exEmployer);
                    }
                    if ($insr) {
                        $result['addStatus'] = true;
                        $result['message'] = 'Plan details added successfully';
                    } else {
                        $result['addStatus'] = false;
                        $result['message'] = 'Could not add plan details!';
                    }
                }


            } else {
                // echo "else";exit;
                $result['addStatus'] = false;
                $result['message'] = 'Employer registration incorrect!';
            }
        }
        return $result;
    }


    // get data
    public function getPlan_new(Request $request)
    {
        $editId = $request->editId;
        if ($editId != "") {
            $editData = DB::connection('read')->table('franchise_company_debits as fcs')
                ->selectRaw('fcs.*,CONCAT(plan_id,"#@#",plan_name,"#@#",trim(txn_amt)+0) as  selectplan, fcs.debit_id as id,i.name as iname,`company-name` as company,DATE_FORMAT(starting_date,"%d-%m-%Y") as start,DATE_FORMAT(expired_date,"%d-%m-%Y") as ending,DATE_FORMAT(txn_date,"%d-%m-%Y") as tx_date,DATE_FORMAT(invoice_date,"%d-%m-%Y") as invoic_date,e.state as state, if(fcs.zoho_invoice_url !="", fcs.zoho_invoice_url, fcs.invoice_pdf) as invoice_pdfs')
                ->leftJoin('employer as e', 'e.id', '=', 'fcs.employer_id')
                ->leftJoin('user as i', 'i.id', '=', 'fcs.cby')
                ->where('fcs.debit_id', '=', $editId)
                ->get();

            // feature_details_plan
            $featureData = DB::table('feature_details_plan')->where('plan_purchace_id', '=', $editId)->get();

            $result['featureData'] = $featureData;
            $result['editData'] = $editData;

        } else {
            $where = $dates = $where_vr_status = $where_ins_user = $where_ccihtml = $wherePlanname = $whereDiscount = $whereMode = array();
            $whereIn = $whereInDate = $whereInvr_status = $whereInins_user = $whereIn_ccihtml = $whereIn_Planname = $whereIn_Discount = $whereIn_Mode = '1';

            $mode = $request->mode;
            $date = $request->date;
            $vr_status = $request->vr_status;
            $ins_user = $request->ins_user;
            $cancel_credit_invoice_html = $request->cancel_credit_invoice_html;
            $plan_name = $request->plan_name;
            $discount = $request->discount;


            $fromdate = date("Y-m-d", strtotime($date['startDate']));
            $todate = date("Y-m-d", strtotime($date['endDate']));
//        echo "start : ".$fromdate;
//        print("\n");
//        echo "enddate :".$todate;exit;
            if ($date != "") {
                $fr = $fromdate;
                $t = $todate;
                $from = date('Y-m-d', strtotime($fr));
                $to = date('Y-m-d', strtotime($t));
                $dates[] = "date(txn_date) BETWEEN '$from' and '$to'";
                $date_from = strtotime($from);
                $date_to = strtotime($to);
            }
            if (count($dates)) {
                $whereInDate = implode(' AND ', $dates);
            }

            if ($vr_status != "") {
                $where_vr_status[] = "fcs.is_proof_verify = '$vr_status'";
            }
            if (count($where_vr_status)) {
                $whereInvr_status = implode(' AND', $where_vr_status);
            }

            if ($ins_user != "") {
                $where_ins_user[] = "fcs.incentive_userid = $ins_user";
            }
            if (count($where_ins_user)) {
                $whereInins_user = implode(' AND', $where_ins_user);
            }

            if ($cancel_credit_invoice_html != "") {
                $where_ccihtml[] = "fcs.cancel_credit_invoice = $cancel_credit_invoice_html";
            }
            if (count($where_ccihtml)) {
                $whereIn_ccihtml = implode(' AND', $where_ccihtml);
            }

            if ($plan_name != "") {
                $wherePlanname[] = "fcs.plan_name = '$plan_name' ";
            }
            if (count($wherePlanname)) {
                $whereIn_Planname = implode(' AND', $wherePlanname);
            }

            if ($discount != "") {
                $whereDiscount[] = "fcs.discountIncluded = '$discount' ";
            }
            if (count($whereDiscount)) {
                $whereIn_Discount = implode(' AND', $whereDiscount);
            }

            if (!empty($mode)) {
                if (in_array('Paytm', $mode)) {
                    $whereMode[] = "paymentmode != 'Offer' AND paymentmode !='Axis Bank' AND paymentmode !='RBL Bank' AND paymentmode !='Pay U Money' AND paymentmode != 'Cash' AND paymentmode !='Google Pay' AND paymentmode !='APP-UPI_PAYMENTS' ";
                } else {
                    if (!in_array('Paytm', $mode) && !empty($mode)) {
                        $res = "'" . implode("', '", $mode) . "'"; // print_r($res);exit;
                        $whereMode[] = "paymentmode IN ($res)";
                    } else {
                        $whereMode[] = "paymentmode != '' ";
                    }
                }

                if (count($whereMode)) {
                    $whereIn_Mode = implode(' AND', $whereMode);
                }
            }

            $getData = DB::connection('read')->table('franchise_company_debits as fcs')
                ->selectRaw('fcs.debit_id, fcs.debit_id as id,fcs.paymentmode,fcs.pay_proof, fcs.pay_proof_text,fcs.is_proof_verify, fcs.cancel_credit_invoice,fcs.mob_number,fcs.txn_amt,fcs.company_status,fcs.plan_name,fcs.plan_type, fcs.post_count, fcs.validity, fcs.job_validity, fcs.cdate,fcs.cip,fcs.invoice_pdf, fcs.incentive_userid, `company-name` as company,DATE_FORMAT(starting_date,"%d-%m-%Y") as start,DATE_FORMAT(expired_date,"%d-%m-%Y") as ending,DATE_FORMAT(txn_date,"%d-%m-%Y") as tx_date,if(fcs.is_credit = 1,"Credit","Paid") as paid_status,ifnull(DATE_FORMAT(invoice_date,"%d-%m-%Y"),DATE_FORMAT(txn_date,"%d-%m-%Y")) as invoice_date,concat(inv_prefix,inv_no) as inv,e.gst,round((txn_amt -(txn_amt/1.18))/2,2) as cgst,round((txn_amt -(txn_amt/1.18))/2,2) as sgst,round((txn_amt/1.18),2) as taxable_amt,fcs.res_msg,js.jobsource,cs.source,c_s.source as jSource, IF(`fcs`.`cby` >0, (SELECT `i`.`name` FROM `user` `i` WHERE `i`.`id`=`fcs`.`cby`), "---") as `iname`, IF(`fcs`.`incentive_userid` >0, (SELECT `ins`.`name` FROM `user` `ins` WHERE `ins`.`id`=`fcs`.`incentive_userid`), "---") as `ins_name`,IF(`fcs`.`upby` >0, (SELECT `u`.`name` FROM `user` `u` WHERE `u`.`id`=`fcs`.`upby`), "---") as `up_name`,if(fcs.zoho_invoice_url !="", fcs.zoho_invoice_url, if(SUBSTRING(fcs.invoice_pdf, 1, 16) = "invoice_employer", concat("https://nithrajobs.com/admin/","",fcs.invoice_pdf),fcs.invoice_pdf ) ) as invoice_pdfs, if((e.gst!="" and fcs.zohoMode = 0 and fcs.txn_amt > 0 and fcs.status = "TXN_SUCCESS"), 1, 0) as einvoice, e.id as emprId')
                ->leftJoin('employer as e', 'e.id', '=', 'fcs.employer_id')
                ->leftJoin('jobsource as js', 'js.id', '=', 'fcs.jobsource')
                ->leftJoin('company_source as c_s', 'c_s.id', '=', 'fcs.jobsource')
                ->leftJoin('company_source as cs', 'cs.id', '=', 'fcs.company_source')
                ->where('fcs.status', '=', 'TXN_SUCCESS')
                ->whereRaw($whereInDate)
                ->whereRaw($whereInvr_status)
                ->whereRaw($whereInins_user)
                ->whereRaw($whereIn_ccihtml)
                ->whereRaw($whereIn_Planname)
                ->whereRaw($whereIn_Discount)
                ->whereRaw($whereIn_Mode)
                ->orderBy('fcs.debit_id', 'desc')
                ->get();
            $result['getData'] = $getData;
        }
        return $result;
    }

    // get incentive user dropdown
    public function getIncentiveUserDrop()
    {
        $result['user'] = DB::table('user')->where('is_active', '=', 0)->get();
        return $result;
    }

    // get plan name
    public function getPlanName()
    {
        $result['planName'] = DB::table('franchise_company_debits')
            ->selectRaw('distinct(plan_name) as plan_name')
            ->where('plan_name', '!=', '')
            ->orderBy('plan_name')->get();
        return $result;
    }

    // get payment proof
    public function getPayProofDetails(Request $request)
    {
        $id = $request->id;
        $getData = DB::table('franchise_company_debits')->where('debit_id', '=', $id)->first();
        $result['getData'] = $getData;
        return $result;
    }

    // payment proof verify
    public function payproofVerify(Request $request)
    {
        $id = $request->id;
        $uid = $request->uid;
        $inip = $request->ip();
        $indate = date('Y-m-d H:i:s');
        if ($id) {
            $updatas = array(
                'is_proof_verify' => 1,
                'upby' => $uid,
                'upip' => $inip,
                'up_date' => $indate,
            );
            $updates = DB::table('franchise_company_debits')->where('debit_id', '=', $id)->update($updatas);

            if ($updates > 0) {
                $result['message'] = 'Verified';
            } else {
                $result['message'] = 'Could not verify!';
            }
        } else {
            $result['message'] = 'Could not verify!';
        }
        return $result;
    }

    // updateIncentiveUser
    public function updateIncentiveUser(Request $request)
    {
        $id = $request->id;
        $user = $request->user;
        $uid = $request->uid;
        $inip = $request->ip();
        $indate = date('Y-m-d H:i:s');
        if ($id != "") {
            $updatas = array(
                'incentive_userid' => $user,
                'upby' => $uid,
                'upip' => $inip,
                'up_date' => $indate,
            );
            $ups = DB::table('franchise_company_debits')->where('debit_id', '=', $id)->update($updatas);
            if ($ups > 0) {
                $result['Status'] = true;
                $result['message'] = 'User Updated!';
            } else {
                $result['Status'] = false;
                $result['message'] = 'Could not update user!';
            }
        } else {
            $result['Status'] = false;
            $result['message'] = 'Could not update user!';
        }
        return $result;
    }

    // invoice regenerated
    public function getInvoiceDetails(Request $request)
    {
        $id = $request->id;
        $emp_pdf_dtls = DB::table('franchise_company_debits as f')
            ->selectRaw('f.*,e.address,`company-name` as company,DATE_FORMAT(invoice_date,"%d-%m-%Y") as inv_date')
            ->leftJoin('employer as e', 'e.id', '=', 'f.employer_id')
            ->where('f.debit_id', '=', $id)->get();
        return $emp_pdf_dtls;
    }

    // reGenerateInvPDF
    public function reGenerateInvPDF(Request $request)
    {
        $cloudFrontUrl = "https://d2hy6ree306xec.cloudfront.net/";
        $address = $request->address;
        $inv_date = $request->invoice_date;

        $id = $request->id; // debit id
        $uid = $request->uid;
        $inip = $request->ip();
        $indate = date('Y-m-d H:i:s');

        $empDetails = DB::table('franchise_company_debits')->where('debit_id', '=', $id)->get();
        if (count($empDetails) > 0) {
            $upEmp = array(
                "address" => $address,
                "update" => $indate,
                "upip" => $inip,
                "upby" => $uid,
            );
            DB::table('employer')->where('id', '=', $empDetails[0]->employer_id)->update($upEmp);
            // get data after update
            $updatedEmp = DB::table('employer_registration as er')
                ->selectRaw('er.id,er.firstname,er.phonenumber,er.email,e.gst,e.address,`company-name` as company')
                ->leftJoin('employer as e', 'e.employer', '=', 'er.id')
                ->where('er.company_details', '=', $empDetails[0]->employer_id)
                ->orderBy('er.id', 'desc')
                ->get();
            // print_r($updatedEmp);exit();
            // to update invoice date
            $upPlan = array(
//                "invoice_date" => $inv_date,
                "invoice_date" => date('Y-m-d', strtotime($inv_date)),
            );
//            print_r($upPlan);exit();
            DB::table('franchise_company_debits')->where('debit_id', '=', $id)->update($upPlan);
            // get data after update
            $updatedPlan = DB::table('franchise_company_debits')->where('debit_id', '=', $id)->get();
//              print_r($updatedPlan);die();
            if (count($updatedEmp) > 0) {
                //re generate pdf
                $invoice_no = $updatedPlan[0]->inv_prefix . $updatedPlan[0]->inv_no;
                $invoice_date = $updatedPlan[0]->invoice_date;
                $plan_name = $updatedPlan[0]->plan_name;
                $plan_validity = $updatedPlan[0]->validity;
                $actual_amt = $updatedPlan[0]->actual_amount1;
                $gst_percnt = $updatedPlan[0]->gst_percent;
                $igst = $updatedPlan[0]->is_igst;
                $gst_amt = $updatedPlan[0]->gst_amount;
                $total = number_format((float)$updatedPlan[0]->actual_amount, 2, '.', '');
                $company_name = $updatedEmp[0]->company;
                $company_address = str_replace('#@#', ',<br>', $updatedEmp[0]->address);
                $company_mobile = $updatedEmp[0]->phonenumber;
                $user_gstno = $updatedEmp[0]->gst;
                $find_gst = substr($user_gstno, 0, 2);
                $plan_Description = $updatedPlan[0]->plan_description;
                $preparedby = "Nithra Apps India Pvt Ltd,<br>4th Floor, AV Plaza,<br>South Car Street, Tiruchengode,<br>Namakkal,<br>637211.";
                $preparedby_gst = "33AAECN4289M1Z0";

                if ($uid != 24) {
//                    $filename = $updatedPlan[0]->invoice_pdf;
                    $pdf_no = "EMPLOYER_POSTING_PLAN_" . $updatedPlan[0]->inv_no;
                    $filename1 = "invoices/" . $pdf_no . date('His');
                } else {
                    $pdf_no = "EMPLOYER_POSTING_PLAN_" . $updatedPlan[0]->inv_no;
                    $filename = "invoice_employer/" . $pdf_no . ".pdf";
                    $filename1 = "invoices/" . $pdf_no . date('His');
                }
                $html_Ad_gst = $html_Ad_gst_row = "";
                if ($user_gstno) {
                    $html_Ad_gst = ' <br> <span style="padding: 0px"> GST No: ' . $user_gstno . '</span>';
                }
                // gst igst rows
                if ($igst == 1 || $find_gst != "33") {
                    if (!empty($find_gst) && $find_gst != "33") {
                        $html_Ad_gst_row = '<tr>
                                <td colspan="2" style="padding: 5px"></td>
                                <td align="right" style="padding: 5px">IGST(18%) ( <span class="curFont" style="padding: 0">₹</span> )&nbsp; ' . $gst_amt . '</td>
                            </tr>';
                    } else {
                        $html_Ad_gst_row = '<tr>
                                <td colspan="2" style="padding: 5px"></td>
                                <td align="right" style="padding: 5px">SGST(9%) ( <span class="curFont" style="padding: 0">₹</span> )&nbsp; ' . $gst_amt / 2 . '</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 5px"></td>
                                <td align="right" style="padding: 5px">CGST(9%) ( <span class="curFont" style="padding: 0">₹</span> )&nbsp; ' . $gst_amt / 2 . '</td>
                            </tr>';
                    }
                } else {
                    $html_Ad_gst_row = '<tr>
                            <td colspan="2" style="padding: 5px"></td>
                            <td align="right" style="padding: 5px">SGST(9%) ( <span class="curFont" style="padding: 0">₹</span> )&nbsp; ' . $gst_amt / 2 . '</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 5px"></td>
                            <td align="right" style="padding: 5px">CGST(9%) ( <span class="curFont" style="padding: 0">₹</span> )&nbsp; ' . $gst_amt / 2 . '</td>
                        </tr>';
                }
                $html = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><style>*{margin:0px; padding:20px;}body { font-family:freeserif;font-size : 12pt;background-color : #E8FAFB; } .curFont{font-family:"DeJaVu Sans Mono";} </style><body><h1 class="text-light"><span style = "margin-top : 50px;padding-left: 0px">&nbsp;NITHRA JOBS </span><br /></h1>';
                // Prepared for table
                $html .= '<table height="100%" width="100%" style="margin-top: -30px;margin-left: -10px; border-collapse: collapse;"><tr>
                    <td colspan="2">
                        <h2 style="color: #000;text-align: left;margin-left: 0px;padding-left: 0px" >Prepared for </h2>
                        <span style="line-height: 24px;padding: 0px">
                            ' . $company_name . ' <br>
                            ' . nl2br($company_address) . ' <br>
                            ' . $company_mobile . '
                        </span>' . $html_Ad_gst;
                $html .= '</td><td style="text-align: center;font-weight: bold;font-size: 22px">INVOICE</td><tr></table>';

                // Prepared by table
                $html .= '<table height="100%" width="100%" style="margin-top: -40px;margin-left: -10px; border-collapse: collapse;"><tr>
                    <td>
                        <h2 style="color: #000;text-align: left;margin-left: 0px;padding-left: 0px" >Prepared by </h2>
                        <span style="line-height: 24px;padding: 0px">
                        ' . nl2br($preparedby) . '
                            <br>GST No: 33AAECN4289M1Z0 <br>HSN Code: 998365
                        </span>
                    </td>
                    <td>Invoice No <br><br>Invoice Date</td>
                    <td>' . $invoice_no . '<br><br>' . date("F j, Y", strtotime($invoice_date)) . '</td><tr>';
                $html .= '</table>';
                $html .= '<hr style="border:1px solid #333;height: 0.5px;border-bottom: none;border-left: none;border-right: none">';

                // content table
                $html .= '<table height="100%" width="100%" style="margin-top: -40px;margin-left: -10px; border-collapse: collapse;">
                    <tr style="background: #02671c;color: #fff;">
                        <th style="padding: 3px">#</th>
                        <th style="padding: 3px">Description</th>
                        <th style="padding: 3px" align="right">Total (<span class="curFont" style="padding: 0">₹</span>)</th>
                    </tr>
                    <tr style="border-bottom: 1px solid #333">
                        <td align="center">1</td>
                        <td>' . $plan_Description . '</td>
                        <td align="right">' . $actual_amt . '</td>
                    </tr>
                     // gst rows
                     ' . $html_Ad_gst_row . '
                    // total
                    <tr>
                        <td colspan="2" style="padding: 5px"></td>
                        <td align="right" style="padding: 5px">Total ( <span class="curFont" style="padding: 0">₹</span> )&nbsp; ' . $total . '</td>
                    </tr>

                    // Note
                    <tr>
                        <td colspan="3" style="padding: 5px">
                            <h3 class="text-light opacity-50">Notes</h3>
                            <p style="padding: 0"><strong><em>** This is System Generated Invoice No signature required.</em></strong></p>
                        </td>
                    </tr>
                    ';
                $html .= '</table>';

                $dompdf = new DOMPDF();
                $dompdf->load_html($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                if ($uid != 24) {
//                    echo "if";
//                    exit;
                    $pathToSaveOn = 'invoices/' . $filename1 . '.pdf';
                    $path = \Storage::disk('s3')->put($pathToSaveOn, $dompdf->output());
                    $pdfDets = array(
                        "invoice_pdf" => $cloudFrontUrl . $pathToSaveOn,
                        "mob_number" => $company_mobile,
                        "upip" => $inip,
                        "up_date" => $indate,
                        "upby" => $uid,
                    );
                    DB::table('franchise_company_debits')->where('debit_id', '=', $id)->update($pdfDets);
                } else {
                    $pathToSaveOn = 'invoices/' . $filename1 . '.pdf';
                    //echo $cloudFrontUrl.$pathToSaveOn;exit;
                    $pdfDets = array(
                        "invoice_pdf" => $cloudFrontUrl . $pathToSaveOn,
                        "mob_number" => $company_mobile,
                        "upip" => $inip,
                        "up_date" => $indate,
                        "upby" => $uid,
                    );
                    DB::table('franchise_company_debits')->where('debit_id', '=', $id)->update($pdfDets);
                    $path = \Storage::disk('s3')->put($pathToSaveOn, $dompdf->output());
//                    if($path){
//                        echo "uploaded";
//                        echo $cloudFrontUrl . $pathToSaveOn;exit;
//                    }else{
//                        echo "not uploaded";exit;
//                    }
                }

                $result['addStatus'] = true;
                $result['message'] = 'Invoice Re-generated!';

            } else {
                $result['addStatus'] = false;
                $result['message'] = 'Could not re-generate!';
            }
        } else {
            $result['addStatus'] = false;
            $result['message'] = 'Could not re-generate!';
        }
        return $result;
    }

    // cancel_credit_invoice
    public function cancel_credit_invoice(Request $request)
    {
        // return $request;
        $id = $request->id; // debit_id
        $uid = $request->uid;
        $inip = $request->ip();
        $indate = date('Y-m-d H:i:s');
        if ($id) {
            $updatas = array(
                'cancel_credit_invoice' => 1,
                'cancel_credit_invoice_by' => $uid,
                'cancel_credit_invoice_ip' => $inip,
                'cancel_credit_invoice_date' => $indate,
            );

            $ups = DB::table('franchise_company_debits')->where('debit_id', '=', $id)->update($updatas);
            if ($ups > 0) {
                $result['addStatus'] = true;
                $result['message'] = 'Cancelled';
            } else {
                $result['addStatus'] = false;
                $result['message'] = 'Could not Cancelled!';
            }
        } else {
            $result['addStatus'] = false;
            $result['message'] = 'Could not Cancelled!';
        }
        return $result;
    }

    // update company status
    public function updateCompanyStatus(Request $request)
    {
        $cs_value = $request->cs_value;
        $userId = $request->userId;
        $id = $request->id;
        $inip = $request->ip();
        $indate = date('Y-m-d H:i:s');
        if ($id) {
            $updateData = array(
                'company_status' => $cs_value,
                'upby' => $userId,
                'upip' => $inip,
                'up_date' => $indate,
            );
            $ups = DB::table('franchise_company_debits')->where('debit_id', '=', $id)->update($updateData);
            if ($ups > 0) {
                $result['Status'] = true;
                $result['message'] = 'Company Status Updateds';
            } else {
                $result['Status'] = false;
                $result['message'] = 'Could not update!';
            }
        } else {
            $result['Status'] = false;
            $result['message'] = 'Could not update!';
        }
        return $result;
    }
}
