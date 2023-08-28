<?php

namespace App\Http\Controllers;

use App\Models\WeightModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WeightController extends Controller
{
    public function getWeightList(Request $request)
    {

        return WeightModel::where([['is_delete', 0]])->orderBy('id', 'DESC')->get();
//        return 'pandiya';
    }


    public function getResource($table_name, $columns)
    {
        return DB::table($table_name)
            ->select($columns)
            ->get();
    }

    public function getDynamicMaster(Request $request)
    {

        $result['widget'] = $this->getResource('widget_master', ['id', 'widget_name as name']);
        $result['backgroud_type'] = $this->getResource('backgroud_type', ['id', 'name']);
        $result['style_master'] = $this->getResource('style_master', ['id', 'name']);
        $result['alignment_master'] = $this->getResource('alignment_master', ['id', 'name']);
        $result['font_size_master'] = $this->getResource('font_size_master', ['id', 'font_name as name','font_size']);
        $result['open_type'] = $this->getResource('open_type', ['id', 'name']);
        $result['input_type'] = $this->getResource('input_type', ['id', 'name']);
        $result['all_table'] = DB::select('SHOW TABLES');
        $result['label_type'] = $this->getResource('label_type', ['name as id', 'name']);
        $result['lang'] = $this->getResource('lang', ['id', 'lang as name']);
        $result['mainCat'] = $this->getResource('main_cat', ['id', 'category as name']);
        return $result;
    }


}
