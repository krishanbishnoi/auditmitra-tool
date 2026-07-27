@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card audit-view-card border-0 shadow-lg">
                <div class="card-header bg-primary text-white py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fa fa-file-text mr-2"></i>
                            Legal Audit Report - Detailed View
                        </h5>
                        <div class="d-flex align-items-center">
                            <span class="badge badge-light text-primary mr-3 py-2 px-3">
                                <i class="fa fa-calendar mr-1"></i>
                                {{ date('d M Y', strtotime($audit->audit_date ?? now())) }}
                            </span>
                            <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                                <i class="fa fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    {{-- ================= AUDIT DETAILS SECTION ================= --}}
                    <div class="audit-details-section mb-5">
                        <div class="section-header mb-4">
                            <h6 class="font-weight-bold text-primary mb-0">
                                <i class="fa fa-info-circle mr-2"></i>
                                Audit Information
                            </h6>
                            <div class="section-divider"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="info-card">
                                    <div class="info-icon bg-primary-light">
                                        <i class="fa fa-briefcase text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Advocate Name</div>
                                        <div class="info-value">{{ $audit->advocate_name ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="info-card">
                                    <div class="info-icon bg-info-light">
                                        <i class="fa fa-id-badge text-info"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Auditor Name</div>
                                        <div class="info-value">{{ $audit->auditor_name ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="info-card">
                                    <div class="info-icon bg-success-light">
                                        <i class="fa fa-calendar text-success"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Empanelled From</div>
                                        <div class="info-value">{{ $audit->empanelled_from ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="info-card">
                                    <div class="info-icon bg-warning-light">
                                        <i class="fa fa-user text-warning"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Legal Manager</div>
                                        <div class="info-value">{{ $audit->legal_manager ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="info-card">
                                    <div class="info-icon bg-secondary-light">
                                        <i class="fa fa-map-pin text-secondary"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Location</div>
                                        <div class="info-value">
                                            {{ $audit->location ?? '-' }}, {{ $audit->state ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="info-card">
                                    <div class="info-icon bg-purple-light">
                                        <i class="fa fa-home text-purple"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Audit Date</div>
                                        <div class="info-value">{{ $audit->audit_date ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- ================= OFFICE PHOTO & ADDRESS SIDE BY SIDE ================= --}}
                        <div class="row mt-4">
                            {{-- Office Photo Column --}}
                            @if($audit->auditor_artifact_image)
                            <div class="col-md-6 mb-4">
                                <div class="section-header mb-3">
                                    <h6 class="font-weight-bold text-primary mb-0">
                                        <i class="fa fa-camera mr-2"></i>
                                        Office Photograph
                                    </h6>
                                    <div class="section-divider"></div>
                                </div>
                                
                                <div class="office-photo-card">
                                    <div class="office-photo-wrapper">
                                        @php
                                            // Correct image URL
                                            $imageUrl = asset('storage/app/public/' . $audit->auditor_artifact_image);
                                        @endphp
                                        <img src="{{ $imageUrl }}" 
                                             alt="Office Photograph" 
                                             class="office-photo-img">
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            {{-- Address Column --}}
                            <div class="col-md-6 mb-4">
                                <div class="section-header mb-3">
                                    <h6 class="font-weight-bold text-primary mb-0">
                                        <i class="fa fa-map-pin mr-2"></i>
                                        Full Address
                                    </h6>
                                    <div class="section-divider"></div>
                                </div>
                                
                                <div class="info-card full-width h-100">
                                    <div class="info-icon bg-teal-light">
                                        <i class="fa fa-map-pin text-teal"></i>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-value address-text">{{ $audit->address ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
@php
    $summarizedParameters = array_filter($parameters, function($param) {
        return !empty($param['summary']);
    });
@endphp

@if(count($summarizedParameters) > 0)
<div class="parameter-summaries-section mb-5">
    <div class="section-header mb-4">
        <h6 class="font-weight-bold text-primary mb-0">
            <i class="fa fa-file-text-o mr-2"></i>
            Parameter-wise Summaries
        </h6>
        <div class="section-divider"></div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th style="width: 25%">Parameter Name</th>
                    <th>Summary</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summarizedParameters as $param)
                <tr>
                    <td class="font-weight-bold text-primary">
                        <i class="fa fa-chevron-right mr-2"></i>
                        {{ $param['parameter_name'] }}
                    </td>
                    <td class="summary-text">
                        {{ $param['summary'] }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
                    {{-- ================= PARAMETERS SECTION ================= --}}
                    <div class="parameters-section">
                        <div class="section-header mb-4">
                            <h6 class="font-weight-bold text-primary mb-0">
                                <i class="fa fa-clipboard mr-2"></i>
                                Audit Parameters & Findings
                            </h6>
                            <div class="section-divider"></div>
                        </div>

                        @if(count($parameters) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover audit-parameters-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="20%" class="pl-4">Parameter</th>
                                            <th width="30%" class="pl-4">Sub-Parameter</th>
                                            <th width="50%" class="pl-4">Remarks & Artifacts</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($parameters as $parameter)
                                            @php $rowspan = count($parameter['sub_parameters']); @endphp

                                            @foreach ($parameter['sub_parameters'] as $index => $sp)
                                                <tr class="parameter-row {{ $index === 0 ? 'first-sub-param' : '' }}">
                                                    @if ($index === 0)
                                                        <td rowspan="{{ $rowspan }}" class="parameter-name-cell align-top">
                                                            <div class="parameter-name-wrapper">
                                                                <span class="parameter-badge mr-2">
                                                                    {{ $loop->parent->iteration }}
                                                                </span>
                                                                <strong class="parameter-title">
                                                                    {{ $parameter['parameter_name'] }}
                                                                </strong>
                                                            </div>
                                                        </td>
                                                    @endif

                                                    <td class="sub-parameter-cell align-top">
                                                        <div class="sub-parameter-content">
                                                            <span class="sub-param-badge mr-2">
                                                                {{ $index + 1 }}
                                                            </span>
                                                            {{ $sp['sub_parameter'] }}
                                                        </div>
                                                    </td>

                                                    <td class="remarks-cell align-top">
                                                        <div class="remarks-wrapper">
                                                            {{-- Remarks Section --}}
                                                            <div class="remarks-content">
                                                                <div class="remarks-label">Remarks:</div>
                                                                <div class="remarks-text {{ empty($sp['remark']) ? 'text-muted' : '' }}">
                                                                    {{ $sp['remark'] ?? 'No remarks provided' }}
                                                                </div>
                                                            </div>

                                                            {{-- Artifacts Section --}}
                                                            @if(!empty($sp['artifacts']))
                                                                <div class="artifacts-section mt-3">
                                                                    <div class="artifacts-label">Attachments:</div>
                                                                    <div class="artifacts-grid">
                                                                        @foreach ($sp['artifacts'] as $file)
                                                                            @php
                                                                                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                                                $isImage = in_array($extension, ['jpg','jpeg','png','webp','gif']);
                                                                                $filename = basename($file);
                                                                            @endphp

                                                                            <div class="artifact-card">
                                                                                @if ($isImage)
                                                                                    <a href="{{ $file }}" target="_blank" class="artifact-image-link">
                                                                                        <div class="artifact-image-wrapper">
                                                                                            <img src="{{ $file }}" class="artifact-image" alt="Artifact">
                                                                                            <div class="artifact-overlay">
                                                                                                <i class="fa fa-expand"></i>
                                                                                            </div>
                                                                                        </div>
                                                                                    </a>
                                                                                @else
                                                                                    <a href="{{ $file }}" target="_blank" class="artifact-file-link">
                                                                                        <div class="artifact-file-wrapper">
                                                                                            <i class="fa fa-file artifact-file-icon"></i>
                                                                                            <div class="artifact-file-name" title="{{ $filename }}">
                                                                                                {{ Str::limit($filename, 20) }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </a>
                                                                                @endif
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state text-center py-5">
                                <i class="fa fa-clipboard fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No parameters found</h5>
                                <p class="text-muted">This audit doesn't have any parameters assigned yet.</p>
                            </div>
                        @endif
                    </div>

                    {{-- ================= RECOMMENDATIONS SECTION ================= --}}
@if(!empty($recommendations))
<div class="recommendations-section mb-5">
    <div class="section-header mb-4">
        <h6 class="font-weight-bold text-primary mb-0">
            <i class="fa fa-lightbulb-o mr-2"></i>
            Recommendations
        </h6>
        <div class="section-divider"></div>
    </div>

    <div class="recommendations-card">
        <div class="recommendations-content">
            {!! nl2br(e($recommendations)) !!}
        </div>
    </div>
</div>
@endif

{{-- ================= PARAMETER SUMMARIES SECTION ================= --}}


                    {{-- ================= FOOTER ACTIONS ================= --}}
                    <div class="footer-actions mt-5 pt-4 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="audit-meta">
                                <small class="text-muted">
                                    <i class="fa fa-clock-o mr-1"></i>
                                    Last updated: {{ date('d M Y, h:i A', strtotime($audit->updated_at ?? now())) }}
                                </small>
                            </div>
                            <div class="action-buttons">
                                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mr-2">
                                    <i class="fa fa-arrow-left mr-1"></i> Go Back
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= IMAGE MODAL ================= --}}
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Office Photograph</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" alt="Office Photograph" class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- ================= ENHANCED STYLES ================= --}}
<style>

    /* ===== Recommendations Section ===== */
.recommendations-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.recommendations-content {
    font-size: 15px;
    line-height: 1.6;
    color: #2c3e50;
    white-space: pre-wrap;
    word-wrap: break-word;
}

/* ===== Parameter Summaries ===== */
.parameter-summaries-list {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.parameter-summary-item {
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.3s ease;
}

.parameter-summary-item:last-child {
    border-bottom: none;
}

.parameter-summary-item:hover {
    background-color: #f8f9fa;
}

.parameter-summary-header h6 {
    color: #2c3e50;
    font-weight: 600;
    font-size: 15px;
}

.parameter-summary-header .fa-chevron-right {
    color: #667eea;
    font-size: 12px;
}

.summary-text {
    font-size: 14px;
    line-height: 1.5;
    color: #495057;
    margin-left: 20px;
}
:root {
    --primary-light: #e3f2fd;
    --info-light: #e1f5fe;
    --success-light: #e8f5e9;
    --warning-light: #fff3e0;
    --secondary-light: #f5f5f5;
    --purple-light: #f3e5f5;
    --teal-light: #e0f2f1;
    --border-radius: 10px;
    --transition-speed: 0.3s;
}

.audit-view-card {
    border-radius: var(--border-radius);
    overflow: hidden;
}

.card-header.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* ===== Audit Details Section ===== */
.section-header {
    position: relative;
}

.section-divider {
    height: 3px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    width: 80px;
    margin-top: 8px;
    border-radius: 2px;
}

.info-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    height: 100%;
    display: flex;
    align-items: center;
    transition: all var(--transition-speed);
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.info-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-color: #667eea;
}

.info-card.full-width {
    min-height: 80px;
}

.info-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 16px;
    flex-shrink: 0;
}

.bg-primary-light { background-color: var(--primary-light); }
.bg-info-light { background-color: var(--info-light); }
.bg-success-light { background-color: var(--success-light); }
.bg-warning-light { background-color: var(--warning-light); }
.bg-secondary-light { background-color: var(--secondary-light); }
.bg-purple-light { background-color: var(--purple-light); }
.bg-teal-light { background-color: var(--teal-light); }

.info-content {
    flex: 1;
}

.info-label {
    font-size: 12px;
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.info-value {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    line-height: 1.4;
}

.address-text {
    font-size: 14px;
    line-height: 1.6;
}

/* ===== Office Photograph Section ===== */
.office-photo-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 15px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    height: 100%;
}

.office-photo-wrapper {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    background: #f5f5f5;
    height: 100%;
    min-height: 200px;
}

.office-photo-img {
    width: 100%;
    height: 100%;
    max-height: 300px;
    object-fit: cover;
    cursor: pointer;
    transition: transform var(--transition-speed);
}

.office-photo-img:hover {
    transform: scale(1.02);
}

.office-photo-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.3), transparent);
    display: flex;
    align-items: flex-end;
    justify-content: center;
    padding: 20px;
    opacity: 0;
    transition: opacity var(--transition-speed);
}

.office-photo-wrapper:hover .office-photo-overlay {
    opacity: 1;
}

.view-full-btn {
    backdrop-filter: blur(5px);
    background: rgba(255,255,255,0.9);
    border: none;
    border-radius: 6px;
    padding: 8px 16px;
    font-weight: 500;
    transition: all var(--transition-speed);
}

.view-full-btn:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* ===== Parameters Table ===== */
.audit-parameters-table {
    border-collapse: separate;
    border-spacing: 0;
    border: 1px solid #e0e0e0;
    border-radius: var(--border-radius);
    overflow: hidden;
}

.audit-parameters-table thead th {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-bottom: 2px solid #dee2e6;
    color: #2c3e50;
    font-weight: 600;
    padding: 16px;
    text-transform: uppercase;
    font-size: 13px;
    letter-spacing: 0.5px;
}

.audit-parameters-table tbody tr {
    transition: background-color var(--transition-speed);
}

.audit-parameters-table tbody tr:hover {
    background-color: #f8f9fa;
}

.audit-parameters-table tbody tr:not(:last-child) {
    border-bottom: 1px solid #e9ecef;
}

.parameter-name-cell {
    background-color: #f8f9fa;
    border-right: 2px solid #dee2e6;
}

.parameter-name-wrapper {
    display: flex;
    align-items: flex-start;
}

.parameter-badge {
    width: 24px;
    height: 24px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
    flex-shrink: 0;
}

.parameter-title {
    color: #2c3e50;
    font-size: 15px;
    line-height: 1.4;
}

.sub-parameter-cell {
    border-left: 1px solid #e9ecef;
}

.sub-parameter-content {
    display: flex;
    align-items: flex-start;
    padding: 4px 0;
}

.sub-param-badge {
    width: 20px;
    height: 20px;
    background-color: #e3f2fd;
    color: #2196f3;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 600;
    flex-shrink: 0;
    margin-top: 2px;
}

.remarks-cell {
    border-left: 1px solid #e9ecef;
}

.remarks-wrapper {
    padding: 12px 0;
}

.remarks-content {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 12px;
    border-left: 4px solid #667eea;
}

.remarks-label {
    font-size: 12px;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 6px;
    letter-spacing: 0.5px;
}

.remarks-text {
    font-size: 14px;
    line-height: 1.6;
    color: #2c3e50;
}

/* ===== Artifacts Section ===== */
.artifacts-section {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 16px;
}

.artifacts-label {
    font-size: 12px;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 12px;
    letter-spacing: 0.5px;
}

.artifacts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
}

.artifact-card {
    transition: transform var(--transition-speed);
}

.artifact-card:hover {
    transform: translateY(-2px);
}

.artifact-image-link {
    display: block;
    text-decoration: none;
    border-radius: 8px;
    overflow: hidden;
    position: relative;
}

.artifact-image-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    height: 120px;
}

.artifact-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--transition-speed);
}

.artifact-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity var(--transition-speed);
}

.artifact-image-link:hover .artifact-overlay {
    opacity: 1;
}

.artifact-overlay i {
    color: white;
    font-size: 20px;
}

.artifact-image-link:hover .artifact-image {
    transform: scale(1.05);
}

.artifact-file-link {
    display: block;
    text-decoration: none;
    color: inherit;
}

.artifact-file-wrapper {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 16px;
    text-align: center;
    transition: all var(--transition-speed);
    height: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.artifact-file-wrapper:hover {
    background-color: #e9ecef;
    border-color: #667eea;
}

.artifact-file-icon {
    font-size: 24px;
    color: #667eea;
    margin-bottom: 8px;
}

.artifact-file-name {
    font-size: 11px;
    color: #6c757d;
    font-weight: 500;
    word-break: break-all;
    text-align: center;
    max-width: 100%;
}

/* ===== Empty State ===== */
.empty-state {
    padding: 60px 20px;
}

/* ===== Footer Actions ===== */
.footer-actions {
    padding-top: 20px;
}

.audit-meta {
    font-size: 14px;
}

.action-buttons .btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 500;
}

.action-buttons .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.action-buttons .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

/* ===== Responsive Design ===== */
@media (max-width: 768px) {
    .info-card {
        flex-direction: column;
        text-align: center;
        padding: 16px;
    }
    
    .info-icon {
        margin-right: 0;
        margin-bottom: 12px;
    }
    
    .audit-parameters-table {
        font-size: 13px;
    }
    
    .artifacts-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
}

@media print {
    .action-buttons,
    .card-header .btn,
    .office-photo-overlay {
        display: none !important;
    }
    
    .info-card:hover {
        transform: none;
        box-shadow: none;
    }
}
</style>

