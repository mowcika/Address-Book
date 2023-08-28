<?php

namespace App\Http\Controllers;

use App\Models\EmployerRegistration;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use App\Models\Employer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\UploadedFile;

use Symfony\Component\Console\Helper\Table;

class EmployerController extends Controller
{

    public function getSingleGovtEmployer(Request $request)
    {
        return Employer::where([['type', 0], ['deleted', 0], ['id', $request->id]])->orderBy('id', 'DESC')->get();
    }

    public function saveGovtEmployer(Request $request)
    {
//        \DB::enableQueryLog();
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();
        if ($request->id) {
            $this->validate($request, [
                'companyname' => [
                    'required',
                    Rule::unique('employer', 'company-name')->where(function ($query) use ($request) {
                        return $query->where([['deleted', '0'], ['type', '0'], ['id', '!=', $request->id]]);
                    })
                ]
            ]);

            Employer::where('id', $request->id)->update([
                'id' => $request->id,
                'company-name' => $request->companyname,
                'image' => $request->image,
                'type' => 0,
            ]);

        } else {
            $this->validate($request, [
                'companyname' => [
                    'required',
                    Rule::unique('employer', 'company-name')->where(function ($query) {
                        return $query->where([['deleted', '0'], ['type', '0']]);
                    })
                ]
            ]);

            DB::table('employer')->insertOrIgnore([
                'company-name' => $request->companyname,
                'image' => $request->image,
                'type' => 0,
            ]);
        }
        return $this->getGovtEmployer();
//        ddd(\DB::getQueryLog());
    }

    public function getGovtEmployer()
    {
        return Employer::where([['type', 0], ['deleted', 0]])->orderBy('id', 'DESC')->get();
    }

    public function deleteGovtEmployer(Request $request)
    {
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();
        if ($request->id) {

            Employer::where('id', $request->id)->update([
                'deleted' => 1,
            ]);

        }
        return $this->getGovtEmployer();
    }


    public function searchGovtEmployer(Request $request)
    {
//        \DB::enableQueryLog();
//        return $request;
        return DB::connection('read')->table('employer')->select('id', 'company-name as name')
            ->where([
                ['type', 0],
                ['deleted', 0]
            ])
            ->whereRaw(" `company-name` like '%" . $request->search_term . "%'")
            ->orderByRaw("INSTR(`company-name`,'" . $request->search_term . "') asc")
            ->orderBy('id', 'DESC')->get();
//        ddd(\DB::getQueryLog());
    }

