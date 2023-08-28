<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function saveArticle(Request $request)
    {
//        print_r($request);exit;
        $cloudFrontUrl = "https://d2hy6ree306xec.cloudfront.net/";
        $new_file_name = "";
        \DB::enableQueryLog();
        $indate = Carbon::now()->toDateString();
        $current_date_time = date('Y-m-d H:i:s');

        $articleTitle = $request->articleTitle;
        $articleMessage = $request->articleMessage;
        $uid = $request->uid;
        $inip = $request->ip();
        $file = $request->file('fileUpload');
        if ($request->editId) { // edit
            if (empty($file)) {
                $new_file_name = $request->EditfileUpload;
            } else {
                if ($request->file()) {
                    if ($request->file('fileUpload')) {
                        $file = $request->file('fileUpload');
                        $articles_upload = $request->file('fileUpload')->store(
                            '/employer_files/articles',
                            's3'
                        );
                        $new_file_name = $cloudFrontUrl . $articles_upload;
                    }
                    // print_r($new_file_name);exit;
                }
            }


            $update_array = array(
                'title' => $articleTitle,
                'content' => $articleMessage,
                'imageUrl' => $new_file_name,
                'isVerify' => 1,
                'mby' => $uid,
                'mdate' => $current_date_time,
                'mip' => $inip,
            );
//                print_r($update_array);exit;
            if (sizeof($update_array)) { //return $inserDataArray;
                $Details = DB::table('articles')
                    ->where('id', $request->editId)
                    ->update($update_array);
//                dd(\DB::getQueryLog());
                \DB::getQueryLog();
                if ($Details) {
                    return array('adStatus' => true, 'adMessage' => 'Article Update Successfully');
                } else {
                    return array('adStatus' => false, 'adMessage' => 'Article Not Update!');
                }
            } else {
                return array('adStatus' => false, 'adMessage' => 'Something went wrong!');
            }

        } else { // add
            $response = array('status' => false, 'message' => 'Failed');
            if (empty($file)) {
//            print_r("empty");exit;
//                return $response;
                $new_file_name = "";
            } else {
                if ($request->file()) {
                    if ($request->file('fileUpload')) {
                        $file = $request->file('fileUpload');
                        $articles_upload = $request->file('fileUpload')->store(
                            '/employer_files/articles',
                            's3'
                        );
                        $new_file_name = $cloudFrontUrl . $articles_upload;
                    }
                    // print_r($new_file_name);exit;
                }
                // print_r("not empty");exit;
            }
            $insert_array = array(
                'title' => $articleTitle,
                'content' => $articleMessage,
                'imageUrl' => $new_file_name,
                'type' => 2,
                'isVerify' => 1,
                'verifyBy' => $uid,
                'verifyAt' => $current_date_time,
                'cby' => $uid,
                'cdate' => $current_date_time,
                'cip' => $inip,
            );
//                print_r(sizeof($insert_array));exit;
            if (sizeof($insert_array)) { //return $inserDataArray;
                $Details = DB::table('articles')->insert($insert_array);
//                dd(\DB::getQueryLog());
                \DB::getQueryLog();
                if ($Details) {
                    return array('adStatus' => true, 'adMessage' => 'Article Added Successfully');
                } else {
                    return array('adStatus' => false, 'adMessage' => 'Article Not Insert!');
                }
            } else {
                return array('adStatus' => false, 'adMessage' => 'Something went wrong!');
            }
        }
        return $result;
    }

    public function getArticledata(Request $request)
    {
        if ($request->empORadmin) {
            $eA = $request->empORadmin;
        } else {
            $eA = 1;
        }
//        if (Redis::exists('getArticledata')) {
//            $result = unserialize(Redis::get('getArticledata'));
//            $result['from'] = 'redis';
//        } else {
        $result['articleData'] = DB::table("articles")
            ->selectRaw("*")
            ->where("type", $eA)
            ->orderBy("id", "desc")
            ->get();
//            $result['from'] = 'database';
//            Redis::set('getArticledata', serialize($result));
//            Redis::expire('getArticledata', 86400);
//        }
        return $result;
    }

    public function getArticleEditdata(Request $request)
    {

        $id = $request->id;
        $getData = DB::table('articles as ar')
            ->selectRaw("ar.id, ar.title, ar.imageUrl, ar.content")
            ->where("ar.id", $id)
            ->get();
        $result['editData'] = $getData;
        return $result;
    }

    public function getarticleVerifyData(Request $request)
    {
        $id = $request->id;
        $getData = DB::table('articles as ar')
            ->selectRaw("ar.id, ar.isVerify, ar.content")
            ->where("ar.id", $id)
            ->get();
        $result['verifyData'] = $getData;
        return $result;
    }

    public function articleVerify(Request $request)
    {
//        print_r($request->editId);exit;
        \DB::enableQueryLog();
        $indate = Carbon::now()->toDateString();
        $current_date_time = date('Y-m-d H:i:s');
//print_r($current_date_time);exit;
        $rowid = $request->editId;
        $isVerify = $request->isVerify;
        $userid = $request->userid;
//        if($isVerify)

        if ($rowid) { // update verify
            $updateArray = array(
                'isVerify' => $isVerify,
                'verifyBy' => $userid,
                'verifyAt' => $current_date_time,
            );
//            print_r(sizeof($updateArray));exit;
            if (sizeof($updateArray)) { //return $updateArray;
                $Details = DB::table('articles')
                    ->where('id', $rowid)
                    ->update($updateArray);
//                dd(DB::getQueryLog());
//                print_r($Details);exit;
                if ($Details) {
                    return array('adStatus' => true, 'adMessage' => 'Article Verified Done! ');
                } else {
                    return array('adStatus' => false, 'adMessage' => 'Article Not Verified !');
                }
            } else {
                return array('adStatus' => false, 'adMessage' => 'Something went wrong!');
            }


        } else { // not update verify
            return array('adStatus' => false, 'adMessage' => 'Article Not Verified! ');
        }
    }

    // article title fetch
