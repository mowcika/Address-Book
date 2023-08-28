<?php

namespace App\Http\Controllers;

use App\Models\MsgTypeModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MsgTypeController extends Controller
{
    //
    public function getMsgType(Request $request)
    {

        return MsgTypeModel::where([['is_delete', 0]])->orderBy('id', 'DESC')->get();
//        return 'pandiya';
    }

    public function getSingleMsgType(Request $request)
    {

        return MsgTypeModel::where([['id', $request->id]])->orderBy('id', 'DESC')->get();
//        return 'pandiya';
    }

    public function saveMsgType(Request $request)
    {
        $indate = Carbon::now()->toDateTimeString();
//        \DB::enableQueryLog();
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();
        if ($request->id) {


            MsgTypeModel::where('id', $request->id)->update([
                'id' => $request->id,
                'type' => $request->msgType,


            ]);

        } else {
            $this->validate($request, [
                'msgType' => [
                    'required',
                    Rule::unique('msg_type', 'type')->where(function ($query) {
                        return $query->where([['is_delete', '0']]);
                    })
                ],

            ]);

            DB::table('msg_type')->insertOrIgnore([
                'type' => $request->msgType,
                'cdate' => $indate,


            ]);
        }
        return $this->getMsgType($request);
//        ddd(\DB::getQueryLog());
    }

    public function deleteMsgType(Request $request)
    {
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();
        if ($request->id) {

            MsgTypeModel::where('id', $request->id)->update([
                'is_delete' => 1,
            ]);

        }
        return $this->getMsgType($request);
    }
}
