<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DynamicComponentController extends Controller
{
    public function saveDynamicComponent(Request $request)
    {
//        return $request;
        $indate = Carbon::now()->toDateTimeString();
        $cby = $request->cby;
        $compType = $request->compType;
        $lang = $request->lang;
        $weight = $request->weight;
        $audioUrl = $request->audioUrl;
        $mainCat = $request->mainCat;
        $subCat = $request->subCat;
        $title_text_color = $request->title_text_color;
        $title_aligenment = $request->title_aligenment;
        $title_text_size = $request->title_text_size;
        $top_title = $request->top_title;
        $des_text_color = $request->des_text_color;
        $des_aligenment = $request->des_aligenment;
        $des_text_size = $request->des_text_size;
        $description = $request->description;
        $compontUrl = $request->compontUrl;
        $open_type = $request->open_type;
        $share = $request->share;
        $required = $request->required;
        $wshare = $request->wshare;
        foreach ($weight as $KeysId => $values) {

            $dynamicWeight = array(
                'widget_type' => isset($weight[$KeysId]) ? $weight[$KeysId] : '',
                'lang' => isset($lang) ? $lang : '',
                'main_cat' => isset($mainCat) ? $mainCat : '',
                'sub_cat' => isset($subCat) ? $subCat : '',
                'type' => isset($compType[$KeysId]) ? $compType[$KeysId] : '',
                'title_text_color' => isset($title_text_color[$KeysId]) ? $title_text_color[$KeysId] : '',
                'title_aligenment' => isset($title_aligenment[$KeysId]) ? $title_aligenment[$KeysId] : '',
                'title_text_size' => isset($title_text_size[$KeysId]) ? $title_text_size[$KeysId] : '',
                'title' => isset($top_title[$KeysId]) ? $top_title[$KeysId] : '',
                'des_text_color' => isset($des_text_color[$KeysId]) ? $des_text_color[$KeysId] : '',
                'des_aligenment' => isset($des_aligenment[$KeysId]) ? $des_aligenment[$KeysId] : '',
                'des_text_size' => isset($des_text_size[$KeysId]) ? $des_text_size[$KeysId] : '',
                'des' => isset($description[$KeysId]) ? $description[$KeysId] : '',
                'url' => isset($compontUrl[$KeysId]) ? $compontUrl[$KeysId] : '',
                'open_type' => isset($open_type[$KeysId]) ? $open_type[$KeysId] : '',
                'share' => isset($share[$KeysId]) ? $share[$KeysId] : '',
                'required' => isset($required[$KeysId]) ? $required[$KeysId] : '',
                'wshare' => isset($wshare[$KeysId]) ? $wshare[$KeysId] : '',
                'audio_url' => isset($audioUrl[$KeysId]) ? $audioUrl[$KeysId] : '',
                'cdate' => $indate,
                'cby' => $cby,
                'cip' => $request->ip()
            );
            DB::table('dynamic_component')->insertOrIgnore($dynamicWeight);


        }
        $result['status'] = 'successfully inserted';

        return $result;
    }
}
