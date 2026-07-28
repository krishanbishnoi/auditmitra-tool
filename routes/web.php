<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Artisan;
// use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PredefinedQuestionController;
use App\Http\Controllers\HelpChatController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GovernanceDashboard;
 

Route::get('/', function () {
    return view('auth.login');
});
Route::get('/index', function () {
    return view('index');
});
Route::get('/form', function () {
    return view('form');
});
Route::get('/testMail/{val}', 'AuditController@sendTestMail');

Auth::routes(['register' => false]);
Route::group(['middleware' => ['auth', 'switch.client.db']], function () {});

Route::get('/reportAutomationData', 'AuditController@reportAutomationData')->name('reportAutomationData');
Route::get('/reportAutomationDataColl', 'AuditController@reportAutomationDataColl')->name('reportAutomationDataColl');
Route::get('/reportAutomationDatagap', 'AuditController@reportAutomationDatagap')->name('reportAutomationDatagap');


Route::group(['middleware' => ['auth']], function () {

    Route::get('getClientDashboard', 'DashboardController@getClientDashboard')->name('getClientDashboard');
    Route::get('getNewClientDashboard', 'NewDashboardController@getNewClientDashboard')->name('getNewClientDashboard');

    //new route for dashboard test   
    Route::get('newdashboard', 'NewDashboardController@newdashboard')->name('newdashboard');

    Route::get('/get-audit-by-date', 'NewDashboardController@getAuditByDate')->name('get.audit.by.date');
    Route::get('/get-action-issues', 'NewDashboardController@getActionIssuesAjax');
    //Dashboard Ajax routes
    Route::post('getproductdata', 'AjaxController@getproductdata')->name('getproductdata');
    Route::post('getzonedata', 'AjaxController@getzonedata')->name('getzonedata');
    Route::post('audit_schedule', 'AjaxController@audit_schedule')->name('audit_schedule');
    Route::post('audit_schedule_detail', 'AjaxController@audit_schedule_detail')->name('audit_schedule_detail');
    Route::post('state_wise_data_top', 'AjaxController@state_wise_data_top')->name('state_wise_data_top');
    Route::post('state_wise_data_bot', 'AjaxController@state_wise_data_bot')->name('state_wise_data_bot');
    Route::post('agency_wise_data_top', 'AjaxController@agency_wise_data_top')->name('agency_wise_data_top');
    Route::post('agency_wise_data_bot', 'AjaxController@agency_wise_data_bot')->name('agency_wise_data_bot');
    Route::post('param_wise_data', 'AjaxController@param_wise_data')->name('param_wise_data');
    Route::post('param_detail_modal_view', 'AjaxController@param_detail_modal_view')->name('param_detail_modal_view');
    Route::post('param_compliance_data', 'AjaxController@param_compliance_data')->name('param_compliance_data');
    Route::post('param_compliance_table_view', 'AjaxController@param_compliance_table_view')->name('param_compliance_table_view');
    Route::post('pareto_state_wise', 'AjaxController@pareto_state_wise')->name('pareto_state_wise');
    Route::post('pareto_param_view', 'AjaxController@pareto_param_view')->name('pareto_param_view');
    Route::post('getCrossTabData', 'AjaxController@getCrossTabData')->name('getCrossTabData');


    Route::get('/test', 'HomeController@updateArtifact');

    Route::get('/run', 'HomeController@runmigration');

    // Route::any('/dashboard', '@index')->name('dashboard');
    Route::any('/dashboard', 'DashboardController@index')->name('dashboard');
    // Route::any('/dashboard', [DashboardController::class, 'qaAuditTable'])->name('dashboard');
    Route::get('/audit-dump-download', 'DashboardController@auditDumpDownload')->name('auditdumpdownload');
    Route::get('/schedule-audit-download', 'DashboardController@scheduleAuditDownload')->name('scheduleAuditDownload');

    Route::get('open-pointers-dump', 'DashboardController@openPointersDump')->name('openPointersDump');

    Route::get('/grades/{grade}', 'DashboardController@getGradeData')->name('getGradeData');
    Route::get('/audits/by-product/{productId}/region/{region}', 'DashboardController@getAuditsByProductAndRegion')->name('getAuditsByProductAndRegion');

    Route::get('/reportAutomation', 'AuditController@reportAutomation')->name('reportAutomation');
    Route::post('/reportDataUploader', 'AuditController@reportDataUploader')->name('reportDataUploader');
    Route::post('/dump-excel', 'UploadController@rawDumpAudit')->name('dump-excel');
    Route::get('/dump-excel-test', 'UploadController@rawDumpTest')->name('dump-excel-test');
    Route::get('/cycle', 'DashboardController@bottomProductParameter')->name('getAuditCycle');
    Route::get('/get-branch/{state_id}', 'DashboardController@getBranch')->name('getBranch');
    Route::get('/get-state-data/{state_id}', 'DashboardController@getStateData')->name('getStateData');
    Route::get('/get-agencies/{id}', 'DashboardController@getagencyOfCollection')->name('getagencyOfCollection');
    Route::get('/get-agencies-parameter/{agency_id}', 'DashboardController@getAgencyParameter')->name('getAgencyParameter');

    Route::post('/all-porudct', 'DashboardController@allProduct')->name('allProduct');
    Route::post('/fetch-map', 'DashboardController@fetchMapData')->name('fetchMap');
    Route::get('/home', 'DashboardController@index')->name('home');
    // Route::get('/home', 'HomeController@index')->name('home');
    Route::get('profile', 'UserController@profile')->name('profile');
    Route::patch('update_profile/{id}', 'UserController@updateProfile')->name('updateProfile');
    // to change password
    Route::get('/change-password', [App\Http\Controllers\UserController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('/change-password', [App\Http\Controllers\UserController::class, 'changePassword'])->name('password.makechange');
    // Route::group(['middleware' => ['role:zonal']], function () {
    Route::get('dowmload-user-excel', 'UserController@ExcelDownloadUser')->name('excelDownloadUser');

    Route::get('dowmload-branch-excel', 'BranchController@excelDownloadBranch')->name('excelDownloadBranch');

    Route::get('dowmload-branch-repo-excel', 'BranchRepoController@excelDownloadBranchRepo')->name('excelDownloadBranchRepo');
    Route::get('dowmload-agency-excel', 'AgencyController@excelDownloadAgency')->name('excelDownloadAgency');
    Route::get('dowmload-agency-repo-excel', 'AgencyRepoController@excelDownloadAgencyRepo')->name('excelDownloadAgencyRepo');
    Route::get('dowmload-yard-excel', 'YardController@excelDownloadYard')->name('excelDownloadYard');
    Route::get('dowmload-allocation-excel', 'AllocationController@excelDownloadAllocation')->name('excelDownloadAllocation');
    Route::post('dowmload-qa-changes-excel', 'AuditController@excelDownloadQaChanges')->name('excelDownloadQaChanges');
    Route::resource('user', 'UserController');

    Route::get('/auditor/request', 'UserController@show')->name('auditor.request');
    Route::post('/auditor/status/{user_id}', 'UserController@auditor_status')->name('auditor.status');

    Route::resource('userhierarchy', 'UserhierarchyController');
    // Route::delete('/userhierarchy/{id}', 'UserhierarchyController@destroy')->name('userhierarchy.destroy');
    Route::get('userhierarchy-excel-import', 'UserhierarchyController@showUserHeirarchyImport')->name('userhierarchyBulkUpload');
    Route::post('userhierarchyimport', 'UserhierarchyController@userHierarchyImport')->name('userhierarchyimport');
    Route::get('/download-sample-excel', 'UserhierarchyController@downloadsampleExcelUH')->name('uh.download.sampleexcel');


    // summet
    Route::get('user/{id}/disable', 'UserController@disable');  // for diable user
    Route::post('user/update-password', 'UserController@updatePassword')->name('update.password');  // for diable user

    // PermissionController
    Route::resource('permissions', 'PermissionController');

    //sumeet
    // });
    Route::resource('roles', 'RoleController');

    Route::resource('masters', MasterController::class);


    Route::get('user/status/{user_id}/{status}', 'UserController@change_user_status');
    Route::resource('yard', 'YardController');
    Route::get('yard-excel-import', 'YardController@showYardImport')->name('yardExcelUpload');
    Route::post('yardimport', 'YardController@yardImport')->name('yardimport');
    Route::resource('agency', 'AgencyController');
    Route::get('agency-excel-import', 'AgencyController@showAgencyImport')->name('agencyExcelUpload');
    Route::post('agencyimport', 'AgencyController@agencyImport')->name('agencyimport');
    Route::get('download-agency-sample', 'AgencyController@downloadAgencySample')->name('downloadAgencySample');
    Route::resource('branch', 'BranchController');
    Route::get('branch-excel-import', 'BranchController@showBranchImport')->name('branchExcelUpload');
    Route::post('branchimport', 'BranchController@branchImport')->name('branchimport');
    Route::get('location/city_view', 'LocationController@cityView')->name('location.city_view');
    Route::get('location/state_view', 'LocationController@stateView')->name('location.state_view');
    Route::resource('location', 'LocationController');
    Route::post('location/update', 'LocationController@update');
    Route::any('create-audit-cycle', 'AuditController@createCycle')->name('createCycle');
    // Route::post('create-audit-cycle', 'AuditController@createCycle')->name('createCycle');
    Route::get('list-audit-cycle', 'AuditController@listCycle')->name('listCycle');
    Route::post('/toggle-status', 'AuditController@toggleStatus')->name('toggle.status');

    Route::get('get-agencies-upload/{branch}', 'UploadController@getAgencies')->name('get-upload-agency');
    Route::get('get-branch-upload/{lob}', 'UploadController@getBranch')->name('get-upload-branch');
    Route::get('upload/gap-view', 'UploadController@gapView');
    Route::get('upload/gap-show', 'UploadController@gapView')->name('gapShow');
    Route::post('upload/gap-show', 'UploadController@gapShow')->name('getGap');
    Route::resource('upload', 'UploadController');
    Route::get('user-upload', 'UploadController@userUpload')->name('userUpload');
    Route::get('bulk-deactivate', 'UploadController@bulkDeactivate')->name('bulkDeactivate');
    Route::get('download-user', 'UploadController@downloadUser')->name('downloadUser');
    Route::get('download-user-auditor', 'UploadController@downloadUserAuditor')->name('downloaduserauditor');

    // Route::post('user-import', 'UploadController@userImport')->name('userImport');
    Route::post('user-import', 'UserController@userImport')->name('userImport');
    Route::post('user-hierarchy-import', 'UserController@userHierarchyImport')->name('userHierarchyImport');
    Route::resource('audit_agency', 'AuditAgencyController');


    Route::post('bulk_user_deactivate', 'UploadController@bulk_user_deactivate')->name('bulk_user_deactivate');


    // productcontroller
    Route::get('dowmload-product-excel', 'ProductController@excelDownloadProduct')->name('excelDownloadProduct');
    Route::get('product/hierarchy/view', 'ProductController@hierarchyView')->name('HierarchyView');
    Route::get('/product/hierarchy/{id}', 'ProductController@hierarchyEdit')->name('hierarchyEdit');
    //Route::get('product/hierarchy/{branch}/{product}/edit', 'ProductController@hierarchyEdit')->name('hierarchyEdit');
    Route::get('product/hierarchy', 'ProductController@hierarchy')->name('Hierarchy');
    Route::get('/product/hierarchy/{cm_id}', 'ProductController@getCmanagerDetails')->name('product.hierarchy');
    Route::delete('/product/hierarchy/{id}', 'ProductController@destroy')->name('producthierarchy.destroy');
    Route::post('product/do-hierarchy', 'ProductController@doHierarchy')->name('doHierarchy');
    Route::post('product/do-hierarchy-update', 'ProductController@doHierarchyUpdate')->name('doHierarchyUpdate');
    Route::resource('product', 'ProductController');


    // ProductattributeController
    Route::resource('productattribute', 'ProductattributeController');
    // Route::delete('/productattribute/{id}', 'ProductattributeController@destroy')->name('productattribute.destroy');

    Route::get('/get-regions', 'BranchController@getRegions');
    Route::get('/getStates/{id}', 'BranchController@getStates');
    Route::get('/getCities/{id}', 'BranchController@getCities');
    Route::get('/getAgency/{id}', 'YardController@getAgency');
    Route::get('/getAgencyManager/{id}', 'YardController@getAgencyManager');
    Route::resource('audit_alert_box', 'AuditAlertBoxController');
    Route::resource('beat_plan', 'BeatPlanController');
    //Intimation Mail Controller
    Route::resource('intimation_mail', 'IntimationMailController');
    Route::get('/product-attributes/{id}', 'IntimationMailController@getProductAttributes');
    Route::post('/get-agency-emails', 'IntimationMailController@getAgencyEmails');
    Route::get('intimation_mail/view/{id}', 'IntimationMailController@viewMail')->name('intimation_mail.viewMail');

    // Audit/Bulk Allocation Client
    Route::resource('audit_allocation', 'AuditAllocationController');
    Route::get('audit-allocation-excel-import', 'AuditAllocationController@showBulkAllocation')->name('auditallocationBulkUpload');
    Route::post('audit-allocation-import', 'AuditAllocationController@auditAllocationImport')->name('auditallocationimport');
    Route::get('/audit-allocation-sample-excel', 'AuditAllocationController@aaDownloadsampleExcel')->name('auditallocation.sampleexcel');
    Route::post('audit-allocation-bulk-delete', 'AuditAllocationController@bulkDelete')->name('auditallocation.bulkDelete');

    Route::resource('allocation', 'AllocationController');


    // qm sheet controller
    Route::resource('qm', 'QmSheetController');
    Route::resource('qm_sheet', 'QmSheetController');
    Route::get('qm_sheet/{sheet_id}/add_parameter', 'QmSheetController@add_parameter');
    Route::get('qm_sheet/{sheet_id}/list_parameter', 'QmSheetController@list_parameter');
    Route::get('qm_sheet/{sheet_id}/parameter', 'QmSheetController@list_parameter');
    Route::post('qm_sheet/store_parameters', 'QmSheetController@store_parameters')->name('store_parameters');
    Route::delete('delete_parameter/{id}', 'QmSheetController@delete_parameter')->name('delete_parameter');
    Route::get('parameter/{id}/edit', 'QmSheetController@edit_parameter');
    Route::post('update_parameter', 'QmSheetController@update_parameter')->name('update_parameter');
    Route::get('delete_sub_parameter/{id}', 'QmSheetController@delete_sub_parameter');
    Route::post('/qm_sheet/assign', 'QmSheetController@assignQmSheet')->name('qm_sheet.assign');
    Route::get('qm_sheet/assigned-users/{id}', 'QmSheetController@getAssignedUsers');

    // Route::group(['middleware' => ['role:collection']], function () {
    Route::get('audit_sheet/{qm_sheet_id}', 'AuditController@render_audit_sheet');
    Route::get('get_branch_detail/{id}/{type}/{product_id}', 'AuditController@renderBranch');
    Route::get('get_branch_detail_qc/{id}/{type}/{auditid}/{product_id}', 'AuditController@renderBranchQc');
    Route::get('get_product/{id}/{type}', 'AuditController@getProduct');
    Route::get('getProduct/{agencyId}/{type}', 'AuditController@getProductId')->name('getProductId');

    Route::get('audit_sheet/{qm_sheet_id}/edit', 'AuditController@render_audit_sheet_edit');
    Route::get('duplicate_sheet/{audit_id}', 'AuditController@duplicate_audit');
    Route::get('audit_sheet/{qm_sheet_id}/edit', 'AuditController@render_audit_sheet_edit')->name('edit_audit');
    Route::get('audit_sheet/{qm_sheet_id}/qcedit', 'AuditController@render_audit_sheet_qcedit');
    Route::get('getNewPDfUI/{auditID}', 'AuditController@getNewPDfUI');
    // added by nisha 
    Route::get('get_agencies/{id}', 'BeatPlanController@getBranchWiseAgencies');
    //QC
    Route::get('audit_detail/{qm_sheet_id}/edit', 'AuditController@detail_audit_sheet_edit');


    Route::get('audit_detail/{audit_id}/view', 'AuditController@render_audit_sheet_View')->name('view_submit_audited');
    Route::get('audit_detail_qc/{audit_id}/view', 'AuditController@render_audit_sheet_View_QC')->name('view_submit_audited_qc');
    Route::post('save_qc_status', 'AuditController@save_qc_status')->name('saveStatus');

    // });
    Route::get('get_qm_sheet_details_for_audit/{qm_sheet_id}', 'AuditController@get_qm_sheet_details_for_audit');
    Route::get('get_raw_data_for_audit/{comm_instance_id}', 'AuditController@get_raw_data_for_audit');
    Route::get('audited_list', 'AuditController@audited_list')->name('audited_list');
    Route::get('audited_search', 'AuditController@audited_list_new');
    Route::post('audited_search', 'AuditController@audited_list_new')->name('audited_search');
    Route::get('done_audited_list', 'AuditController@done_audited_list');
    Route::post('done_audited_list', 'AuditController@done_audited_list')->name('done_audited_list');
    Route::post('audited_list', 'AuditController@audited_list_Post')->name('audited_list_post');
    Route::post('allocation/store_audit', 'AuditController@store_audit');


    Route::post('agency/send-otp', 'AuditController@sendAgencyOtp');
    Route::post('agency/resend-otp', 'AuditController@resendAgencyOtp');
    Route::post('agency/verify-otp', 'AuditController@verifyAgencyOtp');
    Route::post('collection-manager/send-otp', 'AuditController@sendCollectionManagerOtp');
    Route::post('collection-manager/resend-otp', 'AuditController@resendCollectionManagerOtp');

    // Route::post('check-last-observation', 'AuditController@checkLastObservation');

    Route::post('allocation/update_audit', 'AuditController@update_audit');
    Route::post('allocation/update_audit_qc', 'AuditController@update_audit_qc');
    Route::get('get_reasons_by_type/{type_id}', 'AuditController@get_reasons_by_type');
    Route::resource('red-alert', 'RedAlertController');
    Route::get('download-file/{id}', 'RedAlertController@downloadFile');
    Route::resource('artifact', 'ArtifactController');
    Route::get('download-file-artifact/{id}', 'ArtifactController@downloadFile');
    Route::get('action/{sheet_id}/alert', 'ActionController@create');
    Route::get('action/{id}/view', 'ActionController@view');
    Route::get('action/list', 'ActionController@list')->name('action-list');
    Route::resource('action', 'ActionController');
    Route::get('download-branch', 'BluckUploadController@downloadBranchNew')->name('downloadBranch');
    Route::resource('bulkUpload', 'BluckUploadController');
    Route::resource('branchrepo', 'BranchRepoController');
    Route::get('branchrepo-excel-import', 'BranchRepoController@showBranchRepoImport')->name('branchrepoExcelUpload');
    Route::post('branchrepoimport', 'BranchRepoController@branchRepoImport')->name('branchrepoimport');
    Route::resource('agencyrepo', 'AgencyRepoController');
    Route::get('agencyrepo-excel-import', 'AgencyRepoController@showAgencyRepoImport')->name('agencyrepoExcelUpload');
    Route::post('agencyrepoimport', 'AgencyRepoController@agencyRepoImport')->name('agencyrepoimport');
    Route::resource('yardrepo', 'YardRepoController');
    Route::get('auditor_list/{status?}', 'AllocationController@getSheets')->name('auditor_list');
    Route::get('submit_audited_list', 'AllocationController@done_audited_list')->name('submit_audited_list');
    Route::get('save_audited_list', 'AllocationController@save_audited_list')->name('save_audited_list');
    Route::get('get_users/{value}/{type}', 'AuditController@getUsers')->name('getUsers');
    Route::get('reject-user/{email}/{auditId}/{type}', 'AuditController@rejectUsers')->name('rejectUsers');
    Route::get('save-user/{email}/{auditId}/{type}/{userid}', 'AuditController@saveUsers')->name('saveUsers');
    Route::get('download-action-artifact/{id}', 'ActionController@downloadFile');
    Route::get('test-email', 'RedAlertController@test');

    Route::resource('pdf', 'PdfController');
    Route::get('send', 'ActionController@sendNextReport');
    //Route::get('createAuth', 'ActionController@createAuth');
    //Route::get('/dashboard/getbranchdata', 'DashboardController@getBranchData');




    Route::resource('auditReport', 'AuditReportController');
    Route::get('createReports', 'AuditReportController@createReports');
    //Reports Reated Routes
    Route::get('reports', 'UploadController@reportindex')->name('reports');
    Route::post('internalexcel', 'UploadController@internalRawDump')->name('internalexcel');
    Route::any('edit-audit-cycle/{id}', 'AuditController@editCycle');



    // Audit Allocation Upload Client
    // Route::resource('audit_allocation','AuditAllocationController');
    // Route::get('audit-allocation-excel-import', 'AuditAllocationController@showBulkAllocation')->name('auditallocationBulkUpload');
    // Route::post('audit-allocation-import', 'AuditAllocationController@auditAllocationImport')->name('auditallocationimport');
    // Route::get('/audit-allocation-sample-excel', 'AuditAllocationController@aaDownloadsampleExcel')->name('auditallocation.sampleexcel');
    Route::resource('auditor_assign_cases', 'AuditorAssignCasesController');

    // V For Audit Agency -- Audit Allocation Assign Cases
    Route::resource('audit_allocation_assign', 'AuditAllocationAssignController');
    Route::get('/audit-allocation-assign', 'AuditAllocationAssignController@auditallocationassignexport')->name('auditallocation.assignexport');

    // V For Audit Agency -- Auditor Assign
    Route::resource('auditor_assign', 'AuditorAssignController');
    Route::get('auditor-assign-excel-import', 'AuditorAssignController@auditorBulkUpload')->name('auditorassignupload');
    Route::post('auditor-assign-import', 'AuditorAssignController@auditorAssignImport')->name('auditorassignimport');


    //closure data work
    Route::get('audit/closure-list/{status}', 'ClosureController@listAuditClosures')->name('audit.closure.list');
    Route::get('audit/closure-view/{closure_id}', 'ClosureController@viewAuditClosure')->name('audit.closure.view');
    Route::get('audit/closure/status/{id}/{status}', 'ClosureController@changeClosureStatus')->name('audit.closure.status');
    Route::post('artifact/approve/{id}', 'ClosureController@approveArtifact')->name('artifact.approve');
    Route::post('artifact/reject/{id}', 'ClosureController@rejectArtifact')->name('artifact.reject');
    Route::post('audit/resend-closure/{auditId}', 'ClosureController@resendAuditClosure')->name('audit.resend_closure');


    Route::get('audit-reports', 'ReportController@reportList')->name('audit.reportList');
    Route::get('audit-reports/download/{audit_id}', 'ReportController@downloadReports')->name('audit.downloadReports');
    Route::get('audit-closure/download/{audit_id}', 'ReportController@downloadClosureReports')->name('audit.downloadClosureReports');
    Route::get('/bulkDownloadForm', 'ReportController@bulkDownloadForm')->name('bulkDownloadForm');
    Route::get('audit-bulk-reports/download', 'ReportController@downloadBulkReports')->name('audit.downloadBulkReports');
    Route::get('audit-artifacts/download/{closure_id}', 'ClosureController@downloadArtifacts')->name('audit.downloadArtifacts');

    //V Collection Agency Trend Detail Page...
    Route::get('collection_agency_trend_list', 'CollectionAgencyTrendController@index')->name('collectiongencytrend');
    //V Collection National Manager Audit Details Page...
    Route::get('national-collection-manager-list', 'NCMController@index')->name('nationalCollectionManager');
    //Pareto Chart Other Page
    Route::get('paretochart', 'ParetoChartController@index')->name('paretochart');
    Route::get('paretochartdata', 'ParetoChartController@paretoChart')->name('paretochartdata');
    Route::get('repeat_issues', 'ParetoChartController@repeat_issues')->name('repeat_issues');

    Route::resource('support_tickets', SupportTicketController::class);

    Route::get('support_tickets/{id}/close', [SupportTicketController::class, 'showCloseForm'])
        ->name('support_tickets.showCloseForm');

    Route::post('support_tickets/{id}/close', [SupportTicketController::class, 'close'])
        ->name('support_tickets.close');

    Route::resource('help_topics', HelpTopicController::class);
    Route::resource('issue_types', IssueTypeController::class);
    Route::get('/get-issue-types', 'SupportTicketController@getIssueTypes')
        ->name('get.issue_types');


    Route::prefix('legal')->name('legal.')->group(function () {
        Route::get('dashboard', 'Legal\DashboardController@getClientDashboard')->name('dashboard');


        Route::resource('qm', 'Legal\QmSheetController');
        Route::resource('qm_sheet', 'Legal\QmSheetController');
        Route::get('qm_sheet/{sheet_id}/add_parameter', 'Legal\QmSheetController@add_parameter');
        Route::get('qm_sheet/{sheet_id}/list_parameter', 'Legal\QmSheetController@list_parameter');
        Route::get('qm_sheet/{sheet_id}/parameter', 'Legal\QmSheetController@list_parameter');
        Route::post('qm_sheet/store_parameters', 'Legal\QmSheetController@store_parameters')->name('store_parameters');
        Route::delete('delete_parameter/{id}', 'Legal\QmSheetController@delete_parameter')->name('delete_parameter');
        Route::get('parameter/{id}/edit', 'Legal\QmSheetController@edit_parameter');
        Route::post('update_parameter', 'Legal\QmSheetController@update_parameter')->name('update_parameter');
        Route::get('delete_sub_parameter/{id}', 'Legal\QmSheetController@delete_sub_parameter');



        // advocate
        Route::get('advocates', 'Legal\AdvocateController@index')->name('advocates.index');
        Route::get('advocates/create', 'Legal\AdvocateController@create')->name('advocates.create');
        Route::post('advocates/store', 'Legal\AdvocateController@store')->name('advocates.store');
        Route::get('advocates/{id}/edit', 'Legal\AdvocateController@edit')->name('advocates.edit');
        Route::post('advocates/{id}/update', 'Legal\AdvocateController@update')->name('advocates.update');
        Route::get('advocates/{id}/toggle', 'Legal\AdvocateController@toggleStatus')->name('advocates.toggle');


        Route::get('audit-assign', 'Legal\AuditAssignController@index')->name('audit.assign.index');

        Route::get('audit-assign/create', 'Legal\AuditAssignController@create')->name('audit.assign.create');

        Route::post('audit-assign/store', 'Legal\AuditAssignController@store')->name('audit.assign.store');

        Route::get('audit-assign/{id}/edit', 'Legal\AuditAssignController@edit')->name('audit.assign.edit');

        Route::post('audit-assign/{id}/update', 'Legal\AuditAssignController@update')->name('audit.assign.update');

        Route::get('assigned-legal-audits', 'Legal\AuditAssignController@assigned__legal_audits')->name('assigned.audits');



        Route::get('legal-audit/create', 'Legal\AuditController@create')
            ->name('audit.create');

        Route::get('legal-audit/{audit_id}/edit', 'Legal\AuditController@edit')
            ->name('audit.edit');

        Route::post('audit/store', 'Legal\AuditController@storeAudit')
            ->name('audit.store');

        Route::post('audit/parameters/store', 'Legal\AuditController@storeParameters')
            ->name('audit.parameters.store');

        Route::post('audit/sub-parameters', 'Legal\AuditController@getAuditSubParameters')
            ->name('audit.sub-parameters');

        Route::post('audit/update-status', 'Legal\AuditController@updateStatus')
            ->name('audit.update-status');

        Route::post(
            '/audit/parameter-result/store',
            'Legal\AuditController@storeParameterResult'
        )->name('audit.parameter.result.store');


        Route::get('legal_saved_audit/list', 'Legal\AuditController@leagl_saved_audit_list')
            ->name('saved.audit.list');

        Route::get('legal_submitted_audit/list', 'Legal\AuditController@leagl_submitted_audit_list')
            ->name('submitted.audit.list');

        Route::get('/legal-audit/view/{audit_id}', 'Legal\AuditController@viewAuditPage')
            ->name('audit.view');

        Route::post('legal-audit/get-summaries', 'Legal\AuditController@getParameterSummaries')
            ->name('audit.get.summaries');

        Route::post('legal-audit/save-summaries', 'Legal\AuditController@saveSummariesAndRecommendations')
            ->name('audit.save.summaries');

        // Route::get('legal-audit-report/download/{audit_id}', 'Legal\AuditController@downloadAuditReport')->name('audit.report.download');

        Route::get('audit/{audit}/send-draft-pdf', 'Legal\AuditController@sendDraftPdf')->name('audit.sendDraftPdf');

        // routes/web.php

        Route::get('/audit/{id}/generate-pdf', 'Legal\AuditController@downloadSavedPdf')->name('audit.pdf.download');
        Route::get('/audit/{id}/save-pdf', 'Legal\AuditController@generateAuditPdf')->defaults('saveToDb', false);
        // Route::get('/audit/{id}/reports', 'Legal\AuditController@getAuditReports');
        // Route::get('/report/{id}/download', 'Legal\AuditController@downloadSavedPdf');



        Route::any('/create-cycle', 'Legal\CycleController@createCycle')->name('createCycle');
        Route::get('/list-cycle', 'Legal\CycleController@listCycle')->name('listCycle');
        Route::post('/toggle-status-legal', 'Legal\CycleController@toggleStatus')->name('toggle.status');
        Route::any('edit-cycle/{id}', 'Legal\CycleController@editCycle');


        Route::get('legal-audit-reports', 'Legal\ReportController@reportList')->name('audit.reportList');

        Route::get('/legal-audit/intimation', 'Legal\IntimationController@create')->name('intimation.create');
        Route::post('/legal-audit/intimation', 'Legal\IntimationController@store');
        Route::get('/legal-audit/intimations/list', 'Legal\IntimationController@index')->name('intimation.list');


        Route::post('audit_schedule', 'Legal\AjaxController@audit_schedule')->name('audit_schedule');
        Route::post('audit_schedule_detail', 'Legal\AjaxController@audit_schedule_detail')->name('audit_schedule_detail');

        Route::get('/audit-dump-download', 'Legal\DashboardController@auditDumpDownload')->name('auditdumpdownload');
    });
});
Auth::routes();
// Route::group(['middleware' => ['auth','switch.client.db']], function () {
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/route-cache', function () {
    $exitCode = Artisan::call('route:cache');
    return 'Routes cache cleared';
});


