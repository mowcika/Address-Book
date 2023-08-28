<?php

namespace App\Http\Controllers;

use App\Http\Controllers\helpers;
use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

//(new helpers)->method();

class JobsConstroller extends Controller
{
    public function implode_treeSelect($request_array)
    {
        $return_array = array();
        foreach ($request_array as $kay => $value) {
            if (is_numeric($value)) {
                $return_array[] = $value;
            } else {
                $new_values = explode(',', $value);
//                print_r($new_values);
                $return_array = array_merge($return_array, $new_values);
                unset($new_values);
            }
        }
//                print_r($return_array);
        return implode(',', $return_array);
    }

    public function viewJobs(Request $request)
    {
//        return $request;
        \DB::enableQueryLog();
        $table = $request->table;
        $where = array();
        $whereina = ' 1 ';
        $where[] = ['j.deleted', 0];
        $where[] = ['j.blocked', 0];
        if ($table == 'draft_jobs') {

        }
        if ($request->jobtype) {
            $whereina = "j.jobtype IN (" . implode(',', $request->jobtype) . ")";
        }

        $data = DB::connection('read')->table($table . ' as j')
            ->leftJoin('employer as e', 'j.employerid', '=', 'e.id')
            ->leftJoin('jobscat as cat', 'j.job-cat', '=', 'cat.id')
            ->select('j.id',
                'j.jobtype',
                'j.verified',
                'j.views',
                'e.image as logo',
                'company-name as company',
                'j.jobtitle',
                'cat.job-cat as jobcat',
                'j.starting',
                'j.ending',
                'j.examdate',
                'j.examcentre',
                'j.description',
                'j.phone_new',
                'j.paid_date',
                'j.paid_amt',
                'j.appName-salary as gsal',
            )
            ->where($where)
            ->whereRaw($whereina)
            ->orderBy('id', 'DESC')->get();
//        ddd(\DB::getQueryLog());
        return $data;
    }


    public function getJobs(Request $request)
    {
//        return $request;
//        \DB::enableQueryLog();
        $table = $request->table;
        $where = array();
        if ($table == 'draft_jobs') {
            $where[] = ['j.deleted', 0];
            $where[] = ['j.blocked', 0];
        }
        if ($request->id) {
            $where[] = ['j.id', $request->id];
            $id_alias_name = 'id';
        } else if ($request->draft_id) {
            $where[] = ['j.id', $request->draft_id];
            $id_alias_name = 'draft_id';
        }
        $common_columns = ['j.id as ' . $id_alias_name, 'jobtype', 'employerid', 'jobtitle_id', 'jobtitle', 'workmode', 'j.description as jobDetails', 'singlequalification', 'groupqualification', 'agelimit', 'starting as startDate', 'ending as endDate', 'j.website as website1', 'jobsource as jobSource', 'referredby', 'keyword', 'list_view_jobs as listViewImageURL', 'detail_view_jobs as detailedViewImageURL', 'banner_web_url as bannerWebURL', 'j.verified', 'e.company-name as companyname', 'noofvancancy'];
        if ($request->jobtype == 1) {
            $columns = [
                'job_plan_id',
                'location',
                'joblocation',
                'j.address',
                'contact_person_detail',
                'contact_detail',
                'phone_new as mobileSeperate',
                'j.email as emailSingle',
                'email_new as email',
                'qualification',
                'skills',
                'from_exp as experienceFrom',
                'to_exp as experienceTo',
                'experience_remarks as experienceRemarks',
                'agelimit_start as ageLimitFrom',
                'agelimit as ageLimitTo',
                'from_salary as salaryFrom',
                'to_salary as salaryTo',
                'j.gender',
                'marital_status',
                'j.employer',
                'paid_post',
                'paid_date',
                'paid_amt',
                'disable_call_button as responseCall',
                'enable_detail_apply as responseMail',
                'enable_resume_apply as resumeMail',
                'whatsapp_number as whatsappNumber',
                'map_url as mapURL',
                'salary_remarks as salaryRemarks',
                'candidates_apply_location as candidateLocation',
                'contact_person_detail as contactperson',
                'contact_detail as contactDetails',
                'call_from_time as callTimeFrom',
                'call_to_time as callTimeTo',
                'j.website',
                'job_location_remark as joblocationRemarks',
                'Interviewdate as interviewDate',
                'PostSendNotifi as sendNotification',
                'singleGroupPost',
                'show_remark'
            ];
        } else {
            $columns = ['appName-salary as salary', 'selection-process as selectionProcess', 'fees-details as feesDetails', 'examcentre as examCenter', 'how-to-apply as howToApply', 'postal-address as postalAddress', 'examdate as examDate', 'website2'];
        }
        $common_columns = array_merge($common_columns, $columns);
        $data = DB::connection('read')->table($table . ' as j')
            ->leftJoin('employer as e', 'j.employerid', '=', 'e.id')
            ->leftJoin('jobtitle as t', 'j.jobtitle_id', '=', 't.id')
            ->select(
                $common_columns
            )
            ->selectRaw("concat(t.tamil,'(',t.english,')') as jobtitlename")
            ->where($where)
            ->orderBy('j.id', 'DESC')->get();
//        dd(\DB::getQueryLog());
        return $data;
    }