//    public function getArticleTitleDrop(){
//        $getData = DB::table('articles')
//            ->selectRaw("id,title")
//            ->where("isVerify", 1)
//            ->get();
//        $result['verifyATitle'] = $getData;
//        return $result;
//    }

    // get title base content
    public function getTitleBasedContent(Request $request)
    {
//        print_r($request->aTitle);exit;
        $rowid = $request->aTitle;
        if ($rowid) {
            $getData = DB::table('articles')
                ->selectRaw("content")
                ->where("id", $rowid)
                ->get();
            $result['adStatus'] = true;
            $result['verifyAContent'] = $getData;
            return $result;
        } else {
            $result['adStatus'] = false;
            $result['verifyAContent'] = "";
            return $result;
        }
    }

    // save article comment
    public function saveArticleComment(Request $request)
    {
//        print_r($request->aTitle);exit();
        $indate = Carbon::now()->toDateString();
        $current_date_time = date('Y-m-d H:i:s');
        $editId = $request->editId;
        $article_rowid = $request->aTitle;
        $articleContent = $request->articleContent; // comment
        $isVerify = $request->isVerify;
        $uid = $request->uid;
        $article_row_id = $request->article_row_id;
        $inip = $request->ip();

        if ($editId) { // edit
            $update_array = array(
                'comment' => $articleContent,
                'isVerify' => $isVerify,
                'verifyBy' => $uid,
                'verifyAt' => $current_date_time
            );
//            print_r($update_array);exit;
            if (sizeof($update_array)) { //return $inserDataArray;
                $Details = DB::table('articles_comment')
                    ->where('id', $editId)
                    ->update($update_array);
//                dd(\DB::getQueryLog());
                \DB::getQueryLog();
                if ($Details) {
                    return array('adStatus' => true, 'adMessage' => 'Article Comments Update Successfully');
                } else {
                    return array('adStatus' => false, 'adMessage' => 'Article Comments Not Update!');
                }
            } else {
                return array('adStatus' => false, 'adMessage' => 'Something went wrong!');
            }
        } else { // add
            if ($article_row_id) {
                $insert_array = array(
                    'article' => $article_row_id,
                    'comment' => $articleContent,
                    'type' => 2,
                    'isVerify' => 1,
                    'verifyBy' => $uid,
                    'verifyAt' => $current_date_time,
                    'cby' => $uid,
                    'cdate' => $current_date_time,
                    'cip' => $inip,
                );
//            print_r($insert_array);exit;
                if (sizeof($insert_array)) { //return $inserDataArray;
                    $Details = DB::table('articles_comment')->insert($insert_array);
//                dd(\DB::getQueryLog());
                    \DB::getQueryLog();
                    if ($Details) {
                        return array('adStatus' => true, 'adMessage' => 'Article Comment Added Successfully');
                    } else {
                        return array('adStatus' => false, 'adMessage' => 'Article Comment Not Insert!');
                    }
                } else {
                    return array('adStatus' => false, 'adMessage' => 'Something went wrong!');
                }
            }
        }
    }


    // fetch article comments data
    public function getArticleCommentsdata(Request $request)
    {
        $edit_id = $request->id;
        $cmts_no_cmts = $request->cmts_no_cmts;
        if ($edit_id) { // get edit data
//            print_r($edit_id);exit;
            $getData = DB::table('articles_comment as ac')
                ->selectRaw("ac.id, ac.article, ac.comment, ac.isVerify, ac.type, a.title,a.content ")
                ->leftJoin('articles as a', 'a.id', '=', 'ac.article')
                ->where('ac.id', '=', $edit_id)
                ->get();
            $result['commentsEditData'] = $getData;
            return $result;
        } else { // get all data
//            print_r("get");exit;
//            if (Redis::exists('getArticleCommentsdata')) {
//                $result = unserialize(Redis::get('getArticleCommentsdata'));
//                $result['from'] = 'redis';
//            } else {
            $empORadmin = $request->empORadmin;
            if ($empORadmin) {
                switch ($empORadmin) {
                    case 1:
                        $where1 = "ac.type = 1 OR ac.type IS NULL";
                        break;
                    case 2:
                        $where1 = "ac.type = 2";
                        break;
                    default :
                        $where1 = "1 = 1";
                }
            } else {
                $where1 = "`ac`.`type` = 1 OR `ac`.`type` IS NULL";
            }
            if ($cmts_no_cmts == 1) { // no comments
                $where2 = "`ac`.`id` IS NULL";
            } else if ($cmts_no_cmts == 2) {
                $where2 = "`ac`.`id` IS NOT NULL";
            } else {
                $where2 = "`ac`.`id` IS NOT NULL OR `ac`.`id` IS NULL";
            }

            $orderby = "ac.isVerify asc,`comment` ASC, `ac`.`id` desc";
            $getData = DB::table('articles as a')
                ->selectRaw("`a`.`id` as `arID`,ac.id as ac_id, ac.article, ac.comment, ac.isVerify, ac.type,u.name as cBy, date_format(ac.cdate, '%d-%m-%Y') as cDate, v.name as verifyBy,date_format(ac.verifyAt, '%d-%m-%Y') as verifyAt ")
                ->leftJoin('articles_comment as ac', 'a.id', '=', 'ac.article')
                ->leftJoin('user as u', 'u.id', '=', 'ac.cby')
                ->leftJoin('user as v', 'v.id', '=', 'ac.verifyBy')
                ->whereRaw($where1)
                ->havingRaw($where2)
                ->orderByRaw($orderby)
                ->get();
            $result['commentsData'] = $getData;
//                $result['from'] = 'database';
//                Redis::set('getArticleCommentsdata', serialize($result));
//                Redis::expire('getArticleCommentsdata', 86400);
//            }
            return $result;
        }
    }

    // get not post article comments
    public function getNotPostCommentArticls(Request $request)
    {
//        print_r($request->arID);exit;
        $article_id = $request->arID;
        $getData = DB::table('articles as ar')
            ->selectRaw("ar.id, ar.title, ar.isVerify, ar.content")
            ->where("ar.id", $article_id)
            ->get();
        $result['articleData'] = $getData;
        return $result;
    }
}