Route::get('/audit-closure-justification/{closure_id}/{audit_id}/{link}', 'ClosureController@showAuditClosureForm')->name('audit.closure.form');
Route::post('/audit-closure-justification/{closure_id}/{audit_id}/{link}', 'ClosureController@submitAuditClosure')->name('audit.closure.submit');

Route::resource('client', 'ClientManagementController');

Route::get('masterqa_list', 'ClientManagementController@masterqa_list')->name('masterqa.list');
Route::get('/masterqa_client_list/{id}', 'ClientManagementController@masterqa_client_list')->name('masterqa.client_list');
Route::post('/masterqa_save', 'ClientManagementController@masterqa_save')->name('masterqa.save');
Route::get('masterqa/dashboard', 'ClientManagementController@masterQaDashboard')->name('masterqa.dashboard');
Route::post('masterqa/login-client', 'ClientManagementController@loginClient')->name('masterqa.login.client');
Route::get('back-to-masterqa', 'ClientManagementController@backToMasterQa')->name('back.masterqa');


Route::resource('module_permissions', 'ModulePermissionController');
Route::get('/module-allocation-view/{id}', 'ClientModuleAllocationController@module_allocation_view')->name('client_module_allocation.module_allocation_view');

Route::post('/module-allocation-update', 'ClientModuleAllocationController@module_allocation_update')->name('client_module_allocation.module_allocation_update');


