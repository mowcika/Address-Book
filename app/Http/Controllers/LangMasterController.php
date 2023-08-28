<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\LangMasterModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LangMasterController extends Controller
{
    public function getLang(Request $request)
    {

        return LangMasterModel::where([['is_delete', 0]])->orderBy('id', 'DESC')->get();
//        return 'pandiya';
    }

    public function getLangSelect(Request $request)
    {
//        return $request;
        $editLangg = LangMasterModel::select('id', 'lang', 'logo')->where('is_delete', 0)->get();


        return $editLangg;
    }

    public function getSingleLang(Request $request)
    {

        return LangMasterModel::where([['id', $request->id]])->orderBy('id', 'DESC')->get();
//        return 'pandiya';
    }

    public function deleteLang(Request $request)
    {
//        return $request;
        $indate = Carbon::now()->toDateTimeString();
//        return Employer::orderBy('id', 'DESC')->get();
        if ($request->id) {

            LangMasterModel::where('id', $request->id)->update([
                'is_delete' => 1,
                'mby' => $request->user,
                'mdate' => $indate
            ]);

        }
        return $this->getLang($request);
    }

    public function saveLang(Request $request)
    {
//        \DB::enableQueryLog();
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();
        $indate = Carbon::now()->toDateTimeString();
        if ($request->id) {


            LangMasterModel::where('id', $request->id)
                ->update([
                    'id' => $request->id,
                    'lang' => $request->lang,
                    'logo' => $request->logo,
                    'mby' => DB::raw("CONCAT(mby, '," . $request->inby . "')"),
                    'mdate' => DB::raw("CONCAT(mdate, '," . $indate . "')")


                ]);

        } else {
            $this->validate($request, [
                'lang' => [
                    'required',
                    Rule::unique('lang', 'lang')->where(function ($query) {
                        return $query->where([['is_delete', '0']]);
                    })
                ],

            ]);

            DB::table('lang')->insertOrIgnore([
                'lang' => $request->lang,
                'logo' => $request->logo,
                'cby' => $request->inby,
                'cdate' => $indate,


            ]);
        }
        return $this->getLang($request);
//        ddd(\DB::getQueryLog());
    }
}
