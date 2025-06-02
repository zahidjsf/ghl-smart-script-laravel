<?php

use App\Http\Controllers\AdminPanel\AccountController;
use App\Http\Controllers\AdminPanel\EmailTemplateController;
use App\Http\Controllers\AdminPanel\ProjectController;
use App\Http\Controllers\AdminPanel\PackageController;
use App\Http\Controllers\AdminPanel\SnapshotController;
use App\Http\Controllers\AdminPanel\SettingController;
use App\Http\Controllers\FrontPanel\FrontController;
use App\Http\Controllers\FrontPanel\ConnectionController;
use App\Http\Controllers\FrontPanel\SmartRewardController;
use App\Http\Controllers\AdminPanel\Auth\CustomAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CRMConnectionController;
use App\Http\Controllers\FrontPanel\CustomValueController;
use App\Http\Controllers\FrontPanel\CVSmartRewardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [CustomAuthController::class, 'loginForm'])->name('login');
Route::post('/login-post', [CustomAuthController::class, 'login'])->name('login-post');
Route::get('/', function () {

    if (auth()->check()) {
        return redirect()->route('accounts');
    }
    return redirect()->route('login');
});


Route::middleware('auth')->group(function () {

    Route::get('/logout', [CustomAuthController::class, 'logout'])->name('logout');

    Route::name('admin.')->group(function () {

        Route::get('accounts', [AccountController::class, 'index'])->name('accounts');
        Route::get('/getaccounts', [AccountController::class, 'getAccounts'])->name('get-accounts');
        Route::get('accountcreate', [AccountController::class, 'create'])->name('accountcreate');
        Route::post('accountstore', [AccountController::class, 'store'])->name('accountstore');
        Route::get('accountedit/{id}', [AccountController::class, 'edit'])->name('accountedit');
        Route::put('accounts/{account}', [AccountController::class, 'update'])->name('accountupdate');
        Route::get('accountdelete/{id}', [AccountController::class, 'destroy'])->name('accountdelete');
        Route::get('license-operation/project/{projId}/account/{accId}/type/{type}', [AccountController::class, 'licenseOperation'])->name('licenseoperation');
        Route::post('license-update', [AccountController::class, 'licenseUpdate'])->name('licenseUpdate');

        Route::get('email-template', [EmailTemplateController::class, 'index'])->name('emailtemplate');
        Route::post('/email-template', [EmailTemplateController::class, 'update'])->name('templateupdate');

        Route::get('projects', [ProjectController::class, 'index'])->name('projects');
        Route::get('/getprojects', [ProjectController::class, 'getprojects'])->name('get-projects');
        Route::get('projectcreate', [ProjectController::class, 'create'])->name('projectcreate');
        Route::post('projectstore', [ProjectController::class, 'store'])->name('projectstore');
        Route::get('projectedit/{id}', [ProjectController::class, 'edit'])->name('projectedit');
        Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projectupdate');
        Route::get('projectdelete/{id}', [ProjectController::class, 'destroy'])->name('projectdelete');


        Route::get('packages', [PackageController::class, 'index'])->name('packages');
        Route::get('/getpackages', [PackageController::class, 'getpackages'])->name('get-packages');
        Route::get('packagecreate', [PackageController::class, 'create'])->name('packagecreate');
        Route::post('packagestore', [PackageController::class, 'store'])->name('packagestore');
        Route::get('packageedit/{id}', [PackageController::class, 'edit'])->name('packageedit');
        Route::put('packages/{package}', [PackageController::class, 'update'])->name('packageupdate');
        Route::get('packagedelete/{id}', [PackageController::class, 'destroy'])->name('packagedelete');
        Route::get('package/{package}/project/{detail}', [PackageController::class, 'removeProject'])->name('removeProject');
        Route::delete('packages/{package}/projects/{detail}', [PackageController::class, 'removeProject'])
            ->name('packages.projects.remove');

        Route::get('snapshots', [SnapshotController::class, 'index'])->name('snapshots');
        Route::get('/getsnapshots', [SnapshotController::class, 'getsnapshots'])->name('get-snapshots');
        Route::get('snapshotcreate', [SnapshotController::class, 'create'])->name('snapshotcreate');
        Route::post('snapshotstore', [SnapshotController::class, 'store'])->name('snapshotstore');
        Route::get('snapshotedit/{id?}', [SnapshotController::class, 'edit'])->name('snapshotedit');
        Route::put('snapshots/{snapshot}', [SnapshotController::class, 'update'])->name('snapshotupdate');
        Route::get('snapshotdelete/{id}', [SnapshotController::class, 'destroy'])->name('snapshotdelete');


        Route::get('/settings', [SettingController::class, 'index'])->name('settings');
        Route::post('/settings-save', [SettingController::class, 'update'])->name('setting-save');
    });
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'member'])->name('frontend.')->group(function () {

    Route::get('/logout-member', [CustomAuthController::class, 'logout'])->name('logout-member');
    Route::get('/profile-detail', [FrontController::class, 'profileData'])->name('profile-detail');
    Route::get('/edit-profile/{id}', [FrontController::class, 'profileEdit'])->name('edit-profile');
    Route::put('/profile/{account}', [FrontController::class, 'update_profile'])->name('profile-update');

    Route::get('api-history', [FrontController::class, 'apiHistory'])->name('api-history');
    Route::get('get-apihistory', [FrontController::class, 'getApiHistory'])->name('get-apihistory');

    Route::get('dashboard', [FrontController::class, 'dashboard'])->name('dashboard');
    Route::post('agency-update', [FrontController::class, 'agencyUpdate'])->name('agencyUpdate');
    Route::get('/articles/{id}/{licensekey}', [ArticleController::class, 'show'])->name('articles');

    Route::get('/locations-display', [ConnectionController::class, 'locationDisplay'])->name('location-display');
    Route::post('/final-connect', [ConnectionController::class, 'finalConnect'])->name('final-connect');

    Route::name('smart_reward.')->prefix('smart-reward')->group(function () {

        Route::get('index', [SmartRewardController::class, 'index'])->name('index');
        Route::get('get-locations', [SmartRewardController::class, 'getLocations'])->name('getlocations');
        Route::get('/location-action-manage/{id?}/action/{action?}', [SmartRewardController::class, 'actionManage'])->name('action_manage');
        Route::post('location-update', [SmartRewardController::class, 'locationUpdate'])->name('locationUpdate');
        Route::post('setting-update', [SmartRewardController::class, 'settingUpdate'])->name('settingUpdate');
        Route::post('save-css', [SmartRewardController::class, 'saveCSS'])->name('savecss');

        Route::get('add-locations', [SmartRewardController::class, 'addLocations'])->name('addlocations');
        Route::post('location-add', [SmartRewardController::class, 'locationAdd'])->name('locationAdd');

        Route::get('cv-updater', [CustomValueController::class, 'cvUpdater'])->name('cvcupdater');
        Route::get('get-collections', [CustomValueController::class, 'getCollections'])->name('getcollections');


        Route::get('edit-collection/{id}', [CustomValueController::class, 'editCollection'])->name('editcollection');
        Route::get('add-collection', [CustomValueController::class, 'addCollection'])->name('addcollection');
        Route::get('copy-collection/{id}', [CustomValueController::class, 'copyCollection'])->name('copycollection');
        Route::post('copy-collection-save', [CustomValueController::class, 'duplicateCollection'])->name('duplicatecollection');
        Route::get('remove-collection/{id}', [CustomValueController::class, 'removeCollection'])->name('removecollection');
        Route::post('create-collection', [CustomValueController::class, 'createCollection'])->name('createcollection');
        Route::get('get-customvalues/{id}', [CustomValueController::class, 'getCustomValue'])->name('getcustomvalue');
        Route::post('update-collection-customvalues', [CustomValueController::class, 'updateCollectionCustomValues'])->name('updatecollectioncustomvalues');
        Route::post('updatecollection/{id}', [CustomValueController::class, 'updatecollection'])->name('updatecollection');

         Route::get('/cv-smartreward/{location}', [CVSmartRewardController::class, 'index'])->name('cv_smartreward');

    });
});


Route::prefix('authorization')->name('crm.')->group(function () {
    Route::get('/crm/oauth/callback', [CRMConnectionController::class, 'crmCallback'])->name('oauth_callback');
});
