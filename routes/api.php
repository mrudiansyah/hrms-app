<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\WhatsappController;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileAttendanceController;
use App\Http\Controllers\Api\MobileInsideChecktimeController;
use App\Http\Controllers\Api\MobileSlipController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/send-message', [WhatsappController::class, 'sendMessage']);

// HCMIS proxy routes
Route::prefix('hcmis')->group(function () {
    Route::post('/login', [\App\Http\Controllers\HcmisController::class, 'login']);
    Route::get('/employees/datatables', [\App\Http\Controllers\HcmisController::class, 'employeesDatatables']);
    Route::post('/employees/datatables', [\App\Http\Controllers\HcmisController::class, 'employeesDatatables']);
    Route::post('/employees/show', [\App\Http\Controllers\HcmisController::class, 'employeesShow']);
    Route::post('/employees/store', [\App\Http\Controllers\HcmisController::class, 'employeesStore']);
    Route::put('/employees/update', [\App\Http\Controllers\HcmisController::class, 'employeesUpdate']);
    Route::delete('/employees/delete', [\App\Http\Controllers\HcmisController::class, 'employeesDelete']);
});

Route::prefix('mobile')->group(function () {
    Route::post('/login', [MobileAuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware('mobile.token')->group(function () {
        Route::get('/profile', [MobileAuthController::class, 'profile']);
        Route::post('/logout', [MobileAuthController::class, 'logout']);
        Route::get('/attendance', [MobileAttendanceController::class, 'index']);
        Route::post('/attendance', [MobileAttendanceController::class, 'store'])->middleware('throttle:10,1');
        Route::get('/inside-checktime', [MobileInsideChecktimeController::class, 'index']);
        Route::get('/slips/salary', [MobileSlipController::class, 'salary']);
        Route::get('/slips/overtime', [MobileSlipController::class, 'overtime']);
        Route::get('/slips/salary/{source}/{id}/pdf', [MobileSlipController::class, 'salaryPdf'])
            ->where('source', 'current|previous')
            ->whereNumber('id');
        Route::get('/slips/overtime/{source}/{id}/pdf', [MobileSlipController::class, 'overtimePdf'])
            ->where('source', 'current|previous')
            ->whereNumber('id');
    });
});