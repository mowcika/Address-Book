<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DynamicDialogueWeightModel;
use Illuminate\Validation\Rule;

class DynamicDialogueController extends Controller
{
    //
    public function saveDynamicDialogue(Request $request)
    {
        $title = $request['title'];
        $action = $request['action'];
        $cby = $request['cby'];
        $aligenment = $request['aligenment'];
        $bacground_color = $request['bacground_color'];
        $corner_radius = $request['corner_radius'];
        $error_message = $request['error_message'];
        $hint_value = $request['hint_value'];
        $input_type = $request['input_type'];
        $keyName = $request['keyName'];
        $lable_Click = $request['lable_Click'];
        $lable_aligenment = $request['lable_aligenment'];
        $lable_bacground_color = $request['lable_bacground_color'];
        $lable_backgroud_type = $request['lable_backgroud_type'];
        $lable_backgroud_type_img = $request['lable_backgroud_type_img'];
        $lable_corner_radius = $request['lable_corner_radius'];
        $lable_open_type = $request['lable_open_type'];
        $lable_open_type_action = $request['lable_open_type_action'];
        $lable_padding_bottom = $request['lable_padding_bottom'];
        $lable_padding_left = $request['lable_padding_left'];
        $lable_padding_right = $request['lable_padding_right'];
        $lable_padding_top = $request['lable_padding_top'];
        $lable_share = $request['lable_share'];
        $lable_style = $request['lable_style'];
        $lable_text_color = $request['lable_text_color'];
        $lable_text_size = $request['lable_text_size'];
        $lable_text_value = $request['lable_text_value'];
        $lable_type = $request['lable_type'];
        $maximum_line = $request['maximum_line'];
        $open_type = $request['open_type'];
        $open_type_action = $request['open_type_action'];
        $optionTable = $request['optionTable'];
        $optionAlignment = $request['optionAlignment'];
        $required = $request['required'];
        $share = $request['share'];
        $style = $request['style'];
        $text_color = $request['text_color'];
        $text_size = $request['text_size'];
        $weight = $request['weight'];
        $buttonAction = $request['buttonAction'];
        $idate = Carbon::now()->toDateTimeString();

        $dynamicDialogueId = DB::table('dynamic_dialogue')->insertGetId([
            'title' => $title,
            'action' => $action,
            'cby' => $cby,
            'cdate' => $idate,

        ]);

        foreach ($weight as $KeysId => $values) {
            if ($values == 4) {
                $dynamicWeight = array(
                    'dynamic_id' => $dynamicDialogueId ?? '',
                    'widget_type' => $values ?? '',
                    'lable_type' => $lable_type[$KeysId] ?? '',
                    'backgroud_type' => $lable_backgroud_type[$KeysId] ?? '',
                    'padding_top' => $lable_padding_top[$KeysId] ?? '',
                    'padding_bottom' => $lable_padding_bottom[$KeysId] ?? '',
                    'padding_left' => $lable_padding_left[$KeysId] ?? '',
                    'padding_right' => $lable_padding_right[$KeysId] ?? '',
                    'bacground_color' => $lable_bacground_color[$KeysId] ?? '',
                    'text_color' => $lable_text_color[$KeysId] ?? '',
                    'text_value' => $lable_text_value[$KeysId] ?? '',
                    'style' => $lable_style[$KeysId] ?? '',
                    'aligenment' => $lable_aligenment[$KeysId] ?? '',
                    'text_size' => $lable_text_size[$KeysId] ?? '',
                    'corner_radius' => $lable_corner_radius[$KeysId] ?? '',
                    'option_alignment' => $optionAlignment[$KeysId] ?? '',
                    'cby' => $cby,
                    'cdate' => $idate,
                    'cip' => $request->ip(),
                );
                DB::table('dynamic_button')->insertOrIgnore($dynamicWeight);
            } else {
                $dynamicWeight = array(
                    'lable_share' => $lable_share[$KeysId] ?? '',
                    'lable_backgroud_img' => $lable_backgroud_type_img[$KeysId] ?? '',
                    'dynamic_id' => $dynamicDialogueId ?? '',
                    'widget_type' => $values ?? '',
                    'lable_type' => $lable_type[$KeysId] ?? '',
                    'lable_backgroud_type' => $lable_backgroud_type[$KeysId] ?? '',
                    'lable_padding_top' => $lable_padding_top[$KeysId] ?? '',
                    'lable_padding_bottom' => $lable_padding_bottom[$KeysId] ?? '',
                    'lable_padding_left' => $lable_padding_left[$KeysId] ?? '',
                    'lable_padding_right' => $lable_padding_right[$KeysId] ?? '',
                    'lable_bacground_color' => $lable_bacground_color[$KeysId] ?? '',
                    'lable_text_color' => $lable_text_color[$KeysId] ?? '',
                    'lable_text_value' => $lable_text_value[$KeysId] ?? '',
                    'lable_style' => $lable_style[$KeysId] ?? '',
                    'lable_aligenment' => $lable_aligenment[$KeysId] ?? '',
                    'lable_text_size' => $lable_text_size[$KeysId] ?? '',
                    'lable_corner_radius' => $lable_corner_radius[$KeysId] ?? '',
                    'lable_Click' => $lable_Click[$KeysId] ?? '',
                    'lable_open_type' => $lable_open_type[$KeysId] ?? '',
                    'lable_open_type_action' => $lable_open_type_action[$KeysId] ?? '',
                    'input_type' => $input_type[$KeysId] ?? '',
                    'bacground_color' => $bacground_color[$KeysId] ?? '',
                    'text_color' => $text_color[$KeysId] ?? '',
                    'hint_value' => $hint_value[$KeysId] ?? '',
                    'style' => $style[$KeysId] ?? '',
                    'aligenment' => $aligenment[$KeysId] ?? '',
                    'keyName' => $keyName[$KeysId] ?? '',
                    'text_size' => $text_size[$KeysId] ?? '',
                    'corner_radius' => $corner_radius[$KeysId] ?? '',
                    'error_message' => $error_message[$KeysId] ?? '',
                    'open_type' => $open_type[$KeysId] ?? '',
                    'open_type_action' => $open_type_action[$KeysId] ?? '',
                    'share' => $share[$KeysId] ?? '',
                    'required' => $required[$KeysId] ?? '',
                    'maximum_line' => $maximum_line[$KeysId] ?? '',
                    'optionTable' => $optionTable[$KeysId] ?? '',
                    'option_alignment' => $optionAlignment[$KeysId] ?? '',
                    'cby' => $cby,
                    'cdate' => $idate,
                    'cip' => $request->ip(),
                );
                DB::table('dynamic_dialogue_widget')->insertOrIgnore($dynamicWeight);
            }


        }
        $result['status'] = 'successfully inserted';

        return $result;
    }
}