Route::get('/location/{id}/state_edit', [LocationController::class, 'state_edit'])->name('location.state_edit');

Route::put('/location/{id}/update', [LocationController::class, 'updateState'])->name('location.updateState');

Route::get('location/city/delete/{id}', [LocationController::class, 'deleteCity'])->name('location.city_delete');

Route::get('location/city/edit/{id}', [LocationController::class, 'editCity'])->name('location.city_edit');
Route::put('location/city/update/{id}', [LocationController::class, 'updateCity'])->name('location.city_update');


Route::resource('cms', CmsPageController::class);


// Route::resource('support_tickets', SupportTicketController::class);

// Route::get('support_tickets', [SupportTicketController::class, 'index'])->name('support_tickets.index');
// Route::get('support_tickets/{id}/close', [SupportTicketController::class, 'showCloseForm'])->name('support_tickets.showCloseForm');
// Route::post('support_tickets/{id}/close', [SupportTicketController::class, 'close'])->name('support_tickets.close');


// Route::resource('help_topics', HelpTopicController::class);
// Route::resource('issue_types', IssueTypeController::class);
// Route::get('/get-issue-types', [SupportTicketController::class, 'getIssueTypes'])->name('get.issue_types');


Route::post('/switch-user', [UserController::class, 'switchUser'])->name('switch.user');



