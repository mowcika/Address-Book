<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DynamicJsonController extends Controller
{

    function optionMaster($table, $colum)
    {
        return DB::table($table)
            ->select($colum)
            ->get();
    }

    public function dynamicJson(Request $request)
    {
        $indate = Carbon::now()->toDateTimeString();
        $inip = $request->ip();

        $action = $request->action;
        $type = $request->type;

        if ($action == 'dynamic_json') {
            if ($type == 1) {
                $payload = array(

                    array(
                        'widget_type' => 'edittext',
                        'lable_type' => 'type1',
                        'lable_backgroud_type' => 'card',
                        'lable_padding_top' => '5',
                        'lable_padding_bottom' => '5',
                        'lable_padding_left' => '5',
                        'lable_padding_right' => '5',
                        'lable_bacground_color' => '',
                        'lable_text_color' => '#3B71CA',
                        'lable_text_value' => 'உங்கள் பெயர்',
                        'lable_style' => 'bold',
                        'lable_aligenment' => 'left',
                        'lable_text_size' => '8',
                        'lable_corner_radius' => '0',
                        'lable_Click' => 'No',
                        'lable_open_type' => 'NO',
                        'lable_open_type_action' => '',
                        'lable_In_app_class' => '',
                        'lable_image' => '',
                        'lable_share' => 'No',
                        'input_type' => 'name',
                        'bacground_color' => '',
                        'text_color' => '#DC4C64',
                        'border' => '',
                        'hint_value' => 'உங்கள் பெயரை உள்ளிடவும்',
                        'style' => 'bold',
                        'aligenment' => 'left',
                        'text_size' => 'X2',
                        'corner_radius' => '',
                        'error_message' => '*உங்கள் பெயரை உள்ளிடவும்',
                        'open_type' => '',
                        'open_type_action' => '',
                        'In_app_class' => '',
                        'image' => '',
                        'share' => 'No',
                        'required' => 'Yes',
                        'maximum_line' => '1',
                        'option_alignment' => '',
                    ),
                    array(
                        'widget_type' => 'edittext',
                        'lable_type' => 'type2',
                        'lable_backgroud_type' => 'card',
                        'lable_padding_top' => '5',
                        'lable_padding_bottom' => '5',
                        'lable_padding_left' => '5',
                        'lable_padding_right' => '5',
                        'lable_bacground_color' => '',
                        'lable_text_color' => '#3B71CA',
                        'lable_text_value' => 'உங்கள் தாயின் பெயர்',
                        'lable_style' => 'bold',
                        'lable_aligenment' => 'left',
                        'lable_text_size' => '8',
                        'lable_corner_radius' => '0',
                        'lable_Click' => 'No',
                        'lable_open_type' => 'NO',
                        'lable_open_type_action' => '',
                        'lable_In_app_class' => '',
                        'lable_image' => '',
                        'lable_share' => 'No',
                        'input_type' => 'name',
                        'bacground_color' => '',
                        'text_color' => '#DC4C64',
                        'border' => '',
                        'hint_value' => 'உங்கள் தாயின் பெயரை உள்ளிடவும்',
                        'style' => 'bold',
                        'aligenment' => 'left',
                        'text_size' => '',
                        'corner_radius' => '',
                        'error_message' => '*உங்கள் தாயின் பெயரை உள்ளிடவும்',
                        'open_type' => '',
                        'open_type_action' => '',
                        'In_app_class' => '',
                        'image' => '',
                        'share' => 'No',
                        'required' => 'Yes',
                        'maximum_line' => '1',
                        'option_alignment' => '',
                    ),

                    array(
                        'widget_type' => 'edittext',
                        'lable_type' => 'type3',
                        'bacground_color' => '',
                        'text_color' => '#DC4C64',
                        'border' => '',
                        'hint_value' => 'உங்கள் தந்தையின் உள்ளிடவும்',
                        'style' => 'bold',
                        'aligenment' => 'left',
                        'text_size' => '',
                        'corner_radius' => '',
                        'error_message' => '*உங்கள் தந்தையின் உள்ளிடவும்',
                        'open_type' => '',
                        'open_type_action' => '',
                        'In_app_class' => '',
                        'image' => '',
                        'share' => 'No',
                        'required' => 'Yes',
                        'maximum_line' => '1',
                        'option_alignment' => '',

                    ),


                    array(
                        'widget_type' => 'textview',
                        'lable_type' => 'type1',
                        'lable_backgroud_type' => 'card',
                        'lable_bacground_color' => '#f0ad4e',
                        'lable_text_color' => '#292b2c',
                        'lable_text_value' => 'NITHRA APPS INDIA PRIVATE LIMITED',
                        'lable_style' => 'bold',
                        'lable_aligenment' => 'left',
                        'lable_padding_top' => '5',
                        'lable_padding_bottom' => '5',
                        'lable_padding_left' => '5',
                        'lable_padding_right' => '5',
                        'lable_text_size' => '8',
                        'lable_corner_radius' => '4',
                        'lable_Click' => 'No',
                        'lable_open_type' => 'NO',
                        'lable_open_type_action' => '',
                        'lable_In_app_class' => '',
                        'lable_image' => '',
                        'lable_share' => 'No',
                    ),

                    array(
                        'widget_type' => 'options',
                        'lable_type' => 'type1',
                        'lable_backgroud_type' => 'card',
                        'lable_bacground_color' => '',
                        'lable_text_color' => '#3B71CA',
                        'lable_text_value' => 'Gendar',
                        'lable_style' => 'bold',
                        'lable_padding_top' => '5',
                        'lable_padding_bottom' => '5',
                        'lable_padding_left' => '5',
                        'lable_padding_right' => '5',
                        'lable_aligenment' => 'left',
                        'lable_text_size' => '8',
                        'lable_corner_radius' => '0',
                        'lable_Click' => 'No',
                        'lable_open_type' => 'NO',
                        'lable_open_type_action' => '',
                        'lable_In_app_class' => '',
                        'lable_image' => '',
                        'lable_share' => 'No',
                        'input_type' => 'name',
                        'bacground_color' => '',
                        'text_color' => '#DC4C64',
                        'border' => '',
                        'hint_value' => 'SELECT YOUR GENDER',
                        'style' => 'bold',
                        'aligenment' => 'left',
                        'text_size' => 'X2',
                        'corner_radius' => '',
                        'error_message' => '*SELECT YOUR GENDER',
                        'open_type' => '',
                        'open_type_action' => '',
                        'In_app_class' => '',
                        'image' => '',
                        'share' => 'No',
                        'required' => 'Yes',
                        'maximum_line' => '1',
                        'option_alignment' => 2,
                        "option_array" =>
                            array(
                                array(
                                    'id' => '1',
                                    'text' => 'MALE',
                                ),
                                array(
                                    'id' => '2',
                                    'text' => 'FEMALE',
                                ),
                            ),

                    ),
                    array(
                        'widget_type' => 'options',
                        'lable_type' => 'type2',
                        'lable_backgroud_type' => 'card',
                        'lable_bacground_color' => '',
                        'lable_text_color' => '#3B71CA',
                        'lable_text_value' => 'Course',
                        'lable_style' => 'bold',
                        'lable_padding_top' => '5',
                        'lable_padding_bottom' => '5',
                        'lable_padding_left' => '5',
                        'lable_padding_right' => '5',
                        'lable_aligenment' => 'left',
                        'lable_text_size' => '8',
                        'lable_corner_radius' => '0',
                        'lable_Click' => 'No',
                        'lable_open_type' => 'NO',
                        'lable_open_type_action' => '',
                        'lable_In_app_class' => '',
                        'lable_image' => '',
                        'lable_share' => 'No',
                        'input_type' => 'name',
                        'bacground_color' => '',
                        'text_color' => '#DC4C64',
                        'border' => '',
                        'hint_value' => 'SELECT Course',
                        'style' => 'bold',
                        'aligenment' => 'left',
                        'text_size' => 'X2',
                        'corner_radius' => '',
                        'error_message' => '*SELECT Course',
                        'open_type' => '',
                        'open_type_action' => '',
                        'In_app_class' => '',
                        'image' => '',
                        'share' => 'No',
                        'required' => 'Yes',
                        'maximum_line' => '1',
                        'option_alignment' => 1,
                        "option_array" =>
                            array(
                                array(
                                    'id' => '1',
                                    'text' => 'Java',
                                ),
                                array(
                                    'id' => '2',
                                    'text' => 'Kotlin',
                                ),
                                array(
                                    'id' => '3',
                                    'text' => 'Flutter',
                                ),
                                array(
                                    'id' => '4',
                                    'text' => 'PHP',
                                ),
                            ),

                    ),
                    array(
                        'widget_type' => 'options',
                        'lable_type' => 'type3',
                        'lable_backgroud_type' => 'card',
                        'lable_bacground_color' => '',
                        'lable_text_color' => '#3B71CA',
                        'lable_text_value' => 'Course',
                        'lable_style' => 'bold',
                        'lable_padding_top' => '5',
                        'lable_padding_bottom' => '5',
                        'lable_padding_left' => '5',
                        'lable_padding_right' => '5',
                        'lable_aligenment' => 'left',
                        'lable_text_size' => '8',
                        'lable_corner_radius' => '0',
                        'lable_Click' => 'No',
                        'lable_open_type' => 'NO',
                        'lable_open_type_action' => '',
                        'lable_In_app_class' => '',
                        'lable_image' => '',
                        'lable_share' => 'No',
                        'input_type' => 'name',
                        'bacground_color' => '',
                        'text_color' => '#DC4C64',
                        'border' => '',
                        'hint_value' => 'SELECT Course',
                        'style' => 'bold',
                        'aligenment' => 'left',
                        'text_size' => 'X2',
                        'corner_radius' => '',
                        'error_message' => '*SELECT Course',
                        'open_type' => '',
                        'open_type_action' => '',
                        'In_app_class' => '',
                        'image' => '',
                        'share' => 'No',
                        'required' => 'Yes',
                        'maximum_line' => '1',
                        'option_alignment' => 1,
                        "option_array" =>
                            array(
                                array(
                                    'id' => '1',
                                    'text' => 'Java',
                                ),
                                array(
                                    'id' => '2',
                                    'text' => 'Kotlin',
                                ),
                                array(
                                    'id' => '3',
                                    'text' => 'Flutter',
                                ),
                                array(
                                    'id' => '4',
                                    'text' => 'PHP',
                                ),
                            ),

                    ),
                    array(
                        'widget_type' => 'options',
                        'lable_type' => 'type4',
                        'lable_backgroud_type' => 'card',
                        'lable_bacground_color' => '',
                        'lable_text_color' => '#3B71CA',
                        'lable_text_value' => 'Course',
                        'lable_style' => 'bold',
                        'lable_padding_top' => '5',
                        'lable_padding_bottom' => '5',
                        'lable_padding_left' => '5',
                        'lable_padding_right' => '5',
                        'lable_aligenment' => 'left',
                        'lable_text_size' => '8',
                        'lable_corner_radius' => '0',
                        'lable_Click' => 'No',
                        'lable_open_type' => 'NO',
                        'lable_open_type_action' => '',
                        'lable_In_app_class' => '',
                        'lable_image' => '',
                        'lable_share' => 'No',
                        'input_type' => 'name',
                        'bacground_color' => '',
                        'text_color' => '#DC4C64',
                        'border' => '',
                        'hint_value' => 'SELECT Course',
                        'style' => 'bold',
                        'aligenment' => 'left',
                        'text_size' => 'X2',
                        'corner_radius' => '',
                        'error_message' => '*SELECT Course',
                        'open_type' => '',
                        'open_type_action' => '',
                        'In_app_class' => '',
                        'image' => '',
                        'share' => 'No',
                        'required' => 'Yes',
                        'maximum_line' => '1',
                        'option_alignment' => 1,
                        "option_array" =>
                            array(
                                array(
                                    'id' => '1',
                                    'text' => 'Java',
                                ),
                                array(
                                    'id' => '2',
                                    'text' => 'Kotlin',
                                ),
                                array(
                                    'id' => '3',
                                    'text' => 'Flutter',
                                ),
                                array(
                                    'id' => '4',
                                    'text' => 'PHP',
                                ),
                            ),

                    ),
                    array(
                        'widget_type' => 'button',
                        'lable_type' => 'type1',
                        'lable_backgroud_type' => '',
                        'lable_bacground_color' => '',
                        'lable_text_color' => '',
                        'lable_text_value' => '',
                        'lable_style' => '',
                        'lable_padding_top' => '',
                        'lable_padding_bottom' => '',
                        'lable_padding_left' => '',
                        'lable_padding_right' => '',
                        'lable_aligenment' => '',
                        'lable_text_size' => '',
                        'lable_corner_radius' => '',
                        'lable_Click' => '',
                        'lable_open_type' => '',
                        'lable_open_type_action' => '',
                        'lable_In_app_class' => '',
                        'lable_image' => '',
                        'lable_share' => '',
                        'input_type' => '',
                        'bacground_color' => '',
                        'text_color' => '',
                        'border' => '',
                        'hint_value' => '',
                        'style' => 'bold',
                        'aligenment' => '',
                        'text_size' => '',
                        'corner_radius' => '',
                        'error_message' => '',
                        'open_type' => '',
                        'open_type_action' => '',
                        'In_app_class' => '',
                        'image' => '',
                        'share' => 'No',
                        'required' => 'Yes',
                        'maximum_line' => '1',
                        'option_alignment' => 2,
                        "option_array" =>
                            array(
                                array(
                                    'id' => '1',
                                    'action' => 'cancel',
                                    'bacground_color' => '',
                                    'text_color' => '#3B71CA',
                                    'text_value' => 'Cancel',
                                    'style' => 'bold',
                                    'padding_top' => '5',
                                    'padding_bottom' => '5',
                                    'padding_left' => '5',
                                    'padding_right' => '5',
                                    'text_size' => '8',
                                    'corner_radius' => '0',
                                ),
                                array(
                                    'id' => '2',
                                    'action' => 'submit',
                                    'bacground_color' => '',
                                    'text_color' => '#3B71CA',
                                    'text_value' => 'Save',
                                    'style' => 'bold',
                                    'padding_top' => '5',
                                    'padding_bottom' => '5',
                                    'padding_left' => '5',
                                    'padding_right' => '5',
                                    'text_size' => '8',
                                    'corner_radius' => '0',
                                ),
                            ),

                    ),
                    array(
                        'widget_type' => 'button',
                        'lable_type' => 'type1',
                        'lable_backgroud_type' => '',
                        'lable_bacground_color' => '',
                        'lable_text_color' => '',
                        'lable_text_value' => '',
                        'lable_style' => '',
                        'lable_padding_top' => '',
                        'lable_padding_bottom' => '',
                        'lable_padding_left' => '',
                        'lable_padding_right' => '',
                        'lable_aligenment' => '',
                        'lable_text_size' => '',
                        'lable_corner_radius' => '',
                        'lable_Click' => '',
                        'lable_open_type' => '',
                        'lable_open_type_action' => '',
                        'lable_In_app_class' => '',
                        'lable_image' => '',
                        'lable_share' => '',
                        'input_type' => '',
                        'bacground_color' => '',
                        'text_color' => '',
                        'border' => '',
                        'hint_value' => '',
                        'style' => 'bold',
                        'aligenment' => '',
                        'text_size' => '',
                        'corner_radius' => '',
                        'error_message' => '',
                        'open_type' => '',
                        'open_type_action' => '',
                        'In_app_class' => '',
                        'image' => '',
                        'share' => 'No',
                        'required' => 'Yes',
                        'maximum_line' => '1',
                        'option_alignment' => 2,
                        "option_array" =>
                            array(

                                array(
                                    'id' => '2',
                                    'action' => 'submit',
                                    'bacground_color' => '',
                                    'text_color' => '#3B71CA',
                                    'text_value' => 'Save',
                                    'style' => 'bold',
                                    'padding_top' => '5',
                                    'padding_bottom' => '5',
                                    'padding_left' => '5',
                                    'padding_right' => '5',
                                    'text_size' => '8',
                                    'corner_radius' => '0',
                                ),
                            ),

                    ),


                );
                return $payload;


            }


        }
    }

    public function dynamicServerJson(Request $request)
    {
        $indate = Carbon::now()->toDateTimeString();
        $inip = $request->ip();
        $dynamicDialogue = DB::table('dynamic_dialogue')
            ->select('id', 'title', 'action')
            ->where('is_delete', '=', '0')
            ->orderBy('id', 'DESC')->get();
        $action = $request->action;
        $type = $request->type;
        $output = array();
        $getData = json_decode($dynamicDialogue);
        $dynamicId = $getData[0]->id;
        $dynamicTitle = $getData[0]->title;
        $dynamicAction = $getData[0]->action;


        $dynamicWidget = DB::table('dynamic_dialogue_widget as d')
            ->leftJoin('widget_master as w', 'w.id', '=', 'd.widget_type')
            ->leftJoin('backgroud_type as b', 'b.id', '=', 'd.lable_backgroud_type')
            ->leftJoin('style_master as s', 's.id', '=', 'd.lable_style')
            ->leftJoin('alignment_master as a', 'a.id', '=', 'd.lable_aligenment')
            ->leftJoin('alignment_master as aa', 'aa.id', '=', 'd.aligenment')
            ->leftJoin('font_size_master as f', 'f.id', '=', 'd.lable_text_size')
            ->leftJoin('font_size_master as ff', 'ff.id', '=', 'd.text_size')
            ->leftJoin('open_type as o', 'o.id', '=', 'd.lable_open_type')
            ->leftJoin('input_type as i', 'i.id', '=', 'd.input_type')
            ->leftJoin('style_master as ss', 'ss.id', '=', 'd.style')
            ->selectRaw("d.*,w.widget_name as widget_type,d.widget_type as widget_type_id,b.name as lable_backgroud_type,s.name as lable_style,a.name as lable_aligenment,aa.name as aligenment,f.font_size as lable_text_size,ff.font_size as text_size,i.name as input_type ,ss.name as style ")
            ->selectRaw("if(d.lable_Click=1,'YES','NO') as lable_Click")
            ->selectRaw("if(d.required=1,'YES','NO') as required")
            ->selectRaw("if(d.share=1,'YES','NO') as share")
            ->selectRaw("if(d.lable_share=1,'YES','NO') as lable_share")
            ->selectRaw("if(d.lable_open_type=0,'',o.name) as lable_open_type")
            ->where('d.is_delete', '=', '0')
            ->where('d.dynamic_id', '=', $dynamicId)
            ->orderBy('d.id', 'DESC')
            ->limit(1)
            ->get();

        $dynamicButton = DB::table('dynamic_button as d')
            ->selectRaw("d.*,w.widget_name as widget_type ")
            ->leftJoin('widget_master as w', 'w.id', '=', 'd.widget_type')
            ->where('d.is_delete', '=', '0')
            ->where('d.dynamic_id', '=', $dynamicId)
            ->orderBy('d.id', 'DESC')->get();

//print_r($getData[0]->id);exit;
        foreach ($dynamicWidget as $key => $valus) {
            $values_s = (array)$valus;
            $widget_type_id = $values_s['widget_type_id'];
            if ($widget_type_id == 3) {
                $optionTable = $values_s['optionTable'];

                $optionArray['optionArray'] = $this->optionMaster($optionTable, ['id', 'maser as name']);
//        echo $key;
//        print_r($dynamicWidget[$key]);exit;
                $dynamicWidget[$key] = array_merge($values_s, $optionArray);
            }
        }

        $output['dynamicId'] = $dynamicId;
        $output['dynamicTitle'] = $dynamicTitle;
        $output['dynamicAction'] = $dynamicAction;
        $output['widget_array'] = $dynamicWidget;
        $output['button_array'] = $dynamicButton;
        if ($action == 'dynamic_json') {

            return $output;
        }
    }
}
