<?php

namespace App\Http\Controllers;

use App\Models\NotiTypeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class NotiTypeController extends Controller
{
    //
    public function getNotiType(Request $request)
    {

        return NotiTypeModel::where([['is_delete', 0]])->orderBy('id', 'DESC')->get();
//        return 'pandiya';
    }

    public function getSingleNotiType(Request $request)
    {

        return NotiTypeModel::where([['id', $request->id]])->orderBy('id', 'DESC')->get();
//        return 'pandiya';
    }

    public function saveNotiType(Request $request)
    {
//        \DB::enableQueryLog();
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();
        if ($request->id) {


            NotiTypeModel::where('id', $request->id)->update([
                'id' => $request->id,
                'noti' => $request->notiType,


            ]);

        } else {
            $this->validate($request, [
                'notiType' => [
                    'required',
                    Rule::unique('noti_type', 'noti')->where(function ($query) {
                        return $query->where([['is_delete', '0']]);
                    })
                ],

            ]);

            DB::table('noti_type')->insertOrIgnore([
                'noti' => $request->notiType,


            ]);
        }
        return $this->getNotiType($request);
//        ddd(\DB::getQueryLog());
    }

    public function deleteNotiType(Request $request)
    {
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();
        if ($request->id) {

            NotiTypeModel::where('id', $request->id)->update([
                'is_delete' => 1,
            ]);

        }
        return $this->getNotiType($request);
    }
}