// PredefinedQuestionController

Route::get('/predefined-questions/create', [PredefinedQuestionController::class, 'create'])->name('predefined_questions.create');
Route::post('/predefined-questions', [PredefinedQuestionController::class, 'store'])->name('predefined_questions.store');
Route::get('/predefined-questions', [PredefinedQuestionController::class, 'index'])->name('predefined_questions.index');
Route::get('predefined-questions/{id}/edit', [PredefinedQuestionController::class, 'edit'])->name('predefined_questions.edit');
// Route::put('predefined-questions/{id}', [PredefinedQuestionController::class, 'update'])->name('predefined_questions.update');
Route::delete('predefined-questions/{id}', [PredefinedQuestionController::class, 'destroy'])->name('predefined_questions.destroy');
Route::put('predefined-question/{id}', [PredefinedQuestionController::class, 'update'])->name('predefined_question.update');


// HelpChatController
Route::post('/help-chat-answer-custom', [HelpChatController::class, 'customAnswer'])->name('help.chat.answer.custom');
// for question and answers

Route::get('/help-chat', [HelpChatController::class, 'index'])->name('help.chat');
Route::post('/help-chat/answer', [HelpChatController::class, 'getAnswer'])->name('help.chat.answer');


// Developer Important route
Route::get('/correctChecksheetPDF/{auditId}', 'AuditController@correctChecksheetPDF')->name('correctChecksheetPDF');
Route::get('/checksheetCorrect/{auditId}', 'AuditController@checksheetCorrect')->name('checksheetCorrect');
// });

