<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\AppNameModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AppNameController extends Controller
{
    //
    public function getAppName(Request $request)
    {

        return AppNameModel::where([['is_delete', 0]])->orderBy('id', 'DESC')->get();
//        return 'pandiya';
    }

    public function getSingleAppname(Request $request)
    {

        return AppNameModel::where([['id', $request->id]])->orderBy('id', 'DESC')->get();
//        return 'pandiya';
    }

    public function saveAppName(Request $request)
    {
//        \DB::enableQueryLog();
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();
        if ($request->id) {


            AppNameModel::where('id', $request->id)->update([
                'id' => $request->id,
                'app_name' => $request->appName,

            ]);

        } else {
            $this->validate($request, [
                'appName' => [
                    'required',
                    Rule::unique('app_master', 'app_name')->where(function ($query) {
                        return $query->where([['is_delete', '0']]);
                    })
                ],

            ]);

            DB::table('app_master')->insertOrIgnore([
                'app_name' => $request->appName,


            ]);
        }
        return $this->getAppName($request);
//        ddd(\DB::getQueryLog());
    }

    public function deleteAppName(Request $request)
    {
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();
        if ($request->id) {

            AppNameModel::where('id', $request->id)->update([
                'is_delete' => 1,
            ]);

        }
        return $this->getAppName($request);
    }
}
