<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/Dashboard');
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

Route::get('/Staff', [App\Http\Controllers\staff_controller::class, 'index']);
Route::post('/Staff/SelectUser', [App\Http\Controllers\staff_controller::class, 'selectUser']);
Route::post('/Staff/AddDept', [App\Http\Controllers\staff_controller::class, 'addDept']);
Route::post('/Staff/RemoveDept', [App\Http\Controllers\staff_controller::class, 'removeDept']);


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
Route::get('/payroll/tax_overtime_pdf/{start?}/{end?}', [App\Http\Controllers\PayrollController::class, 'tax_overtime_pdf']);
Route::get('/payroll/tax_overtime_approval/{tipe?}/{periode?}', [App\Http\Controllers\PayrollController::class, 'tax_overtime_approval']);
Route::post('/payroll/import_rapel', [App\Http\Controllers\PayrollController::class, 'import_rapel']);
Route::get('/payroll/download_format_rapel', [App\Http\Controllers\PayrollController::class, 'download_format_rapel']);
Route::post('/payroll/tax_overtime/calculation', [App\Http\Controllers\PayrollController::class, 'tax_overtime_calculation']);
Route::get('/payroll/slip/overtime/{start}/{end}/{id_employee}', [App\Http\Controllers\PayrollController::class, 'slip_overtime']);
Route::get('/payroll/distribute_spl_slip/{start?}/{end?}', [App\Http\Controllers\PayrollController::class, 'distribute_spl_slip']);
Route::post('/payroll/collect_meals', [App\Http\Controllers\PayrollController::class, 'collect_meals']);
Route::get('/payroll/overtime_detail', [App\Http\Controllers\PayrollController::class, 'overtime_detail']);
Route::post('/payroll/capture_rapel', [App\Http\Controllers\PayrollController::class, 'capture_rapel']);

Route::get('/payroll/capture_assignment/{start?}/{end?}', [App\Http\Controllers\PayrollController::class, 'capture_assignment']);
Route::post('/payroll/save_summary_assignment', [App\Http\Controllers\PayrollController::class, 'save_summary_assignment']);
Route::get('/payroll/summary_assignment/{start?}/{end?}', [App\Http\Controllers\PayrollController::class, 'summary_assignment']);
Route::get('/payroll/tax_assignment_excel/{start?}/{end?}', [App\Http\Controllers\PayrollController::class, 'tax_assignment_excel']);
Route::get('/payroll/tax_assignment_pdf/{start?}/{end?}', [App\Http\Controllers\PayrollController::class, 'tax_assignment_pdf']);
Route::get('/payroll/distribute_assignment_slip/{start?}/{end?}', [App\Http\Controllers\PayrollController::class, 'distribute_assignment_slip']);

//ESS
Route::get('/Profile', [App\Http\Controllers\ess_controller::class, 'index']);
Route::get('/SlipGaji', [App\Http\Controllers\ess_controller::class, 'employee_slip']);
Route::get('/Slip/{periode}/{id_employee}', [App\Http\Controllers\ess_controller::class, 'slip']);
Route::get('/SlipGaji/Temp/{periode}/{NIK}', [App\Http\Controllers\ess_controller::class, 'employee_slip_temp_download']);
Route::get('/SlipOT', [App\Http\Controllers\ess_controller::class, 'employee_slip_ot']);
Route::get('/payroll/slip/overtime_personal/{start}/{end}/{id_employee}', [App\Http\Controllers\PayrollController::class, 'slip_overtime_personal']);
Route::get('/payroll/slip/assignment/{start}/{end}/{id_employee}', [App\Http\Controllers\PayrollController::class, 'slip_assignment']);