//     Route::get('/current-db', function () {
//     return DB::connection()->getDatabaseName();
// })->middleware(['auth', 'switch.client.db']);




Route::get('/sub-parameter/{sub_param_id}/create-question', [QuestionController::class, 'create'])->name('create_question');
Route::post('/sub-parameter/store-question', [QuestionController::class, 'store'])->name('store_question');
// Questions
Route::get('/questions/{id}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
Route::post('/questions/{id}/update', [QuestionController::class, 'update'])->name('questions.update');
Route::delete('/questions/{id}', [QuestionController::class, 'destroy'])->name('questions.destroy');


// parameter category 
Route::get('category/create', [ParameterController::class, 'category_create'])->name('category.create');
Route::post('category/store', [ParameterController::class, 'category_store'])->name('category.store');
Route::get('category/{id}/edit', [ParameterController::class, 'category_edit'])->name('category.edit');
Route::put('category/{id}', [ParameterController::class, 'category_update'])->name('category.update');


Route::get('parameters/create', [ParameterController::class, 'create'])->name('parameters.create');

Route::get('parameters/{id}/edit', [ParameterController::class, 'edit'])->name('parameters.edit');
Route::put('parameters/{id}', [ParameterController::class, 'update'])->name('parameters.update');
Route::delete('parameters/{id}', [ParameterController::class, 'destroy'])->name('parameters.destroy');

Route::get('parameters/{id}/edit', [ParameterController::class, 'edit'])->name('parameters.edit');
Route::put('parameters/{id}', [ParameterController::class, 'update'])->name('parameters.update');
Route::delete('parameters/{id}', [ParameterController::class, 'destroy'])->name('parameters.destroy');




Route::post('parameters/store', [ParameterController::class, 'store'])->name('parameters.store');

Route::get('/sub-parameter/create', [ParameterController::class, 'createSubParameter'])->name('sub-parameters.create');
Route::post('/sub-parameter/store', [ParameterController::class, 'storeSubParameter'])->name('sub-parameters.store');
Route::get('/get-sub-parameters/{parameter_id}', [ParameterController::class, 'getSubParameters']);
Route::get('/sub-parameters/list', [ParameterController::class, 'listSubParameters'])->name('sub-parameters.list');
Route::get('/sub-parameters/by-parameter/{id}', [ParameterController::class, 'getSubParametersByParameter']);
// Route::get('/get-sub-parameters/{parameter_id}', [ParameterController::class, 'getSubParameters'])->name('get.sub.parameters');
Route::get('/get-sub-parameters/{id}', [ParameterController::class, 'getSubParameters'])->name('get.sub.parameters');

Route::get('/get-sub-parameters/{parameter}', [App\Http\Controllers\ParameterController::class, 'getSubParameters'])
    ->name('get.subparameters');


Route::post('check-last-observation', 'AuditController@checkLastObservation');
Route::post('check-last-repeat-observation', 'AuditController@checkLastRepeatObservation');

Route::post('/save-sub-parameter-issues', 'AuditController@saveSubParameterIssues');

// Route::post('newraghav/auditmitr/save-image', [AuditController::class, 'saveImage'])->name('save-image');

Route::get('/audit-closure/{audit_id}', 'AuditController@sendAuditClosureEmail');

Route::post('qm_sheet/{id}/active-status', 'QmSheetController@activeStatus')->name('qm_sheet.activeStatus');

Route::get('/audit/{audit_id}/download-artifacts', 'AuditController@downloadArtifacts')->name('audit.downloadArtifactsall');
Route::get('/audit/download-artifacts-all', 'AuditController@downloadArtifactsAll');




Route::post('/rewrite-remark', 'OpenAIVisionController@rewrite');




Route::get('/clear-cache', function () {
    // Clear the cache
    $exitCode = Artisan::call('cache:clear');
    // Rebuild the config cache
    $exitCode = Artisan::call('config:cache');
    // Optimize the framework
    $exitCode = Artisan::call('optimize');
    return 'DONE'; // Return a confirmation message
});


// use App\Http\Controllers\MappingController;

Route::get('/mapping', 'MappingController@index');

Route::get('/mapping/create', 'MappingController@create');

Route::post('/mapping/store', 'MappingController@store');
Route::post('/get-pillars', 'MappingController@getPillars');
Route::post('/get-touch-points', 'MappingController@getTouchPoints');
Route::post('/get-checkpoints', 'MappingController@getCheckpointParameters');


Route::get('/mapping-list', 'MappingController@listing');
Route::get('/mapping/{id}/parameters', 'MappingController@parameterMapping');
Route::post('/mapping/{id}/parameters/store', 'MappingController@storeParameters');
Route::get('/parameter-selection', 'MappingController@parameterSelection');
Route::post('/get-final-parameters', 'MappingController@getFinalParameters');



// v2
// Route::post('/export-audit-dump-v2','AuditController@exportAuditDumpV2');

// Route::get('/export-audit-dump-v2/{audit_id}','AuditController@exportAuditDumpV2');
Route::get('/audit-pdf-v2/{audit_id}', 'AuditController@downloadAuditPdfV2');

Route::get('/export-audit-dump-v2', 'AuditController@exportAuditDumpV2')->name('v2report');

Route::get('submitted-audit-data-v2/{audit_id}', 'AuditController@submittedAuditDataViewV2');



Route::post('audit/get-closure-artifact-data', 'AuditController@getClosureArtifactData');

Route::post('/audit/{id}/qc-approve', 'AuditController@qcApprove')
    ->name('audit.qc.approve');


    // new Route for governance Dashboard

    Route::get('/governanceDashboard', [GovernanceDashboard::class, 'gDashboard'])->name('governanceDashboard');