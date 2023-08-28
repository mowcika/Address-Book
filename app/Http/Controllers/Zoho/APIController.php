<?php

namespace App\Http\Controllers\Zoho;

use App\Models\FSEModel;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class APIController extends Controller
{
    private $prefixURI = 'https://nithra.in/tjobspro';

    private $client_id = '1000.8O6EP152R41WEZCBF14L5HOBUC4W0Z';
    private $client_secret = '00bffbd1718f837daae673a8f6eb73de1affcf2c8b';
    private $redirect_uri = 'http://15.206.173.184/upload/raja/astro/response.php';
    private $refresh_token = '1000.459d929359e8cfc84bba8a477ef097b1.18a2add5e036a208a454474bf48c197d';

    private $scope = 'ZohoBooks.fullaccess.all';
    private $api_domain = 'https://www.zohoapis.in/books/v3/';
    private $token_type = 'Bearer';

    private $authtoken = '1000.3c0c944f1c9398d0335521ec922f3dd3.4ab815baf41875707d741ed7d632dd5e';

    private $organization_id = '60020541418'; // 60020541418 // 60010693180
    private $currency_id = '1258895000000000064'; // 60020541418 // 60010693180
    private $item_id = '1258895000000015426'; // 60020541418 // 60010693180
    private $account_id = '1258895000000021015'; // 60020541418 // 60010693180
    private $hsn = 998365;

    public function commonCURL($url, $method, $data)
    {
        $data_json = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Zoho-oauthtoken {$this->authtoken}",
            "content-type: application/json"
        ]);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        }
        if ($method == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        }
        if ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        }
        $response = curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        return $server_output;
    }

    public function getPrivateEmployer($employerID, $getDebitID)
    {
        $data = DB::table('employer as e')
            ->join('employer_registration as er', 'e.employer', '=', 'er.id')
            ->join('franchise_company_debits as fcd', 'e.id', '=', 'fcd.employer_id')
            ->select('e.id', 'e.company-name', 'e.address', 'e.state', 'e.district', 'e.city', 'er.firstname', 'er.lastname', 'er.phonenumber', 'er.email', 'er.designation', 'e.gst', 'e.zohoID', 'fcd.debit_id', 'fcd.inv_prefix', 'fcd.inv_no', 'fcd.plan_id', 'fcd.plan_name', 'fcd.post_count', 'fcd.actual_amount', 'fcd.job_validity', 'fcd.txn_amt', 'fcd.is_credit', 'fcd.actual_amount1', 'fcd.gst_amount', 'fcd.gst_percent', 'fcd.paymentmode', 'fcd.currency', 'fcd.txn_date', 'fcd.status', 'fcd.cdate', 'fcd.incentive_userid', 'fcd.is_igst', 'fcd.plan_description')
            ->where([
                ['e.type', 1],
                ['e.gst', "!=", ""],
                ['fcd.zoho_invoice_url', "=", ""],
                ['e.gst', "!=", "undefined"],
                ['fcd.debit_id', $getDebitID],
                ['fcd.status', 'TXN_SUCCESS'],
                ['fcd.txn_amt', '>', 0]
            ])->first();
        return $data;
    }

    public function zohoInitialize(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
                [
                    'code' => 'required' // from get url
                ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 200);
            }
            $server = 'https://accounts.zoho.in/oauth/v2/token';
            $http_build_query_array = array();
            $http_build_query_array[] = 'code=' . $request->code;
            $http_build_query_array[] = 'client_id=' . $this->client_id;
            $http_build_query_array[] = 'client_secret=' . $this->client_secret;
            $http_build_query_array[] = 'redirect_uri=' . $this->redirect_uri;
            $http_build_query_array[] = 'grant_type=authorization_code';
            if (sizeof($http_build_query_array)) {
                $http_build_query_string = implode("&", $http_build_query_array);
            }
            if (!$http_build_query_string)
                return false;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $server);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $http_build_query_string);
            $response = curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
            $date = Carbon::now();
            $date->addDays(30);
            Storage::disk("public")->put("zoho/response.json", $server_output);
            return response()->json([
                'status' => true,
                'response' => json_decode($server_output, true)
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function AccessTokenFromRefreshToken(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
                [
                    'refresh_token' => '' // from get url
                ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 200);
            }
            $server = 'https://accounts.zoho.in/oauth/v2/token';
            $http_build_query_array = array();
            $http_build_query_array[] = 'refresh_token=' . $this->refresh_token;
            $http_build_query_array[] = 'client_id=' . $this->client_id;
            $http_build_query_array[] = 'client_secret=' . $this->client_secret;
            $http_build_query_array[] = 'redirect_uri=' . $this->redirect_uri;
            $http_build_query_array[] = 'grant_type=refresh_token';
            if (sizeof($http_build_query_array)) {
                $http_build_query_string = implode("&", $http_build_query_array);
            }
            if (!$http_build_query_string)
                return false;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $server);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $http_build_query_string);
            $response = curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
            $date = Carbon::now()->toDateTimeString(); // remove ->toDateTimeString() while add
