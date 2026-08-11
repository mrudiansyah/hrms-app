<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\WhatsappController;

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