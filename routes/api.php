<?php

use App\Http\Controllers\API\AdsNetworkConroller;
use App\Http\Controllers\API\AllConsoleController;
use App\Http\Controllers\API\CommanMasterController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\GithubTokenController;
use App\Http\Controllers\API\GoogleAdsApiController;
use App\Http\Controllers\API\GooglePlayApiController;
use App\Http\Controllers\API\SpyAppsController;
use App\Http\Controllers\API\TelegramController;
use App\Http\Controllers\API\TodoListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\AppsController;
use App\Http\Controllers\API\AdvertiseContoller;
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
    Route::post('startTask/{id}', [TaskController::class, 'startTask']);
    Route::get('endTask/{id}', [TaskController::class, 'endTask']);
    Route::get('taskSendToTester/{id}', [TaskController::class, 'taskSendToTester']);
    Route::get('getCompletedTask', [TaskController::class, 'getCompletedTask']);
    Route::post('deleteLogoBanner', [TaskController::class, 'deleteLogoBanner']);
    Route::post('deleteScreenshots', [TaskController::class, 'deleteScreenshots']);


    // notification //
    Route::get('getAllNotification', [NotificationController::class, 'getAllNotification']);
    Route::get('getUnreadNotification', [NotificationController::class, 'getUnreadNotification']);
    Route::get('markAsRead', [NotificationController::class, 'markAsRead']);
    Route::delete('deleteNotification/{id}', [NotificationController::class, 'deleteNotification']);
    Route::get('markAsReadSpecific/{id}', [NotificationController::class, 'markAsReadSpecific']);

    // apps //
    Route::resource('app', AppsController::class);
    Route::get('fetchAppData/{package_name}', [AppsController::class, 'fetchAppData']);

    // all apps //
    Route::resource('allApp', AllAppsController::class);
    Route::get('allApp/{package_name}', [AllAppsController::class, 'show']);
    Route::post('allApp/{package_name}', [AllAppsController::class, 'update']);
    Route::post('appForceDelete', [AllAppsController::class, 'forceDelete']);
    Route::post('appRestore/{id}', [AllAppsController::class, 'appRestore']);
    Route::get('getDeletedApp', [AllAppsController::class, 'getDeletedApp']);
    Route::get('getRemovedApp/{appType}', [AllAppsController::class, 'getRemovedApp']);
    Route::post('searchApplication', [AllAppsController::class, 'searchApplication']);

    // test All Apps //
    Route::get('testAllApps/{testAllApp}', [AllAppsController::class, 'testShow']);
    Route::post('testAllApps/{testAllApp}', [AllAppsController::class, 'testUpdate']);

    // apikey list //
    Route::resource('apikey_list', ApiKeyListController::class);
    Route::post('assignApiKey', [ApiKeyListController::class, 'assignApiKey']);

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

    // privacy policy link update  //
    Route::post('updatePrivacypolicyLink/{id}', [AllAppsController::class, 'updatePrivacypolicyLink']);


    // AdsPro Version //
    Route::resource('adsProVersion', AdsProVersionController::class);

    // Telegram //
    Route::resource('telegram', TelegramController::class);

    // github token  //
    Route::resource('gitHubToken', GithubTokenController::class);

    // Company master //
    Route::resource('company_master', CompanyMasterController::class);

    // Comman master //
    Route::resource('comman_master', CommanMasterController::class);

    // Plateform //
    Route::resource('platform', PlatformController::class);

    // AdsNetwork //
    Route::resource('adsNetwork', AdsNetworkConroller::class);

    // TodoList //
    Route::resource('todoList', TodoListController::class);
    Route::get('task_todo/{task_id}', [TodoListController::class, 'task_todo']);
    Route::post('addDefaultTodo', [TodoListController::class, 'addDefaultTodo']);
    Route::get('getDefaultTodo', [TodoListController::class, 'getDefaultTodo']);

    // all console //
    Route::resource('console', AllConsoleController::class);
    Route::get('getConsoleManager', [AllConsoleController::class, 'getConsoleManager']);

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
Route::post('copyDataFromTo', [AppsController::class, 'CopyDataFromTo']);
Route::get('getWebCreonPackage', [AppsController::class, 'getWebCreonPackage']);
Route::get('webCreon2List', [AppsController::class, 'webCreon2List']);


// monetize setting //
Route::put('store_monetize', [AllAppsController::class, 'store_monetize']);

//generate packagename //
Route::get('generatePackageName/{name}', [AllAppsController::class, 'generatePackageName']);

// test monetize setting //
Route::put('test_store_monetize', [AllAppsController::class, 'test_store_monetize']);

// Activity History //
Route::get('activity_history', [ActivityHistoryController::class, 'index']);
Route::post('filter_activity_history', [ActivityHistoryController::class, 'filterDateRangeOrUser']);
Route::post('activityStore', [ActivityHistoryController::class, 'activityStore']);


