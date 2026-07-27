<?php

use App\Http\Controllers\Api\AuditController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

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



Route::middleware('auth:api')->get('/user', function (Request $request) {

	return $request->user();
});


#get audit cycle

Route::namespace('Api')->group(function () {
	Route::post('/loginUser', 'AuthController@login');
	Route::post('/updatePassword', 'AuthController@updatePassword');

	Route::post('/qmsheet_list', 'QmSheetController@qmsheet_list');
	// Route::post('auditList', 'AuditController@auditList');

	//V Profile Image API
	Route::post('updateProfileApi', 'AuthController@updateProfileApi');
	Route::get('getProfileImage', 'AuthController@getProfileImage');

	Route::post('dashboard', 'DashboardController@dashboard');

	Route::get('client_dashboard_counts', 'DashboardController@client_dashboard_counts');
	Route::get('getoverallAuditScore', 'DashboardController@getOverallAuditandScore');
	Route::get('getoverallAuditScoreAgencyWise', 'DashboardController@getoverallAuditScoreAgencyWise');
	Route::get('getProductCount_details', 'DashboardController@getProductCount_details');
	Route::get('getAuditAgencyData', 'DashboardController@getAuditAgencyData');
	Route::get('getCollectionAgencyTrend', 'DashboardController@getCollectionAgencyTrend');
	Route::get('getZoneWiseData', 'DashboardController@getZoneWiseData');
	Route::get('gradeWiseData', 'DashboardController@gradeWiseData');
	Route::get('parameterWiseScore', 'DashboardController@parameterWiseScore');
	Route::get('nationalCollectionManagerData', 'DashboardController@nationalCollectionManagerData');

	//Audit Agency Dashboard API
	Route::get('auditAgencyDashboardCount', 'DashboardController@auditAgencyDashboardCount');

	//Auditor Dashboard API
	Route::get('auditorDashboardCount', 'DashboardController@auditorDashboardCount');



	//Audit Sheets
	Route::post('getSheets', 'AllocationController@getSheets'); //Sheet List
	Route::post('savedAuditList', 'AllocationController@savedAuditList');
	Route::post('submittedAuditList', 'AllocationController@submittedAuditList');



	/* For Audit Form fill */
	Route::post('audit_sheet', 'AuditController@render_audit_sheet');
	Route::post('audit_sheet_edit', 'AuditController@render_audit_sheet_edit');

	Route::post('/get_agencies_from_city', 'AgencyController@get_agencies_from_city');
	Route::post('agency-details', 'AgencyController@agencyDetails');

	Route::post('getProduct', 'AuditController@getProduct');
	Route::post('getSubProduct', 'AuditController@getSubProduct');

	Route::post('renderBranch', 'AuditController@renderBranch');
	Route::post('storeAudit', 'AuditController@store_audit');

	Route::post('auditResult', 'AuditController@auditResult');
	Route::post('auditClosure', 'AuditController@auditClosure');
	// by sumeet
	Route::post('storeArtifact', 'ArtifactController@storeArtifact');
	// Route::post('transfer_artifact_from_temp_to_main', 'ArtifactController@transfer_artifact_from_temp_to_main');
	// by sumeet
	Route::post('storeRedAlert', 'RedAlertController@storeRedAlert');

	/* Beat Plan APis */
	Route::post('beatplanList', 'BeatPlanController@list');
	Route::post('beatPlanFormField', 'BeatPlanController@beatPlanFormField');
	Route::post('createBeatPlan', 'BeatPlanController@createBeatPlan');
	Route::post('editBeatPlan', 'BeatPlanController@editBeatPlan');
	Route::post('updateBeatPlan', 'BeatPlanController@updateBeatPlan');
	Route::post('deleteBeatPlan', 'BeatPlanController@deleteBeatPlan');

	/* Artifacts List */
	Route::post('artifactsList', 'ArtifactController@artifacts_list');
	Route::post('deleteArtifact', 'ArtifactController@deleteArtifact');
	Route::post('getdata', 'DummyController@getdata');
	Route::post('getdata/{id}', 'DummyController@getdata');
	Route::post('check/{id}', 'DummyController@checkuser');

	Route::post('artifact_audit_file_links', 'AuditController@artifact_audit_file_links');

	Route::get('get_audit_cycle', 'AuditController@get_audit_cycle');

	Route::get('formet_user_list', 'AuditController@formet_user_list');

	Route::post('agency/send-otp', 'AuditController@sendAgencyOtp');
	Route::post('agency/verify-otp', 'AuditController@verifyAgencyOtp');

	Route::post('collection_manager/send-otp', 'AuditController@sendCollectionManagerOtp');
	Route::post('collection_manager/verify-otp', 'AuditController@verifyCollectionManagerOtp');

	Route::post('auditor_assign_case_list', 'AuditController@auditor_assign_case_list');
	Route::post('auditor_assign_case_details', 'AuditController@auditor_assign_case_details');

	Route::post('/check-unsat-observation', 'AuditController@checkUnsatObservation');

	Route::post('/saveBasicInfoaudit', 'AuditController@saveBasicInfoaudit');

	Route::post('/saveQuestionWiseData', 'AuditController@saveQuestionWiseData');

	Route::post('/submitAudit', 'AuditController@submitAudit');

	Route::post('/allocated-modules', 'AllocationController@allocatedModuleList');

	Route::post('/saveUnsatIssueResponse', 'AuditController@saveUnsatIssueResponse');

	Route::post('/get_audit_parameter_result', 'AuditController@getAuditParameterResults');

	Route::post('/rewrite-remark', 'OpenAIVisionController@rewrite');


	// route to send result mail using postman
	Route::post('/send-audit-email', 'AuditController@sendAuditResultEmailApi');


	Route::prefix('v2')->group(function () {

		Route::post('/get_agencies_from_city', 'v2\AgencyController@get_agencies_from_city');

		Route::post('agency-details', 'v2\AgencyController@agencyDetails');

		Route::get('get_audit_cycle', 'v2\AuditController@get_audit_cycle');




		// Route::post('getSheets', 'v2/AllocationController@getSheets'); //Sheet List
		Route::post('/get-mapping-dropdown-data', 'v2\MappingController@getMappingDropdownData');
		Route::get('/get-mapping-location-data', 'v2\MappingController@getMappingLocation');
		Route::post('/get-pillars', 'v2\MappingController@getPillars');

		Route::post('/get-touch-points', 'v2\MappingController@getTouchPoints');
		Route::post('/get-final-parameters', 'v2\MappingController@getFinalParameters');



		Route::post('getSheets', 'v2\AllocationController@getSheets'); //Sheet List
		Route::post('savedAuditList', 'v2\AllocationController@savedAuditList');
		Route::post('submittedAuditList', 'v2\AllocationController@submittedAuditList');

		Route::post('/submitted_audit_data','v2\AuditController@submitted_audit_data');

		Route::post('/get_saved_parameter_details','v2\AuditController@get_saved_parameter_details');
		
		Route::post('/delete_saved_parameter_details','v2\AuditController@delete_saved_parameter_details');

		Route::post('saved_audit_details', 'v2\AuditController@saved_audit_details');


		Route::post('auditor_assign_case_list', 'v2\AuditController@auditor_assign_case_list');
		Route::post('auditor_assign_case_details', 'v2\AuditController@auditor_assign_case_details');


		Route::get('auditorDashboardCount', 'v2\DashboardController@auditorDashboardCount');



		Route::post('/saveBasicInfoaudit', 'v2\AuditController@saveBasicInfoaudit');
		Route::post('/save-audit-result-v2', 'v2\AuditController@saveAuditResultV2');
		Route::post('/get_audit_complete_result', 'v2\AuditController@get_audit_complete_result');
		Route::post('/submitAudit', 'v2\AuditController@submitAudit');


		Route::post('storeArtifact', 'v2\ArtifactController@storeArtifact');

		Route::post('artifactsList', 'v2\ArtifactController@artifacts_list');
		Route::post('deleteArtifact', 'v2\ArtifactController@deleteArtifact');




		// new
		Route::post('view-mapping', 'v2\MappingController@viewMapping');
		Route::get('view-sub-parameters', 'v2\MappingController@viewSubParameters');
		Route::post('view-parameter-details','v2\MappingController@viewParameterDetails');
	});
});




Route::post('/openai-scan', 'OpenAIVisionController@scan');

Route::post('/google-form-submit', 'OpenAIVisionController@store');