Route::get('/Documents/{id}', [App\Http\Controllers\ess_controller::class, 'document']);
Route::post('/Document/Upload', [App\Http\Controllers\ess_controller::class, 'document_upload']);
Route::post('/Delete/Document', [App\Http\Controllers\ess_controller::class, 'delete_document']);
Route::get('/Document/Download/{id}', [App\Http\Controllers\ess_controller::class, 'document_download']);
Route::get('/Training/Invitation', [App\Http\Controllers\ess_controller::class, 'training_invitation']);
Route::get('/Training/Schedule/{id}', [App\Http\Controllers\ess_controller::class, 'training_schedule']);
Route::get('/Training/Actual/{id}/{id_doc}', [App\Http\Controllers\ess_controller::class, 'training_actual']);
Route::get('/FreeTest/{id_participant}', [App\Http\Controllers\ess_controller::class, 'free_test']);
Route::post('/Simpan/FreeTest', [App\Http\Controllers\ess_controller::class, 'simpan_free_test']);
Route::get('/PostTest/{id_participant}', [App\Http\Controllers\ess_controller::class, 'post_test']);
Route::post('/Simpan/PostTest', [App\Http\Controllers\ess_controller::class, 'simpan_post_test']);


//ESS End

Route::get('/FPPK', [App\Http\Controllers\fppk_controller::class, 'index']);
Route::get('/FPPK/create', [App\Http\Controllers\fppk_controller::class, 'create']);
Route::get('/FPPK/show/{id}', [App\Http\Controllers\fppk_controller::class, 'show']);
Route::get('/FPPK/edit/{id}', [App\Http\Controllers\fppk_controller::class, 'edit']);
Route::post('/FPPK/store', [App\Http\Controllers\fppk_controller::class, 'store']);
Route::put('/FPPK/update/{id}', [App\Http\Controllers\fppk_controller::class, 'update']);
Route::post('/FPPK/approve/{id}', [App\Http\Controllers\fppk_controller::class, 'approve']);
Route::delete('/FPPK/delete/{id}', [App\Http\Controllers\fppk_controller::class, 'destroy']);
Route::get('/FPPK/print/{id}', [App\Http\Controllers\fppk_controller::class, 'print']);
Route::get('/FPPK/export', [App\Http\Controllers\fppk_controller::class, 'export']);

// Renewal Routes
Route::get('/renewal', [App\Http\Controllers\RenewalController::class, 'index'])->name('renewal.index');
Route::get('/renewal/search/employee', [App\Http\Controllers\RenewalController::class, 'searchEmployee'])->name('renewal.search');  // ← UBAH INI
Route::get('/renewal/create', [App\Http\Controllers\RenewalController::class, 'create'])->name('renewal.create');
Route::post('/renewal', [App\Http\Controllers\RenewalController::class, 'store'])->name('renewal.store');
Route::get('/renewal/{id}', [App\Http\Controllers\RenewalController::class, 'show'])->name('renewal.show');
Route::get('/renewal/{id}/edit', [App\Http\Controllers\RenewalController::class, 'edit'])->name('renewal.edit');
Route::put('/renewal/{id}', [App\Http\Controllers\RenewalController::class, 'update'])->name('renewal.update');
Route::delete('/renewal/{id}', [App\Http\Controllers\RenewalController::class, 'destroy'])->name('renewal.destroy');
Route::post('/renewal/approve/{id}', [App\Http\Controllers\RenewalController::class, 'approve'])->name('renewal.approve');
Route::get('/renewal/{id}/print', [App\Http\Controllers\RenewalController::class, 'print'])->name('renewal.print');

//Migration
Route::get('/Admin/Employee', [App\Http\Controllers\admin_employee::class, 'index']);
Route::get('/Admin/Employee/Create', [App\Http\Controllers\admin_employee::class, 'createData']);

