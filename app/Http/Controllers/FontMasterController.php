<?php

namespace App\Http\Controllers;

use App\Models\FontMasterModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FontMasterController extends Controller
{
    public function getFontMaster(Request $request)
    {

        return FontMasterModel::where([['is_delete', 0]])->orderBy('id', 'DESC')->get();
//        return 'pandiya';
    }

    public function getSingleFontMaster(Request $request)
    {

        return FontMasterModel::where([['id', $request->id]])->orderBy('id', 'DESC')->get();
//        return 'pandiya';
    }

    public function saveFontMaster(Request $request)
    {
//        \DB::enableQueryLog();
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();
        $idate = Carbon::now()->toDateTimeString();
        if ($request->id) {


            FontMasterModel::where('id', $request->id)->update([
                'id' => $request->id,
                'font_size' => $request->fontSize,
                'font_name' => $request->fontName,
                'mby' => $request->cby,


            ]);

        } else {
            $this->validate($request, [
                'fontSize' => [
                    'required',
                    Rule::unique('font_size_master', 'font_size')->where(function ($query) {
                        return $query->where([['is_delete', '0']]);
                    })
                ],
                'fontName' => [
                    'required',
                    Rule::unique('font_size_master', 'font_name')->where(function ($query) {
                        return $query->where([['is_delete', '0']]);
                    })
                ],

            ]);

            DB::table('font_size_master')->insertOrIgnore([
                'font_size' => $request->fontSize,
                'font_name' => $request->fontName,
                'cdate' => $idate,
                'cby' => $request->cby,


            ]);
        }
        return $this->getFontMaster($request);
//        ddd(\DB::getQueryLog());
    }

    public function deleteFontMaster(Request $request)
    {
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();
        if ($request->id) {

            FontMasterModel::where('id', $request->id)->update([
                'is_delete' => 1,
            ]);

        }
        return $this->getFontMaster($request);
    }
}
