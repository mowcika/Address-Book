<?php

namespace App\Http\Controllers;

use App\Models\CategoryMasterModel;
use App\Models\MainCatLangModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function getResource($table_name, $columns)
    {
        return DB::table($table_name)
            ->select($columns)
            ->get();
    }

    public function getCatSelect(Request $request)
    {

        return DB::table('main_cat')
            ->select('id', 'category as name')
            ->where([['is_delete', 0]])
            ->where('id', '!=', '2')
            ->get();
    }

    public function getCat(Request $request)
    {

        return CategoryMasterModel::where([['is_delete', 0]])->orderBy('id', 'DESC')->get();
//        return 'pandiya';
    }


    public function getSingleCat(Request $request)
    {

        $category['lang_based'] = CategoryMasterModel::join('main_cat_lang_map as ml', 'ml.cid', '=', 'main_cat.id')->where('ml.cid', $request->data)
            ->get(['ml.lid', 'ml.cname as title', 'main_cat.logo as image']);
        return $category;
    }


    public function deleteCat(Request $request)
    {
//        return $request;
        $indate = Carbon::now()->toDateTimeString();
//        return Employer::orderBy('id', 'DESC')->get();
        if ($request->id) {

            CategoryMasterModel::where('id', $request->id)->update([
                'is_delete' => 1,
                'mby' => $request->user,
                'mdate' => $indate
            ]);

        }
        return $this->getCat($request);
    }

    public function saveCat(Request $request)
    {
//        \DB::enableQueryLog();
//        return $request;
//        return Employer::orderBy('id', 'DESC')->get();
        $indate = Carbon::now()->toDateTimeString();
        if ($request->id) {


            CategoryMasterModel::where('id', $request->id)
                ->update([
                    'id' => $request->id,
                    'category' => $request->cat,
                    'logo' => $request->logo,
                    'mby' => DB::raw("CONCAT(mby, '," . $request->inby . "')"),
                    'mdate' => DB::raw("CONCAT(mdate, '," . $indate . "')")


                ]);

        } else {
            $this->validate($request, [
                'cat' => [
                    'required',
                    Rule::unique('main_cat', 'category')->where(function ($query) {
                        return $query->where([['is_delete', '0']]);
                    })
                ],

            ]);

            DB::table('lang')->insertOrIgnore([
                'category' => $request->cat,
                'logo' => $request->logo,
                'cby' => $request->inby,
                'cdate' => $indate,


            ]);
        }
        return $this->getCat($request);
//        ddd(\DB::getQueryLog());
    }

    public function addCat(Request $request)
    {
//        return $request;
        $data['msg'] = false;
        $product_img = $product_img_col = '';
        $array_value = json_decode(($request->prolist));
        $cloudFrontUrl = "https://d2u1fav05r2331.cloudfront.net/";
        $Category = new CategoryMasterModel;
        if ($request->file('image')) {
            $file = $request->file('image');
            $product_img = $request->file('image')->store('/nam_veedu/nlife', 's3');
            $product_img = $cloudFrontUrl . $product_img;
        }
        if ($request->id) {
            $update_array = array(
                'category' => $request['catname'],
                'mip' => $request->ip(),
                'mdate' => now(),
                'logo' => $product_img
            );
            if ($product_img == '') {
                unset($update_array['image']);
            }
            $result_edit = CategoryMasterModel::where("category", '=', $request->catname)->Where('id', '<>', $request->id)->exists();
            if ($result_edit) {
                $data['button'] = 'error';
                $data['notify'] = 'Data Already Exists';
                $data['msg'] = true;
            } else {
                CategoryMasterModel::where('id', $request->id)->update($update_array);
                MainCatLangModel::where('cid', '=', $request->id)->delete();
                foreach ($array_value as $key => $values) {
                    if (!empty($values)) {
                        $langmast = new MainCatLangModel;
                        $langmast->lid = $key;
                        $langmast->cid = $request->id;
                        $langmast->cname = $values;
                        $langmast->cdate = now();
                        $langmast->cip = $request->ip();
                        $langmast->save();
                    }
                }

                $data['button'] = 'success';
                $data['notify'] = 'Data Updated Successfully';
            }
        } else {
            $result = CategoryMasterModel::where("category", '=', $request->catname)->exists();
            if ($result) {
                $data['notify'] = 'Category Already Exists';
                $data['button'] = 'error';
                $data['msg'] = true;
            } else {
                if (!empty($request->catname)) {
                    $Category->category = $request->catname;
                    $Category->cdate = now();
                    $Category->logo = $product_img;
                    $Category->cip = $request->ip();
                    $Category->cby = $request->cby;
                    $Category->save();
                    $Lastidd = $Category->id;
                }
                foreach ($array_value as $key => $values) {
                    $langmast = new MainCatLangModel;
                    $langmast->lid = $key;
                    $langmast->cid = $Lastidd;
                    $langmast->cname = $values;
                    $langmast->cdate = now();
                    $langmast->cip = $request->ip();
                    $langmast->cby = $request->cby;
                    $langmast->save();
                }
                $data['button'] = 'success';
                $data['notify'] = 'Category Added Successfully';
            }

        }

        return $data;
    }
}
