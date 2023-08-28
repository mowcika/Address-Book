<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ExecutiveFollowup;

class EditRequestMasterCtrl extends Controller
{
    public function saveEditRequest(Request $request)
    {
        $person_proof = $companyProof = $id_proof = '';
        $cloudFrontUrl = "https://d2hy6ree306xec.cloudfront.net/";
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

//            return $request->file();

        }

        $this->validate($request, [
            'id' => 'required',
        ]);

        $add_remark = array(
            'employer_id' => $request->id,
            'remark' => $request->remark,
            'cdate' => Carbon::now(),
            'inby' => $request->inby,
            'status' => '1',
        );

        if ($companyProof) {
            $add_remark['companyProof'] = $companyProof;
        }

        if ($id_proof) {
            $add_remark['id_proof'] = $id_proof;
        }

        if ($person_proof) {
            $add_remark['person_proof'] = $person_proof;
        }

        $employer_table = DB::table('edit_request')->insertOrIgnore([
            $add_remark
        ]);


        $save_type = 'Edit Request Send Successfully';
        return array(
            'msg' => $save_type,
            'type' => $save_type,
            'employer_ID' => $request->id,
        );
    }

    public function acceptEditRequest(Request $request)
    {
        DB::table('edit_request')
            ->where('id', $request->id)
            ->update([
                'status' => 2,
            ]);
        $save_type = 'Edit Request Send Successfully';
        return $this->getEditRequest();
    }

    public function getEditRequest()
    {

        return DB::table('employer as e')
            ->join('employer_registration as er', 'er.company_details', '=', 'e.id')
            ->Join('edit_request as ee', 'ee.employer_id', '=', 'e.id')
            ->select('e.id', 'ee.id as edit_request_id', 'er.phonenumber', 'e.company-name as companyname', 'ee.remark',
                'ee.person_proof', 'ee.companyProof', 'ee.id_proof')
            ->where([['ee.status', 1]])
            ->orderBy('ee.id', 'ASC')->get();
    }

    public function rejectEditRequest(Request $request)
    {
        DB::table('edit_request')
            ->where('id', $request->id)
            ->update([
                'status' => 3,
            ]);
        return $this->getEditRequest();
    }

    public function getFollowupStatus()
    {

        $output['expiry_followup_status'] = $this->followupsource('expiry_followup_status', ['id as value', 'source_name as text']);
        $output['employer_feedback'] = $this->followupsource('employer_feedback', ['id as value', 'source_name as text']);
        $output['followup_source'] = $this->followupsource('followup_source', ['id as value', 'source_name as text']);
        $output['employer_mindset'] = $this->followupsource('employer_mindset', ['id as value', 'source_name as text']);
        $output['adclosing'] = $this->followupsource('master_adclosing', ['id as value', 'adclosing as text']);
        $output['adextend'] = $this->followupsource('master_adextend', ['id as value', 'adextend as text']);
        return $output;
    }

    function followupsource($table_name, $columns)
    {
        return DB::table($table_name)
            ->select($columns)
            ->get();
    }

    public function saveFollowup(Request $request)
    {
        $this->validate($request, [
            'inby' => 'required',
            'employer_id' => 'required',
            'executive_id' => 'required',
            'feedback' => 'required',
            'followup_status' => 'required',
            'mindset' => 'required',
            'target_source' => 'required',
        ]);

        $add_followup = array(
            'employer_id' => $request->employer_id,
            'executive_id' => $request->executive_id,
            'followup_status' => $request->followup_status,
            'feedback' => $request->feedback,
            'followup_date' => $request->followup_date,
            'target_source' => $request->target_source,
            'extend_reason' => $request->adextend,
            'mindset' => $request->mindset,
            'close_reason' => $request->adclosing,
            'remarks' => $request->remark,
            'indate' => Carbon::now(),
            'inby' => $request->inby,
        );
        $update_executive = array(
            'last_followup_status' => $request->followup_status,
            'last_feedback' => $request->feedback,
            'last_target_source' => $request->target_source,
            'last_mindset' => $request->mindset,
        );
        $followup_table = DB::table('executive_followup')->insertOrIgnore([
            $add_followup
        ]);

        $executive_table = DB::table('executive_employer')->where('id', $request->executive_id)->update($update_executive);

//        return $request;

        $save_type = 'Followup Successfully';
        return array(
            'msg' => $save_type,
            'type' => $save_type,
            'employer_ID' => $request->id,
        );
    }

    public function getFollowupHistory(Request $request)
    {
        $empID = $request->empId;
//        $empID=2266;
        $data = DB::table('executive_followup as e')
            ->leftJoin('expiry_followup_status as efs', 'e.followup_status', '=', 'efs.id')
            ->leftJoin('employer_feedback as ef', 'e.feedback', '=', 'ef.id')
            ->leftJoin('followup_source as fs', 'e.target_source', '=', 'fs.id')
            ->leftJoin('employer_mindset as em', 'e.mindset', '=', 'em.id')
            ->leftJoin('master_adclosing as ma', 'e.close_reason', '=', 'ma.id')
            ->leftJoin('master_adextend as mx', 'e.extend_reason', '=', 'mx.id')
            ->select('e.employer_id', 'efs.source_name as expiryfollowupstatus', 'ef.source_name as employerfeedback', 'fs.source_name as followupsource', 'em.source_name as employermindset', 'ma.adclosing as masteradclosing', 'mx.adextend as masteradextend')
            ->where([['e.employer_id', $empID]])
            ->orderBy('e.id', 'ASC')->get();
        return $data;

    }
}