Route::post('/Admin/Employee/SelectDept', [App\Http\Controllers\admin_employee::class, 'selectDept']);
Route::post('/Admin/Employee/Select', [App\Http\Controllers\admin_employee::class, 'selectLeader']);
Route::post('/Admin/Employee/SelectCC', [App\Http\Controllers\admin_employee::class, 'selectCC']);
Route::post('/AgreementCheck2', [App\Http\Controllers\contract_controller::class, 'AgreementCheck2']);
Route::post('/Admin/Employee/Create/Submit', [App\Http\Controllers\admin_employee::class, 'insertData']);
Route::get('/Admin/Employee/Update/{id}', [App\Http\Controllers\admin_employee::class, 'updateData']);
Route::post('/Admin/Employee/Update/Submit', [App\Http\Controllers\admin_employee::class, 'saveData']);
Route::get('/Admin/Employee/Create/Delete/{id}/{id2}', [App\Http\Controllers\admin_employee::class, 'selectDelete']);
Route::post('/Admin/Employee/Create/Posisi', [App\Http\Controllers\admin_employee::class, 'saveBagian']);
Route::post('/Admin/Employee/Create/Detail', [App\Http\Controllers\admin_employee::class, 'saveDetail']);
Route::post('/Admin/Employee/Create/Family', [App\Http\Controllers\admin_employee::class, 'saveFamily']);
Route::post('/Admin/Employee/Kabupaten', [App\Http\Controllers\admin_employee::class, 'selectKab']);
Route::post('/Admin/Employee/Kecamatan', [App\Http\Controllers\admin_employee::class, 'selectKec']);
Route::post('/Admin/Employee/Desa', [App\Http\Controllers\admin_employee::class, 'selectDes']);
Route::post('/Admin/Employee/Link', [App\Http\Controllers\admin_employee::class, 'selectLink']);
Route::post('/Admin/Employee/Create/Address', [App\Http\Controllers\admin_employee::class, 'saveAddress']);
Route::post('/Admin/Employee/Create/Domicile', [App\Http\Controllers\admin_employee::class, 'saveDomicile']);
Route::post('/Admin/Employee/Create/Kontak', [App\Http\Controllers\admin_employee::class, 'saveKontak']);
Route::post('/Admin/Employee/Create/Education', [App\Http\Controllers\admin_employee::class, 'saveEducation']);
Route::post('/Admin/Employee/Create/Experience', [App\Http\Controllers\admin_employee::class, 'saveExperience']);
Route::post('/Admin/Employee/Create/Skill', [App\Http\Controllers\admin_employee::class, 'saveSkill']);
Route::get('/Admin/EmployeeBPJS', [App\Http\Controllers\admin_employee::class, 'bpjs']);
Route::post('/Admin/EmployeeBPJS', [App\Http\Controllers\admin_employee::class, 'bpjsUpdate']);
Route::post('/Admin/EmployeeBPJS/Kes', [App\Http\Controllers\admin_employee::class, 'bpjsUpdateKes']);
Route::post('/Admin/EmployeeBPJS/TK', [App\Http\Controllers\admin_employee::class, 'bpjsUpdateTK']);
Route::get('/Admin/Employee/Domiciles', [App\Http\Controllers\admin_employee::class, 'employeeDomiciles']);
Route::get('/Admin/Employee/Address', [App\Http\Controllers\admin_employee::class, 'employeeAddress']);
Route::get('/Admin/Employee/PSAB', [App\Http\Controllers\admin_employee::class, 'psab_list']);
Route::get('/Admin/Employee/Other/{tipe}', [App\Http\Controllers\admin_employee::class, 'otherEmployee']);
Route::post('/Admin/Employee/Other', [App\Http\Controllers\admin_employee::class, 'otherEmployeeSave']);
Route::get('/Admin/Employee/Record/Show/{periode}', [App\Http\Controllers\admin_employee::class, 'recordShow']);
Route::post('/Admin/Employee/Record/Submit', [App\Http\Controllers\admin_employee::class, 'recordSave']);
Route::post('/Admin/Employee/Record/Update', [App\Http\Controllers\admin_employee::class, 'recordUpdate']);
Route::get('/Admin/Employee/Other/{tipe}', [App\Http\Controllers\admin_employee::class, 'otherEmployee']);
Route::get('/Admin/Employee/Education', [App\Http\Controllers\admin_employee::class, 'education']);
Route::get('/Admin/Department/{id}/{periode}', [App\Http\Controllers\admin_employee::class, 'department']);

