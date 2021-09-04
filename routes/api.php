<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ReturnedController;
use App\Http\Controllers\CompletedController;
use App\Http\Controllers\RoutedController;
use App\Http\Controllers\CompanyController;

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


Route::post('register', [RegisterController::class, 'register']);
Route::post('re-password', [RegisterController::class, 'linkIssuance']);
Route::post('re-register-password', [RegisterController::class, 'passwordReRegister'])->name('passwordReRegister');
Route::get('token-check/{token}', [RegisterController::class, 'tokenCheck']);
Route::get('password-token-check/{token}', [RegisterController::class, 'passwordTokenCheck']);
Route::post('official-registry', [RegisterController::class, 'officialRegistryOrdinaryUser'])->name('officalRegistry');
Route::post('official-admin-registry', [CompanyController::class, 'officialAdminRegistry'])->name('officalAdminRegistry');
Route::post('login', [LoginController::class, 'login']);
Route::get('admin-token-check/{token}', [CompanyController::class, 'adminTokenCheck']);



Route::middleware('auth:sanctum')->group(function () {
    Route::namespace('Api')->group(function () {
        Route::post('logout', [LoginController::class, 'logout']);
        Route::get('me', [UserController::class, 'me']);
        Route::get('fetch-dep-users', [UserController::class, 'fetchDepUsers']);
        Route::delete('delete-dep-user/{id}', [UserController::class, 'deleteDepUser']);
        Route::delete('delete-dep-admin-user/{id}', [UserController::class, 'deleteDepAdminUser']);
        Route::get('fetch-admin-users', [UserController::class, 'fetchAdminUsers']);
        Route::get('fetch-normal-users/{department}', [UserController::class, 'fetchNormalUsers']);
        Route::post('change-department', [UserController::class, 'changeDepartment'])->name('changeDep');
        Route::post('department-registry', [UserController::class, 'departmentRegistry'])->name('departmentRegistry');
        Route::post('section-registry', [UserController::class, 'sectionRegistry'])->name('sectionRegistry');
        Route::post('job-title-registry', [UserController::class, 'jobTitleRegistry'])->name('jobTitleRegistry');
        Route::get('fetch-department', [UserController::class, 'fetchDepartment']);
        Route::get('fetch-dep-sec', [UserController::class, 'fetchDepartmentANDSection']);

        Route::prefix('company')->group(function () {
            Route::post('edit-normal-user-info', [CompanyController::class, 'editNormalUserInfo'])->name('editDepUserInfo');
            Route::post('send-register-email', [CompanyController::class, 'sendRegisterEmail'])->name('ordinary');
            Route::post('register-dep-admin', [CompanyController::class, 'registerDepAdmin'])->name('depAdmin');
            Route::post('change-dep-admin-info', [CompanyController::class, 'changeDepAdminInfo'])->name('changeDepAdminInfo');
            Route::post('register-company', [CompanyController::class, 'registerCompany'])->name('companyRegister');
            Route::post('change-dep-info', [CompanyController::class, 'changeDepName'])->name('changeDepName');
            Route::post('change-sec-info', [CompanyController::class, 'changeSecName'])->name('changeSecName');
            Route::post('change-job-title', [CompanyController::class, 'changeJobTitle'])->name('changeJobTitle');
            Route::delete('delete-sec/{id}', [CompanyController::class, 'deleteSection']);
            Route::delete('delete-dep/{id}', [CompanyController::class, 'deleteDep']);
            Route::delete('delete-job-title/{id}', [CompanyController::class, 'deleteJobTitle'])->name('deleteJobTitle');
            Route::get('fetch-job-title', [CompanyController::class, 'fetchJobTitle']);
            Route::get('fetch-sections/{id}', [CompanyController::class, 'fetchSections']);
        });

        Route::prefix('draft')->group(function () {
            Route::get('fetch-ppl/{id}', [DraftController::class, 'fetchSectionPpl']);
            Route::post('search-task', [DraftController::class, 'searchTask']);
            Route::post('register-draft', [DraftController::class, 'registerDraft']);
            Route::get('fetch-unreached-task', [DraftController::class, 'fetchUnreachedTask']);
            Route::get('get-fiscal-year', [DraftController::class, 'getFiscalYear']);
            Route::get('selected-unreached-task/{id}', [DraftController::class, 'fetchSelectedUnreachedTask']);
        });

        Route::prefix('returned')->group(function () {
            Route::get('fetch-task', [ReturnedController::class, 'fetchReturnedTask']);
            Route::get('fetch-detail/{id}', [ReturnedController::class, 'fetchReturnedDetail']);
            Route::post('remove-file', [ReturnedController::class, 'removeFile']);
            Route::delete('remove-task/{id}', [ReturnedController::class, 'removeTask']);
        });

        Route::prefix('completed')->group(function () {
            Route::post('discard-task', [CompletedController::class, 'discardTask']);
            Route::get('fetch-task/{choice}', [CompletedController::class, 'fetchCompletedTask']);
            Route::get('fetch-detail-task/{id}', [CompletedController::class, 'fetchCompletedTaskDetail']);
        });

        Route::prefix('route')->group(function () {
            Route::post('register-route', [RoutedController::class, 'registerRoute'])->middleware('trim');
            Route::delete('remove-registered-route/{id}', [RoutedController::class, 'removeRegisteredRoute']);
            Route::post('agent-setting', [RoutedController::class, 'agentSetting']);
            Route::post('agent-status-2false', [RoutedController::class, 'agentStatus2False']);
            Route::post('agent-status-2true', [RoutedController::class, 'agentStatus2True']);
            Route::get('fetch-registered', [RoutedController::class, 'fetchRegisteredRoute']);
            Route::get('fetch-agent-status', [RoutedController::class, 'fetchAgentStatus']);
        });

        Route::prefix('progress')->group(function () {
            Route::get('fetch-in-progress/{offset}', [ProgressController::class, 'fetchPaginatedTaskInProgress']);
            Route::get('get-total-length', [ProgressController::class, 'fetchTaskInProgress']);
            Route::get('fetch-detail-task/{id}', [ProgressController::class, 'fetchDetailTask']);
            Route::post('fetch-file', [ProgressController::class, 'fetchFile']);
            Route::post('action-inprogress', [ProgressController::class, 'actionInProgress']);
            Route::get('fetch-recieved', [ProgressController::class, 'fetchRecievedTask']);
            Route::post('action-inescalation', [ProgressController::class, 'actionInEscalation']);
            Route::post('return', [ProgressController::class, 'returnToDrafter']);
        });
    });
});