    public function savegovtjobs(Request $request)
    {
        \DB::enableQueryLog();
        $today = Carbon::now()->toDateString();
        if ($request->singleTextFromGroupQualification && count($request->singlequalification)) {
            $qualification = (implode(',', $request->singlequalification)) . ',' . $request->singleTextFromGroupQualification;
        } else {
            $qualification = ($request->singleTextFromGroupQualification) ? $request->singleTextFromGroupQualification : implode(',', $request->singlequalification);
        }
        $job_category_details = '';
        if ($request->jobtitle_id) {
            $job_title_details = DB::table('jobtitle')
                ->where('id', '=', $request->jobtitle_id)
                ->get();
            $job_category_details = $job_title_details[0]->category;
        }
//        return $job_category_details;
        $form_data = [
            'jobtype' => $request->jobtype,
            'employerid' => $request->employerid,
            'jobtitle_id' => $request['jobtitle_id'],
            'jobtitle' => $request['jobtitle'],
            'job-cat' => explode(',', $job_category_details)[0],
            'mul_category' => $job_category_details,
            'workmode' => $request->workmode,
            'description' => $request->jobDetails,
            'qualification' => $qualification,
            'singlequalification' => implode(',', $request->singlequalification),
            'groupqualification' => implode(',', $request->groupqualification),
            'agelimit' => $request->agelimit,
            'starting' => $request->startDate,
            'ending' => $request->endDate,
            'jobsource' => $request->jobSource,
            'referredby' => $request->referredby,
            'keyword' => implode(',', $request->key_word_db_web),
            'list_view_jobs' => $request->listViewImageURL,
            'detail_view_jobs' => $request->detailedViewImageURL,
            'banner_web_url' => $request->bannerWebURL,
//            'verified' => ($request->verified) ?: 0,
            'verified' => 1,
            'noofvancancy' => $request->noofvancancy,
//            'photo_job_id' => $photo_id,
        ];
        if ($request->jobtype == 1) {

            $candidateLocation_string = $request->candidateLocation ? $this->implode_treeSelect($request->candidateLocation) : '';
            $joblocation_string = $request->joblocation ? $this->implode_treeSelect($request->joblocation) : '';
//            return "000";
            $private_form_data = [
                'job_plan_id' => $request->job_plan_id,
//                'district' => $request->district,
                'location' => $candidateLocation_string,
                'joblocation' => $joblocation_string,
//                'joblocation' => explode(',', $joblocation_string)[0],
                'address' => $request->address,
                'contact_person_detail' => $request->contactperson,
                'contact_detail' => $request->contactDetails,
                'phone' => explode(',', $request->mobileSeperate)[0],
                'phone_new' => $request->mobileSeperate,
                'email' => explode(',', $request->email)[0],
                'email_new' => $request->email,
                'skills' => implode(',', $request->skills),
                'from_exp' => $request->experienceFrom,
                'to_exp' => $request->experienceTo,
                'experience_remarks' => $request->experienceRemarks,
                'agelimit_start' => $request->ageLimitFrom,
                'agelimit' => $request->ageLimitTo,
                'from_salary' => $request->salaryFrom,
                'to_salary' => $request->salaryTo,
                'gender' => $request->gender,
                'marital_status' => $request->marital_status,
                'employer' => $request->employer,
                'paid_post' => $request->paid_post,
                'paid_date' => $request->paid_date,
                'paid_amt' => $request->paid_amt,
                'disable_call_button' => !$request->responseCall,
                'enable_detail_apply' => $request->responseMail,
                'enable_resume_apply' => $request->resumeMail,
                'map_url' => $request->mapURL,
                'salary_remarks' => $request->salaryRemarks,
                'candidates_apply_location' => $candidateLocation_string,
                'call_from_time' => $request->callTimeFrom,
                'call_to_time' => $request->callTimeTo,
                'website' => $request->website,
                'job_location_remark' => $request->joblocationRemarks,
                'Interviewdate' => $request->interviewDate,
                'PostSendNotifi' => $request->sendNotification,
                'singleGroupPost' => $request->singleGroupPost,
                'tags' => $request->tags,
                'show_remark' => $request->show_remark ? $request->show_remark : 0,
//                'request' => "222",
            ];
            if ($request->whatsappNumber) {
                $form_data['whatsapp_number'] = $request->whatsappNumber;
                $form_data['enable_whatsapp_apply'] = 1;
            }
            $form_data = array_merge($form_data, $private_form_data);
//            return $form_data;
//            return $request;
//            return ['request' => '', 'form_data' => $form_data];
        } else {
            $gov_form_data = [
                'appName-salary' => $request->salary,
                'selection-process' => $request->selectionProcess,
                'fees-details' => $request->feesDetails,
                'examcentre' => $request->examCenter,
                'how-to-apply' => $request->howToApply,
                'postal-address' => $request->postalAddress,
                'examdate' => $request->examDate,
                'website' => $request->website1,
                'website2' => $request->website2,
                'post_live_purpose' => 1,
            ];
            $form_data = array_merge($form_data, $gov_form_data);
        }
        if ($request->table == 'jobs' && $request->id) {
            $form_data['upby'] = $request->inby;
            $form_data['upip'] = (new helpers)->getIp();
            $form_data['update'] = Carbon::now();
            $form_data['id'] = $request->id;
            DB::table($request->table)
                ->where('id', '=', $request->id)
                ->update($form_data);
        } elseif ($request->table == 'draft_jobs' && $request->draft_id) {
            $form_data['from_admin'] = 1;
            $form_data['upby'] = $request->inby;
            $form_data['upip'] = (new helpers)->getIp();
            $form_data['update'] = Carbon::now();
            $form_data['draft_date'] = $today;
            $form_data['id'] = $request->draft_id;
            DB::table($request->table)
                ->where('id', '=', $request->draft_id)
                ->update($form_data);
        } else {
            if ($request->table == 'draft_jobs') {
                $form_data['from_admin'] = 1;
            }
            $form_data['jobspro'] = 1;
            $form_data['inby'] = $request->inby;
            $form_data['inip'] = (new helpers)->getIp();
            $form_data['indate'] = Carbon::now();
            $form_data['posteddate'] = Carbon::now();
            $form_data['expiredate'] = Carbon::now();
            DB::table($request->table)->insertOrIgnore([
                $form_data
            ]);
            if ($request->table == 'jobs' && $request->draft_id) {
                DB::table('draft_jobs')->where('id', '=', $request->draft_id)->delete();
            }
        }
//        return $form_data;
//        dd(\DB::getQueryLog());

    }