Route::get('/Status/Active', [App\Http\Controllers\contract_controller::class, 'active']);
Route::post('/Status/New', [App\Http\Controllers\contract_controller::class, 'newContract']);
Route::get('/Status/Permanen', [App\Http\Controllers\contract_controller::class, 'permanen']);
Route::get('/Status/Kontrak', [App\Http\Controllers\contract_controller::class, 'kontrak']);
Route::get('/Status/Magang', [App\Http\Controllers\contract_controller::class, 'magang']);
Route::get('/Status/Other', [App\Http\Controllers\contract_controller::class, 'other']);
Route::get('/Status/SAB', [App\Http\Controllers\contract_controller::class, 'SAB']);
Route::get('/Status/Draft', [App\Http\Controllers\contract_controller::class, 'draft']);
Route::get('/Status/NonActive/{periode}', [App\Http\Controllers\contract_controller::class, 'nonactive']);
Route::get('/Status/Reactive/{id}', [App\Http\Controllers\contract_controller::class, 'reactive']);
Route::get('/Status/Deactive/{id}', [App\Http\Controllers\contract_controller::class, 'deactive']);
Route::get('/Status/Delete/{id}', [App\Http\Controllers\contract_controller::class, 'delete']);
Route::get('/Status/Arsif/{periode}', [App\Http\Controllers\contract_controller::class, 'arsif']);

Route::get('/Status/KSK/{id}', [App\Http\Controllers\contract_controller::class, 'ksk']);
Route::get('/Status/KSK/Create/{id}', [App\Http\Controllers\contract_controller::class, 'kskCreate']);
Route::get('/Status/KSK/Refresh/{id}', [App\Http\Controllers\contract_controller::class, 'kskRefresh']);
Route::get('/Employee/KSK/Print/{id_ksk}', [App\Http\Controllers\employee_controller::class, 'kskPrint']);
Route::post('/Status/KSK/Target', [App\Http\Controllers\contract_controller::class, 'kskTarget']);
Route::get('/Status/KSK/Detail/{id}/{id2}', [App\Http\Controllers\contract_controller::class, 'kskDetail']);
Route::get('/Status/KSK/Print/{id}', [App\Http\Controllers\contract_controller::class, 'kskPrint']);
Route::get('/Status/KSK/Performance/{id}/{id2}', [App\Http\Controllers\contract_controller::class, 'kskPerformance']);
Route::post('/Status/KSK/Update', [App\Http\Controllers\contract_controller::class, 'kskUpdate']);
Route::post('/Status/KSKDetail/Refresh', [App\Http\Controllers\contract_controller::class, 'kskDetailRefresh']);

Route::get('/Employee/{id}/{id2}', [App\Http\Controllers\employee_controller::class, 'employee']);

Route::get('/Leader', [App\Http\Controllers\employee_controller::class, 'leader']);
Route::get('/Leader/{id}', [App\Http\Controllers\employee_controller::class, 'updateLeader']);
Route::post('/LeaderUpdate', [App\Http\Controllers\employee_controller::class, 'saveData']);

Route::get('/Setup', [App\Http\Controllers\setup_controller::class, 'index']);
Route::post('/Setup/Update', [App\Http\Controllers\setup_controller::class, 'setup_update']);
Route::post('/Setup/UpdateLimit', [App\Http\Controllers\setup_controller::class, 'setup_limit']);

Route::get('/Training/List/{type}/{category}', [App\Http\Controllers\training_controller::class, 'index']);
Route::post('/Training/Simpan/List', [App\Http\Controllers\training_controller::class, 'simpan_list']);
Route::get('/Training/Delete/List/{id}', [App\Http\Controllers\training_controller::class, 'delete_list']);
Route::get('/Training/Document/{id_doc}', [App\Http\Controllers\training_controller::class, 'training_document']);
Route::get('/Training/Examination', [App\Http\Controllers\training_controller::class, 'training_examination']);
Route::post('/Training/Simpan/Examination', [App\Http\Controllers\training_controller::class, 'simpan_examination']);
Route::post('/Training/Delete/Examination', [App\Http\Controllers\training_controller::class, 'delete_examination']);
Route::get('/Training/Question/{id}', [App\Http\Controllers\training_controller::class, 'training_question']);
Route::post('/Training/Simpan/Question', [App\Http\Controllers\training_controller::class, 'simpan_question']);
Route::post('/Training/Delete/Question', [App\Http\Controllers\training_controller::class, 'delete_question']);
Route::get('/Training/Periode/{periode}', [App\Http\Controllers\training_controller::class, 'training_periode']);
Route::post('/Training/Simpan/Plan', [App\Http\Controllers\training_controller::class, 'simpan_plan']);
Route::get('/Training/Plan/{id}', [App\Http\Controllers\training_controller::class, 'training_plan_participant']);
Route::post('/Training/Simpan/Supporting', [App\Http\Controllers\training_controller::class, 'simpan_supporting']);
Route::post('/Training/Delete/Supporting', [App\Http\Controllers\training_controller::class, 'delete_supporting']);
Route::post('/Training/Simpan/Supporting/Test', [App\Http\Controllers\training_controller::class, 'simpan_supporting_test']);
Route::get('/Training/Delete/Supporting/Test/{id}', [App\Http\Controllers\training_controller::class, 'delete_supporting_test']);
Route::post('/Training/Simpan/Plan/Participant', [App\Http\Controllers\training_controller::class, 'simpan_plan_participant']);
Route::post('/Training/Delete/Plan/Participant', [App\Http\Controllers\training_controller::class, 'delete_plan_participant']);
Route::get('/Training/Actual/{id}', [App\Http\Controllers\training_controller::class, 'training_actual_participant']);
Route::get('/Training/Actuals/{type}/{category}', [App\Http\Controllers\training_controller::class, 'training_actual_group']);
Route::get('/Training/Personal/{id_employee?}', [App\Http\Controllers\training_controller::class, 'training_personal']);

