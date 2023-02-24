<?php

use App\Http\Controllers\API\CommanMasterController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\GithubTokenController;
use App\Http\Controllers\API\SpyAppsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\AppsController;
use App\Http\Controllers\API\AdvertiseContoller;
//use App\Http\Controllers\API\RedisController;
use App\Http\Controllers\API\RawDataController;
use App\Http\Controllers\API\AllAppsController;
use App\Http\Controllers\API\PlatformController;
use App\Http\Controllers\API\ActivityHistoryController;
use App\Http\Controllers\API\ApiKeyListController;
use App\Http\Controllers\API\DummyPackageController;
use App\Http\Controllers\API\ExpenseRevenueController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\AdxMasterController;
use App\Http\Controllers\API\AdsMasterController;
use App\Http\Controllers\API\PartyMasterController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\AdsProVersionController;
use App\Http\Controllers\API\CompanyMasterController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('auth')->middleware('guest:api')->group(function () {

    Route::post('login', [AuthController::class, 'login']);

});

Route::middleware('auth:api')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);

    // employee //
    Route::resource('user', UserController::class);
    Route::post('updateProfile/{user_id}', [UserController::class, 'updateProfile']);

    // permission //
    Route::get('permission', [PermissionController::class, 'index']);
    Route::post('updatePermission/{role_id}', [PermissionController::class, 'updatePermission']);

    // task //
    Route::resource('task', TaskController::class);


    // task status change //
    Route::post('start_task/{task_id}', [TaskController::class, 'task_start']);
    Route::post('stop_task/{task_id}', [TaskController::class, 'stop_task']);
    Route::post('ready_testing_task/{task_id}', [TaskController::class, 'ready_testing_task']);
    Route::post('done_task/{task_id}', [TaskController::class, 'done_task']);
    Route::post('reworking_task/{task_id}', [TaskController::class, 'task_reworking']);
    Route::post('task_pending/{task_id}', [TaskController::class, 'task_pending']);
    Route::post('task_status_change/{task_id}', [TaskController::class, 'task_status_change']);


    // aso status change //
    Route::post('aso_task_start/{task_id}', [TaskController::class, 'aso_task_start']);
    Route::post('aso_task_stop/{task_id}', [TaskController::class, 'aso_task_stop']);
    Route::post('aso_task_done/{task_id}', [TaskController::class, 'aso_task_done']);
    Route::post('get_aso_task', [TaskController::class, 'getAsoTask']);


    //delete attchment //
    Route::post('deleteAttchment/{id}', [TaskController::class, 'deleteAttchment']);


    // show task //
    Route::get('ready_testing_task_show/{prev_person_id}', [TaskController::class, 'ready_testing_task_show']);
    Route::get('user_done_task_show/{prev_person_id}', [TaskController::class, 'user_done_task_show']);
    Route::get('tester_done_task_show', [TaskController::class, 'tester_done_task_show']);
    Route::get('tester_reworking_task_show/{prev_person_id}', [TaskController::class, 'reworking_task_show']);
    Route::get('get_delete_task', [TaskController::class, 'getDeleteTask']);
    Route::get('get_delete_task_show/{id}', [TaskController::class, 'getDeleteTaskShow']);
    Route::post('get_user_task', [TaskController::class, 'getUserTask']);
    Route::post('get_task_time', [TaskController::class, 'getTaskTime']);
    Route::get('get_app_no', [TaskController::class, 'get_app_no']);


    // notification //
    Route::get('getAllNotification', [NotificationController::class, 'getAllNotification']);
    Route::get('getUnreadNotification', [NotificationController::class, 'getUnreadNotification']);
    Route::get('markAsRead', [NotificationController::class, 'markAsRead']);
    Route::delete('deleteNotification/{id}', [NotificationController::class, 'deleteNotification']);


    // Analytics  //
    Route::post('employee_task_status', [TaskController::class, 'employeeTaskStatus']);
    Route::post('employee_task', [TaskController::class, 'employeeTask']);

    // apps //
    Route::resource('app', AppsController::class);
    Route::get('fetchAppData/{package_name}', [AppsController::class, 'fetchAppData']);
    Route::post('searchApp', [AppsController::class, 'search']);


    // all apps //
    Route::resource('allApp', AllAppsController::class);
    Route::post('appForceDelete', [AllAppsController::class, 'forceDelete']);
    Route::post('appRestore/{id}', [AllAppsController::class, 'appRestore']);
    Route::get('getDeletedApp', [AllAppsController::class, 'getDeletedApp']);

    // test All Apps //
    Route::get('testAllApps/{testAllApp}', [AllAppsController::class, 'testShow']);
    Route::post('testAllApps/{testAllApp}', [AllAppsController::class, 'testUpdate']);




    // apikey list //
    Route::resource('apikey_list', ApiKeyListController::class);

    // adx master //
    Route::resource('adx_master', AdxMasterController::class);

    // ads master //
    Route::resource('ads_master', AdsMasterController::class);

    // party master //
    Route::resource('party_master', PartyMasterController::class);

    // expense revenue //
    Route::resource('expense_revenue', ExpenseRevenueController::class);
    Route::post('store_expense', [ExpenseRevenueController::class, 'storeExpense']);
    Route::post('store_revenue', [ExpenseRevenueController::class, 'storeRevenue']);

    // all apps with DB 6//
    Route::get('storePackage', [AllAppsController::class, 'storePackage']);

    // apikey list //
    Route::post('assignApiKey', [ApiKeyListController::class, 'assignApiKey']);

    // privacy policy link update  //
    Route::post('updatePrivacypolicyLink/{id}', [AllAppsController::class, 'updatePrivacypolicyLink']);


    // AdsPro Version //
    Route::resource('adsProVersion', AdsProVersionController::class);

    // github token  //
    Route::resource('gitHubToken', GithubTokenController::class);

    // Company master //
    Route::resource('company_master', CompanyMasterController::class);

    // Comman master //
    Route::resource('comman_master', CommanMasterController::class);

    // Plateform //
    Route::resource('platform', PlatformController::class);
});


