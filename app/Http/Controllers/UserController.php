<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class UserController extends Controller
{

    public function index()
    {
        return User::leftJoin('usergroup', 'user.usergroup', '=', 'usergroup.id')
            ->select('user.id', 'user.phonenumber', 'user.name', 'user.uname', 'usergroup.name as usergroup')
            ->where([['is_active', 0]])->orderBy('id', 'DESC')->get();
    }


    public function getSingleUser(Request $request)
    {
        return User::where([['is_active', 0], ['id', $request->id]])->orderBy('id', 'DESC')->get();
    }

    public function saveUser(Request $request)
    {
//        \DB::enableQueryLog();
//        return $request;
//        return User::orderBy('id', 'DESC')->get();
        if ($request->id) {
            $this->validate($request, [
                'name' => [
                    'required',
                    Rule::unique('user', 'name')->where(function ($query) use ($request) {
                        return $query->where([['is_active', 0], ['id', '!=', $request->id]]);
                    })
                ],
                'uname' => [
                    'required',
                    Rule::unique('user', 'uname')->where(function ($query) use ($request) {
                        return $query->where([['is_active', 0], ['id', '!=', $request->id]]);
                    })
                ],
                'phonenumber' => [
                    'required',
                    Rule::unique('user', 'phonenumber')->where(function ($query) use ($request) {
                        return $query->where([['is_active', 0], ['id', '!=', $request->id]]);
                    })
                ],
            ]);

            User::where('id', $request->id)->update([
                'id' => $request->id,
                'name' => $request->name,
                'uname' => $request->uname,
                'phonenumber' => $request->phonenumber,
                'usergroup' => $request->usergroup,
            ]);

        } else {
            $this->validate($request, [
                'name' => [
                    'required',
                    Rule::unique('user', 'name')->where(function ($query) use ($request) {
                        return $query->where([['is_active', 0]]);
                    })
                ],
                'uname' => [
                    'required',
                    Rule::unique('user', 'uname')->where(function ($query) use ($request) {
                        return $query->where([['is_active', 0]]);
                    })
                ],
                'phonenumber' => [
                    'required',
                    Rule::unique('user', 'phonenumber')->where(function ($query) use ($request) {
                        return $query->where([['is_active', 0]]);
                    })
                ],
            ]);

            DB::table('User')->insertOrIgnore([
                'id' => $request->id,
                'name' => $request->name,
                'uname' => $request->uname,
                'phonenumber' => $request->phonenumber,
                'usergroup' => $request->usergroup,
                'pass' => 'changepass',
            ]);
        }
        return $this->index();
//        ddd(\DB::getQueryLog());
    }

    public function deleteUser(Request $request)
    {
//        return $request;
//        return User::orderBy('id', 'DESC')->get();
        if ($request->id) {

            User::where('id', $request->id)->update([
                'is_active' => 1,
            ]);

        }
        return $this->index();
    }

    public function loginFirst(Request $request)
    {
//        \DB::enableQueryLog();
        $uname = $request->uname;
//$uname='check';
        $data = DB::connection('read')->table('nithrausers as user')
            ->select('user.id', 'user.personal_mobile as phonenumber', 'user.username as name', 'user.username as uname', 'user.user_role as usergroup', 'user.ugroup as usergroupid')
            ->where([['username', $uname], ['password', $request->pass]])->orderBy('id', 'DESC')
            ->get();
        $result = count($data);
//print_r($data);exit;
//        ddd(\DB::getQueryLog());
        if ($result) {
            $jobDataResult = json_decode(json_encode($data), true);
//            print_r($jobDataResult);exit;
            $user_id = $jobDataResult[0]['id'];
            $code = mt_rand("111111", "999999");
            DB::connection('read')->table('nithrausers')->where('id', $user_id)->update([
                'mfa_code' => $code,
                'mfa_code_time' => Carbon::now(),
            ]);
            $jobDataResult[0]['code'] = $code;
        }

        return $jobDataResult;
    }

    public function loginSecond(Request $request)
    {
//        \DB::enableQueryLog();

        $data = DB::connection('read')->table('nithrausers as user')
            ->select('user.id', 'user.personal_mobile as phonenumber', 'user.username as name', 'user.username as uname', 'user.user_role as usergroup', 'user.ugroup as usergroupid')
            ->where([['user.id', $request->id], ['mfa_code', $request->mfacode]])
            ->whereRaw("TIMESTAMPDIFF(SECOND, `mfa_code_time`, '" . Carbon::now() . "' ) < 70 ")
            ->orderBy('id', 'DESC')->get();
//        ddd(\DB::getQueryLog());
        $jobDataResult = json_decode(json_encode($data), true);
//        print_r($jobDataResult);exit;
        return $jobDataResult;
    }

    public function getAllUser1(Request $request)
    {
        return User::select('id', 'name as title')->where([['is_active', 0]])->orderBy('id', 'DESC')->get();
    }

    public function getAllUser(Request $request)
    {
        if (Redis::exists('getNithraUsers')) {
            $result = unserialize(Redis::get('getNithraUsers'));
            $result['from'] = 'redis';
        } else {


            $getUser = DB::connection('read')->table('user')->select('id', 'name as title')->where([['is_active', 0]])->orderBy('id', 'DESC')->get();
            $result['users'] = $getUser;

            $result['from'] = 'database';
            Redis::set('getNithraUsers', serialize($result));
            Redis::expire('getNithraUsers', 86400);
        }
        return $result;
    }

    public function getjobsource(Request $request)
    {
        if (Redis::exists('getJobsource')) {
            $result = unserialize(Redis::get('getJobsource'));
            $result['from'] = 'redis';
        } else {
            $getjobsource = DB::connection('read')->table('jobsource')->select('id', 'jobsource as title')->orderBy('jobsource', 'ASC')->get();
            $result['users'] = $getjobsource;

            $result['from'] = 'database';
            Redis::set('getJobsource', serialize($result));
            Redis::expire('getJobsource', 86400);
        }
        return $result;
    }

    public function getAreas(Request $request)
    {
        if (Redis::exists('areasmodel')) {
            $result = unserialize(Redis::get('areasmodel'));
            $result['from'] = 'redis';
        } else {
            $areasmodel = DB::connection('read')->table('areas')->select('id', 'area_name', 'area_name_telugu', 'district', 'district_telugu', 'dist_id')->orderBy('area_name', 'ASC')->get();
            $result['users'] = $areasmodel;

            $result['from'] = 'database';
            Redis::set('areasmodel', serialize($result));
            Redis::expire('areasmodel', 86400);
        }
        return $result;
    }

    public function getDistArray(Request $request)
    {
        if (Redis::exists('distmodel')) {
            $result = unserialize(Redis::get('distmodel'));
            $result['from'] = 'redis';
        } else {
            $distmodel = DB::connection('read')->table('dist')->select('dist_id as id', 'dist', 'dist_telugu')->orderBy('dist_id', 'ASC')->get();
            $result['users'] = $distmodel;

            $result['from'] = 'database';
            Redis::set('distmodel', serialize($result));
            Redis::expire('distmodel', 86400);
        }
        return $result;
    }

    public function getSkills(Request $request)
    {
        if (Redis::exists('skillsmodel')) {
            $result = unserialize(Redis::get('skillsmodel'));
            $result['from'] = 'redis';
        } else {
            $skillsmodel = DB::connection('read')->table('skills')->select('id', 'skills')->orderBy('skills', 'ASC')->get();
            $result['users'] = $skillsmodel;

            $result['from'] = 'database';
            Redis::set('skillsmodel', serialize($result));
            Redis::expire('skillsmodel', 86400);
        }
        return $result;
    }

    public function getCourse(Request $request)
    {
        if (Redis::exists('coursemodel')) {
            $result = unserialize(Redis::get('coursemodel'));
            $result['from'] = 'redis';
        } else {
            $coursemodel = DB::connection('read')->table('course')->select('id', 'course')->orderBy('id', 'ASC')->get();
            $result['users'] = $coursemodel;

            $result['from'] = 'database';
            Redis::set('coursemodel', serialize($result));
            Redis::expire('coursemodel', 86400);
        }
        return $result;
    }

    public function getJobscat(Request $request)
    {
        if (Redis::exists('jobscatmodel')) {
            $result = unserialize(Redis::get('jobscatmodel'));
            $result['from'] = 'redis';
        } else {
            $jobscatmodel = DB::connection('read')->table('jobscat')->select('id', 'job-cat as job_cat', 'exnglish_txtCat')->orderBy('id', 'ASC')->get();
            $result['users'] = $jobscatmodel;

            $result['from'] = 'database';
            Redis::set('jobscatmodel', serialize($result));
            Redis::expire('jobscatmodel', 86400);
        }
        return $result;
    }

    public function getMindset(Request $request)
    {
        if (Redis::exists('employermindsetmodel')) {
            $result = unserialize(Redis::get('employermindsetmodel'));
            $result['from'] = 'redis';
        } else {
            $employermindsetmodel = DB::connection('read')->table('employer_mindset')->select('id', 'source_name')->orderBy('id', 'ASC')->get();
            $result['users'] = $employermindsetmodel;

            $result['from'] = 'database';
            Redis::set('employermindsetmodel', serialize($result));
            Redis::expire('employermindsetmodel', 86400);
        }
        return $result;
    }

    public function getFollowupSource(Request $request)
    {
        if (Redis::exists('followupsourcemodel')) {
            $result = unserialize(Redis::get('followupsourcemodel'));
            $result['from'] = 'redis';
        } else {
            $followupsourcemodel = DB::connection('read')->table('followup_source')->select('id', 'source_name')->orderBy('id', 'ASC')->get();
            $result['users'] = $followupsourcemodel;

            $result['from'] = 'database';
            Redis::set('followupsourcemodel', serialize($result));
            Redis::expire('followupsourcemodel', 86400);
        }
        return $result;
    }

    public function getEmployerFeedback(Request $request)
    {
        if (Redis::exists('employerfeedback')) {
            $result = unserialize(Redis::get('employerfeedback'));
            $result['from'] = 'redis';
        } else {
            $employerfeedback = DB::connection('read')->table('employer_feedback')->select('id', 'source_name')->orderBy('id', 'ASC')->get();
            $result['users'] = $employerfeedback;

            $result['from'] = 'database';
            Redis::set('employerfeedback', serialize($result));
            Redis::expire('employerfeedback', 86400);
        }
        return $result;
    }


    public function getExpiryFollowupStatus(Request $request)
    {
        if (Redis::exists('expiryfollowupstatusmodel')) {
            $result = unserialize(Redis::get('expiryfollowupstatusmodel'));
            $result['from'] = 'redis';
        } else {
            $expiryfollowupstatusmodel = DB::connection('read')->table('expiry_followup_status')->select('id', 'source_name')->orderBy('id', 'ASC')->get();
            $result['users'] = $expiryfollowupstatusmodel;

            $result['from'] = 'database';
            Redis::set('expiryfollowupstatusmodel', serialize($result));
            Redis::expire('expiryfollowupstatusmodel', 86400);
        }
        return $result;
    }


    public function getCompanyType(Request $request)
    {
        if (Redis::exists('companytypemodel')) {
            $result = unserialize(Redis::get('companytypemodel'));
            $result['from'] = 'redis';
        } else {
            $companytypemodel = DB::connection('read')->table('company_type')->select('id', 'name')->orderBy('id', 'ASC')->get();
            $result['users'] = $companytypemodel;

            $result['from'] = 'database';
            Redis::set('companytypemodel', serialize($result));
            Redis::expire('companytypemodel', 86400);
        }
        return $result;
    }
}