    public function searchPrivateEmployer(Request $request)
    {
//        \DB::enableQueryLog();
//        return $request;
        $data = DB::connection('read')->table('employer as e')
            ->leftJoin('employer_registration as er', 'e.employer', '=', 'er.id')
//            ->select('e.id', 'company-name as name', 'e.is_block', 'er.status')
            ->selectRaw('e.id, `company-name` as name, e.is_block, er.status,e.employer as eEmployer, er.status as erStatus, CASE
            when e.is_block = 1 then concat(`company-name`," - (Blocked)")
            when (e.employer != 0 and e.is_block = 0) then concat(`company-name`," - (Employer Verified)")
            else `company-name`
            end as comp')
            ->where([
                ['type', 1],
                ['deleted', 0]
            ])
            ->whereRaw(" `company-name` like '%" . $request->search_term . "%'")
            ->orderByRaw("INSTR(`company-name`,'" . $request->search_term . "') asc")
            ->orderBy('id', 'DESC')->get();
        return $data;
//        ddd(\DB::getQueryLog());
    }


    public function savePrivateEmployer(Request $request)
    {

        $cloudFrontUrl = "https://d2hy6ree306xec.cloudfront.net/";
        $person_proof = $companyProof = $id_proof = $company_logo = '';
        if ($request->file()) {
            if ($request->file('person_proof')) {
                $file = $request->file('person_proof');
                $person_proof_upload = $request->file('person_proof')->store(
                    '/employer_files/person_proof',
                    's3'
                );


                $person_proof = $cloudFrontUrl . $person_proof_upload;
            }
            if ($request->file('companyProof')) {
                $file = $request->file('companyProof');
                $companyProoffile = $request->file('companyProof')->store(
                    '/employer_files/company_proof',
                    's3'
                );


                $companyProof = $cloudFrontUrl . $companyProoffile;
            }
            if ($request->file('id_proof')) {
                $file = $request->file('id_proof');
                $id_prooffile = $request->file('id_proof')->store(
                    '/employer_files/id_proof',
                    's3'
                );


                $id_proof = $cloudFrontUrl . $id_prooffile;
            }
            if ($request->file('image')) {
                $file = $request->file('image');
                $company_logo = $request->file('image')->store(
                    '/employer_files/company_logo',
                    's3'
                );


                $company_logo = $cloudFrontUrl . $company_logo;
            }
//            return $request->file();

        }

        if ($request->id) {
            $this->validate($request, [
                'phonenumber' => 'required',
                'firstname' => 'required',
                'companyname' => 'required',
                'company_type' => 'required',
                'address' => 'required',
                'pincode' => 'required',
                'state' => 'required',
                'district' => 'required',
                'city' => 'required',
                'cityText' => 'required',
            ]);
        } else {
            $this->validate($request, [
                'phonenumber' => 'required|unique:employer_registration,phonenumber',
                'firstname' => 'required',
//                'person_proof_type' => 'required',
                'person_proof_text' => 'unique:employer_registration,person_proof_text',
                'pan_card' => 'unique:employer,pan_card',
                'companyname' => 'required|unique:employer,company-name',
//                'tcompanyname' => 'unique:employer,tcompany_name',
                'company_type' => 'required',
                'address' => 'required',
                'pincode' => 'required',
                'gst' => 'unique:employer,gst',
                'industry_type' => 'required',
//            'companyNature' => 'required',
                'state' => 'required',
                'district' => 'required',
                'city' => 'required',
                'cityText' => 'required',
            ]);
        }


        $address = $request->address . '#@#' . $request->city . '#@#' . $request->cityText . '#@#' . $request->pincode;
        $gst = $request->gst ? $request->gst : '';
        $industry_type = $request->industry_type ? $request->industry_type : '';
        $company_type = $request->company_type ? $request->company_type : '';
        $pan_card = $request->pan_card ? $request->pan_card : '';
        $company_proof_type = $request->addressProof ? $request->addressProof : '';
        $id_proof_type = $request->company_proof_type ? $request->company_proof_type : '';
        $person_proof_type = $request->person_proof_type ? $request->person_proof_type : '';
        $person_proof_text = $request->person_proof_text ? $request->person_proof_text : '';

        if ($request->id) {
            $add_employer = array(
                'company-name' => $request->companyname,
                'tcompany_name' => $request->tcompanyname,
                'landmark' => $request->landmark,
                'whatsapp' => $request->whatsapp,
                'address' => $address,
                'type' => 1,
                'state' => $request->state,
                'district' => $request->district,
                'city' => $request->city,
                'industry_type' => $industry_type,
                'company_type' => $company_type,
                'pan_card' => $pan_card,
                'company_proof_type' => $company_proof_type,
                'id_proof_type' => $id_proof_type,
                'gst' => $gst,
                'upby' => $request->inby,
                'edit_laravel' => 1,
                'update' => Carbon::now(),

            );
            $add_employer_reg = array(
                'firstname' => $request->firstname,
                'phonenumber' => $request->phonenumber,
                'email' => $request->email,
                'website' => $request->website,
                'designation' => $request->designation,
                'iscomplete' => 1,
                'person_proof_type' => $person_proof_type,
                'person_proof_text' => $person_proof_text,
                'gst' => $gst,
                'upby' => $request->inby,
//            'companyNature' => $request->companyNature,
                'update' => Carbon::now(),
            );
        } else {
            $add_employer = array(
                'company-name' => $request->companyname,
                'tcompany_name' => $request->tcompanyname,
                'landmark' => $request->landmark,
                'address' => $address,
                'gst' => $gst,
                'type' => 1,
                'state' => $request->state,
                'district' => $request->district,
                'city' => $request->city,
                'industry_type' => $industry_type,
                'company_type' => $company_type,
                'pan_card' => $pan_card,
                'company_proof_type' => $company_proof_type,
                'id_proof_type' => $id_proof_type,
//            'companyNature' => $request->companyNature,
                'indate' => Carbon::now(),
                'inby' => $request->inby,
                'is_laravel' => 1,

            );
            $add_employer_reg = array(
                'firstname' => $request->firstname,
                'phonenumber' => $request->phonenumber,
                'email' => $request->email,
                'gst' => $gst,
                'website' => $request->website,
                'designation' => $request->designation,
                'iscomplete' => 1,
                'person_proof_type' => $person_proof_type,
                'person_proof_text' => $person_proof_text,
//            'companyNature' => $request->companyNature,
                'indate' => Carbon::now(),
                'inby' => $request->inby,
            );
        }

        if ($companyProof) {
            $add_employer['company_proof'] = $companyProof;
        }
        if ($id_proof) {
            $add_employer['id_proof'] = $id_proof;
        }
        if ($person_proof) {
            $add_employer_reg['person_proof'] = $person_proof;
        }
        if ($company_logo) {
            $add_employer['image'] = $company_logo;
        }

//print_r($add_employer);
//print_r($add_employer_reg);exit;

        if ($request->id) {


            Employer::where('id', $request->id)->update($add_employer);


            EmployerRegistration::where('company_details', $request->id)->update($add_employer_reg);

            $msg = $request->companyname . ' Company Updated Successfully';
            $save_type = 'Employer Updated Successfully';
            $reslut = array(
                'msg' => $msg,
                'type' => $save_type,
                'employer_ID' => $request->id,
                'empReg_ID' => $request->id,
                'companyname' => $request->companyname,
            );


        } else {

            $employer_table = DB::table('employer')->insertOrIgnore([
                $add_employer
            ]);


            $employer_ID = DB::getPdo()->lastInsertId();
            $addExecutiveData = array(
                'employer_id' => $employer_ID,
                'inby' => 1,
                'indate' => Carbon::now(),
                'inip' => '',
                'status' => 'Not Post'
            );


            DB::table('executive_employer')->insertOrIgnore([
                $addExecutiveData
            ]);


            if ($employer_ID) {
                $add_employer_reg['company_details'] = $employer_ID;
            }

            $employerReg_table = DB::table('employer_registration')->insertOrIgnore([
                $add_employer_reg
            ]);


            $empReg_ID = DB::getPdo()->lastInsertId();

            if ($empReg_ID) {


                Employer::where('id', $employer_ID)->update(['employer' => $empReg_ID]);

//                DB::table('employer')
//                    ->UPDATE(
//                        ['employer' => $empReg_ID],
//                    );
            }
            $msg = $request->companyname . ' Company Created Successfully';
            $save_type = 'Employer Updated Successfully';
            $reslut = array(
                'msg' => $msg,
                'type' => $save_type,
                'employer_ID' => $employer_ID,
                'empReg_ID' => $empReg_ID,
                'companyname' => $request->companyname,
            );

        }


        return $reslut;

    }


    public function getPrivateEmployer(Request $request)
    {
//        return $request;
        $where = $whereb = array();
        $whereb = ' 1 ';
        $where[] = ['employer.type', 1];
        $where[] = ['employer.deleted', 0];
        $where[] = ['employer.is_block', 0];

        if (isset($request->postedDate['startDate'])) {
            $expiry_start_date = date("Y-m-d", strtotime($request->postedDate['startDate']));
            $expiry_end_date = date("Y-m-d", strtotime($request->postedDate['endDate']));
            $whereb = " Date(`employer`.`indate`) BETWEEN '$expiry_start_date' AND '$expiry_end_date'";
        } else {
            $where[] = ['employer.indate', '>', now()->subDays(30)->endOfDay()];
        }
        if ($request->filterSearch) {
            $where = array();
            $whereb = ' 1 ';
            $whereina = "CONCAT_WS('', `employer_registration`.`phonenumber`, `employer`.`company-name`) LIKE '%$request->filterSearch%'";
        } else {
//            $where="->whereDate('employer.indate', '>', now()->subDays(30)->endOfDay())";

            $whereina = ' 1 ';
        }
        return DB::connection('read')->table('employer_registration')->Join('employer', 'employer_registration.company_details', '=', 'employer.id')
            ->leftJoin('areas', 'employer.city', '=', 'areas.id')
            ->leftJoin('company_type', 'employer.company_type', '=', 'company_type.id')
            ->leftJoin('user', 'employer.inby', '=', 'user.id')
            ->select('employer.id', 'employer_registration.phonenumber', 'employer.company-name as companyname', 'employer.tcompany_name as companynameTamil', 'employer.gst', 'employer.indate as cdate', 'areas.area_name', 'areas.district', 'areas.state', 'employer.verified', 'employer.viewProfile', 'company_type.name as companytype', DB::raw('if(employer.type=1,"Private","Govt") as emptype'), 'employer.inby', 'employer.is_com_add_proof', 'employer.is_com_id_proof', 'employer_registration.is_person_proof', 'employer_registration.isupdate', 'employer_registration.status as empstatus', 'employer.blocking_reason', 'employer.is_block', 'employer_registration.isupdate', 'employer.isdelay', 'user.uname', 'employer.image', 'employer.pincode')
            ->where([['employer.type', 1], ['employer.deleted', 0], ['employer.is_block', 0]])
            ->where($where)
            ->whereRaw($whereina)
            ->whereRaw($whereb)
            ->orderBy('id', 'DESC')->get();
    }


    public function deletePrivateEmployer(Request $request)
    {
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();
        if ($request->id) {

            Employer::where('id', $request->id)->update([
                'deleted' => 1,
            ]);

        }
//        return $this->getPrivateEmployer();
    }

    public function blockPrivateEmployer(Request $request)
    {
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();
        if ($request->id) {

            Employer::where('id', $request->id)->update([
                'is_block' => 1,
            ]);

        }

//        return $this->getPrivateEmployer();
    }

    public function unblockPrivateEmployer(Request $request)
    {
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();
        if ($request->id) {

            Employer::where('id', $request->id)->update([
                'is_block' => 0,
            ]);

        }
//        return $this->getPrivateEmployer();
    }

    public function privateEmployerHistory(Request $request)
    {
        $employerDeails = DB::connection('read')->table('employer_registration')->Join('employer as e', 'employer_registration.company_details', '=', 'e.id')
            ->leftJoin('company_type as ct', 'e.company_type', '=', 'ct.id')
            ->leftJoin('industry_type_master as induty', 'e.industry_type', '=', 'induty.id')
            ->leftJoin('proof_master as personproof', 'employer_registration.person_proof_type', '=', 'personproof.id')
            ->leftJoin('proof_master as addproof', 'e.company_proof_type', '=', 'addproof.id')
            ->leftJoin('proof_master as idproof', 'e.id_proof_type', '=', 'idproof.id')
            ->leftJoin('designation as des', 'employer_registration.designation', '=', 'des.id')
            ->leftJoin('areas as a', 'e.city', '=', 'a.id')
            ->select('e.id', 'employer_registration.phonenumber', 'e.company-name as companyname', 'e.tcompany_name as companynameTamil', 'e.gst', 'ct.name as companytype', 'employer_registration.firstname', 'employer_registration.email', 'employer_registration.designation', 'induty.industry_type', 'personproof.proof_name as personproof_type', 'addproof.proof_name as addproof_type', 'idproof.proof_name as idproof_type', 'e.address', 'e.pan_card', 'e.website', 'e.company_proof', 'e.id_proof', 'employer_registration.address_proof', 'employer_registration.person_proof', 'employer_registration.is_person_proof', 'e.is_com_id_proof', 'e.is_com_add_proof', 'des.designation as mDesignation', 'e.pincode', 'a.district', 'a.area_name')
            ->where([['e.type', 1], ['e.deleted', 0], ['e.id', $request->id]])->orderBy('e.id', 'DESC')->offset(0)->limit(1)->get();

        $jobs_details = DB::connection('read')->table('employer_registration')->Join('employer as e', 'employer_registration.company_details', '=', 'e.id')
            ->Join('jobs as j', 'e.id', '=', 'j.employerid')
            ->Join('franchise_company_debits as fcd', 'fcd.debit_id', '=', 'j.job_plan_id')
            ->select('j.id', 'j.jobtitle', 'j.ending as ending', DB::raw('if(fcd.plan_name!="", fcd.plan_name, "Free Plan") as plan_name'), DB::raw('if(j.old_job_id!="", "Repost","New") as PostStatus'), DB::raw('DATEDIFF(j.ending, j.posteddate) as days'), DB::raw('CASE
            WHEN j.ending >= CURRENT_DATE() THEN "Live"
            WHEN j.ending <= CURRENT_DATE() THEN "Expiry"
            ELSE "Not Post"
        END AS jobstatus'))
            ->where([['e.type', 1], ['e.deleted', 0], ['e.id', $request->id]])->orderBy('e.id', 'DESC')->get();

        $payment_details = DB::connection('read')->table('employer_registration')->Join('employer as e', 'employer_registration.company_details', '=', 'e.id')
            ->Join('franchise_company_debits as fcd', 'fcd.employer_id', '=', 'e.id')
            ->select('fcd.post_count', 'fcd.validity', DB::raw('DATE_FORMAT(fcd.starting_date, "%d-%m-%Y") as starting_date'), DB::raw('DATE_FORMAT(fcd.expired_date, "%d-%m-%Y") as expired_date'), DB::raw('if(fcd.plan_name!="", fcd.plan_name, "Free Plan") as plan_name'), 'fcd.actual_amount', 'fcd.txn_amt')
            ->where([['e.type', 1], ['e.deleted', 0], ['e.id', $request->id]])->groupBy('e.id')->orderBy('e.id', 'DESC')->get();

        $result['employerDeails'] = $employerDeails;
        $result['jobs_details'] = $jobs_details;
        $result['payment_details'] = $payment_details;
        return $result;


    }

    public function getProofDetailsUpdate($id)
    {
        return DB::connection('read')->table('employer_registration')->Join('employer as e', 'employer_registration.company_details', '=', 'e.id')
            ->select('employer_registration.is_person_proof', 'e.is_com_id_proof', 'e.is_com_add_proof')
            ->where([['e.type', 1], ['e.deleted', 0], ['e.id', $id]])->orderBy('e.id', 'DESC')->offset(0)->limit(1)->get();
    }

    public function proofAction(Request $request)
    {

        $type = $request->type;
        if ($type == 'personProof') {
            EmployerRegistration::where('company_details', $request->id)->update([
                'is_person_proof' => $request->value,
            ]);
        } else if ($type == 'addressProof') {
            Employer::where('id', $request->id)->update([
                'is_com_add_proof' => $request->value,
            ]);
        } else if ($type == 'idProof') {
            Employer::where('id', $request->id)->update([
                'is_com_id_proof' => $request->value,
            ]);
        }
        return $this->getProofDetailsUpdate($request->id);


    }


    public function getEditPrivatedetails(Request $request)
    {


        return DB::connection('read')->table('employer_registration')->Join('employer as e', 'employer_registration.company_details', '=', 'e.id')
            ->leftJoin('company_type as ct', 'e.company_type', '=', 'ct.id')
            ->leftJoin('industry_type_master as induty', 'e.industry_type', '=', 'induty.id')
            ->leftJoin('proof_master as personproof', 'employer_registration.person_proof_type', '=', 'personproof.id')
            ->leftJoin('proof_master as addproof', 'e.company_proof_type', '=', 'addproof.id')
            ->leftJoin('proof_master as idproof', 'e.id_proof_type', '=', 'idproof.id')
            ->select('e.id', 'employer_registration.phonenumber', 'e.landmark', 'e.whatsapp', 'e.company-name as companyname', 'e.tcompany_name as companynameTamil', 'employer_registration.gst', 'ct.name as companytype', 'e.company_type', 'employer_registration.firstname', 'employer_registration.email', 'employer_registration.designation', 'induty.industry_type', 'e.industry_type as industry_type_id', 'personproof.proof_name as personproof_type', 'employer_registration.person_proof_type as person_proof_id', 'addproof.proof_name as addproof_type', 'e.id_proof_type', 'e.company_proof_type as addproof_id', 'idproof.proof_name as idproof_type', 'e.address', 'e.pan_card', 'employer_registration.website', 'e.company_proof', 'e.id_proof', 'employer_registration.address_proof', 'employer_registration.person_proof', 'employer_registration.is_person_proof', 'e.is_com_id_proof', 'e.is_com_add_proof', 'e.state', 'e.city', 'e.district', 'employer_registration.person_proof_text', 'e.image')
            ->where([['e.type', 1], ['e.deleted', 0], ['e.id', $request->id]])->orderBy('e.id', 'DESC')->offset(0)->limit(1)->get();


    }


    public function removeProofImg(Request $request)
    {


//return $request;
        $imgType = $request->imgType;
        if ($imgType == 'companyProofImg') {

            Employer::where('id', $request->id)->update([
                'company_proof' => '',
            ]);

        } elseif ($imgType == 'id_proofImg') {

            Employer::where('id', $request->id)->update([
                'id_proof' => '',
            ]);

        } elseif ($imgType == 'person_proofImg') {
            EmployerRegistration::where('company_details', $request->id)->update([
                'person_proof' => '',
            ]);

        } elseif ($imgType == 'company_logo') {
            Employer::where('id', $request->id)->update([
                'image' => '',
            ]);

        }

        return $request;
    }

    public function verifyPrivateEmployer(Request $request)
    {
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();

        $actEmpreg = array(
            'isupdate' => 2,
            'status' => 1,
        );
        $actEmp = array(
            'isdelay' => 0,
        );
        if ($request->id) {

            Employer::where('id', $request->id)->update($actEmp);


            EmployerRegistration::where('company_details', $request->id)->update($actEmpreg);

        }

//        return $this->getPrivateEmployer();
    }

}
