<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\EmployerRegistration;
use App\Models\Employer;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployerRegistrationController extends Controller
{
    public function checkPrivateEmployer(Request $request)
    {
//        return EmployerRegistration::where([['phonenumber', $request]])->get();


        return EmployerRegistration::Join('employer', 'employer_registration.company_details', '=', 'employer.id')
            ->select('employer.id', 'employer_registration.phonenumber', 'employer.company-name as companyname')
            ->where([['employer_registration.phonenumber', $request->value]])->get(1);
    }

    public function checkCompanyName(Request $request)
    {
//        return EmployerRegistration::where([['phonenumber', $request]])->get();
//        DB::enableQueryLog();
        if ($request->empid) {

            $duplicate = DB::table('employer')
                ->select('id', 'company-name as companyname')
                ->where('id', '!=', $request->empid)
                ->where('company-name', '=', $request->value)
                ->get();

            $duplicate_match = Employer::select('id', 'company-name as name')
                ->where([
                    ['type', 0],
                    ['deleted', 0]
                ])
                ->whereRaw(" `company-name` like '%" . $request->value . "%'")
                ->where('id', '!=', $request->empid)
                ->orderByRaw("INSTR(`company-name`,'" . $request->value . "') asc")
                ->orderBy('id', 'DESC')->get();
        } else {
            $duplicate = DB::table('employer')
                ->select('id', 'company-name as companyname')
                ->where('company-name', '=', $request->value)
                ->get();

            $duplicate_match = Employer::Join('dist as d', 'd.dist_id', '=', 'employer.district')
                ->select('employer.id', 'employer.company-name as name', 'd.dist')
                ->where([
                    ['deleted', 0]
                ])
                ->whereRaw(" `company-name` like '%" . $request->value . "%'")
                ->orderByRaw("INSTR(`company-name`,'" . $request->value . "') asc")
                ->orderBy('id', 'DESC')->get();
        }


        $result['duplicateName'] = $duplicate;
        $result['duplicate_match'] = $duplicate_match;
//        dd(DB::getQueryLog());
        return $result;

    }
}
