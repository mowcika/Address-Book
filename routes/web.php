<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\EmployerController;

use App\Http\Controllers\UsergroupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AppNameController;
use App\Http\Controllers\MenuMasterController;
use App\Http\Controllers\MsgTypeController;
use App\Http\Controllers\NotiTypeController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FontMasterController;
use App\Http\Controllers\WeightController;
use App\Http\Controllers\DynamicDialogueController;
use App\Http\Controllers\LangMasterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DynamicComponentController;
use App\Http\Controllers\SubCategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/{any}', [ApplicationController::class, 'index'])->where('any', '.*');

//user start

Route::post('/user-group', [UsergroupController::class, 'index']);
Route::post('/getSingleUserGroup', [UsergroupController::class, 'getSingleUserGroup']);
Route::post('/saveUserGroup', [UsergroupController::class, 'saveUserGroup']);
Route::post('/deleteUserGroup', [UsergroupController::class, 'deleteUserGroup']);

Route::post('/user', [UserController::class, 'index']);
Route::post('/getSingleUser', [UserController::class, 'getSingleUser']);
Route::post('/saveUser', [UserController::class, 'saveUser']);
Route::post('/deleteUser', [UserController::class, 'deleteUser']);

Route::post('/loginFirst', [UserController::class, 'loginFirst']);
Route::post('/loginSecond', [UserController::class, 'loginSecond']);

//user end

//lang start
Route::post('/getLang', [LangMasterController::class, 'getLang']);
Route::post('/getSingleLang', [LangMasterController::class, 'getSingleLang']);
Route::post('/saveLang', [LangMasterController::class, 'saveLang']);
Route::post('/deleteLang', [LangMasterController::class, 'deleteLang']);
Route::post('/getLangSelect', [LangMasterController::class, 'getLangSelect']);
//lang end

//category start
Route::post('/getCat', [CategoryController::class, 'getCat']);
Route::post('/getSingleCat', [CategoryController::class, 'getSingleCat']);
Route::post('/saveCat', [CategoryController::class, 'saveCat']);
Route::post('/addCat', [CategoryController::class, 'addCat']);
Route::post('/deleteCat', [CategoryController::class, 'deleteCat']);
Route::post('/getCatSelect', [CategoryController::class, 'getCatSelect']);

//category end

//sub category start
Route::post('/addSubCat', [SubCategoryController::class, 'addSubCat']);
Route::post('/getSubCat', [SubCategoryController::class, 'getSubCat']);
Route::post('/deleteCat', [SubCategoryController::class, 'deleteCat']);
Route::post('/getSingleSubCat', [SubCategoryController::class, 'getSingleSubCat']);
Route::post('/subCatFilter', [SubCategoryController::class, 'subCatFilter']);

//sub category end


//app name start
Route::post('/getnotiType', [NotiTypeController::class, 'getNotiType']);
Route::post('/getSinglenotiType', [NotiTypeController::class, 'getSingleNotiType']);
Route::post('/savenotiType', [NotiTypeController::class, 'saveNotiType']);
Route::post('/deletenotiType', [NotiTypeController::class, 'deleteNotiType']);

//app name end

//Master tables Start
Route::post('/getMaster', [MasterController::class, 'getMaster']);
//Master tables end

//Notification Part Start
Route::post('/saveNotification', [NotificationController::class, 'saveNotification']);
Route::post('/getNotification', [NotificationController::class, 'getNotification']);
Route::post('/getSingleNotification', [NotificationController::class, 'getSingleNotification']);
Route::post('/deleteNotification', [NotificationController::class, 'deleteNotification']);
Route::post('/sendNotification', [NotificationController::class, 'sendNotification']);
//Notification Part end

//font size master

Route::post('/getFontMaster', [FontMasterController::class, 'getFontMaster']);
Route::post('/saveFontName', [FontMasterController::class, 'saveFontMaster']);
Route::post('/getSingleFontMaster', [FontMasterController::class, 'getSingleFontMaster']);
Route::post('/deleteFontMaster', [FontMasterController::class, 'deleteFontMaster']);

//Role Master
Route::post('/role-master', [MenuMasterController::class, 'getAllRole']);
Route::post('/getActiveRole', [MenuMasterController::class, 'getActiveRole']);
Route::post('/saveRole', [MenuMasterController::class, 'saveRole']);
Route::post('/getSingleRole', [MenuMasterController::class, 'getSingleRole']);
Route::post('/activateRole', [MenuMasterController::class, 'activateRole']);
Route::post('/inActivateRole', [MenuMasterController::class, 'inActivateRole']);


//User Role
Route::post('/getUserWithRole', [MenuMasterController::class, 'getUserWithRole']);
Route::post('/saveUserRole', [MenuMasterController::class, 'saveUserRole']);
Route::post('/getSingleUserRole', [MenuMasterController::class, 'getSingleUserRole']);
Route::post('/getAllUserRole', [MenuMasterController::class, 'getAllUserRole']);


//Menu Master
Route::post('/showMenuGroup', [MenuMasterController::class, 'showMenuGroup']);
Route::post('/saveMenuList', [MenuMasterController::class, 'saveMenuList']);
Route::post('/deleteMenuList', [MenuMasterController::class, 'deleteMenuList']);
Route::post('/getMenuBarToShow', [MenuMasterController::class, 'getMenuBarToShow']);


Route::post('/getMenuSelect', [MenuMasterController::class, 'getMenuSelect']);
Route::post('/saveAccessPermission', [MenuMasterController::class, 'saveAccessPermission']);
Route::post('/getRoleWithAccessPremission', [MenuMasterController::class, 'getRoleWithAccessPremission']);
Route::post('/getSingleRoleAccess', [MenuMasterController::class, 'getSingleRoleAccess']);

//weight

Route::post('/getDynamicMaster', [WeightController::class, 'getDynamicMaster']);

//DynamicDialogue

Route::post('/saveDynamicDialogue', [DynamicDialogueController::class, 'saveDynamicDialogue']);

//DynamicComponent

Route::post('/saveDynamicComponent', [DynamicComponentController::class, 'saveDynamicComponent']);

