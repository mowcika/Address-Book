<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class ForumController extends Controller
{
    // form save
    public function saveForum(Request $request)
    {
//         print_r($request->editId);exit();
        $indate = Carbon::now()->toDateString();

        $current_date_time = date('Y-m-d H:i:s');
        $ip = $request->ip();
        $inby = $request->userId;
        $forumContent = $request->forumContent;
        $isVerify = $request->isVerify;

        $edit_id = $request->editId;
        if ($edit_id) { // edit
            $update_array = array(
                'question' => $forumContent,
                'isVerify' => $isVerify,
                'verifyBy' => $inby,
                'verifyAt' => $current_date_time,
                'mby' => $inby,
                'mdate' => $current_date_time,
                'mip' => $ip,
            );
            if (sizeof($update_array)) { //return $inserDataArray;
                $Details = DB::table('forum')
                    ->where('id', $edit_id)
                    ->update($update_array);
//                dd(\DB::getQueryLog());
                \DB::getQueryLog();
                if ($Details) {
                    return array('adStatus' => true, 'adMessage' => 'Forum Update Successfully');
                } else {
                    return array('adStatus' => false, 'adMessage' => 'Forum Not Update!');
                }
            } else {
                return array('adStatus' => false, 'adMessage' => 'Something went wrong!');
            }
        } else { // save
            $insert_array = array(
                'question' => $forumContent,
                'type' => 2,
                'isVerify' => 1,
                'verifyBy' => $inby,
                'verifyAt' => $current_date_time,
                'cby' => $inby,
                'cdate' => $current_date_time,
                'cip' => $ip,
            );
            // print_r($insert_array);exit;
            if (sizeof($insert_array)) { //return $insert_array;
                $Details = DB::table('forum')->insert($insert_array);
//                dd(\DB::getQueryLog());
                \DB::getQueryLog();
                if ($Details) {
                    return array('adStatus' => true, 'adMessage' => 'Forum Added Successfully');
                } else {
                    return array('adStatus' => false, 'adMessage' => 'Forum Not Insert!');
                }
            } else {
                return array('adStatus' => false, 'adMessage' => 'Something went wrong!');
            }
        }
    }

    // get forum datas
    public function getForumdata(Request $request)
    {
//        if (Redis::exists('getForumdata')) {
//            $result = unserialize(Redis::get('getForumdata'));
//            $result['from'] = 'redis';
//        } else {
        $empORadmin = $request->empORadmin;
        if ($empORadmin) {
            switch ($empORadmin) {
                case 1:
                    $where1 = "f.type = 1";
                    break;
                case 2:
                    $where1 = "f.type = 2";
                    break;
                default :
                    $where1 = "1 = 1";
            }
        } else {
            $where1 = "`f`.`type` = 1";
        }
        $orderby = "f.isVerify asc, id desc";
        $result['forumData'] = DB::table("forum as f")
            ->selectRaw("f.id, f.question,f.type, f.isVerify, v.name as verifyBy, date_format(f.verifyAt, '%d-%m-%Y') as verifiedDate, c.name as cBy, date_format(f.cdate, '%d-%m-%Y') as cDate,m.name as mby, date_format(f.mdate, '%d-%m-%Y') as mDate")
            ->leftJoin('user as c', 'c.id', '=', 'f.cby')
            ->leftJoin('user as v', 'v.id', '=', 'f.verifyBy')
            ->leftJoin('user as m', 'm.id', '=', 'f.mby')
            ->whereRaw($where1)
            ->orderByRaw($orderby)
            ->get();
//            $result['from'] = 'database';
//            Redis::set('getForumdata', serialize($result));
//            Redis::expire('getForumdata', 86400);
//        }
        return $result;
    }

    // get forum edit data
    public function getForumEditdata(Request $request)
    {
        $id = $request->id;
        $getData = DB::table('forum as f')
            ->selectRaw("f.id, f.question, f.isVerify")
            ->where("f.id", $id)
            ->get();
        $result['editData'] = $getData;
        return $result;
    }

    // get form answers
    public function getForumAnswersdata(Request $request)
    {
        $edit_id = $request->id;
        $empORadmin = $request->empORadmin;
        $cmts_no_cmts = $request->cmts_no_cmts;
        if ($edit_id) { // edit data
            $getData = DB::table('forum as f')
                ->selectRaw("f.id as fid, fa.id as faId, fa.question, fa.answer, fa.isVerify, f.question as fQuestion")
                ->leftJoin('forum_answer as fa', 'f.id', '=', 'fa.question')
                ->where("fa.id", $edit_id)
                ->get();
            $result['editData'] = $getData;
            return $result;
        } else { // get data
            if ($empORadmin) {
                switch ($empORadmin) {
                    case 1:
                        $where1 = "fa.type = 1 OR fa.type IS NULL";
                        break;
                    case 2:
                        $where1 = "fa.type = 2";
                        break;
                    default :
                        $where1 = "1 = 1";
                }
            } else {
                $where1 = "`fa`.`type` = 1 OR `fa`.`type` IS NULL";
            }
            if ($cmts_no_cmts == 1) { // no comments
                $where2 = "`fa`.`id` IS NULL";
            } else if ($cmts_no_cmts == 2) {
                $where2 = "`fa`.`id` IS NOT NULL";
            } else {
                $where2 = "`fa`.`id` IS NOT NULL OR `fa`.`id` IS NULL";
            }
            $orderby = "`fa`.`isVerify` asc, `f`.`question` ASC, `fa`.`id` desc";
            $result['forumData'] = DB::table("forum as f")
                ->selectRaw("`f`.`id` as `f_id`, `f`.`question` as `fQuestion`, `f`.`type` as `f_type`, `f`.`isVerify` as `f_isVerify`, `fa`.`question`, `fa`.`type` as `fa_type`,date_format(`fa`.`cdate`, '%d-%m-%Y') as cDate, c.name as cBy, date_format(`fa`.`verifyAt`, '%d-%m-%Y') as verifyDate, v.name as verifyBy, fa.answer, fa.id as faId, `fa`.`isVerify` as `fa_isVerify`")
                ->leftJoin('forum_answer as fa', 'f.id', '=', 'fa.question')
                ->leftJoin('user as c', 'c.id', '=', 'fa.cby')
                ->leftJoin('user as v', 'v.id', '=', 'fa.verifyBy')
                ->whereRaw($where1)
                ->havingRaw($where2)
                ->orderByRaw($orderby)
                ->get();
            return $result;
        }
    }

    // get Not Answers Forums
    public function getNotAnswersForums(Request $request)
    {
//        print_r($request->f_id);exit;
        $forums_id = $request->f_id;
        $getData = DB::table('forum as fr')
            ->selectRaw("fr.id, fr.question")
            ->where("fr.id", $forums_id)
            ->get();
        $result['forumsData'] = $getData;
        return $result;
    }

    // SAVE ANSWERS
    // form save
    public function saveAnswers(Request $request)
    {
//         print_r($request->ForumAns);exit();
        $indate = Carbon::now()->toDateString();

        $current_date_time = date('Y-m-d H:i:s');
        $ip = $request->ip();
        $inby = $request->userId;
        $forumContent = $request->forumContent;
        $forum_row_id = $request->forum_row_id;
        $ForumAns = $request->ForumAns;
        $isVerify = $request->isVerify;

        $edit_id = $request->editId;
        if ($edit_id) { // edit
            $update_array = array(
                'question' => $forum_row_id,
                'isVerify' => $isVerify,
                'verifyBy' => $inby,
                'verifyAt' => $current_date_time,
                'mby' => $inby,
                'mdate' => $current_date_time,
                'mip' => $ip,
            );
//            print_r($update_array);exit;
            if (sizeof($update_array)) { //return $inserDataArray;
                $Details = DB::table('forum_answer')
                    ->where('id', $edit_id)
                    ->update($update_array);
//                dd(\DB::getQueryLog());
                \DB::getQueryLog();
                if ($Details) {
                    return array('adStatus' => true, 'adMessage' => 'Answers Update Successfully');
                } else {
                    return array('adStatus' => false, 'adMessage' => 'Answers Not Update!');
                }
            } else {
                return array('adStatus' => false, 'adMessage' => 'Something went wrong!');
            }
        } else { // save
            $insert_array = array(
                'question' => $forum_row_id,
                'answer' => $ForumAns,
                'type' => 2,
                'isVerify' => 1,
                'verifyBy' => $inby,
                'verifyAt' => $current_date_time,
                'cby' => $inby,
                'cdate' => $current_date_time,
                'cip' => $ip,
            );
            // print_r($insert_array);exit;
            if (sizeof($insert_array)) { //return $insert_array;
                $Details = DB::table('forum_answer')->insert($insert_array);
//                dd(\DB::getQueryLog());
                \DB::getQueryLog();
                if ($Details) {
                    return array('adStatus' => true, 'adMessage' => 'Answer Added Successfully');
                } else {
                    return array('adStatus' => false, 'adMessage' => 'Answer Not Insert!');
                }
            } else {
                return array('adStatus' => false, 'adMessage' => 'Something went wrong!');
            }
        }
    }
}
