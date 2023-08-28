<?php

namespace App\Http\Controllers;


use App\Models\SubCatModel;
use App\Models\SubCatLangModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function addSubCat(Request $request)
    {
//        return $request;
        $data['msg'] = false;
        $product_img = $product_img_col = '';
        $array_value = json_decode(($request->prolist));
        $Category = new SubCatModel;

        if ($request->id) {
            $update_array = array(
                'sub_category' => $request['catname'],
                'mip' => $request->ip(),
                'mdate' => now(),
                'cat_id' => $request['mainCat']
            );

            $result_edit = SubCatModel::where("sub_category", '=', $request->catname)->Where('id', '<>', $request->id)->exists();
            if ($result_edit) {
                $data['button'] = 'error';
                $data['notify'] = 'Data Already Exists';
                $data['msg'] = true;
            } else {
                SubCatModel::where('id', $request->id)->update($update_array);
                SubCatLangModel::where('cid', '=', $request->id)->delete();
                foreach ($array_value as $key => $values) {
                    if (!empty($values)) {
                        $langmast = new SubCatLangModel;
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
            $result = SubCatModel::where("sub_category", '=', $request->catname)->exists();
            if ($result) {
                $data['notify'] = 'Sub Category Already Exists';
                $data['button'] = 'error';
                $data['msg'] = true;
            } else {
                if (!empty($request->catname)) {
                    $Category->sub_category = $request->catname;
                    $Category->cat_id = $request->mainCat;
                    $Category->cdate = now();
                    $Category->cip = $request->ip();
                    $Category->cby = $request->cby;
                    $Category->save();
                    $Lastidd = $Category->id;
                }
                foreach ($array_value as $key => $values) {
                    $langmast = new SubCatLangModel;
                    $langmast->lid = $key;
                    $langmast->cid = $Lastidd;
                    $langmast->cname = $values;
                    $langmast->cdate = now();
                    $langmast->cip = $request->ip();
                    $langmast->cby = $request->cby;
                    $langmast->save();
                }
                $data['button'] = 'success';
                $data['notify'] = 'Sub Category Added Successfully';
            }

        }

        return $data;
    }

    public function getSingleSubCat(Request $request)
    {

        $category['lang_based'] = SubCatModel::join('sub_cat_lang_map as ml', 'ml.cid', '=', 'sub_cat.id')->where('ml.cid', $request->data)
            ->get(['ml.lid', 'ml.cname as title', 'sub_cat.cat_id']);
        return $category;
    }

    public function deleteCat(Request $request)
    {
//        return $request;
        $indate = Carbon::now()->toDateTimeString();
//        return Employer::orderBy('id', 'DESC')->get();
        if ($request->id) {

            SubCatModel::where('id', $request->id)->update([
                'is_delete' => 1,
                'mby' => $request->user,
                'mdate' => $indate
            ]);

        }
        return $this->getSubCat($request);
    }

    public function getSubCat(Request $request)
    {

        return SubCatModel::join('sub_cat_lang_map as ml', 'ml.cid', '=', 'sub_cat.id')
            ->join('main_cat as mc', 'mc.id', '=', 'sub_cat.cat_id')
            ->where([['sub_cat.is_delete', 0]])
            ->groupBy('sub_cat.id')
            ->orderBy('id', 'DESC')
            ->get(['sub_cat.id', 'sub_cat.sub_category', 'mc.category']);
//        return 'pandiya';
    }

    public function subCatFilter(Request $request)
    {
        $catId = $request->cat_id;
        $conditions = [
            ['is_delete', '=', 0],
            ['cat_id', '=', $catId],
        ];
        return SubCatModel::where($conditions)
            ->orderBy('id', 'DESC')
            ->get(['id', 'sub_category as name']);
//        return 'pandiya';
    }
}
