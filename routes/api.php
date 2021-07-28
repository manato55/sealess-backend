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
Route::post('login', [LoginController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::namespace('Api')->group(function () {
        Route::post('logout', [LoginController::class, 'logout']);
        Route::get('me', [UserController::class, 'me']);
        Route::post('register-dep-admin', [RegisterController::class, 'registerDepAdmin'])->name('depAdmin');
        Route::post('send-register-email', [RegisterController::class, 'sendRegisterEmail'])->name('ordinary');

        Route::prefix('draft')->group(function () {
            Route::post('fetch-ppl', [DraftController::class, 'fetchSectionPpl']);
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
            Route::post('remove-task', [ReturnedController::class, 'removeTask']);
        });

        Route::prefix('completed')->group(function () {
            Route::post('discard-task', [CompletedController::class, 'discardTask']);
            Route::get('fetch-task/{choice}', [CompletedController::class, 'fetchCompletedTask']);
            Route::get('fetch-detail-task/{id}', [CompletedController::class, 'fetchCompletedTaskDetail']);
        });

        Route::prefix('route')->group(function () {
            Route::post('register-route', [RoutedController::class, 'registerRoute']);
            Route::post('remove-registered-route', [RoutedController::class, 'removeRegisteredRoute']);
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