Route::post('/Training.FinishTest', [App\Http\Controllers\ess_controller::class, 'send_progress'])->name('Training.FinishTest');
Route::post('/Training.FinishTestPost', [App\Http\Controllers\ess_controller::class, 'send_progress_post'])->name('Training.FinishTestPost');
Route::post('/Training.DocPreview', [App\Http\Controllers\ess_controller::class, 'DocPreview'])->name('Training.DocPreview');

Route::get('/Permit',[App\Http\Controllers\permit_controller::class,'index']);
Route::post('/Permit/SelectApprove',[App\Http\Controllers\permit_controller::class,'selectApprove']);
Route::post('/Permit/Add',[App\Http\Controllers\permit_controller::class,'addPermit']);
Route::get('/Permit/Preview/{id}',[App\Http\Controllers\permit_controller::class,'previewPermit']);
Route::get('/Permit/Delete/{id}',[App\Http\Controllers\permit_controller::class,'deletePermit']);
Route::get('/Permit/Approves/{id}/{id1}',[App\Http\Controllers\permit_controller::class,'approvePermit']);
Route::get('/Permit/Approve/{id}/{id1}',[App\Http\Controllers\permit_controller::class,'approveSign']);
Route::get('/Permit/Personalia/{id}/{id1}',[App\Http\Controllers\permit_controller::class,'personaliaPermit']);
Route::get('/Permit/PersonaliaSign/{id}/{id1}',[App\Http\Controllers\permit_controller::class,'personaliaSign']);
Route::get('/Permit/Scurities/{id}/{id1}',[App\Http\Controllers\permit_controller::class,'scurityPermit']);
Route::post('/Permit/Scurity',[App\Http\Controllers\permit_controller::class,'scuritySign']);
Route::get('/Permit/Report/{id}/{id1}',[App\Http\Controllers\permit_controller::class,'reportPermit']);

Route::get('/Leave',[App\Http\Controllers\leave_controller::class,'index']);
Route::get('/Leave/Create/{id}',[App\Http\Controllers\leave_controller::class,'createNew']);
Route::get('/Leave/Employee/Delete/{id}',[App\Http\Controllers\leave_controller::class,'deleteEmployeeLeave']);
Route::get('/Leave/Inactive',[App\Http\Controllers\leave_controller::class,'inactiveLeave']);
Route::post('/Leave/LimitUpdate',[App\Http\Controllers\leave_controller::class,'limitUpdate']);
Route::get('/Leave/Employee/{id}',[App\Http\Controllers\leave_controller::class,'selectEmployee']);
Route::get('/Leave/Active/{id}',[App\Http\Controllers\leave_controller::class,'activated']);
Route::post('/Leave/Add',[App\Http\Controllers\leave_controller::class,'addLeave']);
Route::post('/Leave/Update/Opsi',[App\Http\Controllers\leave_controller::class,'updateOpsi']);
Route::get('/Leave/Sisa/{id}/{id2}',[App\Http\Controllers\leave_controller::class,'sisaCuti']);
Route::get('/Leave/Kurang/{id}/{id2}',[App\Http\Controllers\leave_controller::class,'kurangCuti']);
Route::post('/Leave/Count',[App\Http\Controllers\leave_controller::class,'leaveCount']);
Route::get('/Leave/Special',[App\Http\Controllers\leave_controller::class,'special']);
Route::get('/Leave/SKD/{id}/{id2}',[App\Http\Controllers\leave_controller::class,'skd']);
Route::get('/Leave/Approves/{id}/{id1}',[App\Http\Controllers\leave_controller::class,'approveLeave']);
Route::get('/Leave/Approve/{id}/{id2}/{id3}',[App\Http\Controllers\leave_controller::class,'approveSign']);
Route::get('/Leave/Legalizes/{id}/{id1}/{id3}',[App\Http\Controllers\leave_controller::class,'legalizeLeave']);
Route::get('/Leave/Reports/{id}/{id1}/{id3}',[App\Http\Controllers\leave_controller::class,'reportLeave']);

