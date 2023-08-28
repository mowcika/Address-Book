<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RegisterModel;
use App\Models\BookMarkModel;
use App\Models\ViewTableModel;

class MobileApi extends Controller
{
    function optionMaster($table, $colum)
    {
        return DB::table($table)
            ->select($colum)
            ->get();
    }


    public function dynamicServerJson(Request $request)
    {
        $indate = Carbon::now()->toDateTimeString();
        $inip = $request->ip();
        $action = $request->action;
        $lang = 1;
        $lang = isset($request->lang) ? $request->lang : 1;
        $limit = isset($request->limit) ? $request->limit : 0;
        $catId = isset($request->cat_id) ? $request->cat_id : '';
        $userId = isset($request->user_id) ? $request->user_id : '';
        $mainCatId = isset($request->main_cat_id) ? $request->main_cat_id : '';
        $subCatId = isset($request->sub_cat_id) ? $request->sub_cat_id : '';
        $output = array();
        if ($action == 'getLang') {
            $output = DB::table('lang')
                ->selectRaw("id, lang, logo,'status' as 'success'")
                ->where('is_delete', '=', '0')
                ->orderBy('id', 'ASC')->get();
        } elseif ($action == 'getBottomCat') {

            $output = DB::table('main_cat as m')
                ->leftjoin('main_cat_lang_map as cl', function ($join) use ($lang) {
                    $join->on('m.id', '=', 'cl.cid')
                        ->where('cl.lid', '=', $lang);
                })
                ->selectRaw("m.id,  m.logo,'status' as 'success'")
                ->selectRaw("if(`cl`.`cname` != '', `cl`.`cname`, `m`.`category`) as category")
                ->where('m.is_delete', '=', '0')
                ->orderBy('m.id', 'ASC')->get();
        } elseif ($action == 'getTopCat') {

            if ($catId && $catId != 2) {
                $conditions = [
                    ['m.cat_id', '=', $catId],
                ];
            } else {
                $conditions = array();
            }
            $output = DB::table('sub_cat as m')
                ->leftjoin('sub_cat_lang_map as cl', function ($join) use ($lang) {
                    $join->on('m.id', '=', 'cl.cid')
                        ->where('cl.lid', '=', $lang);
                })
                ->selectRaw("m.id,'status' as 'success'")
                ->selectRaw("if(`cl`.`cname` != '', `cl`.`cname`, `m`.`sub_category`) as category")
                ->where('m.is_delete', '=', '0')
                ->where($conditions)
                ->orderBy('m.id', 'ASC')->get();


        } elseif ($action == 'checkUser') {
            $device_id = isset($request->device_id) ? $request->device_id : '';
            $email = isset($request->email) ? $request->email : '';
            $name = isset($request->name) ? $request->name : '';
            $image = isset($request->image) ? $request->image : '';
            $fcm_id = isset($request->fcm_id) ? $request->fcm_id : '';
            $device_type = isset($request->device_type) ? $request->device_type : '';
            $vcode = isset($request->vcode) ? $request->vcode : '';
            $device_version = isset($request->device_version) ? $request->device_version : '';
            $app_name = isset($request->app_name) ? $request->app_name : '';
            $condition = DB::table('register')->select('id')
                ->where('email', $email)
                ->first();
            if ($condition) {
                $id = $condition->id;
                RegisterModel::where('id', $id)->update([
                    'device_id' => $device_id,
                    'fcm_id' => $fcm_id,
                    'device_type' => $device_type,
                    'email' => $email,
                    'image' => $image,
                    'name' => $name,
                    'vcode' => $vcode,
                    'device_version' => $device_version,
                    'app_name' => $app_name,
                ]);
                $result['status'] = 'success';
                $result['userId'] = $id;
                $result['msg'] = 'The information was updated successfully';
                $output = $result;
            } else {

                DB::table('register')->insertOrIgnore([
                    'device_id' => $device_id,
                    'fcm_id' => $fcm_id,
                    'email' => $email,
                    'name' => $name,
                    'image' => $image,
                    'device_type' => $device_type,
                    'vcode' => $vcode,
                    'device_version' => $device_version,
                    'app_name' => $app_name,
                    'cip' => $inip,
                    'cdate' => $indate,

                ]);
                $lastInsertId = DB::getPdo()->lastInsertId();
                $result['status'] = 'success';
                $result['userId'] = $lastInsertId;
                $result['msg'] = 'The information was inserted successfully';
                $output = $result;
            }
        } elseif ($action == 'getList') {
            if ($lang) {
                $conditionsLand = [
                    ['d.lang', '=', $lang],

                ];
            } else {
                $conditionsLand = array();
            }

            if ($mainCatId && $subCatId) {
                if ($mainCatId != 2) {
                    $conditions = [
                        ['d.main_cat', '=', $mainCatId],
                        ['d.sub_cat', '=', $subCatId],
                    ];
                } else {
                    $conditions = array();
                }

            } elseif ($mainCatId) {
                if ($mainCatId != 2) {
                    $conditions = [
                        ['d.main_cat', '=', $mainCatId],
                    ];
                } else {
                    $conditions = array();
                }

            } else {
                $conditions = array();
            }

            $output['others'] = DB::table('dynamic_component as d')
                ->leftjoin('bookmark as b', function ($join) use ($userId) {
                    $join->on('b.post_id', '=', 'd.id')
                        ->where('b.user_id', '=', $userId);
                })
                ->leftJoin('widget_master as w', 'w.id', '=', 'd.widget_type')
                ->leftJoin('alignment_master as a', 'a.id', '=', 'd.title_aligenment')
                ->leftJoin('alignment_master as aa', 'aa.id', '=', 'd.des_aligenment')
                ->leftJoin('font_size_master as f', 'f.id', '=', 'd.title_text_size')
                ->leftJoin('font_size_master as ff', 'ff.id', '=', 'd.des_text_size')
                ->leftJoin('open_type as o', 'o.id', '=', 'd.open_type')
                ->leftjoin('main_cat_lang_map as m', function ($join) use ($lang) {
                    $join->on('m.cid', '=', 'd.main_cat')
                        ->where('m.lid', '=', $lang);
                })
                ->leftjoin('sub_cat_lang_map as sc', function ($join) use ($lang) {
                    $join->on('sc.cid', '=', 'd.sub_cat')
                        ->where('sc.lid', '=', $lang);
                })
                ->selectRaw("d.id post_id,w.widget_name, d.type,d.title_text_color, a.name as title_aligenment,f.font_size as title_text_size,d.title,d.des_text_color,aa.name as des_aligenment,ff.font_size as des_text_size,sc.cname as sub_category,d.audio_url,d.des,d.url,m.cname as category,o.name as open_type,'status' as 'success'")
                ->selectRaw("if(d.required=1,'YES','NO') as bookmark")
                ->selectRaw("if(d.share=1,'YES','NO') as share")
                ->selectRaw("if(d.wshare=1,'YES','NO') as wshare")
                ->selectRaw("if(b.type=2,'2','1') as is_fav")
                ->where('d.widget_type', '!=', '4')
                ->where($conditions)
                ->where($conditionsLand)
                ->orderBy('d.id', 'ASC')->offset($limit)->limit(10)->get();


//            $output = json_decode(json_encode($output1), true);
//            print_r($output);exit;

            $output['shorts_video'] = DB::table('dynamic_component as d')
                ->leftJoin('widget_master as w', 'w.id', '=', 'd.widget_type')
                ->leftJoin('alignment_master as a', 'a.id', '=', 'd.title_aligenment')
                ->leftJoin('alignment_master as aa', 'aa.id', '=', 'd.des_aligenment')
                ->leftJoin('font_size_master as f', 'f.id', '=', 'd.title_text_size')
                ->leftJoin('font_size_master as ff', 'ff.id', '=', 'd.des_text_size')
                ->leftJoin('open_type as o', 'o.id', '=', 'd.open_type')
                ->selectRaw("d.id post_id, w.widget_name, d.type,d.url,'status' as 'success'")
                ->selectRaw("if(d.required=1,'YES','NO') as bookmark")
                ->selectRaw("if(d.share=1,'YES','NO') as share")
                ->selectRaw("if(d.wshare=1,'YES','NO') as wshare")
                ->where('d.widget_type', '=', '4')
                ->where($conditions)
                ->where($conditionsLand)
                ->orderBy('d.id', 'ASC')->offset($limit)->limit(10)->get();
            $output['shorts_video_pro'] = array('title' => 'shorts video');
//            array_push($output,$o);


        } elseif ($action == 'addBookMark') {
            $postId = $request->postId;
            $type = isset($request->type) ? $request->type : 1;
//            type:2=add,type:1=remove

            $update_array = array(
                'type' => $type,
                'mip' => $request->ip(),
                'mdate' => now(),

            );
            $result_edit = BookMarkModel::where("post_id", '=', $postId)->Where('user_id', '=', $userId)->exists();
            if ($result_edit) {
                BookMarkModel::where("post_id", '=', $postId)->Where('user_id', '=', $userId)->update($update_array);
                $output['status'] = 'success';
                $output['msg'] = 'Bookmark Updated Successfully';
                $output['type'] = $type;
            } else {
                DB::table('bookmark')->insertOrIgnore([
                    'post_id' => $postId,
                    'user_id' => $userId,
                    'type' => $type,
                    'cip' => $inip,
                    'cdate' => $indate,

                ]);
                $output['status'] = 'success';
                $output['msg'] = 'Bookmark Added Successfully';
                $output['type'] = $type;
            }

        } elseif ($action == 'getBookmarkList') {

            $output['others'] = DB::table('dynamic_component as d')
                ->Join('bookmark as b', 'b.post_id', '=', 'd.id')
                ->leftJoin('widget_master as w', 'w.id', '=', 'd.widget_type')
                ->leftJoin('alignment_master as a', 'a.id', '=', 'd.title_aligenment')
                ->leftJoin('alignment_master as aa', 'aa.id', '=', 'd.des_aligenment')
                ->leftJoin('font_size_master as f', 'f.id', '=', 'd.title_text_size')
                ->leftJoin('font_size_master as ff', 'ff.id', '=', 'd.des_text_size')
                ->leftJoin('open_type as o', 'o.id', '=', 'd.open_type')
                ->leftjoin('main_cat_lang_map as m', function ($join) use ($lang) {
                    $join->on('m.cid', '=', 'd.main_cat')
                        ->where('m.lid', '=', $lang);
                })
                ->leftjoin('sub_cat_lang_map as sc', function ($join) use ($lang) {
                    $join->on('sc.cid', '=', 'd.sub_cat')
                        ->where('sc.lid', '=', $lang);
                })
                ->selectRaw("d.id post_id,w.widget_name, d.type,d.title_text_color, a.name as title_aligenment,f.font_size as title_text_size,d.title,d.des_text_color,aa.name as des_aligenment,ff.font_size as des_text_size,sc.cname as sub_category,d.audio_url,d.des,d.url,m.cname as category,o.name as open_type,'status' as 'success'")
                ->selectRaw("if(d.required=1,'YES','NO') as bookmark")
                ->selectRaw("if(d.share=1,'YES','NO') as share")
                ->selectRaw("if(d.wshare=1,'YES','NO') as wshare")
                ->where('d.widget_type', '!=', '4')
                ->where('b.user_id', '=', $userId)
                ->where('b.type', '=', '2')
                ->orderBy('d.id', 'ASC')->get();


        } elseif ($action == 'getMenu') {
            $langArray = [
                '1' => [
                    'home' => 'Home',
                    'bookmark' => 'Bookmark',
                    'help' => 'Help',
                    'tools' => 'Tools',
                    'langChange' => 'Change Language',
                ],
                '2' => [
                    'home' => 'முகப்பு',
                    'bookmark' => 'பிடித்தது',
                    'help' => 'உதவி',
                    'tools' => 'கருவிகள்',
                    'langChange' => 'மொழியை மாற்ற',
                ],
                '3' => [
                    'home' => 'హోమ్',
                    'bookmark' => 'ఇష్టమైన',
                    'help' => 'సహాయం',
                    'tools' => 'ఉపకరణాలు',
                    'langChange' => 'భాష మార్చు',
                ],
                '4' => [
                    'home' => 'होम',
                    'bookmark' => 'पसंदीदा',
                    'help' => 'मदद',
                    'tools' => 'औजार',
                    'langChange' => 'भाषा बदलें',
                ],
                '5' => [
                    'home' => 'ಮು',
                    'bookmark' => 'ನೆಚ್ಚಿನ',
                    'help' => 'ಸಹಾಯ',
                    'tools' => 'ಪರಿಕರಗಳು',
                    'langChange' => 'ಭಾಷೆ ಬದಲಾಯಿಸಿ',
                ],
            ];

//echo $langArray[1]['title'];exit;
            $output = [
                [
                    'title' => $langArray[$lang]['home'],
                    'class' => 'homescreen',
                    'des' => '',
                ],
                [
                    'title' => $langArray[$lang]['bookmark'],
                    'class' => 'FavList',
                    'des' => '',
                ],
                [
                    'title' => $langArray[$lang]['help'],
                    'class' => 'HelpPage',
                    'type' => '1',
                    'sendto' => 'info@nithra.mobi',
                    'des' => '<b><div align="left"><center><font size="3" color="green">If you have any technical issues, please contact this email address <span style="color:blue;"><a href="mailto:info@nithra.mobi">info@nithra.mobi</a></span>.</font></div></b>',
                ],
                [
                    'title' => $langArray[$lang]['langChange'],
                    'class' => 'SelectLanguages',
                    'des' => '',
                ],
                [
                    'title' => $langArray[$lang]['tools'],
                    'class' => 'ToolsPage',
                    'des' => '',
                ],

            ];
        } elseif ($action == 'getTool') {
            $langArray = [
                '1' => [
                    'home' => 'Home',
                    'bookmark' => 'Bookmark',
                    'help' => 'Help',
                    'tools' => 'Tools',
                    'langChange' => 'Change Language',
                ],
                '2' => [
                    'home' => 'முகப்பு',
                    'bookmark' => 'பிடித்தது',
                    'help' => 'உதவி',
                    'tools' => 'கருவிகள்',
                    'langChange' => 'மொழியை மாற்ற',
                ],
                '3' => [
                    'home' => 'హోమ్',
                    'bookmark' => 'ఇష్టమైన',
                    'help' => 'సహాయం',
                    'tools' => 'ఉపకరణాలు',
                    'langChange' => 'భాష మార్చు',
                ],
                '4' => [
                    'home' => 'होम',
                    'bookmark' => 'पसंदीदा',
                    'help' => 'मदद',
                    'tools' => 'औजार',
                    'langChange' => 'भाषा बदलें',
                ],
                '5' => [
                    'home' => 'ಮು',
                    'bookmark' => 'ನೆಚ್ಚಿನ',
                    'help' => 'ಸಹಾಯ',
                    'tools' => 'ಪರಿಕರಗಳು',
                    'langChange' => 'ಭಾಷೆ ಬದಲಾಯಿಸಿ',
                ],
            ];

//echo $langArray[1]['title'];exit;
            $output['title'] = $langArray[$lang]['tools'];
            $output['tap'] = 'Money,Health';
            $output['money'] = [
                [
                    'title' => 'Gst',
                    'class' => 'gstcalculation',
                    'des' => '',
                    'icon' => 'https://d2u1fav05r2331.cloudfront.net/nam_veedu/nlife/icons/compound_image.png',
                ],
                [
                    'title' => 'Percentage',
                    'class' => 'PercentageCalculator',
                    'des' => '',
                    'icon' => 'https://d2u1fav05r2331.cloudfront.net/nam_veedu/nlife/icons/percentage_image.png',
                ],
                [
                    'title' => 'Compound Interest',
                    'class' => 'CompoundInterestCalculator',
                    'des' => '',
                    'icon' => 'https://d2u1fav05r2331.cloudfront.net/nam_veedu/nlife/icons/compound_image.png',
                ],
                [
                    'title' => 'Simple Intereset',
                    'class' => 'SimpleInterest',
                    'des' => '',
                    'icon' => 'https://d2u1fav05r2331.cloudfront.net/nam_veedu/nlife/icons/simple_interest_image.png',
                ],
                [
                    'title' => 'EMI',
                    'class' => 'emi',
                    'des' => '',
                    'icon' => 'https://d2u1fav05r2331.cloudfront.net/nam_veedu/nlife/icons/emi_image.png',
                ],
                [
                    'title' => 'TN-EB',
                    'class' => 'Tneb_Bill_Calculator',
                    'des' => '',
                    'icon' => 'https://d2u1fav05r2331.cloudfront.net/nam_veedu/nlife/icons/eb_image.png',
                ],
                [
                    'title' => 'SIP',
                    'class' => 'Sys_Investment_Plan',
                    'des' => '',
                    'icon' => 'https://d2u1fav05r2331.cloudfront.net/nam_veedu/nlife/icons/compound_image.png',
                ],
                [
                    'title' => 'PF',
                    'class' => 'Provident_Fund',
                    'des' => '',
                    'icon' => 'https://d2u1fav05r2331.cloudfront.net/nam_veedu/nlife/icons/compound_image.png',
                ],
                [
                    'title' => 'Tax',
                    'class' => 'calculator-Taxes_Ca',
                    'des' => '',
                    'icon' => 'https://d2u1fav05r2331.cloudfront.net/nam_veedu/nlife/icons/compound_image.png',
                ],

            ];
            $output['health'] = [
                [
                    'title' => 'BMI',
                    'class' => 'bmicalculater',
                    'des' => '',
                    'icon' => 'https://d2u1fav05r2331.cloudfront.net/nam_veedu/nlife/icons/bmi_image.png',
                ],
                [
                    'title' => 'Height, Weight Chart',
                    'class' => 'Height_weight',
                    'des' => '',
                    'icon' => 'https://d2u1fav05r2331.cloudfront.net/nam_veedu/nlife/icons/height_weight_image.png',
                ],


            ];
        } elseif ($action == 'addview') {
            $postId = $request->postId;

//            type:2=add,type:1=remove

            $update_array = array(
                'mip' => $request->ip(),
                'mdate' => now(),
                'count' => DB::raw('count + 1'),

            );
            $result_edit = ViewTableModel::where("post_id", '=', $postId)->Where('user_id', '=', $userId)->exists();
            if ($result_edit) {
                ViewTableModel::where("post_id", '=', $postId)->Where('user_id', '=', $userId)->update($update_array);
                $output['status'] = 'success';
                $output['msg'] = 'Updated Successfully';

            } else {
                DB::table('view_table')->insertOrIgnore([
                    'post_id' => $postId,
                    'user_id' => $userId,
                    'count' => '1',
                    'cip' => $inip,
                    'cdate' => $indate,

                ]);
                $output['status'] = 'success';
                $output['msg'] = 'Added Successfully';

            }

        }

        return $output;

    }
}