//            $date->addDays(30);
            $response_output = json_decode($server_output, true);
            $getZohoFile = Storage::disk("public")->get("zoho/response.json");
            $getZohoFileArray = json_decode($getZohoFile, true);
            $getZohoFileArray['access_token'] = $response_output['access_token'];
            $getZohoFileArray['last_access_token_time'] = $date;
            Storage::disk("public")->put("zoho/response.json", json_encode($getZohoFileArray));
            return response()->json([
                'status' => true,
                'response' => $response_output
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function gstValidate($getGSTnumber, $employerID, $debitID)
    {
        try {
            $getGSTnumber = $getGSTnumber;
            $server = 'https://sheet.gstincheck.co.in/check/4e3115f7969022c8a76ab66c6f45c799/' . $getGSTnumber;
            $server_output = $this->commonCURL($server, 'GET', array());
            $response_output = json_decode($server_output, true);
            return array(
                'status' => true,
                'response' => $response_output
            );
        } catch (\Throwable $th) {
            $indate = Carbon::now()->toDateTimeString();
            DB::table('zohoIssueTable')->insert([
                'employer_id' => $employerID,
                'debit_id' => $debitID,
                'json' => json_encode($th->getMessage()),
                'gst' => $getGSTnumber,
                'remarks' => "GST Validate Exception",
                'cdate' => $indate
            ]);
            return array(
                'status' => true,
                'response' => $th->getMessage()
            );
        }
    }

    public function createCustomer($getGSTINdetails, $getPrivateEmployer)
    {
        $getPrivateEmployerArray = json_decode(json_encode($getPrivateEmployer), true);
        if ($getGSTINdetails['response']['data']['sts'] != 'Active')
            return false;
        $tradeNam = $getGSTINdetails['response']['data']['tradeNam'];
        $tradeName = ($tradeNam == 'NA') ? $getPrivateEmployerArray['company-name'] : $tradeNam;
        $req = array(
            "contact_name" => $tradeName,
            "company_name" => $tradeName,
            "website" => "",
            "language_code" => "en",
            "contact_type" => "customer",
            "customer_sub_type" => "business",
//            "credit_limit" => 1000,
            "tags" => array(),
            "is_portal_enabled" => false,
            "currency_id" => $this->currency_id,
            "payment_terms" => "",
            "payment_terms_label" => "",
            "notes" => "Payment made via " . $getPrivateEmployerArray['paymentmode'],
            "billing_address" => array(
                "attention" => "",
                "address" => trim($getGSTINdetails['response']['data']['pradr']['addr']['bnm']) . ", " . trim($getGSTINdetails['response']['data']['pradr']['addr']['st']),
                "street2" => trim($getGSTINdetails['response']['data']['pradr']['addr']['loc']),
//                "state_code" => "TN",
                "city" => (trim($getGSTINdetails['response']['data']['pradr']['addr']['city'])) ? $getGSTINdetails['response']['data']['pradr']['addr']['city'] : $getGSTINdetails['response']['data']['pradr']['addr']['dst'],
                "state" => trim($getGSTINdetails['response']['data']['pradr']['addr']['stcd']),
                "zip" => trim($getGSTINdetails['response']['data']['pradr']['addr']['pncd']),
                "country" => "India",
                "fax" => "",
                "phone" => "91" . $getPrivateEmployerArray['phonenumber']
            ),
//            "shipping_address" => array(
//                "attention" => "Mr.Balasubramaniam",
//                "address" => $getGSTINdetails['response']['data']['pradr']['addr']['bnm'] . ", " . $getGSTINdetails['response']['data']['pradr']['addr']['st'],
//                "street2" => $getGSTINdetails['response']['data']['pradr']['addr']['loc'],
////                "state_code" => "TN",
//                "city" => $getGSTINdetails['response']['data']['pradr']['addr']['city'],
//                "state" => "Tamil Nadu",
//                "zip" => $getGSTINdetails['response']['data']['pradr']['addr']['pncd'],
//                "country" => "India",
//                "fax" => "",
//                "phone" => "919842701007"
//            ),
            "contact_persons" => array(array(
                "salutation" => "",
                "first_name" => $getPrivateEmployerArray['firstname'],
                "last_name" => $getPrivateEmployerArray['lastname'],
                "email" => $getPrivateEmployerArray['email'],
                "phone" => "",
                "mobile" => "91" . $getPrivateEmployerArray['phonenumber'],
                "designation" => $getPrivateEmployerArray['designation'],
                "department" => "",
                "skype" => "",
                "is_primary_contact" => true,
                "enable_portal" => false
            )),
//            "default_templates" => array(
//                "invoice_template_id" => 460000000052069,
//                "estimate_template_id" => 460000000000179,
//                "creditnote_template_id" => 460000000000211,
//                "purchaseorder_template_id" => 460000000000213,
//                "salesorder_template_id" => 460000000000214,
//                "retainerinvoice_template_id" => 460000000000215,
//                "paymentthankyou_template_id" => 460000000000216,
//                "retainerinvoice_paymentthankyou_template_id" => 460000000000217,
//                "invoice_email_template_id" => 460000000052071,
//                "estimate_email_template_id" => 460000000052073,
//                "creditnote_email_template_id" => 460000000052075,
//                "purchaseorder_email_template_id" => 460000000000218,
//                "salesorder_email_template_id" => 460000000000219,
//                "retainerinvoice_email_template_id" => 460000000000220,
//                "paymentthankyou_email_template_id" => 460000000000221,
//                "retainerinvoice_paymentthankyou_email_template_id" => 460000000000222
//            ),
            "custom_fields" => array(),
            "opening_balance_amount" => "",
            "exchange_rate" => "",
//            "vat_reg_no" => "",
//            "owner_id" => "",
//            "tax_reg_no" => "",
//            "country_code" => "IN",
//            "vat_treatment" => "",
//            "tax_treatment" => "",
//            "tax_regime" => "general_legal_person",
//            "is_tds_registered" => true,
            "place_of_contact" => "TN",
            "gst_no" => $getGSTINdetails['response']['data']['gstin'],
            "gst_treatment" => "business_gst",
            "tax_authority_name" => "",
//            "avatax_exempt_no" => "",
//            "avatax_use_code" => "",
            "tax_exemption_id" => "",
            "tax_exemption_code" => "",
            "tax_authority_id" => "",
//            "tax_id" => 648792000000025177,
            "tds_tax_id" => "",
            "is_taxable" => true,
            "facebook" => "",
            "twitter" => "",
//            "track_1099" => true,
//            "tax_id_type" => "",
//            "tax_id_value" => ""
        );
        return $req;
    }

    public function createInvoice($zohoID, $getPrivateEmployer)
    {
        $getPrivateEmployerArray = json_decode(json_encode($getPrivateEmployer), true);
        $req = array(
            "customer_id" => $zohoID,
            "currency_id" => $this->currency_id,
//            "contact_persons" => array(
//                "648792000000056003"
//            ),
            "invoice_number" => substr($getPrivateEmployerArray['inv_prefix'] . $getPrivateEmployerArray['inv_no'], 2),
            "place_of_supply" => "TN",
//            "vat_treatment"=> "string",
//            "tax_treatment"=> "vat_registered",
            "gst_treatment" => "business_gst",
            "gst_no" => $getPrivateEmployerArray['gst'],
//            "cfdi_usage"=> "acquisition_of_merchandise",
            "reference_number" => $getPrivateEmployerArray['debit_id'],
//            "template_id"=> 982000000000143,
            "date" => date('Y-m-d', strtotime($getPrivateEmployerArray['cdate'])),
//            "payment_terms"=> 15,
//            "payment_terms_label"=> "Net 15",
//            "due_date"=> "2013-12-03",
            "discount" => 0,
            "is_discount_before_tax" => true,
            "discount_type" => "item_level",
            "is_inclusive_tax" => false,
//            "exchange_rate"=> 1,
//            "recurring_invoice_id"=> " ",
//            "invoiced_estimate_id"=> " ",
//            "salesperson_name"=> " ",
            "custom_fields" => array(),
            "line_items" => array(
                array(
                    "item_id" => $this->item_id,
//                    "project_id"=> 90300000087378,
                    "time_entry_ids" => array(),
                    "product_type" => "service",
                    "hsn_or_sac" => $this->hsn,
//                    "sat_item_key_code"=> 71121206,
//                    "unitkey_code"=> "E48",
//                    "warehouse_id"=> "",
//                    "expense_id"=> " ",
//                    "expense_receipt_name"=> "string",
                    "name" => "Job Advertisement",
                    "description" => $getPrivateEmployerArray['plan_description'],
                    "item_order" => 1,
                    "bcy_rate" => $getPrivateEmployerArray['actual_amount1'],
                    "rate" => $getPrivateEmployerArray['actual_amount1'],
                    "quantity" => 1,
                    "unit" => " ",
                    "discount_amount" => 0,
                    "discount" => 0,
                    "tags" => array(),
//                    "tax_id"=> 648792000000025177,
//                    "tds_tax_id"=> "982000000557012",
//                    "tax_name"=> "VAT",
//                    "tax_type"=> "tax",
                    "tax_percentage" => $getPrivateEmployerArray['gst_percent'],
//                    "tax_treatment_code"=> "uae_others",
                    "header_name" => "Job Advertisement"
                )
            ),
//            "payment_options"=> Array(
//                "payment_gateways"=> Array(
//                    Array(
//                        "configured"=> true,
//                        "additional_field1"=> "standard",
//                        "gateway_name"=> "paypal"
//                    )
//                )
//            ),
            "allow_partial_payments" => true,
            "custom_body" => " ",
            "custom_subject" => " ",
            "notes" => "Looking forward for your business.",
            "terms" => "Terms & Conditions apply",
            "shipping_charge" => 0,
            "adjustment" => 0,
            "adjustment_description" => " ",
            "reason" => " ",
//            "tax_authority_id"=> 11149000000061052,
//            "tax_exemption_id"=> 11149000000061054,
//            "avatax_use_code"=> "string",
//            "avatax_exempt_no"=> "string",
//            "tax_id"=> 982000000557028,
//            "expense_id"=> " ",
//            "salesorder_item_id"=> " ",
//            "avatax_tax_code"=> "string",
//            "time_entry_ids"=> Array()
        );
        return $req;
    }

    public function createPayment($zohoID, $getPrivateEmployer, $getCustomerAddDetails)
    {
        $getPrivateEmployerArray = json_decode(json_encode($getPrivateEmployer), true);
        $getCustomerAddDetailsArray = json_decode(json_encode($getCustomerAddDetails), true);
        $req = array(
            "customer_id" => $zohoID,
            "payment_mode" => "banktransfer",
            "amount" => $getPrivateEmployerArray['txn_amt'],
            "date" => date('Y-m-d', strtotime($getPrivateEmployerArray['txn_date'])),
            "reference_number" => $getCustomerAddDetailsArray['invoice']['invoice_number'],
            "description" => "Payment has been added to " . $getCustomerAddDetailsArray['invoice']['invoice_number'],
            "invoices" => array(
                array(
                    "invoice_id" => $getCustomerAddDetailsArray['invoice']['invoice_id'],
                    "amount_applied" => $getPrivateEmployerArray['txn_amt']
                )
            ),
//            "exchange_rate" => 1,
//            "payment_form" => "cash",
            "bank_charges" => 0,
//            "custom_fields" => array(
//                array(
//                    "label" => "label",
//                    "value" => 129890
//                )
//            ),
            "invoice_id" => $getCustomerAddDetailsArray['invoice']['invoice_id'],
            "amount_applied" => $getPrivateEmployerArray['txn_amt'],
//            "tax_amount_withheld" => 0,
            "account_id" => $this->account_id,
//            "contact_persons" => array(
//                "982000000870911",
//                "982000000870915"
//            )
        );
        return $req;
    }

    public function ZohoApiCalls(Request $request)
    {
        $employerID = $request->employerID;
        $debitID = $request->debitID;
//        $employerID = 13117;
//        $debitID = 67576;
//        return $this->createPayment();
        return $this->zohoBooksInvoice($employerID, $debitID);
    }

    public function zohoBooksInvoice($employerID, $debitID)
    {
        try {
            $getZohoFile = Storage::disk("public")->get("zoho/response.json");
            $getZohoFileArray = json_decode($getZohoFile, true);
            $this->authtoken = $getZohoFileArray['access_token'];
            $remarks = $json = $gst = $zohoMode = "";
            $getPrivateEmployer = $this->getPrivateEmployer($employerID, $debitID);
            $getPrivateEmployerArray = json_decode(json_encode($getPrivateEmployer), true);
            if (isset($getPrivateEmployerArray['gst']) && trim($getPrivateEmployerArray['gst'] ?? '') && !trim($getPrivateEmployerArray['zohoID'] ?? '')) {
                $getGSTIN = $this->gstValidate($getPrivateEmployerArray['gst'], $employerID, $debitID); // '33AAACD8549G1ZO'
//                $getGSTIN = Storage::disk("public")->get("matrimony/gstin.json");
//                $getGSTINArray = json_decode($getGSTIN, true);
                $getGSTINArray = $getGSTIN;
                if (is_array($getGSTINArray['response']) && $getGSTINArray['response']['flag'] && $getGSTINArray['response']['data']['sts'] == "Active") {
                    $createCustomer = $this->createCustomer($getGSTINArray, $getPrivateEmployer);
                    $server = $this->api_domain . 'contacts?organization_id=' . $this->organization_id;
                    $getCustomerAddDetails = $this->commonCURL($server, 'POST', $createCustomer);
                    $getCustomerAddDetailsArray = json_decode($getCustomerAddDetails, true);
                    if (isset($getCustomerAddDetailsArray['contact']['contact_id']) && $getCustomerAddDetailsArray['contact']['contact_id'] && $getCustomerAddDetailsArray['code'] == 0) {
                        $Details = DB::table('employer')->where([['id', $employerID]])->update([
                            'zohoID' => $getCustomerAddDetailsArray['contact']['contact_id'],
                        ]);
                        if ($Details) {
                            $createInvoice = $this->createInvoice($getCustomerAddDetailsArray['contact']['contact_id'], $getPrivateEmployer);
                            $server = $this->api_domain . 'invoices?organization_id=' . $this->organization_id;
                            $getInvoiceDetails = $this->commonCURL($server, 'POST', $createInvoice);
                            $getInvoiceAddDetailsArray = json_decode($getInvoiceDetails, true);
                            if ($getInvoiceAddDetailsArray['code'] == 0) {
                                $isFCDupdate = DB::table('franchise_company_debits')->where([['debit_id', $debitID]])->update([
                                    'zoho_invoice_id' => $getInvoiceAddDetailsArray['invoice']['invoice_id'],
                                    'zoho_invoice_number' => $getInvoiceAddDetailsArray['invoice']['invoice_number'],
                                    'zoho_inv_ref_num' => $getInvoiceAddDetailsArray['invoice']['einvoice_details']['inv_ref_num'],
                                    'zoho_status' => $getInvoiceAddDetailsArray['invoice']['einvoice_details']['status'],
                                    'zoho_invoice_url' => $getInvoiceAddDetailsArray['invoice']['invoice_url'],
                                    'zohoMode' => 1
                                ]);
                                if ($isFCDupdate) {
                                    $createPayment = $this->createPayment($getCustomerAddDetailsArray['contact']['contact_id'], $getPrivateEmployer, $getInvoiceAddDetailsArray);
                                    $server = $this->api_domain . 'customerpayments?organization_id=' . $this->organization_id;
                                    $this->commonCURL($server, 'POST', $createPayment);
                                }
                            } else {
                                $remarks = 'IF : Invoice creation issue';
                                $json = json_encode($getInvoiceAddDetailsArray);
                                $gst = $getPrivateEmployerArray['gst'];
                                $zohoMode = 4;
                            }
                        }
                    } else {
                        $remarks = 'IF : Contact creation issue';
                        $json = json_encode($getCustomerAddDetailsArray);
                        $gst = $getPrivateEmployerArray['gst'];
                        $zohoMode = 4;
                    }
                } else {
                    $remarks = 'IF : GST No Active';
                    $json = json_encode($getGSTINArray);
                    $gst = $getPrivateEmployerArray['gst'];
                    $zohoMode = 3;
                }
            } else if (isset($getPrivateEmployerArray['gst']) && trim($getPrivateEmployerArray['gst'] ?? '') && trim($getPrivateEmployerArray['zohoID'] ?? '')) {
                $getGSTIN = $this->gstValidate($getPrivateEmployerArray['gst'], $employerID, $debitID);// '33AAACD8549G1ZO'
//                $getGSTIN = Storage::disk("public")->get("matrimony/gstin.json");
//                $getGSTINArray = json_decode($getGSTIN, true);
                $getGSTINArray = $getGSTIN;
                if (is_array($getGSTINArray['response']) && $getGSTINArray['response']['flag'] && $getGSTINArray['response']['data']['sts'] == "Active") {
                    $createInvoice = $this->createInvoice($getPrivateEmployerArray['zohoID'], $getPrivateEmployer);
                    $server = $this->api_domain . 'invoices?organization_id=' . $this->organization_id;
                    $getInvoiceDetails = $this->commonCURL($server, 'POST', $createInvoice);
                    $getCustomerAddDetailsArray = json_decode($getInvoiceDetails, true);
                    if ($getCustomerAddDetailsArray['code'] == 0) {
                        $isFCDupdate = DB::table('franchise_company_debits')->where([['debit_id', $debitID]])->update([
                            'zoho_invoice_id' => $getCustomerAddDetailsArray['invoice']['invoice_id'],
                            'zoho_invoice_number' => $getCustomerAddDetailsArray['invoice']['invoice_number'],
                            'zoho_inv_ref_num' => $getCustomerAddDetailsArray['invoice']['einvoice_details']['inv_ref_num'],
                            'zoho_status' => $getCustomerAddDetailsArray['invoice']['einvoice_details']['status'],
                            'zoho_invoice_url' => $getCustomerAddDetailsArray['invoice']['invoice_url'],
                            'zohoMode' => 1
                        ]);
                        if ($isFCDupdate) {
                            $createPayment = $this->createPayment($getPrivateEmployerArray['zohoID'], $getPrivateEmployer, $getCustomerAddDetailsArray);
                            $server = $this->api_domain . 'customerpayments?organization_id=' . $this->organization_id;
                            $this->commonCURL($server, 'POST', $createPayment);
                        }
                    } else {
                        $remarks = 'IF ELSE : Invoice creation issue';
                        $json = json_encode($getCustomerAddDetailsArray);
                        $gst = $getPrivateEmployerArray['gst'];
                        $zohoMode = 4;
                    }
                } else {
                    $remarks = 'IF ELSE : GST No Active';
                    $json = json_encode($getGSTINArray);
                    $gst = $getPrivateEmployerArray['gst'];
                    $zohoMode = 3;
                }
            } else {
                $remarks = 'ELSE : no GST, else part';
                $gst = (isset($getPrivateEmployerArray['gst']) && trim($getPrivateEmployerArray['gst'] ?? '')) ? $getPrivateEmployerArray['gst'] : "";
                $zohoMode = 5;
            }
            if ($remarks) {
                $indate = Carbon::now()->toDateTimeString();
                DB::table('zohoIssueTable')->insert([
                    'employer_id' => $employerID,
                    'debit_id' => $debitID,
                    'json' => $json,
                    'gst' => $gst,
                    'remarks' => $remarks,
                    'cdate' => $indate
                ]);
                DB::table('franchise_company_debits')->where([['debit_id', $debitID]])->update([
                    'zohoMode' => $zohoMode
                ]);
            }
        } catch (\Throwable $th) {
            $indate = Carbon::now()->toDateTimeString();
            DB::table('zohoIssueTable')->insert([
                'employer_id' => $employerID,
                'debit_id' => $debitID,
                'json' => json_encode($th->getMessage()),
                'gst' => "",
                'remarks' => "Exception",
                'cdate' => $indate
            ]);
            DB::table('franchise_company_debits')->where([['debit_id', $debitID]])->update([
                'zohoMode' => 2
            ]);
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getAll(Request $request)
    {
        $validateUser = Validator::make($request->all(),
            [
                'route' => 'required', // from get url
                'requestMethod' => 'required'
            ]);

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 200);
        }
        $params = $request->all();
        unset($params['route']);
        unset($params['requestMethod']);
        $http_build_query_array = array();
        $http_build_query_string = '';
        foreach ($params as $key => $value) {
            $http_build_query_array[] = $key . '=' . $value;
        }
        $http_build_query_array[] = 'organization_id=' . $this->organization_id;
        if (sizeof($http_build_query_array)) {
            $http_build_query_string = implode("&", $http_build_query_array);
        }
        if (!$http_build_query_string)
            return false;
        $getZohoFile = Storage::disk("public")->get("zoho/response.json");
        $getZohoFileArray = json_decode($getZohoFile, true);
        $this->authtoken = $getZohoFileArray['access_token'];
        $getRequestmethod = $request->requestMethod;
        $server = $this->api_domain . $request->route . '?organization_id=' . $this->organization_id;
        $server_output = $this->commonCURL($server, $getRequestmethod, $http_build_query_string);
        $response_output = json_decode($server_output, true);
        return response()->json([
            'status' => true,
            'response' => $response_output
        ], 200);
    }
}