Route::get('/Download/{file}', [App\Http\Controllers\download_controller::class, 'index']);
Route::get('/Show/{file}', [App\Http\Controllers\download_controller::class, 'lihat']);

Route::get('/SKD/Image/{id}',[App\Http\Controllers\skd_controller::class,'image']);
Route::get('/SKD',[App\Http\Controllers\skd_controller::class,'index']);
Route::post('/SKD/NIK/Check',[App\Http\Controllers\skd_controller::class,'check']);
Route::get('/SKD/Exit',[App\Http\Controllers\skd_controller::class,'keluar']);
Route::post('/SKD/Compress',[App\Http\Controllers\skd_controller::class,'compress']);
Route::post('/SKD/Leave/Count',[App\Http\Controllers\skd_controller::class,'leaveCount']);

Route::get('/PolicyControl', [App\Http\Controllers\policy_controller::class, 'policy_control']);
Route::post('/PolicyControl/Save', [App\Http\Controllers\policy_controller::class, 'policy_control_save']);
Route::post('/PolicyControl/Delete', [App\Http\Controllers\policy_controller::class, 'policy_control_delete']);
Route::get('/PolicyArsifControl', [App\Http\Controllers\policy_controller::class, 'policyArsif_control']);

Route::get('/Policy', [App\Http\Controllers\policy_controller::class, 'index']);
Route::get('/PolicyArsif', [App\Http\Controllers\policy_controller::class, 'policyArsif']);
Route::post('/Policy/Save', [App\Http\Controllers\policy_controller::class, 'document_upload']);
Route::post('/Policy/Delete', [App\Http\Controllers\policy_controller::class, 'document_delete']);
Route::post('/Policy/Download', [App\Http\Controllers\policy_controller::class, 'document_download']);
Route::get('/Policy/Show', [App\Http\Controllers\policy_controller::class, 'document_show'])->name('policy.show');
Route::get('/PolicyLevel/{id_policy}', [App\Http\Controllers\policy_controller::class, 'policyLevel']);
Route::post('/PolicyLevel/Save', [App\Http\Controllers\policy_controller::class, 'policyLevelSave']);
Route::post('/Policy/Nonactive', [App\Http\Controllers\policy_controller::class, 'policyNonactive']);
Route::post('/Policy/Reactive', [App\Http\Controllers\policy_controller::class, 'policyReactive']);