    public function deleteJobs(Request $request)
    {
        \DB::enableQueryLog();

        DB::table($request->table)
            ->where('id', $request->id)
            ->update([
                'deleted' => 1,
            ]);
//        dd(\DB::getQueryLog());

    }

    public function inactiveJobs(Request $request)
    {
        \DB::enableQueryLog();

        DB::table('jobs')
            ->where('id', $request->id)
            ->update([
                'inactive_flag' => 1,
                'active_master_reason' => $request->inactiveReason,
            ]);
//        dd(\DB::getQueryLog());

    }

    public function activeJobs(Request $request)
    {
        \DB::enableQueryLog();

        DB::table('jobs')
            ->where('id', $request->id)
            ->update([
                'inactive_flag' => 0,
                'active_master_reason' => 0,
            ]);
//        dd(\DB::getQueryLog());

    }

    public function getSinglejobs(Request $request)
    {
        DB::statement('SET SESSION group_concat_max_len = 1000000');
        $type = $request->jobtype;
        $table = 'jobs';
        if ($request->table) {
            $table = $request->table;
        }
        if ($type == 'government') {
            $data = DB::connection('read')->table($table . ' as j')
                ->leftJoin('employer as e', 'e.id', '=', 'j.employerid')
                ->leftJoin('employer_registration as er', 'e.employer', '=', 'er.id')
                ->select('j.phone_new', 'j.paid_post', 'j.phone', 'j.id as job_id', 'j.jobtitle', 'e.image as logo_image', 'e.id as emp_id', 'e.company-name as name', 'er.firstname as name', 'er.status', 'j.jobtype', 'j.call_count', 'j.marital_status', 'j.workmode', 'j.address', 'j.from_exp', 'j.to_exp', 'j.from_salary', 'j.agelimit', 'j.fees-details as feesDetails', 'j.description', 'j.to_salary', 'j.noofvancancy', 'j.workmode', 'j.website', 'j.website2', 'j.keyword', 'j.jobsource', 'j.starting', 'j.ending', 'j.starting as start_check', 'j.ending as end_check', 'j.examdate', 'j.appName-salary as govtSalary', 'j.fees-details as fees', 'j.how-to-apply as howToApply', 'j.postal-address as postaladdr', 'j.selection-process as selection', 'j.views as viewcount', 'j.indate as postedDate', 'j.inip as postedIp', 'j.inby', 'j.upby', 'j.referredby', 'j.mul_category', 'j.singlequalification', 'j.skills', 'j.groupqualification',
                    'j.location', 'j.joblocation', 'j.candidates_apply_location', 'j.district', 'j.marital_status', DB::raw('case
        when j.deleted = 1
        then "Deleted"
        when j.blocked = 1
        then "Blocked"
        when j.ending > current_date()
        then "Live"
        when j.ending = current_date()
        then "Live / Expires Today"
        else "Expiry"
        end as jobstatus'),

                )
                ->where([
                    ['j.id', $request->id]
                ])->get();

        } elseif ($type == 'private') {
            $data = DB::connection('read')->table($table . ' as j')
                ->leftJoin('employer as e', 'e.id', '=', 'j.employerid')
                ->leftJoin('employer_registration as er', 'e.employer', '=', 'er.id')
                ->leftJoin("franchise_company_debits as fcd", function ($join) {
                    $join->on("j.job_plan_id", "=", "fcd.debit_id")
                        ->on("fcd.status", "=", DB::raw("'TXN_SUCCESS'"));
                })
                ->select('j.phone_new', 'fcd.txn_amt', 'j.id as job_id', 'fcd.plan_name', 'fcd.starting_date', 'fcd.post_count', 'fcd.expired_date', 'fcd.txn_date', 'j.call_confirm_count', 'j.email_new', 'j.email', 'j.call as call_count', 'j.paid_post', 'j.phone', 'j.id as id', 'j.jobtitle', 'e.image as logo_image', 'e.id as emp_id', 'e.company-name as cname', 'er.firstname as name', 'er.status', 'j.jobtype', 'j.call_count', 'j.marital_status', 'j.from_exp', 'j.to_exp', 'j.from_salary', 'j.agelimit', 'j.fees-details as feesDetails', 'j.description', 'j.to_salary', 'j.noofvancancy', 'j.workmode', 'j.website', 'j.website2', 'j.keyword', 'j.jobsource', 'j.starting', 'j.ending', 'j.starting as start_check', 'j.ending as end_check', 'j.examdate', 'j.appName-salary as govtSalary', 'j.fees-details as fees', 'j.how-to-apply as howToApply', 'j.address as postaladdr', 'j.selection-process as selection', 'j.views as viewcount', 'j.indate as postedDate', 'j.contact_person_detail', 'j.gender', 'j.whatsapp_number', 'j.map_url', 'j.salary_remarks', 'j.call_from_time', 'j.call_to_time', 'j.update as job_edit_date', 'j.how-to-apply as how_to_apply', 'j.postal-address as gover_addresspost', 'j.contact_detail', 'j.tags', 'j.inip as postedIp', 'j.inby', 'j.upby', 'j.referredby', 'j.mul_category', 'j.singlequalification', 'j.skills', 'j.groupqualification',
                    'j.location', 'j.joblocation', 'j.candidates_apply_location', 'j.district', 'j.marital_status', DB::raw('case
        when j.deleted = 1
        then "Deleted"
        when j.blocked = 1
        then "Blocked"
        when j.ending > current_date()
        then "Live"
        when j.ending = current_date()
        then "Live / Expires Today"
        else "Expiry"
        end as jobstatus')

                )
                ->where([
                    ['j.id', $request->id]
                ])->get();
        }
        return $data;
    }

    public function viewPrivateJobs(Request $request)
    {
//        return $request;
        \DB::enableQueryLog();
        $table = $request->table;
        $page = $request->page;
//        return $page;
        $where = $whereb = array();
        $whereina = $whereinb = ' 1 ';

        $where[] = ['j.blocked', 0];
        if ($table == 'draft_jobs') {
            $where[] = ['j.deleted', 0];
        }
        if ($request->jobtype) {
            $whereina = "j.jobtype IN (" . implode(',', $request->jobtype) . ")";
        }

        if ($request->from_admin) {
            if ($request->from_admin == 2) {
                $request->from_admin = 0;
            }
            $whereina .= " AND j.from_admin = " . $request->from_admin;
        }

        $where_status = '1';
        switch ($request->jobstatus) {
            case 'live':
                $where_status = 'date(j.ending) >=date(now())';
                $where[] = ['j.deleted', 0];
                break;
            case 'expired':
                $where_status = 'date(j.ending) < date(now())';
                $where[] = ['j.deleted', 0];
                break;
            case 'deleted':
                $where_status = 'j.deleted = 1';
                break;
            case 'all':
                $where[] = ['j.deleted', 0];
                break;
        }

        if ($request->verify == 1) {
            $where[] = ['j.post_live_purpose', 1];
        } elseif ($request->verify == 2) {
            $where[] = ['j.post_live_purpose', 0];
        }

        if ($request->jobtitle_id) {
            $jobtitle_id_string = implode(',', $request->jobtitle_id);
            $whereb[] = " `j`.`jobtitle_id` IN ($jobtitle_id_string) ";
        }
        if ($request->joblocation) {
            $joblocation_string = $this->implode_treeSelect($request->joblocation);
            $whereb[] = " `j`.`location` IN ($joblocation_string) ";
//            return $joblocation_string;
        }
        if ($request->candidateLocation) {
            $candidateLocation_string = $this->implode_treeSelect($request->candidateLocation);
            $whereb[] = " `j`.`candidates_apply_location` IN ($candidateLocation_string) ";
        }
        if ($request->postedDate['startDate']) {
            $expiry_start_date = date("Y-m-d", strtotime($request->postedDate['startDate']));
            $expiry_end_date = date("Y-m-d", strtotime($request->postedDate['endDate']));
            $whereb[] = " Date(`j`.`indate`) BETWEEN '$expiry_start_date' AND '$expiry_end_date'";
        }
        if ($request->expiryDate['startDate']) {
            $expiry_start_date = date("Y-m-d", strtotime($request->expiryDate['startDate']));
            $expiry_end_date = date("Y-m-d", strtotime($request->expiryDate['endDate']));
            $whereb[] = " `j`.`ending` BETWEEN '$expiry_start_date' AND '$expiry_end_date'";
        }
        if (count($whereb)) {
            $whereinb = implode(' AND ', $whereb);
        }
//        return $whereb;

        $data = DB::connection('read')->table($table . ' as j')
            ->leftJoin('employer as e', 'j.employerid', '=', 'e.id')
            ->leftJoin('employer_registration as er', 'er.company_details', '=', 'e.id')
            ->leftJoin('executive_employer as ee', 'e.id', '=', 'ee.employer_id')
            ->leftJoin("franchise_company_debits as fcd", function ($join) {
                $join->on("j.job_plan_id", "=", "fcd.debit_id")
                    ->on("fcd.status", "=", DB::raw("'TXN_SUCCESS'"));
            })
            ->select('j.id',
                'j.jobtype',
                'j.jobspro',
                'ee.user_id as excutive',
                'e.employer',
                'e.company_proof_type',
                'e.id_proof_type',
                'er.person_proof_type',
                'e.is_com_add_proof',
                'e.is_com_id_proof',
                'er.is_person_proof',
                'e.image as logo',
                'j.gender',
                'j.paid_post',
                'j.paid_date',

                'fcd.txn_amt',
                'company-name as company',
                'j.jobtitle',
                'j.phone',
                'j.phone_new',
                'j.noofvancancy',
                'j.starting',
                'j.ending',
                'j.inby',
                'j.upby',
                'j.indate as indate_job',
                'j.update as update_job',
                'j.inactive_flag',
                DB::raw('case
        when j.deleted = 1
        then "Deleted"
        when j.blocked = 1
        then "Blocked"
        when j.ending > current_date()
        then "Live"
        when j.ending = current_date()
        then "Live / Expires Today"
        else "Expiry"
        end as jobpoststatus, j.show_remark'),
            )
            ->whereRaw($where_status)
            ->where($where)
            ->whereRaw($whereina)
            ->whereRaw($whereinb)
            ->orderBy('id', 'DESC')->paginate(3000);
//        return dd(\DB::getQueryLog());
        return $data;
    }

    public function privateViewCount(Request $request)
    {
//        return $request;
        \DB::enableQueryLog();

        $where[] = ['j.id', $request->id];

        $data = DB::connection('read')->table('jobs' . ' as j')
            ->Join('job_apply as ja', 'j.id', '=', 'ja.job_id')
            ->select('j.id',
                DB::raw('sum(ja.view) AS view_count'),
                DB::raw('sum(ja.call) AS call_count'),
                DB::raw('count(ja.view) AS view_count_unique'),
                DB::raw('count(ja.call) AS call_count_unique'),
                DB::raw('COUNT(CASE WHEN ja.apply_mode = "detail" THEN ja.id END) as detail_count'),
                DB::raw('COUNT(CASE WHEN ja.apply_mode = "resume" THEN ja.id END) as resume_count')
            )
            ->where($where)
            ->groupByRaw('j.id')
            ->orderBy('id', 'DESC')->get();
//        dd(\DB::getQueryLog());
        return $data;
    }

    public function searchJobs(Request $request)
    {
        $where = $whereb = array();
        $whereina = $whereinb = ' 1 ';

        $where[] = ['j.blocked', 0];


        if ($request->type == 1) {
            $whereina = "j.blocked=0 AND j.id='$request->search_text'";

        } elseif ($request->type == 2) {
            $whereina = "j.deleted = 0 AND (j.phone LIKE '%$request->search_text%' OR j.phone_new LIKE '%$request->search_text%' )";
        } elseif ($request->type == 3) {
            $whereina = "j.deleted = 0 AND (j.address LIKE '%$request->search_text%')";
        } elseif ($request->type == 4) {
            $whereina = "j.deleted = 0 AND (j.email LIKE '%$request->search_text%' OR j.email_new LIKE '%$request->search_text%' )";
        } elseif ($request->type == 5) {
            $whereina = "j.deleted = 0 AND (e.`company-name` LIKE '%$request->search_text%')";
        } else {
            $whereina = "CONCAT_WS('', j.jobtitle, j.phone_new, j.address, j.email_new, `company-name`, j.id, j.description) LIKE '%$request->search_text%'";
        }

        if ($request->jobtitle_id) {
            $jobtitle_id_string = implode(',', $request->jobtitle_id);
            $whereb[] = " `j`.`jobtitle_id` IN ($jobtitle_id_string) ";
        }
        if ($request->joblocation) {
            $joblocation_string = $this->implode_treeSelect($request->joblocation);
            $whereb[] = " `j`.`location` IN ($joblocation_string) ";
//            return $joblocation_string;
        }
        if ($request->candidateLocation) {
            $candidateLocation_string = $this->implode_treeSelect($request->candidateLocation);
            $whereb[] = " `j`.`candidates_apply_location` IN ($candidateLocation_string) ";
        }
//        if ($request->postedDate['startDate']) {
//            $expiry_start_date = date("Y-m-d",strtotime($request->postedDate['startDate']));
//            $expiry_end_date = date("Y-m-d",strtotime($request->postedDate['endDate']));
//            $whereb[] = " Date(`j`.`indate`) BETWEEN '$expiry_start_date' AND '$expiry_end_date'";
//        }
//        if ($request->expiryDate['startDate']) {
//            $expiry_start_date = date("Y-m-d",strtotime($request->expiryDate['startDate']));
//            $expiry_end_date = date("Y-m-d",strtotime($request->expiryDate['endDate']));
//            $whereb[] = " `j`.`ending` BETWEEN '$expiry_start_date' AND '$expiry_end_date'";
//        }
        if (count($whereb)) {
            $whereinb = implode(' AND ', $whereb);
        }
//        return $whereb;
        $data = DB::connection('read')->table('jobs as j')
            ->leftJoin('employer as e', 'j.employerid', '=', 'e.id')
            ->leftJoin('employer_registration as er', 'er.company_details', '=', 'e.id')
            ->leftJoin('jobsource as source', 'j.jobsource', '=', 'source.id')
            ->leftJoin('user as u', 'u.id', '=', 'j.inby')
            ->leftJoin('user as up', 'up.id', '=', 'j.upby')
            ->leftJoin('executive_employer as ee', 'e.id', '=', 'ee.employer_id')
            ->leftJoin('user as eu', 'ee.user_id', '=', 'eu.id')
            ->leftJoin("franchise_company_debits as fcd", function ($join) {
                $join->on("j.job_plan_id", "=", "fcd.debit_id")
                    ->on("fcd.status", "=", DB::raw("'TXN_SUCCESS'"));
            })
            ->select('j.id',
                'j.jobtype',
                'eu.name as excutive',
                'e.employer',
                'j.address',
                'e.company_proof_type',
                'e.id_proof_type',
                'er.person_proof_type',
                'e.is_com_add_proof',
                'e.is_com_id_proof',
                'er.is_person_proof',
                'j.paid_post',
                'u.name as iname',
                DB::raw('DATE_FORMAT(j.paid_date, "%d-%m-%Y") as paid_date'),
                'fcd.txn_amt',
                'company-name as company',
                'j.jobtitle',
                'j.phone',
                'j.phone_new',
                'j.noofvancancy',
                'j.starting',
                'j.ending',
                'j.inby',
                'up.name as upby',
                'j.indate as indate_job',
                'j.update as update_job',
                'j.inactive_flag',
                'source.jobsource as jsource',
                DB::raw('case
        when j.paid_post = 1
        then "Paid"
         when fcd.txn_amt > 0
        then "Free"
        else "Not_Paid"
        end as payment_status'),
                DB::raw('if(j.gender=0,"Male","Female") as gender'),
                DB::raw('case
        when j.deleted = 1
        then "Deleted"
        when j.blocked = 1
        then "Blocked"
        when j.ending > current_date()
        then "Live"
        when j.ending = current_date()
        then "Live / Expires Today"
        else "Expiry"
        end as jobpoststatus'),
            )
            ->where($where)
            ->whereRaw($whereina)
            ->whereRaw($whereinb)
            ->orderBy('j.id', 'DESC')->paginate(3000);
//        ddd(\DB::getQueryLog());
        return $data;
    }

}