// app response //
Route::post('viewAppRes', [AllAppsController::class, 'viewAppRes']);

// Advertise //
Route::resource('appAd', AdvertiseContoller::class);

//redis get data //
Route::get('GetRedisData/{package_name}', [RawDataController::class, 'GetRedisData']);


// webcreon //
Route::get('getList', [AppsController::class, 'getPackageList']);
Route::get('getCurrentPackage/{package_name}', [AppsController::class, 'getCurrentPackage']);


// monetize setting //
Route::put('store_monetize', [AllAppsController::class, 'store_monetize']);

// test monetize setting //
Route::put('test_store_monetize', [AllAppsController::class, 'test_store_monetize']);

// Activity History //
Route::get('activity_history', [ActivityHistoryController::class, 'index']);
Route::post('filter_activity_history', [ActivityHistoryController::class, 'filterDateRangeOrUser']);


// dummy package //
Route::get('dummyPackage/{status_code?}', [DummyPackageController::class, 'index']);
Route::get('dummyPackage/store/{package_name}', [DummyPackageController::class, 'store']);


// redis db 6 data get //
Route::get('webcreon2/{package_name}', [AppsController::class, 'getDB6Data']);
Route::get('webcreon2', [AppsController::class, 'getDB6AllData']);
Route::post('webcreonSetData', [AppsController::class, 'setData']);

// search package_name //
Route::get('search/{package_name}', [AllAppsController::class, 'searchPackage']);


// test All Apps db 6 //
Route::get('getTestData/{package_name}', [AllAppsController::class, 'getTestData']);
Route::post('setTestData', [AllAppsController::class, 'setTestData']);


// get developer name //
Route::get('getDeveloperName', [AllAppsController::class, 'getDeveloperName']);
Route::get('searchAppByDeveloper/{developer}', [AllAppsController::class, 'searchAppByDeveloper']);

// AdsPro Version //
Route::get('getAdsProVersion', [AdsProVersionController::class, 'adsProVersion']);

// cron setting //
Route::resource('setting', SettingController::class);
Route::get('showSetting', [SettingController::class, 'show']);
Route::get('startAppDetailsUpdateCron', [SettingController::class, 'startAppDetailsUpdateCron']);
Route::get('stopAppDetailsUpdateCron', [SettingController::class, 'stopAppDetailsUpdateCron']);
Route::get('startCheckAppStatusCron', [SettingController::class, 'startCheckAppStatusCron']);
Route::get('stopCheckAppStatusCron', [SettingController::class, 'stopCheckAppStatusCron']);
Route::get('startWebCreonCron', [SettingController::class, 'startWebCreonCron']);
Route::get('stopWebCreonCron', [SettingController::class, 'stopWebCreonCron']);
Route::get('startSpyAppCron', [SettingController::class, 'startSpyAppCron']);
Route::get('stopSpyAppCron', [SettingController::class, 'stopSpyAppCron']);
Route::get('startSpyAppDetailsCron', [SettingController::class, 'startSpyAppDetailsCron']);
Route::get('stopSpyAppDetailsCron', [SettingController::class, 'stopSpyAppDetailsCron']);

Route::get('storeRedisData/{cursor?}', [RawDataController::class, 'storeRedisData']);

Route::get('getCount',[DashboardController::class,'getCount']);



Route::get('connectQueue', function(){
    dispatch(new App\Jobs\StoreRedisDataJob());
    dd('done');
});

// spy app //
Route::get('saveSpyApps',[SpyAppsController::class,'saveSpyApps']);
Route::post('saveSpyApp',[SpyAppsController::class,'saveSpyApp']);
Route::get('getSpyApps',[SpyAppsController::class,'getSpyApps']);