Route::get('/Admin/Overtime',[App\Http\Controllers\admin_overtime::class,'index']);
Route::get('/Admin/Overtime/Draft/{id}',[App\Http\Controllers\admin_overtime::class,'viewDraft']);
Route::post('Admin.Overtime.GetListLine',[App\Http\Controllers\admin_overtime::class,'GetListLine'])->name('Admin.Overtime.GetListLine');
Route::post('/Admin/Overtime/Note',[App\Http\Controllers\admin_overtime::class,'saveNote']);
Route::post('/Admin/Overtime/Konten',[App\Http\Controllers\admin_overtime::class,'showKonten']);
Route::post('/Admin/Overtime/SelectReason', [App\Http\Controllers\admin_overtime::class, 'selectReason']);
Route::post('/Admin/Overtime/Detail/Save',[App\Http\Controllers\admin_overtime::class,'saveDetail']);
Route::get('/Admin/Overtime/Detail/Delete/{id}',[App\Http\Controllers\admin_overtime::class,'deleteDetail']);
Route::get('/Admin/Overtime/Detail/Before/{id}',[App\Http\Controllers\admin_overtime::class,'signBeforeAll']);
Route::post('/Admin/Overtime/Detail/Quota',[App\Http\Controllers\admin_overtime::class,'quotaOT']);
Route::post('/Admin/Overtime/Select',[App\Http\Controllers\admin_overtime::class,'selectOrder']);
Route::post('/Admin/Overtime/Disetujui',[App\Http\Controllers\admin_overtime::class,'selectApproved']);
Route::post('/Admin/Overtime/Diketahui',[App\Http\Controllers\admin_overtime::class,'selectSeen']);
Route::post('/Admin/Overtime/Create',[App\Http\Controllers\admin_overtime::class,'createspl']);
Route::get('/Admin/Overtime/Confirm/{id}',[App\Http\Controllers\admin_overtime::class,'confirmSPL']);
Route::get('/Admin/Overtime/Dept/{id}',[App\Http\Controllers\admin_overtime::class,'bodyOT']);
Route::get('/Admin/Overtime/Depts/{id}',[App\Http\Controllers\admin_overtime::class,'headOT']);
Route::get('/Admin/Overtime/Draft/{id}',[App\Http\Controllers\admin_overtime::class,'viewDraft']);
Route::get('/Admin/Overtime/Preview/{id}',[App\Http\Controllers\admin_overtime::class,'previewSPL']);
Route::post('/Admin/Overtime/Detail/Before',[App\Http\Controllers\admin_overtime::class,'signBefore']);
Route::post('/Admin/Overtime/Sales',[App\Http\Controllers\admin_overtime::class,'updateSales']);
Route::get('/Admin/Overtime/Approval/{id}',[App\Http\Controllers\admin_overtime::class,'approveSPL']);
Route::post('/Admin/Overtime/Detail/Status',[App\Http\Controllers\admin_overtime::class,'entryStatus']);
Route::get('/Admin/Overtime/Approval/Sign/{id}/{id2}',[App\Http\Controllers\admin_overtime::class,'signSPL']);
Route::get('/Admin/Overtime/Approval/Denied/{id}/{id2}',[App\Http\Controllers\admin_overtime::class,'deniedSPL']);
Route::get('/Admin/Overtime/Approval/Review/{id}/{id2}',[App\Http\Controllers\admin_overtime::class,'reviewSPL']);
Route::post('/Admin/Overtime/Detail/After',[App\Http\Controllers\admin_overtime::class,'signAfter']);
Route::get('/Admin/Overtime/Detail/After/{id}',[App\Http\Controllers\admin_overtime::class,'signAfterAll']);
Route::get('/Admin/Overtime/Verifications/{id}',[App\Http\Controllers\admin_overtime::class,'verificationOT']);
Route::get('/Admin/Overtime/Verifications2/{id}',[App\Http\Controllers\admin_overtime::class,'verificationOT2']);
Route::get('/Admin/Overtime/Verification/{id}',[App\Http\Controllers\admin_overtime::class,'verificationdetailOT']);
Route::post('/Admin/Overtime/Verification/Update',[App\Http\Controllers\admin_overtime::class,'verificationUpdate']);
Route::post('/Admin/Overtime/Verification/UpdateFinger',[App\Http\Controllers\admin_overtime::class,'verificationUpdateFinger']);
Route::get('/Admin/Overtime/Remove/{id}',[App\Http\Controllers\admin_overtime::class,'removeOvertime']);
Route::get('/Admin/Overtime/lock',[App\Http\Controllers\admin_overtime::class,'update_lock']);
Route::get('/Admin/Overtime/Opens',[App\Http\Controllers\admin_overtime::class,'open_lock_all']);

