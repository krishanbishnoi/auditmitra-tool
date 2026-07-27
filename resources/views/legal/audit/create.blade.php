@extends('layouts.master')

@section('content')
    <div id="auditForm">
        @csrf
        <input type="hidden" id="audit_id" value="{{ $audit->id ?? '' }}">

        <!-- Progress Steps Indicator -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body py-3">
                <div class="steps-progress">
                    <div class="step active" data-step="1">
                        <div class="step-circle">1</div>
                        <div class="step-label">Audit Details</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-circle">2</div>
                        <div class="step-label">Select Parameters</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-circle">3</div>
                        <div class="step-label">Parameter Audit</div>
                    </div>
                    <div class="step" data-step="4">
                        <div class="step-circle">4</div>
                        <div class="step-label">Review & Submit</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= STEP 1 ================= --}}
        <div class="card mb-4 step-card border-primary" id="step1">
            <div class="card-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="step-number mr-3">1</div>
                    <div>
                        <h5 class="mb-0">Audit Details</h5>
                        <small class="opacity-75">Basic information about the audit</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Empanelled Advocate <span class="text-danger">*</span></label>
                        <select id="advocate_id" class="form-select " {{ isset($audit) ? 'disabled' : '' }}>
                            <option value="">Select Advocate</option>
                            @foreach ($advocates as $a)
                                <option value="{{ $a->advocate_id }}"
                                    {{ isset($audit) && $audit->advocate_id == $a->advocate_id ? 'selected' : '' }}>
                                    {{ $a->advocate_name }}
                                </option>
                            @endforeach
                        </select>
                        @if (isset($audit))
                            <input type="hidden" id="advocate_id_hidden" value="{{ $audit->advocate_id }}">
                            <input type="hidden" id="advocate_name_hidden" value="{{ $audit->advocate_name }}">
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Audit Date <span class="text-danger">*</span></label>
                        <input type="date" id="audit_date" class="form-control" value="{{ $audit->audit_date ?? '' }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Legal Manager</label>
                        <input type="text" id="legal_manager" class="form-control" placeholder="Enter legal manager name"
                            value="{{ $audit->legal_manager ?? '' }}">
                    </div>
                </div>

                

                <!-- NEW FIELDS ADDED HERE -->
                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Empanelled From <span class="text-danger">*</span></label>
                        <input type="date" id="empanelled_from" class="form-control"
                            value="{{ $audit->empanelled_from ?? '' }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Auditor Name <span class="text-danger">*</span></label>
                        <input type="text" id="auditor_name" class="form-control" placeholder="Enter auditor name"
                            value="{{ $audit->auditor_name ?? '' }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Advocate Artifact</label>
                        <input type="file" id="auditor_artifact_image" class="form-control" accept="image/*,.pdf">
                        <small class="text-muted">Upload auditor's signature or stamp (Image or PDF)</small>
                        @if (isset($audit) && $audit->auditor_artifact_image)
                            <div class="mt-2">
                                <small class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    File already uploaded:
                                    <a href="{{ asset('storage/' . $audit->auditor_artifact_image) }}" target="_blank"
                                        class="text-decoration-none">
                                        View Current File
                                    </a>
                                </small>
                                <br>
                                <small class="text-muted">Upload new file to replace existing one</small>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Address</label>
                        <input type="text" id="address" class="form-control" placeholder="Enter address"
                            value="{{ $audit->address ?? '' }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">State</label>
                        <input type="text" id="state" class="form-control" placeholder="Enter state"
                            value="{{ $audit->state ?? '' }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                        <input type="text" id="location" class="form-control" placeholder="Enter location"
                            value="{{ $audit->location ?? '' }}">
                    </div>
                </div>
                <!-- END OF NEW FIELDS -->

                <div class="mt-4 pt-3 border-top">
                    <button class="btn btn-primary px-5" id="saveAuditBtn">
                        <i class="fas fa-arrow-right mr-2"></i>
                        {{ isset($audit) ? 'Update & Continue' : 'Save & Continue' }}
                    </button>
                </div>
            </div>
        </div>

        {{-- ================= STEP 2 ================= --}}
        <div class="card mb-4 step-card d-none" id="step2">
            <div class="card-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="step-number mr-3">2</div>
                    <div>
                        <h5 class="mb-0">Select Audit Parameters</h5>
                        <small class="opacity-75">Choose parameters to audit</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle mr-2"></i>
                    @if (isset($audit) && isset($selectedParameters) && count($selectedParameters) > 0)
                        <span id="editModeAlert">
                            You are editing an existing audit. Previously selected parameters are checked.
                            You can modify your selection.
                        </span>
                    @else
                        Select one or more parameters to include in this audit. All selected parameters will be audited in
                        the next step.
                    @endif
                </div>

                <div class="row" id="parametersContainer">
                    @foreach ($parameters as $p)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="parameter-card">
                                <div class="form-check">
                                    <input class="form-check-input paramCheck" type="checkbox"
                                        value="{{ $p->id }}" data-qm="{{ $p->qm_sheet_id }}"
                                        id="param_{{ $p->id }}"
                                        {{ isset($selectedParameters) && in_array($p->id, $selectedParameters) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="param_{{ $p->id }}">
                                        <div class="parameter-label">
                                            <span class="fw-semibold">{{ $p->parameter }}</span>
                                            <small class="text-muted d-block">QM Sheet: {{ $p->qm_sheet_id }}</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                    <button class="btn btn-outline-secondary px-4" id="backToStep1">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </button>
                    <button class="btn btn-primary px-5" id="saveParamsBtn">
                        Continue <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- ================= STEP 3 ================= --}}
        <div class="card mb-4 step-card d-none" id="step3">
            <div class="card-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="step-number mr-3">3</div>
                    <div>
                        <h5 class="mb-0">Parameter Wise Audit</h5>
                        <small class="opacity-75">Add remarks and upload artifacts for each parameter</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Please save each parameter before proceeding to the next step. You can navigate between parameters using
                    tabs.
                </div>

                <div id="subParameterSection"></div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                    <button class="btn btn-outline-secondary px-4" id="backToStep2">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </button>
                    <button class="btn btn-primary px-5" id="step3SaveNext" disabled>
                        <i class="fas fa-check-circle mr-2"></i>Review & Continue
                    </button>
                </div>
            </div>
        </div>

        {{-- ================= STEP 4 (REVIEW) ================= --}}
        <div class="card mb-4 step-card d-none" id="step4">
            <div class="card-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="step-number mr-3">4</div>
                    <div>
                        <h5 class="mb-0">Review & Submit</h5>
                        <small class="opacity-75">Review all details before final submission</small>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <button class="btn btn-outline-secondary mb-4" id="backToStep3">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Edit
                </button>

                <!-- ===== AUDIT DETAILS ===== -->
                <div class="review-section mb-5">
                    <h5 class="mb-3 border-bottom pb-2">
                        <i class="fas fa-clipboard-list mr-2"></i>Audit Details
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <div class="review-card">
                                <label class="review-label">Empanelled Advocate</label>
                                <div class="review-value" id="review_advocate">-</div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="review-card">
                                <label class="review-label">Audit Date</label>
                                <div class="review-value" id="review_audit_date">-</div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="review-card">
                                <label class="review-label">Legal Manager</label>
                                <div class="review-value" id="review_legal_manager">-</div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="review-card">
                                <label class="review-label">Address</label>
                                <div class="review-value" id="review_address">-</div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="review-card">
                                <label class="review-label">State</label>
                                <div class="review-value" id="review_state">-</div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="review-card">
                                <label class="review-label">Location</label>
                                <div class="review-value" id="review_location">-</div>
                            </div>
                        </div>

                        <!-- NEW FIELDS FOR REVIEW -->
                        <div class="col-md-6 col-lg-4">
                            <div class="review-card">
                                <label class="review-label">Empanelled From</label>
                                <div class="review-value" id="review_empanelled_from">-</div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="review-card">
                                <label class="review-label">Auditor Name</label>
                                <div class="review-value" id="review_auditor_name">-</div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="review-card">
                                <label class="review-label">Auditor Artifact</label>
                                <div class="review-value" id="review_auditor_artifact">
                                    <div id="auditor_artifact_preview" class="mt-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== PARAMETER DETAILS ===== -->
                <div class="review-section">
                    <h5 class="mb-3 border-bottom pb-2">
                        <i class="mr-2"></i>Parameter Details
                    </h5>
                    <div id="parameterReviewContainer">
                        <!-- Will be populated by JavaScript -->
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Loading parameter details...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== PARAMETER SUMMARIES & RECOMMENDATIONS ===== -->
                <div class="review-section mt-4">
                    <h5 class="mb-3 border-bottom pb-2">
                        <i class="fas fa-file-alt mr-2"></i>Parameter Summaries & Recommendations
                    </h5>
                    
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        Please provide a brief summary for each parameter and overall recommendations for the audit.
                    </div>
                    
                    <!-- Parameter Summaries Section -->
                    <div id="parameterSummariesContainer">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2">Loading parameter summaries...</p>
                        </div>
                    </div>
                    
                    <!-- Save Summaries Button -->
                    <div class="mt-3 text-end">
                        <button class="btn btn-outline-primary" onclick="saveSummaries()">
                            <i class="fas fa-save mr-2"></i>Save All Summaries
                        </button>
                        <span id="summarySaveStatus" class="ms-3"></span>
                    </div>
                    
                    <!-- Recommendations Section -->
                    <div class="mt-5">
                        <h6 class="mb-3">
                            <i class="fas fa-lightbulb mr-2"></i>Overall Recommendations
                        </h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Recommendations & Suggestions</label>
                            <textarea class="form-control" id="recommendations" rows="4" 
                                      placeholder="Enter overall recommendations, suggestions, or improvement areas..."></textarea>
                            <div class="form-text text-muted">
                                These recommendations will be included in the final audit report.
                            </div>
                        </div>
                        
                        <div class="mt-3 text-end">
                            <button class="btn btn-outline-primary" onclick="saveRecommendations()">
                                <i class="fas fa-save mr-2"></i>Save Recommendations
                            </button>
                            <span id="recommendationSaveStatus" class="ms-3"></span>
                        </div>
                    </div>
                </div>

                <!-- ===== ACTION BUTTONS ===== -->
                <div class="mt-5 pt-4 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <i class="fas fa-info-circle mr-2"></i>
                            <small>Select appropriate action based on audit status</small>
                        </div>
                        <div class="action-buttons">
                            <button class="btn btn-outline-info mr-2" onclick="saveAndUpdateStatus('saved')">
                                <i class="fas fa-save mr-2"></i>Save Draft
                            </button>
                            <button class="btn btn-success" onclick="saveAndUpdateStatus('submitted')">
                                <i class="fas fa-paper-plane mr-2"></i>
                                {{ isset($audit) && $audit->status == 'submitted' ? 'Update Submission' : 'Submit Audit' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= FIXED STYLES ================= --}}
    <style>
        /* Progress Steps - Fixed */
        .steps-progress {
            display: flex;
            justify-content: space-between;
            position: relative;
            padding: 0 20px;
        }

        .steps-progress::before {
            content: '';
            position: absolute;
            top: 24px;
            left: 20px;
            right: 20px;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }

        .step {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }

        .step-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #e9ecef;
            border: 3px solid #fff;
            color: #6c757d;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
        }

        .step.active .step-circle {
            background: #0d6efd;
            color: white;
            box-shadow: 0 0 0 6px rgba(13, 110, 253, .1);
        }

        .step-label {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
        }

        .step.active .step-label {
            color: #0d6efd;
            font-weight: 600;
        }

        /* Card Headers - Fixed */
        .card-header.bg-gradient-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%) !important;
            border-bottom: none !important;
            padding: 1rem 1.5rem !important;
        }

        .step-number {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, .2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
        }

        /* Parameter Cards - Fixed */
        .parameter-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.2s ease;
            background: #fff;
            height: 100%;
        }

        .parameter-card:hover {
            border-color: #0d6efd;
            background: #f8f9fa;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .1);
        }

        .parameter-label {
            cursor: pointer;
            user-select: none;
        }

        /* Edit Mode Styling */
        #editModeAlert {
            color: #0c5460;
            font-weight: 500;
        }

        /* ========== STEP 3 - SUB PARAMETER HIGHLIGHTING ========== */
        .sub-parameter-card {
            background: linear-gradient(to right, #f8f9fa, #fff);
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .sub-parameter-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, .1);
        }

        .sub-parameter-header {
            display: flex;
            align-items: flex-start;
            padding-bottom: 10px;
            margin-bottom: 15px;
            border-bottom: 1px dashed #dee2e6;
        }

        .sub-parameter-title {
            font-weight: 600;
            color: #212529;
            margin: 0;
            flex-shrink: 0;
            display: flex;
            align-items: center;
        }

        .sub-parameter-index {
            background: #6c757d;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        /* Compliance Status inline styling - UPDATED FOR BETTER LAYOUT */
        .compliance-status-quick {
            display: inline-block;
            vertical-align: middle;
            margin-left: 15px;
        }

        .compliance-status.form-select-sm {
            height: 32px;
            padding: 4px 8px;
            font-size: 13px;
            border-radius: 4px;
            transition: all 0.2s ease;
            border: 2px solid #dee2e6;
            min-width: 120px;
            max-width: 120px;
        }

        .compliance-status.form-select-sm:focus {
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
            border-color: #86b7fe;
        }

        .compliance-status.form-select-sm option[value="yes"] {
            background-color: #d4edda;
            color: #155724;
            font-weight: 500;
        }

        .compliance-status.form-select-sm option[value="no"] {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: 500;
        }

        .compliance-status.form-select-sm option[value="na"] {
            background-color: #e2e3e5;
            color: #383d41;
            font-weight: 500;
        }

        .compliance-status.form-select-sm:valid {
            border-left: 4px solid #0d6efd !important;
        }

        .compliance-status.form-select-sm.is-invalid {
            border-color: #dc3545 !important;
            border-width: 2px !important;
        }

        /* Header content arrangement */
        .sub-parameter-header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            width: 100%;
            flex-wrap: wrap;
            gap: 10px;
        }

        .sub-parameter-title-section {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        /* Status badges for review */
        .compliance-badge-yes {
            background: #28a745 !important;
            color: white !important;
        }

        .compliance-badge-no {
            background: #dc3545 !important;
            color: white !important;
        }

        .compliance-badge-na {
            background: #6c757d !important;
            color: white !important;
        }

        .compliance-badge-unset {
            background: #ffc107 !important;
            color: #212529 !important;
        }

        /* ========== STEP 4 - TABLE DIFFERENTIATION ========== */
        .parameter-group-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            margin-bottom: 25px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .08);
            background: #fff;
        }

        .parameter-group-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px 20px;
            border-bottom: 2px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .parameter-group-title {
            display: flex;
            align-items: center;
            margin: 0;
        }

        .parameter-group-badge {
            background: #0d6efd;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-weight: bold;
            font-size: 14px;
        }

        .parameter-group-count {
            background: #6c757d;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .parameter-table {
            width: 100%;
            border-collapse: collapse;
        }

        .parameter-table thead th {
            background: #f1f3f4;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        .parameter-table tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s ease;
        }

        .parameter-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .parameter-table tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }

        .parameter-table tbody tr:nth-child(even):hover {
            background-color: #f8f9fa;
        }

        .parameter-table td {
            padding: 15px;
            vertical-align: top;
        }

        .sub-parameter-name {
            display: flex;
            align-items: center;
            font-weight: 500;
            color: #212529;
        }

        .sub-parameter-number {
            background: #e9ecef;
            color: #495057;
            width: 24px;
            height: 24px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 12px;
            font-weight: bold;
        }

        .remarks-box {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            border-left: 3px solid #0d6efd;
            min-height: 60px;
        }

        .artifacts-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        .artifact-item {
            background: #e9ecef;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 6px 10px;
            display: inline-flex;
            align-items: center;
            color: #495057;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .artifact-item:hover {
            background: #0d6efd;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, .1);
        }

        .artifact-icon {
            margin-right: 6px;
            font-size: 12px;
        }

        .no-data {
            color: #6c757d;
            font-style: italic;
            padding: 10px;
            text-align: center;
            background: #f8f9fa;
            border-radius: 6px;
        }

        /* Review Section - Fixed */
        .review-section {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }

        .review-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            height: 100%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .05);
        }

        .review-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, .08);
        }

        .review-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .review-value {
            font-size: 16px;
            color: #212529;
            font-weight: 500;
            min-height: 24px;
            word-break: break-word;
        }

        /* Auditor Artifact Preview */
        .auditor-artifact-preview {
            margin-top: 5px;
        }

        .auditor-artifact-preview img {
            max-width: 100px;
            max-height: 100px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 2px;
            background: white;
        }

        .auditor-artifact-preview a {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
        }

        /* Tabs Customization - Fixed */
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 20px;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 10px 20px;
            margin-right: 5px;
            border-radius: 6px 6px 0 0;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            color: #0d6efd;
            background: #f8f9fa;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            background: white;
            border-bottom: 3px solid #0d6efd;
            font-weight: 600;
        }

        .tab-content {
            padding: 20px;
            background: white;
            border-radius: 0 8px 8px 8px;
            border: 1px solid #dee2e6;
            border-top: none;
        }

        /* Buttons - Fixed */
        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #0d6efd;
            border-color: #0d6efd;
        }

        .btn-primary:hover {
            background: #0b5ed7;
            border-color: #0a58ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(13, 110, 253, .2);
        }

        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            border-color: #6c757d;
            color: white;
        }

        /* Form Controls - Fixed */
        .form-control,
        .form-select {
            border-radius: 6px;
            border: 1px solid #ced4da;
            padding: 8px 12px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, .25);
        }

        /* Alert Styles - Fixed */
        .alert {
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .05);
        }

        /* Artifact Thumbnail Styles */
        .artifact-thumbnail-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 8px;
        }

        .artifact-thumbnail {
            position: relative;
            width: 80px;
            height: 80px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            overflow: hidden;
            background: #f8f9fa;
            transition: all 0.2s ease;
        }

        .artifact-thumbnail:hover {
            border-color: #0d6efd;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .1);
        }

        .artifact-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .artifact-thumbnail .file-icon {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }

        .artifact-thumbnail .file-icon i {
            font-size: 24px;
        }

        .artifact-thumbnail a {
            display: block;
            width: 100%;
            height: 100%;
            text-decoration: none;
        }

        .artifact-thumbnail-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
        }

        /* Artifact Preview Modal */
        .artifact-preview-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .artifact-preview-content {
            max-width: 90%;
            max-height: 90%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }

        .artifact-preview-content img {
            max-width: 100%;
            max-height: 80vh;
            display: block;
        }

        .artifact-preview-close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            font-size: 16px;
        }

        /* Artifact Action Buttons */
        .artifact-action-buttons {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .artifact-action-buttons label {
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
        }

        .form-check-inline {
            margin-right: 15px !important;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .artifact-count-badge {
            background: #28a745;
            color: white;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 5px;
            font-weight: 600;
        }

        /* Artifact Saved Badge */
        .artifact-saved-badge {
            background: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .artifact-count {
            background: #0d6efd;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            margin-left: 5px;
        }

        .required-field {
            border-left: 4px solid #ffc107 !important;
            background: linear-gradient(to right, #fff8e1, #fff) !important;
        }

        .is-invalid {
            border-color: #dc3545 !important;
            border-width: 2px !important;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }

        /* Highlight empty fields in review */
        .text-danger .fa-exclamation-triangle {
            color: #dc3545;
        }

        /* Make required asterisk more noticeable */
        .text-danger[title="Required field"] {
            font-size: 18px;
            vertical-align: middle;
        }

        /* Parameter Summary Styles - NEW */
        .parameter-summary-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 15px;
            background: #fff;
            transition: all 0.3s ease;
        }

        .parameter-summary-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .parameter-summary-header {
            background: #f8f9fa;
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
        }

        .parameter-summary-header h6 {
            margin: 0;
            color: #212529;
            font-weight: 600;
        }

        .parameter-summary-body {
            padding: 15px;
        }

        .char-count {
            font-size: 12px;
            color: #6c757d;
            float: right;
        }

        .char-count.text-danger {
            color: #dc3545;
            font-weight: bold;
        }

        /* Save Status Styles - NEW */
        #summarySaveStatus, #recommendationSaveStatus {
            font-size: 14px;
            font-weight: 500;
        }

        #summarySaveStatus i, #recommendationSaveStatus i {
            margin-right: 5px;
        }

        /* Responsive Adjustments - Fixed */
        @media (max-width: 768px) {
            .steps-progress {
                flex-direction: column;
                gap: 15px;
                padding: 0;
            }

            .steps-progress::before {
                display: none;
            }

            .step {
                display: flex;
                align-items: center;
                text-align: left;
                justify-content: flex-start;
            }

            .step-circle {
                margin: 0 15px 0 0;
                min-width: 40px;
                width: 40px;
                height: 40px;
            }

            .step-label {
                font-size: 14px;
            }

            .action-buttons {
                display: flex;
                flex-direction: column;
                gap: 10px;
                width: 100%;
            }

            .action-buttons .btn {
                width: 100%;
                margin: 2px 0 !important;
            }

            .parameter-table {
                display: block;
                overflow-x: auto;
            }

            .sub-parameter-card {
                margin-left: -10px;
                margin-right: -10px;
            }

            .artifact-thumbnail {
                width: 60px;
                height: 60px;
            }

            .artifact-action-buttons {
                flex-direction: column;
                gap: 8px;
            }

            .form-check-inline {
                margin-bottom: 5px;
            }

            .sub-parameter-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .sub-parameter-header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .sub-parameter-title-section {
                width: 100%;
                margin-bottom: 10px;
            }
            
            .compliance-status-quick {
                margin-left: 0;
                margin-top: 10px;
                width: 100%;
            }
            
            .compliance-status-quick select {
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>

    <!-- Artifact Preview Modal -->
    <div class="artifact-preview-overlay" id="artifactPreviewModal">
        <div class="artifact-preview-content">
            <button class="artifact-preview-close" onclick="closeArtifactPreview()">
                <i class="fas fa-times"></i>
            </button>
            <img id="previewedArtifact" src="" alt="Artifact Preview">
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
    const savedParameters = new Set();
    let isEditMode = {{ isset($audit) ? 'true' : 'false' }};
    let currentStep = 1;

    // Helper function to get file icon based on extension
    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        const pdfExtensions = ['pdf'];
        const wordExtensions = ['doc', 'docx'];
        const excelExtensions = ['xls', 'xlsx'];
        const textExtensions = ['txt', 'rtf'];

        if (imageExtensions.includes(ext)) return 'fas fa-image';
        if (pdfExtensions.includes(ext)) return 'fas fa-file-pdf';
        if (wordExtensions.includes(ext)) return 'fas fa-file-word';
        if (excelExtensions.includes(ext)) return 'fas fa-file-excel';
        if (textExtensions.includes(ext)) return 'fas fa-file-alt';
        return 'fas fa-file';
    }

    // Helper function to create artifact thumbnail HTML
    function createArtifactThumbnail(filePath, index) {
        const filename = filePath.split('/').pop();
        const ext = filename.split('.').pop().toLowerCase();
        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'].includes(ext);
        const fullUrl = `/storage/app/public/${filePath}`;

        let thumbnailContent = '';

        if (isImage) {
            thumbnailContent = `<img src="${fullUrl}" alt="${filename}" loading="lazy" style="width: 100%; height: 100%; object-fit: cover;">`;
        } else {
            const iconClass = getFileIcon(filename);
            thumbnailContent = `
                <div class="file-icon">
                    <i class="${iconClass}"></i>
                </div>
            `;
        }

        return `
            <div class="artifact-thumbnail">
                <a href="${fullUrl}" target="_blank" onclick="event.preventDefault(); previewArtifact('${fullUrl}', '${filename}')">
                    ${thumbnailContent}
                    <span class="artifact-thumbnail-badge">${index + 1}</span>
                </a>
            </div>
        `;
    }

    // Helper function to preview artifact in modal
    function previewArtifact(url, filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'].includes(ext);

        if (isImage) {
            document.getElementById('previewedArtifact').src = url;
            document.getElementById('artifactPreviewModal').style.display = 'flex';
        } else {
            // For non-image files, open in new tab
            window.open(url, '_blank');
        }
    }

    // Close artifact preview modal
    function closeArtifactPreview() {
        document.getElementById('artifactPreviewModal').style.display = 'none';
        document.getElementById('previewedArtifact').src = '';
    }

    // Close modal when clicking outside
    document.getElementById('artifactPreviewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeArtifactPreview();
        }
    });

    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeArtifactPreview();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // If in edit mode and audit_id exists, load appropriate step
        if (isEditMode && document.getElementById('audit_id').value) {
            // If audit already has parameters, start from step 2
            if ({{ isset($selectedParameters) && count($selectedParameters) > 0 ? 'true' : 'false' }}) {
                showStep(2);
            }
        }

        // Update progress indicator
        function updateProgress(step) {
            document.querySelectorAll('.step').forEach(s => {
                s.classList.remove('active');
            });
            document.querySelector(`.step[data-step="${step}"]`).classList.add('active');
        }

        function showStep(stepNumber) {
            // Hide all steps
            document.getElementById('step1').classList.add('d-none');
            document.getElementById('step2').classList.add('d-none');
            document.getElementById('step3').classList.add('d-none');
            document.getElementById('step4').classList.add('d-none');

            // Show the requested step
            document.getElementById('step' + stepNumber).classList.remove('d-none');

            // Update progress indicator
            updateProgress(stepNumber);

            // Update current step
            currentStep = stepNumber;

            // If showing step 3 and in edit mode, load sub-parameters
            if (stepNumber === 3) {
                loadSubParameters();
            }

            // If showing step 4, load review
            if (stepNumber === 4) {
                loadStep4Review();
            }
        }

        // UPDATED saveAuditBtn onclick function to handle new fields
        document.getElementById('saveAuditBtn').onclick = function() {
            const advocate = document.getElementById('advocate_id').value;
            const auditDate = document.getElementById('audit_date').value;
            const locationVal = document.getElementById('location').value;
            const auditorName = document.getElementById('auditor_name').value;
            const empanelledFrom = document.getElementById('empanelled_from').value;

            // Added auditor_name to required fields validation
            if (!advocate || !auditDate || !locationVal || !auditorName || !empanelledFrom) {
                alert('Please fill all required fields marked with *');
                return;
            }

            // Create FormData to handle file upload
            let formData = new FormData();
            const auditId = document.getElementById('audit_id').value;

            if (auditId) {
                formData.append('audit_id', auditId);
            }

            // For edit mode, get advocate name from hidden field if select is disabled
            let advocateName = '';
            if (isEditMode && document.getElementById('advocate_name_hidden')) {
                advocateName = document.getElementById('advocate_name_hidden').value;
            } else {
                const advocateSelect = document.getElementById('advocate_id');
                advocateName = advocateSelect.options[advocateSelect.selectedIndex]?.text || '';
            }

            formData.append('advocate_id', advocate);
            formData.append('advocate_name', advocateName);
            formData.append('audit_date', auditDate);
            formData.append('address', document.getElementById('address').value);
            formData.append('state', document.getElementById('state').value);
            formData.append('location', locationVal);
            formData.append('legal_manager', document.getElementById('legal_manager').value);
            formData.append('auditor_name', auditorName);
            formData.append('empanelled_from', empanelledFrom);

            // Append file if selected
            const fileInput = document.getElementById('auditor_artifact_image');
            if (fileInput.files[0]) {
                formData.append('auditor_artifact_image', fileInput.files[0]);
            }

            fetch("{{ route('legal.audit.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    if (res.audit_id) {
                        document.getElementById('audit_id').value = res.audit_id;
                        isEditMode = true;
                    }

                    // If in edit mode and already has parameters, go to step 3 directly
                    if (isEditMode &&
                        {{ isset($selectedParameters) && count($selectedParameters) > 0 ? 'true' : 'false' }}
                        ) {
                        showStep(2);
                    } else {
                        showStep(2);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving. Please try again.');
                });
        };

        document.getElementById('saveParamsBtn').onclick = function() {
            let params = [];
            document.querySelectorAll('.paramCheck:checked').forEach(p => {
                params.push({
                    parameter_id: p.value,
                    qm_sheet_id: p.dataset.qm
                });
            });

            if (!params.length) {
                alert('Please select at least one parameter to continue');
                return;
            }

            fetch("{{ route('legal.audit.parameters.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    audit_id: document.getElementById('audit_id').value,
                    parameters: params
                })
            }).then(() => {
                showStep(3);
            });
        };

        document.getElementById('backToStep1').onclick = () => {
            showStep(1);
        };

        document.getElementById('backToStep2').onclick = () => {
            showStep(2);
        };

        document.getElementById('backToStep3').onclick = () => {
            showStep(3);
        };

        document.getElementById('step3SaveNext').onclick = () => {
            showStep(4);
        };
    });

    function checkAllSaved() {
        const totalParams = document.querySelectorAll('.save-param-btn').length;
        const savedCount = savedParameters.size;
        
        if (savedCount === totalParams && totalParams > 0) {
            document.getElementById('step3SaveNext').disabled = false;
            document.getElementById('step3SaveNext').innerHTML =
                '<i class="fas fa-check-circle mr-2"></i>All Parameters Saved - Continue';
        } else {
            document.getElementById('step3SaveNext').disabled = true;
            document.getElementById('step3SaveNext').innerHTML =
                `<i class="fas fa-check-circle mr-2"></i>${savedCount}/${totalParams} Saved - Continue`;
        }
    }

    // Function to validate remarks and compliance status
    function validateRemarks(parameterId) {
        const card = document.querySelector(`[data-parameter-id="${parameterId}"]`);
        if (!card) return { isValid: false, emptyFields: [] };
        
        const remarkInputs = card.querySelectorAll('.remark');
        const statusSelects = card.querySelectorAll('.compliance-status');
        const emptyFields = [];
        
        remarkInputs.forEach((input, index) => {
            const subParameterId = input.dataset.sub;
            const subParameterCard = input.closest('.sub-parameter-card');
            const subParameterName = subParameterCard.querySelector('.sub-parameter-title').textContent.trim();
            const statusSelect = statusSelects[index];
            
            // Check if remark is empty
            const remarkEmpty = !input.value || input.value.trim() === '';
            
            // Check if compliance status is not selected
            const statusEmpty = !statusSelect || !statusSelect.value;
            
            if (remarkEmpty || statusEmpty) {
                emptyFields.push({
                    subParameterId: subParameterId,
                    subParameterName: subParameterName,
                    remarkElement: input,
                    statusElement: statusSelect,
                    remarkEmpty: remarkEmpty,
                    statusEmpty: statusEmpty
                });
            }
        });
        
        return {
            isValid: emptyFields.length === 0,
            emptyFields: emptyFields
        };
    }

    // Function to highlight empty remark and status fields
    function highlightEmptyRemarks(emptyFields) {
        // Remove existing highlights
        document.querySelectorAll('.remark, .compliance-status').forEach(element => {
            element.classList.remove('is-invalid');
            element.style.borderColor = '';
            
            const existingError = element.nextElementSibling;
            if (existingError && existingError.classList.contains('invalid-feedback')) {
                existingError.remove();
            }
        });
        
        // Highlight empty fields
        emptyFields.forEach(field => {
            let errorMessage = '';
            
            if (field.remarkEmpty && field.statusEmpty) {
                field.remarkElement.classList.add('is-invalid');
                field.statusElement.classList.add('is-invalid');
                errorMessage = `Please enter remarks and select compliance status for "${field.subParameterName}"`;
            } else if (field.remarkEmpty) {
                field.remarkElement.classList.add('is-invalid');
                errorMessage = `Please enter remarks for "${field.subParameterName}"`;
            } else if (field.statusEmpty) {
                field.statusElement.classList.add('is-invalid');
                errorMessage = `Please select compliance status for "${field.subParameterName}"`;
            }
            
            if (field.remarkEmpty) {
                field.remarkElement.style.borderColor = '#dc3545';
                field.remarkElement.style.borderWidth = '2px';
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i>${errorMessage}`;
                field.remarkElement.parentNode.appendChild(errorDiv);
            }
            
            if (field.statusEmpty) {
                field.statusElement.style.borderColor = '#dc3545';
                field.statusElement.style.borderWidth = '2px';
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i>${errorMessage}`;
                if (field.statusElement.parentNode) {
                    field.statusElement.parentNode.appendChild(errorDiv);
                }
            }
            
            // Scroll to the first empty field
            if (field.remarkElement === emptyFields[0].remarkElement) {
                field.remarkElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                if (field.remarkEmpty) {
                    field.remarkElement.focus();
                } else if (field.statusEmpty) {
                    field.statusElement.focus();
                }
            }
        });
    }

    function loadSubParameters() {
        const auditId = document.getElementById('audit_id').value;

        if (!auditId) {
            alert('No audit ID found. Please save audit details first.');
            return;
        }

        fetch("{{ route('legal.audit.sub-parameters') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    audit_id: auditId
                })
            })
            .then(r => r.json())
            .then(res => {
                if (!res.status || !res.data) {
                    alert('Failed to load sub-parameters');
                    return;
                }

                let tabs = '<ul class="nav nav-tabs" id="parameterTabs">';
                let panes = '<div class="tab-content">';
                let first = true;

                Object.values(res.data).forEach((group, groupIndex) => {
                    if (!group || group.length === 0) return;
                    
                    const pid = group[0].parameter_id;
                    const paramName = group[0].parameter_name;

                    tabs += `<li class="nav-item">
                        <button class="nav-link ${first ? 'active' : ''}"
                            data-bs-toggle="tab" 
                            data-bs-target="#pane-${pid}">
                            <i class="fas fa-list-check mr-2"></i>${paramName}
                        </button>
                    </li>`;

                    panes += `<div class="tab-pane fade ${first ? 'show active' : ''}" id="pane-${pid}">
                        <div class="card mt-2 border-0 shadow-sm" data-parameter-id="${pid}">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-list-check mr-2"></i>${paramName}</h6>
                                <span class="badge bg-info">${group.length} Sub-Parameters</span>
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Remarks & Status Required
                                </span>
                            </div>
                            <div class="card-body">`;

                    group.forEach((sp, index) => {
                        // Ensure artifacts is an array
                        let existingArtifacts = Array.isArray(sp.artifacts) ? sp.artifacts : [];
                        let artifactHtml = '';

                        if (existingArtifacts.length > 0) {
                            const thumbnails = existingArtifacts.map((a, idx) => 
                                createArtifactThumbnail(a, idx)
                            ).join('');

                            artifactHtml = `
                                <div class="mt-3">
                                    <small class="text-success fw-semibold">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Existing artifacts (${existingArtifacts.length}):
                                    </small>
                                    <div class="artifact-thumbnail-container mt-2">
                                        ${thumbnails}
                                    </div>
                                    <div class="artifact-action-buttons mt-2">
                                        <label class="fw-semibold">Choose action for new files:</label>
                                        <div class="d-flex flex-wrap mt-1">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input artifact-action" 
                                                       type="radio" 
                                                       name="artifact_action_${sp.sub_parameter_id}" 
                                                       value="append" 
                                                       id="append_${sp.sub_parameter_id}" 
                                                       checked>
                                                <label class="form-check-label" for="append_${sp.sub_parameter_id}">
                                                    Add to existing
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input artifact-action" 
                                                       type="radio" 
                                                       name="artifact_action_${sp.sub_parameter_id}" 
                                                       value="replace" 
                                                       id="replace_${sp.sub_parameter_id}">
                                                <label class="form-check-label" for="replace_${sp.sub_parameter_id}">
                                                    Replace all
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Choose whether to add new files or replace all existing ones.
                                    </small>
                                </div>
                            `;
                        }

                        // Create saved badge if artifacts exist or remark exists
                        let savedBadge = '';
                        if (existingArtifacts.length > 0) {
                            savedBadge = `<span class="artifact-saved-badge">
                                <i class="fas fa-check me-1"></i>
                                ${existingArtifacts.length} Artifact${existingArtifacts.length > 1 ? 's' : ''}
                            </span>`;
                        } else if (sp.remark && sp.remark.trim() !== '') {
                            savedBadge = `<span class="badge bg-success"><i class="fas fa-check me-1"></i>Saved</span>`;
                        }

                        // Check if remark or status is required (add asterisk if empty)
                        const remarkRequiredClass = (!sp.remark || sp.remark.trim() === '' || !sp.compliance_status) ? 'required-field' : '';
                        const requiredIndicator = (!sp.remark || sp.remark.trim() === '' || !sp.compliance_status) ? 
                            '<span class="text-danger ms-1" title="Required field">*</span>' : '';

                        panes += `
                            <div class="sub-parameter-card m-3 ${remarkRequiredClass}">
                                <div class="sub-parameter-header">
                                    <div class="sub-parameter-header-content">
                                        <div class="sub-parameter-title-section">
                                            <h6 class="sub-parameter-title">
                                                <i class="fas fa-list-check text-primary mr-2"></i>
                                                ${sp.sub_parameter}
                                                ${requiredIndicator}
                                            </h6>
                                            
                                            <!-- Compliance Status RIGHT AFTER Sub-Parameter Name -->
                                            <div class="compliance-status-quick">
                                                <select class="form-select form-select-sm compliance-status" 
                                                        data-sub="${sp.sub_parameter_id}" 
                                                        style="width: 120px;" 
                                                        title="Compliance Status"
                                                        required>
                                                    <option value="">Status</option>
                                                    <option value="yes" ${sp.compliance_status == 'yes' ? 'selected' : ''}>Yes</option>
                                                    <option value="no" ${sp.compliance_status == 'no' ? 'selected' : ''}>No</option>
                                                    <option value="na" ${sp.compliance_status == 'na' ? 'selected' : ''}>N/A</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        ${savedBadge}
                                    </div>
                                    <span class="sub-parameter-index">${index + 1}</span>
                                </div>
                                
                                <div class="m-3">
                                    <label class="form-label fw-semibold mb-2">
                                        <i class="fas fa-comment mr-2 text-primary"></i>Remarks
                                        <span class="text-danger" title="Required field">*</span>
                                    </label>
                                    <textarea class="form-control remark" 
                                        data-sub="${sp.sub_parameter_id}" 
                                        placeholder="Please enter remarks for this sub-parameter (required)..."
                                        rows="3" required>${sp.remark || ''}</textarea>
                                    <div class="form-text text-muted">
                                        <small><i class="fas fa-info-circle me-1"></i>Remarks are mandatory for each sub-parameter</small>
                                    </div>
                                </div>
                                
                                <div class="m-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-paperclip mr-2 text-primary"></i>Upload Artifacts
                                    </label>
                                    <input type="file" class="form-control artifact" 
                                        data-sub="${sp.sub_parameter_id}" 
                                        multiple
                                        accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.rtf">
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Multiple files allowed. Max 10MB each.
                                    </small>
                                    ${artifactHtml}
                                </div>
                            </div>`;
                    });

                    panes += `
                            <div class="text-end mt-4 pt-3 border-top">
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    <strong>Important:</strong> Please fill remarks and select compliance status for all sub-parameters before saving.
                                </div>
                                <button class="btn btn-success save-param-btn px-4" data-parameter-id="${pid}">
                                    <i class="fas fa-save mr-2"></i>
                                    ${isEditMode ? 'Update' : 'Save'} Parameter ${groupIndex + 1}
                                </button>
                            </div>
                        </div></div></div>`;

                    first = false;
                });

                document.getElementById('subParameterSection').innerHTML = tabs + '</ul>' + panes + '</div>';

                // Initialize Bootstrap tabs
                const tabEl = document.querySelector('#parameterTabs .nav-link');
                if (tabEl) {
                    new bootstrap.Tab(tabEl);
                }

                // In edit mode, check if all parameters are already saved
                if (isEditMode) {
                    const totalParams = document.querySelectorAll('.save-param-btn').length;
                    if (totalParams > 0) {
                        // Check each parameter for existing data
                        const checkboxes = document.querySelectorAll('.paramCheck:checked');
                        checkboxes.forEach((cb, index) => {
                            savedParameters.add(cb.value);
                        });
                        checkAllSaved();
                    }
                }
            })
            .catch(error => {
                console.error('Error loading sub-parameters:', error);
                document.getElementById('subParameterSection').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Failed to load parameters. Please try again.
                        <br><small>Error: ${error.message}</small>
                    </div>
                `;
            });
    }

    document.addEventListener('click', function(e) {
        if (!e.target.classList.contains('save-param-btn')) return;

        const pid = e.target.dataset.parameterId;
        const card = document.querySelector(`[data-parameter-id="${pid}"]`);
        const btn = e.target;
        const originalText = btn.innerHTML;

        // Validate remarks and compliance status before proceeding
        const validation = validateRemarks(pid);
        
        if (!validation.isValid) {
            // Show error for empty remarks or status
            highlightEmptyRemarks(validation.emptyFields);
            
            // Show alert message
            const paramName = card.querySelector('.card-header h6').textContent;
            const subParamNames = validation.emptyFields.map(f => f.subParameterName).join(', ');
            
            alert(`❌ Please complete the following for sub-parameters in "${paramName}":\n\n${subParamNames}\n\nBoth remarks and compliance status are required.`);
            return;
        }

        // Show loading state
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        btn.disabled = true;

        let fd = new FormData();
        fd.append('audit_id', document.getElementById('audit_id').value);
        fd.append('parameter_id', pid);

        // Add remarks
        card.querySelectorAll('.remark').forEach(r => {
            fd.append(`remarks[${r.dataset.sub}]`, r.value.trim());
        });

        // Add compliance status
        card.querySelectorAll('.compliance-status').forEach(select => {
            fd.append(`compliance_status[${select.dataset.sub}]`, select.value);
        });

        // Add artifact actions
        card.querySelectorAll('.sub-parameter-card').forEach(subCard => {
            const remarkInput = subCard.querySelector('.remark');
            const subId = remarkInput.dataset.sub;
            const actionInput = subCard.querySelector(`input[name="artifact_action_${subId}"]:checked`);

            if (actionInput) {
                fd.append(`artifact_actions[${subId}]`, actionInput.value);
            }
        });

        // Add artifacts
        card.querySelectorAll('.artifact').forEach(i => {
            for (let f of i.files) {
                fd.append(`artifacts[${i.dataset.sub}][]`, f);
            }
        });

        fetch("{{ route('legal.audit.parameter.result.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: fd
            })
            .then(res => res.json())
            .then((data) => {
                if (data.status) {
                    savedParameters.add(pid);
                    checkAllSaved();

                    // Show success feedback
                    btn.innerHTML = '<i class="fas fa-check mr-2"></i>Saved';
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-success');

                    // Remove validation highlights
                    document.querySelectorAll('.remark, .compliance-status').forEach(element => {
                        element.classList.remove('is-invalid');
                        element.style.borderColor = '';
                        
                        // Remove error messages
                        const existingError = element.nextElementSibling;
                        if (existingError && existingError.classList.contains('invalid-feedback')) {
                            existingError.remove();
                        }
                    });

                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.classList.remove('btn-outline-success');
                        btn.classList.add('btn-success');
                        btn.disabled = false;

                        // Reload sub-parameters to show updated thumbnails
                        if (isEditMode) {
                            setTimeout(() => {
                                loadSubParameters();
                            }, 1000);
                        }
                    }, 2000);
                } else {
                    btn.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Error';
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-danger');

                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.classList.remove('btn-danger');
                        btn.classList.add('btn-success');
                        btn.disabled = false;
                        
                        if (data.errors) {
                            alert(Object.values(data.errors).join('\n'));
                        }
                    }, 2000);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                btn.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Error';
                btn.classList.remove('btn-success');
                btn.classList.add('btn-danger');

                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-danger');
                    btn.classList.add('btn-success');
                    btn.disabled = false;
                    alert('Network error. Please check your connection and try again.');
                }, 2000);
            });
    });

    // UPDATED loadStep4Review function to include compliance status and load summaries
    function loadStep4Review() {
        // Update audit details
        const advocateSelect = document.getElementById('advocate_id');
        document.getElementById('review_advocate').innerText =
            advocateSelect.options[advocateSelect.selectedIndex]?.text || '-';

        document.getElementById('review_audit_date').innerText =
            document.getElementById('audit_date').value || '-';

        document.getElementById('review_legal_manager').innerText =
            document.getElementById('legal_manager').value || '-';

        document.getElementById('review_address').innerText =
            document.getElementById('address').value || '-';

        document.getElementById('review_state').innerText =
            document.getElementById('state').value || '-';

        document.getElementById('review_location').innerText =
            document.getElementById('location').value || '-';

        // New fields
        document.getElementById('review_empanelled_from').innerText =
            document.getElementById('empanelled_from').value || '-';

        document.getElementById('review_auditor_name').innerText =
            document.getElementById('auditor_name').value || '-';

        // Handle auditor artifact image preview
        const fileInput = document.getElementById('auditor_artifact_image');
        const previewDiv = document.getElementById('auditor_artifact_preview');

        if (fileInput.files && fileInput.files[0]) {
            // New file uploaded
            const file = fileInput.files[0];
            const fileName = file.name;
            const fileType = file.type;
            const fileSize = (file.size / 1024).toFixed(2);

            if (fileType.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.innerHTML = `
                        <div class="auditor-artifact-preview">
                            <a href="#" onclick="event.preventDefault(); previewArtifact('${e.target.result}', '${fileName}')" class="artifact-item mb-2">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                Preview Image
                            </a>
                            <div>
                                <img src="${e.target.result}" alt="Auditor Artifact" class="img-thumbnail" 
                                     onclick="previewArtifact('${e.target.result}', '${fileName}')" style="cursor: pointer;">
                                <div class="small text-muted mt-1">
                                    ${fileName} (${fileSize} KB) - New Upload
                                </div>
                            </div>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                previewDiv.innerHTML = `
                    <div class="artifact-item" onclick="window.open(URL.createObjectURL(fileInput.files[0]), '_blank')" style="cursor: pointer;">
                        <i class="fas fa-paperclip mr-2"></i>
                        ${fileName} (${fileSize} KB) - New Upload
                    </div>
                `;
            }
        } else if (isEditMode && {{ isset($audit) && $audit->auditor_artifact_image ? 'true' : 'false' }}) {
            // In edit mode, show existing file
            const fileUrl = "{{ isset($audit) && $audit->auditor_artifact_image ? asset('storage/app/public/' . $audit->auditor_artifact_image) : '#' }}";
            const fileName = "{{ isset($audit) && $audit->auditor_artifact_image ? basename($audit->auditor_artifact_image) : '' }}";
            const isImage = fileName.match(/\.(jpg|jpeg|png|gif|bmp|webp|svg)$/i);

            if (isImage) {
                previewDiv.innerHTML = `
                    <div class="auditor-artifact-preview">
                        
                        <div>
                            <img src="${fileUrl}" alt="Auditor Artifact" class="img-thumbnail" 
                                 onclick="previewArtifact('${fileUrl}', '${fileName}')" style="cursor: pointer;">
                            <div class="small text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Existing file from previous save
                            </div>
                        </div>
                    </div>
                `;
            } else {
                previewDiv.innerHTML = `
                    <div class="artifact-item" onclick="window.open('${fileUrl}', '_blank')" style="cursor: pointer;">
                        <i class="fas fa-paperclip mr-2"></i>
                        ${fileName} - Existing File
                    </div>
                    <div class="small text-muted mt-1">
                        <i class="fas fa-info-circle me-1"></i>
                        Click to view file
                    </div>
                `;
            }
        } else {
            previewDiv.innerHTML = '<span class="no-data">No file uploaded</span>';
        }

        // Load parameter details
        fetch("{{ route('legal.audit.sub-parameters') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    audit_id: document.getElementById('audit_id').value
                })
            })
            .then(res => res.json())
            .then(res => {
                let html = '';

                if (!res.data || Object.keys(res.data).length === 0) {
                    html = `<div class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <h5>No parameter data found</h5>
                            <p>Please go back and add parameters to the audit</p>
                        </div>
                    </div>`;
                } else {
                    Object.values(res.data).forEach((group, groupIndex) => {
                        if (!group || group.length === 0) return;
                        
                        const paramName = group[0].parameter_name;
                        const subCount = group.length;

                        html += `
                            <div class="parameter-group-card">
                                <div class="parameter-group-header">
                                    <h5 class="parameter-group-title">
                                        <span class="parameter-group-badge">${groupIndex + 1}</span>
                                        ${paramName}
                                    </h5>
                                    <span class="parameter-group-count">${subCount} Sub-Parameters</span>
                                </div>
                                <div class="p-0">
                                    <table class="parameter-table">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="25%">Sub-Parameter</th>
                                                <th width="15%">Compliance Status</th>
                                                <th width="25%">Remarks</th>
                                                <th width="30%">Artifacts</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;

                        group.forEach((sp, index) => {
                            // Use artifacts array from response
                            let artifacts = Array.isArray(sp.artifacts) ? sp.artifacts : [];
                            const complianceStatus = sp.compliance_status || 'unset';

                            html += `
                                <tr>
                                    <td>
                                        <div class="sub-parameter-number">${index + 1}</div>
                                    </td>
                                    <td>
                                        <div class="sub-parameter-name">
                                            ${sp.sub_parameter}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge compliance-badge-${complianceStatus}">
                                            ${complianceStatus ? complianceStatus.toUpperCase() : 'N/A'}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="remarks-box">
                                            ${sp.remark ? sp.remark.replace(/\n/g, '<br>') : 
                                            '<span class="no-data text-danger"><i class="fas fa-exclamation-triangle me-1"></i>No remarks added</span>'}
                                        </div>
                                    </td>
                                    <td>`;

                            if (artifacts.length > 0) {
                                html += `<div class="artifact-thumbnail-container">`;
                                artifacts.forEach((a, idx) => {
                                    html += createArtifactThumbnail(a, idx);
                                });
                                html += `</div>
                                <div class="small text-success mt-2">
                                    <i class="fas fa-check-circle me-1"></i>
                                    ${artifacts.length} artifact${artifacts.length > 1 ? 's' : ''} uploaded
                                </div>`;
                            } else {
                                html += `<span class="no-data">No artifacts uploaded</span>`;
                            }

                            html += `</td></tr>`;
                        });

                        html += `      </tbody>
                                    </table>
                                </div>
                            </div>`;
                    });
                }

                document.getElementById('parameterReviewContainer').innerHTML = html;
            })
            .catch((error) => {
                console.error('Error loading review:', error);
                document.getElementById('parameterReviewContainer').innerHTML = `
                    <div class="text-center py-5 text-danger">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <h5>Failed to load parameter details</h5>
                        <p>Please try again or contact support</p>
                        <br><small>Error: ${error.message}</small>
                    </div>`;
            });

        // Load parameter summaries and recommendations
        loadParameterSummaries();
    }

    // ========== PARAMETER SUMMARIES & RECOMMENDATIONS FUNCTIONS ==========
    
    // Load parameter summaries and recommendations
    function loadParameterSummaries() {
        const auditId = document.getElementById('audit_id').value;
        
        if (!auditId) {
            console.error('No audit ID found');
            return;
        }
        
        fetch("{{ route('legal.audit.get.summaries') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ audit_id: auditId })
        })
        .then(res => res.json())
        .then(data => {
            console.log('Summaries response:', data);
            if (data.status) {
                renderParameterSummaries(data.parameters, data.summaries);
                if (data.recommendations) {
                    document.getElementById('recommendations').value = data.recommendations;
                }
            } else {
                console.error('Failed to load summaries:', data.message);
                document.getElementById('parameterSummariesContainer').innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        ${data.message || 'Failed to load parameter summaries'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading summaries:', error);
            document.getElementById('parameterSummariesContainer').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Failed to load parameter summaries. Please refresh the page.
                    <br><small>Error: ${error.message}</small>
                </div>
            `;
        });
    }

    // Render parameter summaries form
    function renderParameterSummaries(parameters, summaries) {
    let html = '';
    
    if (!parameters || parameters.length === 0) {
        html = `<div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            No parameters selected. Please go back to Step 2 and select parameters.
        </div>`;
    } else {
        html = `
            <div class="table-responsive">
                <table class="table table-hover parameter-summaries-table">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="35%">Parameter Name</th>
                            <th width="60%">Summary</th>
                        </tr>
                    </thead>
                    <tbody>`;
        
        parameters.forEach((param, index) => {
            const existingSummary = summaries && summaries[param.parameter_id] ? summaries[param.parameter_id].summary : '';
            
            html += `
                <tr class="parameter-summary-row">
                    <td class="text-center align-middle">
                        <span class="badge bg-primary">${index + 1}</span>
                    </td>
                    <td class="align-middle">
                        <strong>${param.parameter_name}</strong>
                    </td>
                    <td>
                        <div class="mb-2">
                            <textarea class="form-control parameter-summary" 
                                      data-parameter-id="${param.parameter_id}"
                                      rows="3"
                                      placeholder="Enter summary for this parameter..."
                                      oninput="updateCharCount(${param.parameter_id})">${existingSummary}</textarea>
                        </div>
                        <div class="text-end">
                            <span class="char-count small text-muted" id="charCount_${param.parameter_id}">
                                ${existingSummary.length}/500
                            </span>
                        </div>
                    </td>
                </tr>`;
        });
        
        html += `</tbody></table></div>`;
    }
    
    document.getElementById('parameterSummariesContainer').innerHTML = html;
}

    // Update character count
    function updateCharCount(parameterId) {
        const textarea = document.querySelector(`.parameter-summary[data-parameter-id="${parameterId}"]`);
        const charCount = document.getElementById(`charCount_${parameterId}`);
        
        if (textarea && charCount) {
            const length = textarea.value.length;
            charCount.textContent = `${length}/500`;
            
            if (length > 500) {
                charCount.classList.add('text-danger');
                textarea.classList.add('is-invalid');
            } else {
                charCount.classList.remove('text-danger');
                textarea.classList.remove('is-invalid');
            }
        }
    }

    // Save parameter summaries
    function saveSummaries() {
        const auditId = document.getElementById('audit_id').value;
        const summaryElements = document.querySelectorAll('.parameter-summary');
        
        if (!auditId || summaryElements.length === 0) {
            showSaveStatus('summarySaveStatus', 'No summaries to save', 'warning');
            return;
        }
        
        const summaries = {};
        summaryElements.forEach(element => {
            const paramId = element.dataset.parameterId;
            summaries[paramId] = element.value;
        });
        
        const statusElement = document.getElementById('summarySaveStatus');
        statusElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        
        fetch("{{ route('legal.audit.save.summaries') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                audit_id: auditId,
                summaries: summaries
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                showSaveStatus('summarySaveStatus', data.message, 'success');
                
                // Visual feedback
                document.querySelectorAll('.parameter-summary').forEach(element => {
                    element.classList.add('is-valid');
                    setTimeout(() => {
                        element.classList.remove('is-valid');
                    }, 3000);
                });
            } else {
                showSaveStatus('summarySaveStatus', data.message || 'Save failed', 'danger');
            }
        })
        .catch(error => {
            console.error('Error saving summaries:', error);
            showSaveStatus('summarySaveStatus', 'Network error. Please try again.', 'danger');
        });
    }

    // Save recommendations
    function saveRecommendations() {
        const auditId = document.getElementById('audit_id').value;
        const recommendations = document.getElementById('recommendations').value;
        
        if (!auditId) {
            showSaveStatus('recommendationSaveStatus', 'No audit ID found', 'warning');
            return;
        }
        
        const statusElement = document.getElementById('recommendationSaveStatus');
        statusElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        
        fetch("{{ route('legal.audit.save.summaries') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                audit_id: auditId,
                recommendations: recommendations
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                showSaveStatus('recommendationSaveStatus', data.message, 'success');
                
                // Visual feedback
                const textarea = document.getElementById('recommendations');
                textarea.classList.add('is-valid');
                setTimeout(() => {
                    textarea.classList.remove('is-valid');
                }, 3000);
            } else {
                showSaveStatus('recommendationSaveStatus', data.message || 'Save failed', 'danger');
            }
        })
        .catch(error => {
            console.error('Error saving recommendations:', error);
            showSaveStatus('recommendationSaveStatus', 'Network error. Please try again.', 'danger');
        });
    }

    // Helper function to show save status
    function showSaveStatus(elementId, message, type) {
        const element = document.getElementById(elementId);
        let icon = '';
        
        switch(type) {
            case 'success':
                icon = '<i class="fas fa-check-circle text-success me-1"></i>';
                break;
            case 'danger':
                icon = '<i class="fas fa-times-circle text-danger me-1"></i>';
                break;
            case 'warning':
                icon = '<i class="fas fa-exclamation-triangle text-warning me-1"></i>';
                break;
            default:
                icon = '<i class="fas fa-info-circle text-info me-1"></i>';
        }
        
        element.innerHTML = `${icon}${message}`;
        
        // Clear status after 5 seconds
        setTimeout(() => {
            element.innerHTML = '';
        }, 5000);
    }

    // Save all data in Step 4 before final submission
    function saveAllStep4Data(callback) {
        const auditId = document.getElementById('audit_id').value;
        const summaryElements = document.querySelectorAll('.parameter-summary');
        const recommendations = document.getElementById('recommendations').value;
        
        if (!auditId) {
            if (callback) callback(false, 'No audit ID found');
            return;
        }
        
        const summaries = {};
        summaryElements.forEach(element => {
            const paramId = element.dataset.parameterId;
            summaries[paramId] = element.value;
        });
        
        fetch("{{ route('legal.audit.save.summaries') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                audit_id: auditId,
                summaries: summaries,
                recommendations: recommendations
            })
        })
        .then(res => res.json())
        .then(data => {
            if (callback) {
                callback(data.status, data.message);
            }
        })
        .catch(error => {
            console.error('Error saving all data:', error);
            if (callback) {
                callback(false, 'Network error: ' + error.message);
            }
        });
    }

    // Combined function to save summaries/recommendations and update status
    function saveAndUpdateStatus(status) {
        const btn = event.target;
        const originalText = btn.innerHTML;

        // Show loading state
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        btn.disabled = true;

        // First save summaries and recommendations
        saveAllStep4Data(function(success, message) {
            if (success) {
                // Now update the audit status
                fetch("{{ route('legal.audit.update-status') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        audit_id: document.getElementById('audit_id').value,
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        alert('Summaries, recommendations, and audit status saved successfully!');
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1500);
                        }
                    } else {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        alert(data.message || 'Failed to update audit status');
                    }
                })
                .catch(error => {
                    console.error(error);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    alert('Network error occurred while updating status. Please try again.');
                });
            } else {
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('Failed to save summaries and recommendations: ' + message);
            }
        });
    }

    // Keep the original updateStatus function for backward compatibility
    function updateStatus(status) {
        const btn = event.target;
        const originalText = btn.innerHTML;

        // Show loading state
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
        btn.disabled = true;

        fetch("{{ route('legal.audit.update-status') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    audit_id: document.getElementById('audit_id').value,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    alert(data.message);
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                } else {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    alert(data.message || 'Something went wrong');
                }
            })
            .catch(error => {
                console.error(error);
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('Network error occurred. Please try again.');
            });
    }
</script>
@endsection