<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
	return redirect('/Dashboard');
});
Route::get('/Reset', function() {
    Artisan::call('cache:clear');
    dd("Cache Clear All");
});

Auth::routes(['verify' => true]);

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', function () {
	return redirect('/Dashboard');
});

use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserRoleController;
Route::resource('user-management', UserController::class);
Route::resource('role-management', RoleController::class);
Route::get('/userrole-management', [UserRoleController::class, 'index']);
Route::post('/userrole-management/selectUser', [UserRoleController::class, 'selectUser']);
Route::post('/userrole-management/addRole', [UserRoleController::class, 'addRole']);
Route::post('/userrole-management/removeRole', [UserRoleController::class, 'removeRole']);

use App\Http\Controllers\ManifestController;
Route::get('/manifest', [ManifestController::class, 'index']);
Route::post('/manifest/update-status/{id}', [ManifestController::class, 'updateStatus']);
Route::get('/manifest/calculation/{tgl}', [ManifestController::class, 'calculation']);
Route::get('/manifest/updateFinger/{tgl}', [ManifestController::class, 'updateFinger']);
Route::get('/manifest/sync/{tgl}', [ManifestController::class, 'syncManifest']);
Route::get('/manifest/export-pdf', [ManifestController::class, 'exportPdf']);
Route::post('/manifest/update-tl/{id}', [ManifestController::class, 'updateTL']);

use App\Http\Controllers\MasterApController;
use App\Http\Controllers\MasterAreaController;
use App\Http\Controllers\WorkingAreaController;
use App\Http\Controllers\OutsideAssignmentController;

Route::resource('master_ap', MasterApController::class);
Route::resource('master_area', MasterAreaController::class);
Route::resource('working_area', WorkingAreaController::class);
Route::resource('outside', OutsideAssignmentController::class);

use App\Http\Controllers\SecurityController;
Route::get('/scurity/assigment', [SecurityController::class, 'assignment']);
Route::post('/scurity/update-assignment', [SecurityController::class, 'updateAssignment']);
Route::post('/scurity/bulk-update-assignment', [SecurityController::class, 'bulkUpdateAssignment']);
Route::get('/scurity/permit', [SecurityController::class, 'permit']);
Route::post('/scurity/update-permit', [SecurityController::class, 'updatePermit']);

Route::get('/Dashboard', [App\Http\Controllers\DashboardController::class, 'index']);
Route::post('/Dashboard/Notifikasi', [App\Http\Controllers\DashboardController::class, 'notifikasi'])->name('Dashboard.Notifikasi');
Route::get('/ChangePassword', [App\Http\Controllers\DashboardController::class, 'change_password']);
Route::post('/ConfirmPassword', [App\Http\Controllers\DashboardController::class, 'confirm_password']);
Route::get('/PasswordShow/{password}', [App\Http\Controllers\DashboardController::class, 'password_show']);

Route::get('/payroll/summary_overtime/{start?}/{end?}', [App\Http\Controllers\PayrollController::class, 'summary_overtime']);
Route::post('/payroll/save_summary', [App\Http\Controllers\PayrollController::class, 'save_summary']);
Route::get('/payroll/tax_overtime/{start?}/{end?}', [App\Http\Controllers\PayrollController::class, 'tax_overtime']);
Route::get('/payroll/tax_overtime_excel/{start?}/{end?}', [App\Http\Controllers\PayrollController::class, 'tax_overtime_excel']);
Route::post('/payroll/import_rapel', [App\Http\Controllers\PayrollController::class, 'import_rapel']);
Route::get('/payroll/download_format_rapel', [App\Http\Controllers\PayrollController::class, 'download_format_rapel']);
Route::post('/payroll/tax_overtime/calculation', [App\Http\Controllers\PayrollController::class, 'tax_overtime_calculation']);
Route::get('/payroll/slip/overtime/{start}/{end}/{id_employee}', [App\Http\Controllers\PayrollController::class, 'slip_overtime']);
Route::get('/payroll/distribute_spl_slip/{start?}/{end?}', [App\Http\Controllers\PayrollController::class, 'distribute_spl_slip']);
Route::post('/payroll/collect_meals', [App\Http\Controllers\PayrollController::class, 'collect_meals']);

Route::get('/payroll/capture_assignment/{start?}/{end?}', [App\Http\Controllers\PayrollController::class, 'capture_assignment']);
Route::post('/payroll/save_summary_assignment', [App\Http\Controllers\PayrollController::class, 'save_summary_assignment']);
Route::get('/payroll/summary_assignment/{start?}/{end?}', [App\Http\Controllers\PayrollController::class, 'summary_assignment']);
Route::get('/payroll/tax_assignment_excel/{start?}/{end?}', [App\Http\Controllers\PayrollController::class, 'tax_assignment_excel']);
Route::get('/payroll/distribute_assignment_slip/{start?}/{end?}', [App\Http\Controllers\PayrollController::class, 'distribute_assignment_slip']);