Route::get('ApprovalSPL', [App\Http\Controllers\ApprovalSPLController::class, 'index'])->name('ApprovalSPL.index');
Route::post('ApprovalSPL.GetDataPlanApproval', [App\Http\Controllers\ApprovalSPLController::class, 'GetDataPlanApproval'])->name('ApprovalSPL.GetDataPlanApproval');
Route::post('ApprovalSPL.ChartApproval', [App\Http\Controllers\ApprovalSPLController::class, 'ChartApproval'])->name('ApprovalSPL.ChartApproval');
Route::post('ApprovalSPL.GetDataCompleteApproval', [App\Http\Controllers\ApprovalSPLController::class, 'GetDataCompleteApproval'])->name('ApprovalSPL.GetDataCompleteApproval');
Route::post('ApprovalSPL.updateSales', [App\Http\Controllers\ApprovalSPLController::class, 'updateSales'])->name('ApprovalSPL.updateSales');
Route::post('ApprovalSPL.GetSalesAmmount', [App\Http\Controllers\ApprovalSPLController::class, 'GetSalesAmmount'])->name('ApprovalSPL.GetSalesAmmount');
Route::get('ApproveSPLAll', [App\Http\Controllers\ApprovalSPLController::class, 'ApproveSPLAll'])->name('ApproveSPLAll');
Route::get('LegalizeSPL', [App\Http\Controllers\ApprovalSPLController::class, 'LegalizeSPL'])->name('LegalizeSPL');
Route::get('LegalizeSPLAll', [App\Http\Controllers\ApprovalSPLController::class, 'LegalizeSPLAll'])->name('LegalizeSPLAll');
Route::post('LegalizeSPL.GetDataLegalize,', [App\Http\Controllers\ApprovalSPLController::class, 'GetDataLegalize'])->name('LegalizeSPL.GetDataLegalize');


// Route::get('/Leave/EmployeeDelete1/{id}',[App\Http\Controllers\leave_controller::class,'deleteEmployeeLeave1']);
// Route::get('/Leave/Legalize/{id}/{id2}',[App\Http\Controllers\leave_controller::class,'legalizeSign']);
// Route::get('/Leave/Report/{id}/{id2}',[App\Http\Controllers\leave_controller::class,'reportSign']);
// Route::get('/Leave/Delete/{id}',[App\Http\Controllers\leave_controller::class,'deleteLeave']);
// Route::get('/Leave/SKDApprove/{id}',[App\Http\Controllers\leave_controller::class,'skdApprove']);
// Route::get('/Leave/SKDRefuse/{id}',[App\Http\Controllers\leave_controller::class,'skdRefuse']);
// Route::get('/Leave/SKDDelete/{id}',[App\Http\Controllers\leave_controller::class,'skdDelete']);
// Route::get('/Leave/Mass/{id}',[App\Http\Controllers\leave_controller::class,'massLeave']);
// Route::get('/Leave/Create/{id}',[App\Http\Controllers\leave_controller::class,'createNew']);
// Route::post('/Leave/Lock',[App\Http\Controllers\leave_controller::class,'lock']);
// Route::post('/Leave/unLock',[App\Http\Controllers\leave_controller::class,'unlock']);
// Route::get('/Leave/CreateNow/{id}',[App\Http\Controllers\leave_controller::class,'createNow']);
// Route::get('/Leave/Legalizes_dirhr/{id}/{id1}/{id3}',[App\Http\Controllers\leave_controller::class,'legalizeLeave_DirHR']);

// Route::get('/Leave/Extend/{id}',[App\Http\Controllers\leave_controller::class,'extend']);
// Route::get('/Leave/Update',[App\Http\Controllers\leave_controller::class,'updateBalance']);
// Route::post('/Leave/AutoCreateCuti',[App\Http\Controllers\leave_controller::class,'AutoCreateCuti'])->name('/Leave/AutoCreateCuti');

// Route::post('/Leave/CCLimitUpdate',[App\Http\Controllers\leave_controller::class,'cclimitUpdate']);
// Route::post('/Leave/LeaveLimitCheck',[App\Http\Controllers\leave_controller::class,'leaveLimitCheck']);


//End Migration

// HCMIS UI test page (protected)
Route::get('/hcmis', function () {
    return view('hcmis.index');
})->middleware('auth');

Route::get('/hcmis/employees', function () {
    return view('hcmis.employee');
})->middleware('auth');