// dummy package //
Route::get('dummyPackage/{status_code?}', [DummyPackageController::class, 'index']);
Route::get('dummyPackage/store/{package_name}', [DummyPackageController::class, 'store']);


// redis db 6 data get //
Route::get('webcreon2/{package_name}', [AppsController::class, 'getDB6Data']);
Route::get('webcreon2Db2/{package_name}', [AppsController::class, 'getDB2Data']);
Route::get('webcreon2', [AppsController::class, 'getDB6AllData']);
Route::post('webcreonSetData', [AppsController::class, 'setData']);
Route::post('getAppInfoWebCreon2/{packageName}', [AppsController::class, 'getAppInfoWebCreon2']);

// search package_name //
Route::get('search/{package_name}', [AllAppsController::class, 'searchPackage']);


// test All Apps db 6 //
Route::get('getTestData/{package_name}', [AllAppsController::class, 'getTestData']);
Route::post('setTestData', [AllAppsController::class, 'setTestData']);


// get developer name //
Route::get('getDeveloperName', [AllAppsController::class, 'getDeveloperName']);
Route::get('searchAppByDeveloper/{developer}/{status}', [AllAppsController::class, 'searchAppByDeveloper']);

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
Route::get('refreshCheckStatus', [SettingController::class, 'refreshCheckStatus']);

Route::get('storeRedisData/{cursor?}', [RawDataController::class, 'storeRedisData']);

Route::get('getCount', [DashboardController::class, 'getCount']);


Route::get('connectQueue', function () {
    dispatch(new App\Jobs\StoreRedisDataJob());
    dd('done');
});

// spy app //
Route::get('saveSpyApps', [SpyAppsController::class, 'saveSpyApps']);
Route::post('saveSpyApp', [SpyAppsController::class, 'saveSpyApp']);
Route::get('getSpyApps', [SpyAppsController::class, 'getSpyApps']);
Route::get('getSpyApp/{packageName}', [SpyAppsController::class, 'getSpyApp']);
Route::get('appStoreSpy/browse/{mode?}/{available?}/{query?}/{query_short?}/{query_description?}/{revenue?}/{downloads?}/{installs?}/{ipd?}/{size?}/{store?}/{type?}/{released?}/{ratings?}/{reviews?}/{updates?}/{dev?}/{similarapp?}/{builder?}/{address_country?}/{limit?}/{order?}/{dir?}/{bucket?}/{bucket_date?}/{wl?}/{inapp?}/{creatives?}/{website?}/{collection?}/{country?}/{category?}/{storepass?}/{wearos?}', [SpyAppsController::class, 'appBrowse']);


// expense revenue //
Route::get('getAppInfoByPackage/{packageName}', [ExpenseRevenueController::class, 'getAppInfoByPackage']);


// county //
Route::get('getCountry', [CommanMasterController::class, 'getCountry']);

// apikey redis db3 //
Route::get('getRedisApiKey', [ApiKeyListController::class, 'getRedisApiKey']);
Route::post('setRedisApiKey', [ApiKeyListController::class, 'setRedisApiKey']);




//google play //

Route::get('play/apps/{id}', [GooglePlayApiController::class, 'GetGooglePLayAppById']);
Route::post('play/apps', [GooglePlayApiController::class, 'SearchGooglePlayAppsByQuery']);
Route::post('play/apps/query', [GooglePlayApiController::class, 'SearchGooglePlayAppsByQueryPost']);
Route::post('play/apps/{id}/reviews', [GooglePlayApiController::class, 'appReview']);
Route::get('play/info/countries', [GooglePlayApiController::class, 'GetGooglePlayAppAvailableCountry']);
Route::get('play/info/languages', [GooglePlayApiController::class, 'GetGooglePlayAppAvailableLanguage']);
Route::post('play/developers/{id}', [GooglePlayApiController::class, 'getDeveloper']);
Route::post('play/developers', [GooglePlayApiController::class, 'devSearch']);
Route::post('play/esimates', [GooglePlayApiController::class, 'getAppsEsimates']);
Route::post('play/suggestions', [GooglePlayApiController::class, 'getSuggest']);
Route::post('play/liveops', [GooglePlayApiController::class, 'getEvents']);


Route::post('db3', [AllAppsController::class, 'db3']);

// google ads api //
Route::get('GetAllNetwork', [GoogleAdsApiController::class, 'GetAllNetwork']);
Route::get('GetCurrentNetwork', [GoogleAdsApiController::class, 'GetCurrentNetwork']);
Route::get('index', [GoogleAdsApiController::class, 'index']);
Route::get('sendTelegramMessage', [\App\Http\Controllers\GitController::class, 'sendTelegramMessage']);









