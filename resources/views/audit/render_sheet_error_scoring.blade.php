@extends('layouts.master')
@section('css')
{{--
<link rel="stylesheet" href="{{URL::asset('base/style.bundle.css')}}"> --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" ; rel="stylesheet" />
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" />
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<style>
    .sp-row .row {
        margin-bottom: 15px;
    }


    .sp-row .row {
        margin-bottom: 15px;
    }

    .flex-container {
        display: flex;
        align-items: center;
    }

    .flex-container {
        display: flex;
        align-items: center;
    }

    .kt-font-bolder {
        font-weight: 600 !important;
    }

    #seprator {
        margin: 2.5rem 0 0 0;
    }

    #seprator {
        margin: 2.5rem 0 0 0;
    }

    .kt-separator.kt-separator--space-lg {
        margin: 2.5rem 0;
    }

    .kt-separator.kt-separator--border-dashed {
        border-bottom: 1px dashed #ebedf2;
    }

    .kt-separator {
        height: 0;
        margin: 20px 0;
        border-bottom: 1px solid #ebedf2;
    }

    .kt-font-primary {
        color: #5867dd !important;
    }

    .kt-font-bolder {
        font-weight: 600 !important;
    }

    .kt-font-bold {
        font-weight: 500 !important;
    }

    .centerparameter {
        display: flex;
        justify-content: center;
        align-items: center
    }


    /* otp css */

    /* Modal Background */
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.7);
        /* Dark background for better contrast */
    }

    /* Modal Content */
    .modal-content {
        border-radius: 10px;
        /* Rounded corners for the modal */
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        /* Soft shadow effect */
    }

    /* Modal Header */
    .modal-header {
        background-color: #007bff;
        /* Primary color for header */
        color: white;
        /* White text color */
        border-bottom: none;
        /* Remove bottom border */
    }

    /* Modal Title */
    .modal-title {
        font-size: 1.5rem;
        /* Larger font size for title */
        font-weight: bold;
        /* Bold title */
    }

    /* Modal Body */
    .modal-body {
        padding: 20px;
        /* Increased padding for body */
    }

    /* Input Field */
    .form-control {
        border-radius: 5px;
        /* Rounded corners for input */
        border: 1px solid #ced4da;
        /* Border color */
    }

    /* Buttons */
    .btn {
        border-radius: 5px;
        /* Rounded corners for buttons */
    }

    .btn-secondary {
        background-color: #6c757d;
        /* Secondary button color */
        border: none;
        /* Remove border */
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        /* Darker shade on hover */
    }

    .btn-primary {
        background-color: #007bff;
        /* Primary button color */
        border: none;
        /* Remove border */
    }

    .btn-primary:hover {
        background-color: #0056b3;
        /* Darker shade on hover */
    }

    /* Resend OTP Button */
    #resendOtpButton {
        margin-top: 10px;
        /* Space above the resend button */
    }

    #loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        /* White background with opacity */
        z-index: 9999;
        /* Ensure it covers everything */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #loader p {
        font-size: 24px;
        color: #333;
        font-weight: bold;
    }
    .step-progress {
        display: flex;
        justify-content: center;
        margin-bottom: 2rem;
    }

    .step {
        display: flex;
        align-items: center;
        margin: 0 1.5rem;
        cursor: pointer;
        color: #ccc;
    }

    .step.completed .step-circle,
    .step.active .step-circle {
        background: #6c63ff;
        color: white;
        border-radius: 50%;
    }

    .step-circle {
        width: 30px;
        height: 30px;
        border: 2px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 8px;
    }

    .step-label {
        font-weight: 600;
        font-size: 1rem;
    }

    .step.completed,
    .step.active {
        color: #6c63ff;
        font-weight: 700;
    }
     .step-card {
            transition: all 0.3s ease;
        }

        /* Container for each sub-parameter block */
    .step-card .tab-content .parameter-section > .row.align-items-start.mb-3 {
        background: #f9f7ff;          /* Soft lavender background */
        border-radius: 12px;
        box-shadow: 0 2px 6px rgb(111 66 193 / 0.15);
        padding: 20px 15px;
        margin-bottom: 20px;
        border-left: 6px solid #6f42c1; /* Purple accent bar */
        transition: box-shadow 0.3s ease;
    }

    .step-card .tab-content .parameter-section > .row.align-items-start.mb-3:hover {
        box-shadow: 0 4px 12px rgb(111 66 193 / 0.25);
    }

    /* Sub Parameter title and info icon */
    .step-card .tab-content .parameter-section > .row.align-items-start.mb-3 .col-md-3:first-child .col-md-6:first-child {
        font-weight: 600;
        font-size: 1rem;
        color: #4b306a;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Info icon */
    .step-card .tab-content .parameter-section > .row.align-items-start.mb-3 .la-info-circle {
        color: #f0ad4e; /* warm info color */
        cursor: pointer;
    }

    /* Observation select */
    .step-card select.form-control.0bervation {
        background-color: #fff;
        border: 1.5px solid #b3a7d3;
        border-radius: 6px;
        color: #4b306a;
        font-weight: 500;
        transition: border-color 0.3s ease;
    }
    .step-card select.form-control.0bervation:focus {
        border-color: #6f42c1;
        box-shadow: 0 0 8px #6f42c1aa;
        outline: none;
    }

    /* Score input/select */
    .step-card select.ratingSelect,
    .step-card input.form-control[readonly] {
        background-color: #faf8ff;
        border-radius: 6px;
        border: 1.5px solid #b3a7d3;
        color: #4b306a;
        font-weight: 500;
    }

    /* Artifact button */
    .step-card button.artifactModal.btn-info.btn-sm {
        background-color: #6f42c1;
        border-color: #5a32a3;
        color: #fff;
        font-weight: 600;
        border-radius: 6px;
        padding: 6px 12px;
        transition: background-color 0.3s ease;
    }
    .step-card button.artifactModal.btn-info.btn-sm:hover {
        background-color: #59308a;
    }

    /* Checkbox */
    .step-card input[type="checkbox"] {
        transform: scale(1.3);
        cursor: pointer;
        margin-left: 8px;
    }

    /* Textarea */
    .step-card textarea.form-control {
        border-radius: 8px;
        border: 1.5px solid #b3a7d3;
        resize: vertical;
        min-height: 70px;
        font-size: 0.95rem;
        padding: 10px;
        color: #4b306a;
        transition: border-color 0.3s ease;
    }
    .step-card textarea.form-control:focus {
        border-color: #6f42c1;
        box-shadow: 0 0 8px #6f42c1aa;
        outline: none;
    }

    /* Voice button */
    .step-card button.voice-btn.btn-outline-secondary.btn-sm {
        border-radius: 6px;
        font-size: 0.85rem;
        padding: 5px 10px;
        color: #6f42c1;
        border-color: #6f42c1;
        background-color: #f6f4ff;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .step-card button.voice-btn.btn-outline-secondary.btn-sm:hover {
        background-color: #6f42c1;
        color: #fff;
    }

    /* Error messages */
    .step-card .text-danger.small {
        font-weight: 600;
        margin-top: 4px;
    }

    /* Nav tabs */
    .step-card .nav-tabs .nav-link.active {
        background: linear-gradient(90deg, #6f42c1, #a678d1);
        color: #fff !important;
        font-weight: 700;
        border-radius: 12px 12px 0 0;
        box-shadow: 0 4px 8px rgb(111 66 193 / 0.3);
    }

    .step-card .nav-tabs .nav-link {
        color: #6f42c1;
        font-weight: 600;
        border-radius: 12px 12px 0 0;
        border: 1px solid transparent;
        padding: 12px 20px;
        transition: color 0.3s ease, background-color 0.3s ease;
    }

    .step-card .nav-tabs .nav-link:hover {
        background-color: #e6dfff;
        color: #4b306a;
        border-color: #a678d1 #a678d1 transparent;
    }

    /* Step navigation buttons */
    .step-card .step-navigation button.btn-primary {
        background: linear-gradient(90deg, #6f42c1, #a678d1);
        border: none;
        font-weight: 700;
        border-radius: 8px;
        padding: 10px 18px;
        box-shadow: 0 4px 12px rgb(111 66 193 / 0.3);
        transition: background-color 0.3s ease;
    }

    .step-card .step-navigation button.btn-primary:hover {
        background: linear-gradient(90deg, #59308a, #8765af);
    }

    .step-card .step-navigation button.btn-secondary {
        background-color: #dcd7e8;
        border: none;
        font-weight: 600;
        border-radius: 8px;
        padding: 10px 18px;
        color: #6f42c1;
        transition: background-color 0.3s ease;
    }

    .step-card .step-navigation button.btn-secondary:hover {
        background-color: #c9c3e0;
    }

    /* Card and header */
    .card.step-card[data-step="3"] {
        border-radius: 10px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Card Header – keep inline gradient or unify */
    .card.step-card[data-step="3"] .card-header {
        font-size: 1.25rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        padding: 1rem 1.5rem;
        background: linear-gradient(to right, #845ec2, #9575cd, #a78bfa, #bfa3fc, #d1b8ff);
        color: #fff;
    }

    /* Column Header Row */
    .card.step-card[data-step="3"] .row:first-child {
        background-color: #ede7f6;
        border-bottom: 2px solid #b39ddb;
        padding: 0.75rem 1rem;
        font-weight: 700;
        color: #5e35b1;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Each Data Row */
    .card.step-card[data-step="3"] .row:not(:first-child) {
        background-color: #f3e5f5;
        padding: 1rem 1rem;
        border-bottom: 1px solid #d1c4e9;
        transition: background-color 0.3s ease;
        border-radius: 6px;
        margin-bottom: 10px;
        box-shadow: 0 1px 4px rgba(142, 81, 193, 0.15);
    }

    /* Hover Effect */
    .card.step-card[data-step="3"] .row:not(:first-child):hover {
        background-color: #e1bee7;
        box-shadow: 0 4px 10px rgba(142, 81, 193, 0.3);
    }

    /* Parameter Column */
    .card.step-card[data-step="3"] .kt-font-primary {
        color: #6a1b9a;
        font-weight: 600;
    }

    /* Scored or Fatal */
    .card.step-card[data-step="3"] .kt-font-danger {
        color: #c2185b;
        font-weight: 600;
    }

    /* Over All Row (Bottom Summary Row) */
    .card.step-card[data-step="3"] .row:last-child {
        background: linear-gradient(90deg, #d1c4e9, #b39ddb);
        border-top: 2px solid #9575cd;
        font-weight: 700;
        color: #4527a0;
        box-shadow: 0 3px 8px rgba(103, 58, 183, 0.4);
        border-radius: 6px;
    }


    /* Step navigation buttons container fix */
    .step-navigation {
        margin-top: 20px;
        text-align: right;
    }

    /* Buttons spacing */
    .step-navigation .btn {
        min-width: 120px;
        font-weight: 600;
    }

    /* Final buttons styling */
    .step-final-buttons {
        margin-top: 30px;
    }

    .step-final-buttons .btn {
        margin-left: 8px;
        min-width: 90px;
        font-weight: 600;
        border-radius: 4px;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .step-final-buttons .btn-primary:hover {
        background-color: #ffc107;
        border-color: #e6b800;
        box-shadow: 0 0 8px #f7d154;
        color: #333;
    }

    .step-final-buttons .btn-danger:hover {
        background-color: #d9534f;
        box-shadow: 0 0 8px #b52a2a;
    }

    /* error count  */
     .error-count-select {
        background-color: #fff;
        border: 1.5px solid #b3a7d3;
        border-radius: 6px;
        color: #4b306a;
        font-weight: 500;
        transition: border-color 0.3s ease;
    }
    .error-count-select:focus {
        border-color: #6f42c1;
        box-shadow: 0 0 8px #6f42c1aa;
        outline: none;
    }
    .error-count-label {
        font-size: 0.85rem;
        color: #4b306a;
        font-weight: 600;
        margin-top: 8px;
        margin-bottom: 4px;
        display: block;
    }

    /* Category Scorecard Styles (added) */
    .category-scorecard-table {
        width: 100%;
        margin-top: 20px;
        margin-bottom: 20px;
        border-collapse: collapse;
    }
    .category-scorecard-table th, 
    .category-scorecard-table td {
        padding: 10px 8px;
        border-bottom: 1px solid #ebedf2;
        text-align: left;
    }
    .category-scorecard-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #5867dd;
    }
    .category-scorecard-table tr:last-child td {
        border-bottom: none;
    }
    .category-section {
        margin-top: 20px;
        border-top: 2px solid #5867dd;
        padding-top: 15px;
    }
    .total-row {
        background-color: #f0f3ff;
        font-weight: bold;
    }
    .tatalScored {
        font-weight: bold;
    }
</style>

@endsection
@section('title')
Audit
@endsection

@section('sh-detail')
Messages
@endsection
@section('content')
<?php 

    $allocatedmodule=App\Helpers\Helper::allocatedmodulelist();
    $user=App\User::find(Auth::user()->id);

	$roles = Auth::user()->roles;
	$isAuditor = $roles->isNotEmpty() ? $roles->first()->name : 'No role assigned';
	$isQualityAuditor = Auth::user()->roles->contains('name', 'Quality Auditor');

    // ==================== CATEGORY SCORING ADDITIONS START ====================
    // Build category metadata from the QM sheet ($data->parameter)
    $categoriesMeta = [];
    $subParCategoryMap = [];
    $subParWeightMap = [];
    $hasCategories = false;

    foreach ($data->parameter as $par) {
        $catId = $par->category_id ?? null;
        $catName = $par->category ?? ($catId ? 'Category ' . $catId : null);
        $catWeight = $par->category_weight ?? 0;
        
        if ($catId && $catName) {
            $hasCategories = true;
            if (!isset($categoriesMeta[$catId])) {
                $categoriesMeta[$catId] = [
                    'name' => $catName,
                    'weight' => (float)$catWeight,
                    'total_possible_raw' => 0,
                    'sub_parameters' => []
                ];
            }
        }
        
        foreach ($par->qm_sheet_sub_parameter as $sub) {
            $weight = (float)$sub->weight;
            $subParWeightMap[$sub->id] = $weight;
            
            if ($catId && isset($categoriesMeta[$catId])) {
                $categoriesMeta[$catId]['total_possible_raw'] += $weight;
                $categoriesMeta[$catId]['sub_parameters'][] = $sub->id;
                $subParCategoryMap[$sub->id] = $catId;
            } else {
                $subParCategoryMap[$sub->id] = null;
            }
        }
    }
    // ==================== CATEGORY SCORING ADDITIONS END ====================
?>
<div class="row">
	<div class="col-lg-12" style="margin-top:10x">
	</div>
</div>
<div class="animated fadeIn">
	<div id="loader" style="display:none;">
		<p>Loading...</p>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="card step-card" data-step="1">
				<div class="card-header"
					style="background-image: linear-gradient(to right, rgb(132, 94, 194), rgb(144, 109, 198), rgb(156, 125, 201), rgb(168, 140, 205), rgb(179, 156, 208));color:#fff">
					<strong
						class="card-title">{{($data->lob == 'commercial_vehicle') ? 'Commercial Vehicle' : ucfirst($data->lob)}}
					| {{ucfirst(str_replace('_', ' ', $data->type))}}</strong>
				</div>
				<div class="card-body">
					<div id="loader" style="display:none;">
						<p>Loading...</p>
					</div>
					<div class="row">
						@if($data->type=='branch')
							
				
							<div class="col-md-3 form-group">
								<label>Branch*</label>
								<select name="branch" class="form-control branch js-example-basic-single" id="audit_for">
								<option value="">Choose Branch</option>
								@foreach ($branch as $item)  
									<option value="{{$item->id}}">{{$item->name}}</option>
								@endforeach
								</select>
							</div> 
						@elseif($data->type=='agency')
						
							<div class="col-md-3 form-group">
								<label>Agency*</label>
								<select name="agency" class="form-control agency js-example-basic-single" id="audit_for">
								<option value="">Choose Agency</option>
								@foreach ($agency as $item)  
									<option value="{{$item->id}}">{{$item->name}}</option>
								@endforeach
								</select>
							</div> 
						@elseif($data->type=='yard')
							<div class="col-md-3 form-group">
								<label>Yard*</label>
								<select name="yard" class="form-control yard js-example-basic-single" id="audit_for">
								<option value="">Choose Yard</option>
								@foreach ($yard as $item)  
									<option value="{{$item->id}}">{{$item->name}}</option>
								@endforeach
								</select>
							</div>
                            @elseif($data->type=='yard_repo')
							<div class="col-md-3 form-group">
								<label>Yard Repo*</label>
								<select name="yard_repo" class="form-control yard_repo js-example-basic-single" id="audit_for">
								<option value="">Choose Yard Repo</option>
								@foreach ($yardRepo as $item)  
									<option value="{{$item->id}}">{{$item->name}}</option>
								@endforeach
								</select>
							</div>
							@elseif($data->type=='branch_repo')
							<div class="col-md-3 form-group">
								<label>Branch Repo*</label>
								<select name="branch_repo" class="form-control branch_repo js-example-basic-single" id="audit_for">
								<option value="">Choose Branch Repo</option>
								@foreach ($branchRepo as $item)  
									<option value="{{$item->id}}">{{$item->name}}</option>
								@endforeach
								</select>
							</div>
							@elseif($data->type=='agency_repo')
							
							<div class="col-md-3 form-group">
								<label>Agency Repo*</label>
								<select name="agency_repo" class="form-control agency_repo js-example-basic-single" id="audit_for">
								<option value="">Choose Agency Repo</option>
								@foreach ($agencyRepo as $item)  
									<option value="{{$item->id}}">{{$item->name}}</option>
								@endforeach
								</select>
							</div>
						@endif
						<div class="col-md-3 form-group">
							<label>Audit Cycle*</label>
							<select name="audit_cycle" class="form-control audit_cycle js-example-basic-single"
								id="audit_cycle" required="true">
								@foreach ($cycle as $item)
								<option value="{{$item->id}}">{{$item->name}}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-3 form-group">
                            <label>Audit Date*</label>
                            <input type="text" name="audit_date" class="form-control audit_date" id="audit_date"
                                   placeholder="Choose Audit Date" required>
                        </div>
						<div class="col-md-3 form-group" id="product">
							<label>Product*</label>
							<select name="product" class="form-control product" id="productSelect" readonly>
								<option value="">Choose Product</option>
								@foreach ($Products as $id => $name)
								<option value="{{ $id }}">{{ $name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="row" id="data">
					</div>
                    
					<div class="row">
                        @if(in_array(13,  $allocatedmodule))
						<div class="col-md-4 form-group">
							<label>Level 3</label>
							{!! Form::select('lavel_3', $formattedUsers,'', ['id'=>'collection_manager-select', 'class' => 'form-control js-example-basic-single']) !!}
						</div>@endif
						<div class="col-md-4 form-group" style="">
							<label>Present Auditor</label>
							{!! Form::text('present_auditor','', ['id'=>'present_auditor', 'class' => 'form-control js-example-basic-single']) !!}
						</div>

                        @if(auth()->user()->client_id == 15)
                        <div class="col-md-3 form-group" id="sheet">
                            <label for="sheet">Nature of Service</label>
                            <select name="sheet" id="sheet_select" class="form-control sheet" required>
                                <option value="">-- Select Nature of Service --</option>
                                <option value="field">Field</option>
                                <option value="telecalling">Telecalling</option>
                                <option value="field_telecalling">Field + Telecalling</option>
                            </select>
                        </div>
                    @endif
						<!-- <div class="col-md-4 form-group">
							<label>Level 4</label>
							{!! Form::select('lavel_4[]', $formattedUsers, '', ['id'=>'lavel_4','class' => 'form-control js-example-basic-single' , 'multiple' => 'multiple']) !!}
							</div>
							<div class="col-md-4 form-group">
							<label>Level 5</label>
							{!! Form::select('lavel_5[]', $Level_5, null, ['id' => 'lavel_5', 'class' => 'form-control js-example-basic-single', 'multiple' => 'multiple']) !!}
							</div> -->
					</div>
					<div id="intimationUsersContainer">
                        @if(in_array(13,  $allocatedmodule))
						<div class="row intimationUser">
							<div class="col-md-3 form-group">
								<label>Level 4</label>
								{!! Form::select('level_4[]', $Level_5, null, ['class' => 'form-control select2' ,'id'=>'lavel_4']) !!}
							</div>
							<div class="col-md-3 form-group">
								<label>Level 5</label>
								{!! Form::select('level_5[]', $Level_5, null, ['class' => 'form-control select2','id'=>'lavel_5']) !!}
							</div>
							<div class="col-md-3 form-group">
								<button type="button" id="addMoreUsers" class="btn btn-primary" style="margin-top: 11%;">Add More</button>
							</div>
						</div>
                        @endif
					</div>
                    @if(in_array(21,  $allocatedmodule))
                        <div class="col-md-3 form-group">
                            <label>
                                <input type="checkbox" id="virtualAudit"> Virtual Audit
                            </label>
                        </div>
                    
                        <div class="col-md-3 form-group" id="imageCaptureSection" style="display: none;">
                            <label>Auditee Image*</label>
                            <div id="capture-container">
                                <!-- Video element to show webcam feed -->
                                <video id="video" width="100%" height="100%" autoplay style="border: 1px solid #ddd;"></video>
                            
                                <!-- Start Camera Button -->
                                <button id="start-camera" class="btn btn-secondary" style="margin-top: 10px;">Start Camera</button>
                            
                                <!-- Capture Image Button -->
                                <button id="capture" class="btn btn-primary" style="margin-top: 10px; display: none;">Capture Image</button>
                            
                                <!-- Canvas element (hidden) to draw the captured image -->
                                <canvas id="canvas" style="display: none;"></canvas>
                            
                                <!-- Captured image (hidden initially) -->
                                <img id="captured-image" style="display:none; margin-top: 10px; width: 100%; height: auto;" />
                            </div>
                        </div>
                    @endif

					<!-- <div id="AuditUsersContainer">
						<div class="row AuditUser">
						    <div class="col-md-4 form-group">
						        <label>Level 3</label>
						        {!! Form::select('lavel_3', $formattedUsers, '', ['id' => 'collection_manager-select', 'class' => 'form-control js-example-basic-single']) !!}
						    </div>
						    <div class="col-md-4 form-group">
						        <label>Level 4</label>
						        {!! Form::select('lavel_4[]', $formattedUsers, '', ['id' => 'lavel_4', 'class' => 'form-control js-example-basic-single']) !!}
						    </div>
						    <div class="col-md-4 form-group">
						        <label>Level 5</label>
						        {!! Form::select('lavel_5[]', $Level_5, null, ['id' => 'lavel_5', 'class' => 'form-control js-example-basic-single']) !!}
						    </div>
						</div>
						
						<div id="AuditUsersContainer"></div>
						<button type="button" id="addMoreUsers" class="btn btn-primary">Add More</button>
						</div> -->
				</div>
               

                <div class="step-navigation mt-3 mb-3 mr-3 text-right">
                    <button class="btn btn-secondary prev-step">Previous</button>
                    <button class="btn btn-primary next-step">Next</button>
                </div>
			</div>
			<div class="card step-card" data-step="2" style="display: none;">
				<div class="card-header"
                    style="background-image: linear-gradient(to right, rgb(132, 94, 194), rgb(144, 109, 198), rgb(156, 125, 201), rgb(168, 140, 205), rgb(179, 156, 208));color:#fff">
                    <strong class="card-title">Audit</strong>
                </div>
				<div class="card-body">
                     <ul class="nav nav-tabs" id="parameterTabs" role="tablist">
            @foreach ($data->parameter as $index => $item)
                <li class="nav-item">
                    <a class="nav-link {{ $index === 0 ? 'active' : '' }}" id="tab-{{ $item->id }}" data-toggle="tab"
                        href="#param-{{ $item->id }}" role="tab" aria-controls="param-{{ $item->id }}"
                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                        {{ $item->parameter }}
                    </a>
                </li>
            @endforeach
        </ul>
					<div class="tab-content mt-4" id="parameterTabContent">
            @foreach ($data->parameter as $index => $item)
                @php $total = 0; @endphp
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="param-{{ $item->id }}"
                    role="tabpanel" aria-labelledby="tab-{{ $item->id }}">
                    <div class="parameter-section mb-5">
                        <div class="row font-weight-bold mb-2">
                            <div class="col-md-6">Sub Parameter</div>
                            <div class="col-md-3">Observation</div>
                            {{-- <div class="col-md-3">Scored</div> --}}
                            <div class="col-md-3">Action</div>
                        </div>

                        @foreach ($item->qm_sheet_sub_parameter as $value)
                            <div class="row align-items-start mb-3 border-bottom pb-3">
                                {{-- Sub Parameter --}}
                                
                                <div class="col-md-6">
                                    {{ $value->sub_parameter }}
                                    <i title="More info"
                                        class="la la-info-circle kt-font-warning sp-details-top"></i>
                                </div>

                                {{-- Observation --}}
                                <div class="col-md-3">
                                    <select class="form-control 0bervation" id="obs{{ $value->id }}"
                                        data-id="{{ $value->id }}" data-parameterId="{{ $item->id }}"
                                        data-point="{{ $value->weight }}">
                                        <option value="0">Choose type</option>
                                        @if ($value->pass == 1)
                                            <option value="{{ $value->weight }}">Satisfactory</option>
                                        @endif
                                        @if ($value->fail == 1)
                                            <option value="0">Unsatisfactory</option>
                                        @endif
                                        @if ($value->critical == 1)
                                            <option value="Critical">Critical</option>
                                        @endif
                                        @if ($value->na == 1)
                                            <option value="N/A">N/A</option>
                                        @endif
                                        @if ($value->pwd == 1)
                                            <option value="{{ round(($value->weight) / 2, 2) }}">PWD</option>
                                        @endif
                                        @if ($value->per == 1)
                                            <option value="{{ round($value->weight) }}" data-type="rating">Percentage
                                            </option>
                                        @endif
                                    </select>
                                   @if(in_array(22, $allocatedmodule) && $value->use_error_count == 1)
    @php
        $errorScoring = [];
        if (!empty($value->error_scoring)) {
            $decoded = json_decode($value->error_scoring, true);
            if (is_array($decoded)) {
                $errorScoring = $decoded;
            }
        }
    @endphp

    <label class="error-count-label">Error Count</label>
    <select class="form-control error-count-select"
            id="errorCount{{ $value->id }}"
            name="error_count[{{ $value->id }}]"
            data-id="{{ $value->id }}"
            data-base-score="{{ $value->weight }}">

        <option value="0" data-score="{{ $value->weight }}">
            0 errors (Score: {{ $value->weight }})
        </option>

        @foreach ($errorScoring as $rule)
            <option value="{{ $rule['error_count'] }}" data-score="{{ $rule['score'] }}">
                {{ $rule['error_count'] }} error{{ (int)$rule['error_count'] > 1 ? 's' : '' }} (Score: {{ $rule['score'] }})
            </option>
        @endforeach

    </select>

@else
    <input type="hidden"
           id="errorCount{{ $value->id }}"
           name="error_count[{{ $value->id }}]"
           value="0"
           data-id="{{ $value->id }}">
@endif
                                    <span style="display:none" id="org{{ $value->id }}">{{ $value->weight }}</span>
                                </div>

                                {{-- Score --}}
                                <div class="">
                                    <select class="form-control ratingSelect" name="ratingSelect"
                                        id="ratingSelect{{ $value->id }}" style="display:none">
                                        @for ($counting = 0; $counting <= 100; $counting += 5)
                                            <option value="{{ $counting }}"
                                                {{ $counting == 0 ? 'selected' : '' }}>
                                                {{ $counting }}%
                                            </option>
                                        @endfor
                                    </select>

                                    @if ($isQualityAuditor == 1)
                                        <input type="text" id="{{ $value->id }}" readonly class="form-control"
                                            style="display: none;">
                                    @else
                                        <input type="text" id="{{ $value->id }}" readonly class="form-control">
                                    @endif
                                </div>

                                {{-- Actions --}}
                                <div class="col-md-3">
                                    <button class="btn btn-info btn-sm artifactModal mr-1"
                                        data-parameterid="{{ $item->id }}" data-id="{{ $value->id }}">Artifact</button>
                                    <input type="checkbox" id="ackalert{{ $value->id }}"
                                        data-id="{{ $value->id }}" data-parameterId="{{ $item->id }}" />
                                </div>

                                {{-- Error messages --}}
                                <div class="d-flex justify-content-between align-items-start">
                                <div class="p-3 rounded bg-light text-dark" id="error-msg-primary-{{ $value->id }}" style="flex:1; margin-right:10px;"></div>
                                <div class="p-3 rounded bg-light text-danger" id="error-msg-secondary-{{ $value->id }}" style="flex:1; margin-right:10px;"></div>
                                </div>


                                {{-- Remarks --}}
                                <div class="col-md-12 mt-2">
                                    <textarea class="form-control" id="remark{{ $value->id }}" placeholder="Enter Remark Here"
                                        spellcheck="true"></textarea>
                                    <div id="remarkError{{ $value->id }}" class="text-danger small"></div>

                                    @if(in_array(25,  $allocatedmodule))
                                    <button type="button" 
                                        class="btn btn-sm btn-primary mt-2"
                                        onclick="rewriteRemark({{ $value->id }})">
                                            Rewrite Remark
                                    </button>
                                    @endif
                                </div>

                                {{-- Voice Preview --}}
                                <div class="col-md-12 mt-2 d-flex align-items-center justify-content-between">
                                    <div class="preview{{ $value->id }}"></div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm voice-btn"
                                        data-id="{{ $value->id }}">
                                        🎤 Speak Remark
                                    </button>
                                </div>

                                @php
                                    $total += $value->weight;
                                @endphp
                            </div>
                        @endforeach

                        <span style="display:none" id="total{{ $item->id }}">{{ $total }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        <div id="step2ProgressBox" style="
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #fff;
            padding: 16px 24px;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: none;
            transition: all 0.3s ease-in-out;
            min-width: 220px;
            ">
            <div style="font-size: 16px; font-weight: 600; margin-bottom: 4px;">
                📝 Step 2 Progress
            </div>
        
            <div id="step2ProgressText" style="font-size: 20px; font-weight: bold;">
                0 / 0 completed
            </div>
        
            <!-- Progress bar wrapper -->
            <div style="width: 100%; background: rgba(255,255,255,0.2); height: 6px; border-radius: 4px; margin-top: 8px;">
                <div id="step2ProgressBar" style="height: 6px; background: #fff; width: 0%; border-radius: 4px;"></div>
            </div>
        </div>




        {{-- Navigation --}}
            <div class="step-navigation mt-4 text-right">
                <button class="btn btn-secondary prev-step">Previous</button>
                <button class="btn btn-primary next-step">Next</button>
            </div>
        </div>
    </div>

					{{-- 
					<div class="card-footer">
						<button type="submit" class="btn btn-primary btn-sm">
						<i class="fa fa-dot-circle-o"></i> Submit
						</button>
						<button type="reset" class="btn btn-danger btn-sm">
						<i class="fa fa-ban"></i> Reset
						</button>
					</div>
					--}}
				</div>
			</div>
			<div class="card step-card" data-step="3" style="display: none;">
				<div class="card-header step-card-header-purple">
					<strong class="card-title">Result</strong>
					<h5 class="kt-font-bolder kt-font-primary">Parameter Wise Score</h5>
				</div>
				<div class="card-body">
					<div class="row" style="border-bottom: 1px solid rgb(204, 204, 204);">
						<!-- <div class="col-lg-4 kt-font-bolder">&nbsp;</div> -->
						<div class="col-lg-3 kt-font-bolder">Parameter</div>
						<div class="col-lg-3 kt-font-bolder">Scorable</div>
						<div class="col-lg-3 kt-font-bolder">Scored</div>
						<div class="col-lg-2 kt-font-bolder">Scores%</div>
						<div class="col-lg-1 kt-font-bolder">Grade</div>
					</div>
					<!-- <div class="row" style="padding: 15px 0px;">
						<div class="col-lg-2 kt-font-bolder">Parameter</div>
						<div class="col-lg-2 kt-font-bolder">Scorable</div>
						<div class="col-lg-2 kt-font-bolder">With FATAL</div>
						<div class="col-lg-2 kt-font-bolder">Without FATAL</div>
						<div class="col-lg-2 kt-font-bolder">With FATAL</div>
						<div class="col-lg-2 kt-font-bolder">Without FATAL</div>
						</div> -->
					@foreach ($data->parameter as $item)
					<!-- <div class="row" style="border-bottom: 1px solid rgb(204, 204, 204); padding: 20px 0px; height: 100%;">
						<div class="col-lg-2 kt-font-bold kt-font-primary">{{$item->parameter}}</div>
						<div class="col-lg-2" id="scroable{{$item->id}}">0</div>
						<div class="col-lg-2 kt-font-danger" id="wfatal{{$item->id}}">0</div>
						<div class="col-lg-2" id="wnfatal{{$item->id}}">0</div>
						<div class="col-lg-2 kt-font-danger" id="wfatalper{{$item->id}}">0 %</div>
						<div class="col-lg-2" id="wnfatalper{{$item->id}}">0 %</div>
						</div> -->
					<div class="row"
						style="border-bottom: 1px solid rgb(204, 204, 204); padding: 20px 0px; height: 100%;">
						<div class="col-lg-3 kt-font-bold kt-font-primary">{{$item->parameter}}</div>
						<div class="col-lg-3" id="scroable{{$item->id}}">0</div>
						<div class="col-lg-3 kt-font-danger" id="wfatal{{$item->id}}">0</div>
						<div class="col-lg-3" id="wfatalper{{$item->id}}">0</div>
					</div>
					@endforeach
					<!-- <div class="row" style="padding: 20px 0px; height: 100%;">
						<div class="col-lg-2 kt-font-bold kt-font-success">Over All</div>
						<div class="col-lg-2 kt-font-bold" id="scroable">0</div>
						<div class="col-lg-2 kt-font-bold kt-font-danger" id="wfatal">0</div>
						<div class="col-lg-2 kt-font-bold" id="wnfatal">0</div>
						<div class="col-lg-2 kt-font-bold kt-font-danger" id="wfatalper">0%</div>
						<div class="col-lg-2 kt-font-bold"  id="wnfatalper">0%</div>
						</div> -->
					<div class="row" style="padding: 20px 0px; height: 100%;">
						<div class="col-lg-3 kt-font-bold kt-font-success">Over All (Parameter)</div>
						<div class="col-lg-3 kt-font-bold" id="scroable">0</div>
						<div class="col-lg-3 kt-font-bold kt-font-danger" id="wfatal">0</div>
						<div class="col-lg-2 kt-font-bold" id="wfatalper">0</div>
						<div class="col-lg-1 kt-font-bold" id="grade"></div>
					</div>

                    <!-- ==================== CATEGORY SCORING ADDITIONS START ==================== -->
                    @if($hasCategories)
                    <div class="category-section">
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 class="kt-font-bolder kt-font-primary mt-4">Final Score - Category Wise Scorecard</h5>
                                <div id="categoryScorecardContainer">
                                    <table class="category-scorecard-table" id="categoryScorecardTable">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Scorable (Weight)</th>
                                                <th>Scored</th>
                                                <th>Score %</th>
                                            </tr>
                                        </thead>
                                        <tbody id="categoryScorecardBody">
                                            <tr>
                                                <td colspan="4" class="text-center">Loading categories...</td>
                                            </tr>
                                        </tbody>
                                        <tfoot id="categoryScorecardFooter" style="display:none;">
                                            <tr>
                                                <td><strong>Overall (Category)</strong></td>
                                                <td id="categoryTotalWeight" class="tatalScored">0.00</td>
                                                <td id="categoryTotalScored" class="tatalScored">0.00</td>
                                                <td id="categoryTotalPercent" class="tatalScored">0.00%</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <!-- ==================== CATEGORY SCORING ADDITIONS END ==================== -->
				</div>
                <div class="step-navigation mt-3 mb-3 mr-3 text-right">
                    <button class="btn btn-secondary prev-step">Previous</button>
                    <button class="btn btn-primary next-step">Next</button>
                </div>
			</div>
			{{-- 
			<div class="card">
				<div class="card-header"
					style="background-image: linear-gradient(to right, rgb(255, 199, 95), rgb(255, 211, 97), rgb(254, 223, 101), rgb(252, 236, 106), rgb(249, 248, 113));color:#fff">
					<strong class="card-title">Verify OTP Section </strong>
				</div>
			</div>
			--}}
		</div>
	</div>
	<div class="step-final-buttons text-left mt-3" style="display: none;">
        <button type="button" class="btn btn-primary btn-sm savebutton">
            <i class="fa fa-dot-circle-o"></i> Save
        </button>
        @if(auth()->user()->client_id == 285)
        <button type="submit" class="btn btn-primary btn-sm submit" disabled>
            <i class="fa fa-dot-circle-o"></i> Submit
        </button>
        @else
        <button type="submit" class="btn btn-primary btn-sm submit" >
            <i class="fa fa-dot-circle-o"></i> Submit
        </button>
        @endif
        <button type="reset" class="btn btn-danger btn-sm">
            <i class="fa fa-ban"></i> Reset
        </button>
    </div>
</div>
{{-- modal code --}}
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Alert</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12 form-group">
						<label>files</label>
						<input type="file" id="file" name="file" class="form-control-file">
					</div>
					<div class="col-md-12 form-group">
						<label>Messages*</label>
						<input type="hidden" name="alertParameterId" id="alertParameterId" value="" />
						<input type="hidden" name="alertSubParameterId" id="alertSubParameterId" value="" />
						<input type="hidden" name="alerttype" id="alerttype" value="" />
						<input type="hidden" name="alerttypeid" id="alerttypeid" value="" />
						<input type="hidden" name="alertlob" id="alertlob" value="" />
						<textarea name="msg" id="msg" class="form-control" placeholder="Enter message"
							required></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="saveAlert">Save changes</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="artifactModal" tabindex="-1" role="dialog" aria-labelledby="artifactModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="artifactModalLabel">📎 Attach Artifact</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="artifactParameterId" id="artifactParameterId" />
                <input type="hidden" name="artifactSubParameterId" id="artifactSubParameterId" />
                <canvas id="canvas" style="display:none;"></canvas>

                <div class="form-group">
                    <label class="font-weight-bold">Upload Image File</label> 
                    <input type="file" id="file" name="artifactfile[]" class="form-control-file artifact file" accept="image/*,application/pdf" multiple>
                </div>

                <div class="form-group d-flex justify-content-between align-items-center">
                    <label class="font-weight-bold mb-0">Or Take Screenshot</label>
                    <button id="screenBtn" class="btn btn-outline-primary btn-sm">
                        📷 Take Screenshot & Upload
                    </button>
                </div>

                <div id="progress-bar" class="mt-2">
                    <span id="ProgressContaint" class="text-primary font-weight-bold" style="display:none;">0% Complete</span>
                </div>

                <div id="moreArtifact" class="mt-3"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="artifactAlert">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('css')
<style>
    .img-wrap .close {
        position: absolute;
        top: 2px;
        right: 2px;
        z-index: 100;
        background-color: #FFF;
        padding: 5px 2px 2px;
        color: #000;
        font-weight: bold;
        cursor: pointer;
        opacity: .2;
        text-align: center;
        font-size: 22px;
        line-height: 10px;
        border-radius: 50%;
    }

    .img-wrap:hover .close {
        opacity: 1;
    }
</style>
@include('shared.table_css');
@endsection
@section('js')
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.js"></script> -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js" ;></script>
<script>
    const allocatedModules = @json($allocatedmodule);
    // ==================== CATEGORY SCORING ADDITIONS START ====================
    const categoriesMeta = @json($categoriesMeta);
    const subParCategoryMap = @json($subParCategoryMap);
    const subParWeightMap = @json($subParWeightMap);
    const hasCategories = @json($hasCategories);
    // ==================== CATEGORY SCORING ADDITIONS END ====================
</script>
<script>
function rewriteRemark(id) {
    let remark = document.getElementById('remark' + id).value;

    if (!remark.trim()) {
        document.getElementById('remarkError' + id).innerText = "Please enter remark first.";
        return;
    }

    document.getElementById('remarkError' + id).innerText = "Rewriting...";

    fetch('/rewrite-remark', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            remark: remark
        })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('remark' + id).value = data.rewritten;
        document.getElementById('remarkError' + id).innerText = "";
    })
    .catch(err => {
        document.getElementById('remarkError' + id).innerText = "Error rewriting remark.";
        console.error(err);
    });
}
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkbox = document.getElementById('virtualAudit');
        const imageCaptureSection = document.getElementById('imageCaptureSection');

        checkbox.addEventListener('change', function () {
            if (checkbox.checked) {
                imageCaptureSection.style.display = 'block';
            } else {
                imageCaptureSection.style.display = 'none';
            }
        });
    });
</script>

<script>
    const video = document.getElementById('video');
    const startCameraButton = document.getElementById('start-camera');
    const captureButton = document.getElementById('capture');
    const canvas = document.getElementById('canvas');
    const capturedImage = document.getElementById('captured-image');
    let stream;

    // Start the camera when the "Start Camera" button is clicked
    startCameraButton.addEventListener('click', function() {
        // Access user's webcam and stream the video
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(userStream) {
                // If successful, set video srcObject to the webcam stream
                stream = userStream;  // Store the stream in a variable
                video.srcObject = stream;
                video.style.display = 'block'; // Show the video after starting the camera
                captureButton.style.display = 'inline-block'; // Show capture button
                startCameraButton.style.display = 'none'; // Hide start camera button after it's clicked
            })
            .catch(function(err) {
                // Log and display the error if there is an issue with the webcam
                console.log("Error: " + err);
                alert("Unable to access your camera. Please check your camera permissions.");
            });
    });

    // Capture the image when the button is clicked
    captureButton.addEventListener('click', function() {
        // Check if the video element has a valid videoWidth and videoHeight
        if (video.videoWidth === 0 || video.videoHeight === 0) {
            alert("Camera feed is not ready yet. Please wait.");
            return;
        }

        // Set canvas size to match video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        // Draw the current frame of the video to the canvas
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Convert the canvas image to a base64-encoded PNG image
        const imageData = canvas.toDataURL('image/png');

        // Show the captured image by setting the image source to the base64 data
        capturedImage.src = imageData;
        capturedImage.style.display = 'block';  // Display the captured image
        video.style.display = 'none';  // Hide the video feed
        captureButton.style.display = 'none';  // Hide the capture button after image is taken
    });

    // Stop the webcam stream if the page is unloaded (optional, but recommended to release resources)
    window.addEventListener('beforeunload', function() {
        if (stream) {
            let tracks = stream.getTracks();
            tracks.forEach(track => track.stop()); // Stop all media tracks (audio/video)
        }
    });
</script>


<script>
    document.getElementById("screenBtn").addEventListener("click", async () => {
    try {
        const stream = await navigator.mediaDevices.getDisplayMedia({ video: true });
        const track = stream.getVideoTracks()[0];
        const imageCapture = new ImageCapture(track);
        const bitmap = await imageCapture.grabFrame();

        // Draw screenshot to canvas
        const canvas = document.getElementById("canvas");
        canvas.width = bitmap.width;
        canvas.height = bitmap.height;
        const ctx = canvas.getContext("2d");
        ctx.drawImage(bitmap, 0, 0, bitmap.width, bitmap.height);

        // Convert canvas to blob (not just Base64)
        canvas.toBlob(async (blob) => {
            if (!blob) return;

            // Prepare FormData
            const data = new FormData();
            const parid = jQuery('#artifactParameterId').val();
            const subid = jQuery('#artifactSubParameterId').val();
            const sheetID = "{{ $data->id }}";

            data.append('id', subid);
            data.append('parameter_id', parid);
            data.append('sheet_id', sheetID);
            data.append('_token', "{{ csrf_token() }}");
            data.append('file0', blob, 'screenshot.png'); // mimic file input

            // Upload via AJAX
            const saveData = jQuery.ajax({
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = parseInt((evt.loaded / evt.total) * 100);
                            jQuery('#ProgressContaint').show().html(percentComplete + '% Complete');
                        }
                    }, false);
                    return xhr;
                },
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                type: 'POST',
                url: "{{ url('artifact') }}",
                data: data,
                processData: false,
                contentType: false,
                success: function (resultData) {
                    console.log(resultData);
                    jQuery('#moreArtifact').empty();
                    ImgPreview(resultData.data, '.preview' + subid);
                    jQuery('#artifactModal').modal('hide');
                },
                error: function () {
                    alert("Something went wrong during screenshot upload.");
                }
            });

        }, 'image/png');

        // Stop screen sharing
        track.stop();
    } catch (err) {
        console.error("Screenshot error:", err);
    }
    });

  </script>
  <script>
    $(document).ready(function () {
        let currentStep = 1;
        const totalSteps = $(".step-card").length;

        function showStep(step) {
            $(".step-card").hide();
            $(".step-card[data-step='" + step + "']").show();

            // Handle button visibility
            if (step === 1) {
                $(".prev-step").hide();
            } else {
                $(".prev-step").show();
            }
            if (step === 2) {
        $("#step2ProgressBox").show();     // Show progress box
        updateStep2Progress();             // Update the numbers
    } else {
        $("#step2ProgressBox").hide();     // Hide on other steps
    }

            // Show/hide Final Submit buttons
            if (step === 2) {
    $(".step-final-buttons").show();
    $(".savebutton, [type=reset]").show();
    $(".submit").hide();
    $(".next-step").show(); // keep Next visible on step 2 if needed
    } else if (step === totalSteps) {
        $(".step-final-buttons").show();
        $(".savebutton, [type=reset], .submit").show();
        $(".next-step").hide();
    } else {
        $(".step-final-buttons").hide();
        $(".next-step").show();
    }

        }

       

        // Next Step Button
        $(".next-step").click(function () {
            // Validate Step 1 (Audit for field)
            if (currentStep === 1) {
                // Check if the #audit_for field is empty
                if (jQuery('#audit_for').val() == '') {
                    alert('Please select the Audit For field');
                    return false;
                }
                // if ($('#virtualAudit').is(':checked')) {
                //     var capturedImage = $('#capturedImageInput').val();
                //     if (!capturedImage) {
                //         alert('Please capture an image before proceeding with Virtual Audit.');
                //         return false;
                //     }
                // }
            }
            

            // Validate Step 2 (Observations and Remarks)
            if (currentStep === 2) {
                if (!validateObservationsAndRemarks()) {
                    return false; // Stop moving to next step if validation fails
                }
            }

            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            } else {
                // Submit logic
                $('.submit').click(); // simulate your Submit button
            }
        });

        // Previous Step Button
        $(".prev-step").click(function () {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });

        // Initialize first step
        showStep(currentStep);
    });

    // Validate Observations and Remarks for Step 2
    function validateObservationsAndRemarks() {
        var observation_rs = true;
        var validRemarks = true; // To check if remarks are filled when needed

        jQuery('.0bervation').each(function () {
            
            var id = jQuery(this).val();
            var txt1 = jQuery(this).children("option").filter(":selected").text();
            console.log(id);

            // Check if an observation is selected
            if (id == null || typeof (id) == 'undefined' || txt1.trim() == 'Choose type') {
                observation_rs = false;
            }

            // If observation is Unsatisfactory, check if the corresponding remark is filled
            if (txt1.trim() === 'Unsatisfactory') {
                var remarkId = jQuery(this).attr('id').replace('obs', 'remark');
                var remarkValue = jQuery('#' + remarkId).val().trim();

                if (remarkValue === '') {
                    validRemarks = false; // Remarks are not filled
                }
            }
        });

        if (!observation_rs) {
            alert('Please choose an Observation');
            return false;
        }

        if (!validRemarks) {
            alert('Please fill in remarks for Unsatisfactory observations');
            return false;
        }

        return true; // All validations passed
    }
</script>
<script>
    // Make sure this is outside the document.ready block
    function updateStep2Progress() {
        console.log("Updating step 2 progress...");
        const $observations = $(".0bervation");
        let total = $observations.length;
        let completed = 0;

        $observations.each(function () {
            let selectedText = $(this).find("option:selected").text().trim();
            if (selectedText !== "Choose type") {
                completed++;
            }
        });
    console.log(completed);
         $("#step2ProgressText").text(`${completed} / ${total} completed`);

    // Update bar (optional)
    const percent = total > 0 ? (completed / total) * 100 : 0;
    $("#step2ProgressBar").css("width", percent + "%");
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    if (!SpeechRecognition) {
        alert("Your browser does not support Speech Recognition. Please use Google Chrome.");
        return;
    }

    document.querySelectorAll('.voice-btn').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const textarea = document.getElementById('remark' + id);

            if (!textarea) {
                alert("Remark box not found.");
                return;
            }

            const recognition = new SpeechRecognition();
            recognition.lang = 'en-US';
            recognition.interimResults = false;
            recognition.maxAlternatives = 1;

            recognition.start();

            textarea.placeholder = "🎤 Listening...";

            recognition.onresult = function (event) {
                const transcript = event.results[0][0].transcript.trim();

                // Combine and capitalize
                const existing = textarea.value.trim();
                let combined = existing ? existing + ' ' + transcript : transcript;
                combined = combined.charAt(0).toUpperCase() + combined.slice(1);

                textarea.value = combined;
            };

            recognition.onerror = function (event) {
                alert("Speech Recognition Error: " + event.error);
            };

            recognition.onend = function () {
                textarea.placeholder = "Enter Remark Here";
            };
        });
    });
    });
</script>

<script>


    jQuery(document).ready(function() {
        var userCount = 1; // Start from 1 since the initial set is indexed as 0

        // Get PHP variables as JSON for use in JavaScript
        var formattedUsers = @json($formattedUsers);
        var level5Users = @json($Level_5);

        // Add more fields on button click
        jQuery('#addMoreUsers').click(function() {
            var newUserFields = `
                <div class="row intimationUser">
                    
                    <div class="col-md-3 form-group">
                        <label>Level 4</label>
                        <select name="level_4[]" class="form-control select2">
                            ${generateOptions(formattedUsers)}
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Level 5</label>
                        <select name="level_5[]" class="form-control select2">
                            ${generateOptions(level5Users)}
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                    <button type="button" class="btn btn-danger removeUser" style="margin-top: 11%;">Remove</button>
                     </div>
                </div>
            `;
            jQuery('#intimationUsersContainer').append(newUserFields);
            userCount++;
        });

        // Function to generate options for select input
        function generateOptions(options) {
            var htmlOptions = '';
            for (var key in options) {
                htmlOptions += `<option value="${key}">${options[key]}</option>`;
            }
            return htmlOptions;
        }

        // Remove user set on button click
        jQuery(document).on('click', '.removeUser', function() {
            jQuery(this).closest('.intimationUser').remove();
        });
    });

    jQuery(document).ready(function () {
        jQuery('.js-example-basic-single').select2();
    });
    jQuery('.multiselect2').multiselect({
        nonSelectedText: 'Select Framework',
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
    });

    jQuery('#collection_manager-select').on('change', function (e) {
        var code = jQuery(this).data('code');
        var bucket = jQuery(this).data('bucket');
        jQuery('input[name=Collection_Manager_bucket]').val(bucket)
        jQuery('input[name=Collection_Managercode]').val(code)
    });

    jQuery(document).on('change', '.artifact', function (e) {
        // console.log(e)
        if (e.target.files.length > 0) {
            jQuery('#moreArtifact').append('<input type="file" id="file" name="artifactfile[]" class="form-control-file artifact file">')
        }
    });
    
    var redalertData = {};
    var artifactData = {};

    // Function to calculate the sum of numeric values in an object
    function sum(obj) {
        var total = 0;
        for (var key in obj) {
            if (obj.hasOwnProperty(key)) {
                total += parseFloat(obj[key]);
            }
        }
        return total;
    }

    // ==================== CATEGORY SCORING ADDITIONS START ====================
    // Update category scores based on current result state
    function updateCategoryScores() {
        if (!hasCategories) return;

        var categoryRawScored = {};
        var categoryPossibleRaw = {};
        var categoryNames = {};
        var categoryWeights = {};

        for (var catId in categoriesMeta) {
            categoryRawScored[catId] = 0;
            categoryPossibleRaw[catId] = categoriesMeta[catId].total_possible_raw;
            categoryNames[catId] = categoriesMeta[catId].name;
            categoryWeights[catId] = categoriesMeta[catId].weight;
        }

        for (var paramId in result) {
            if (result.hasOwnProperty(paramId)) {
                for (var subId in result[paramId]) {
                    if (result[paramId].hasOwnProperty(subId)) {
                        var catId = subParCategoryMap[subId];
                        if (catId && categoryRawScored.hasOwnProperty(catId)) {
                            var score = result[paramId][subId];
                            if (score === 'Critical' || score === 'N/A') {
                                score = 0;
                            } else {
                                score = parseFloat(score) || 0;
                            }
                            categoryRawScored[catId] += score;
                        }
                    }
                }
            }
        }

        var html = '';
        var totalScaledScore = 0;
        var totalWeightSum = 0;

        for (var catId in categoryRawScored) {
            var rawTotal = categoryRawScored[catId];
            var possibleRaw = categoryPossibleRaw[catId];
            var weight = categoryWeights[catId];
            var name = categoryNames[catId];

            var scaledScore = 0;
            var scorePercent = 0;
            if (possibleRaw > 0 && weight > 0) {
                scaledScore = (rawTotal / possibleRaw) * weight;
                scorePercent = (rawTotal / possibleRaw) * 100;
            } else if (weight > 0 && possibleRaw === 0) {
                scaledScore = 0;
                scorePercent = 0;
            } else {
                scaledScore = rawTotal;
                scorePercent = possibleRaw > 0 ? (rawTotal / possibleRaw) * 100 : 0;
            }

            totalScaledScore += scaledScore;
            totalWeightSum += weight;

            html += '<tr>';
            html += '<td>' + escapeHtml(name) + '</td>';
            html += '<td>' + weight.toFixed(2) + '</td>';
            html += '<td>' + scaledScore.toFixed(2) + '</td>';
            html += '<td>' + scorePercent.toFixed(2) + '%</td>';
            html += '</tr>';
        }

        if (html === '') {
            html = '<tr><td colspan="4" class="text-center">No categories available</td></tr>';
        }
        jQuery('#categoryScorecardBody').html(html);

        if (totalWeightSum > 0) {
            var categoryOverallPercent = (totalScaledScore / totalWeightSum) * 100;
            jQuery('#categoryTotalWeight').text(totalWeightSum.toFixed(2));
            jQuery('#categoryTotalScored').text(totalScaledScore.toFixed(2));
            jQuery('#categoryTotalPercent').text(categoryOverallPercent.toFixed(2) + '%');
            jQuery('#categoryScorecardFooter').show();
        } else {
            jQuery('#categoryScorecardFooter').hide();
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }
    // ==================== CATEGORY SCORING ADDITIONS END ====================

    // Function to calculate totals, percentages, and grades
    function totalfun(obj) {
        console.log('total', obj);
        var total = 0;
        var parameterTotal = 0;

        for (var key in obj) {
            if (obj.hasOwnProperty(key)) {
                var subtotal = 0;
                for (var item in obj[key]) {
                    if (obj[key].hasOwnProperty(item)) {
                        var parameterValue = 0;
                        if (obj[key][item] !== 'N/A') {
                            parameterValue = parseFloat(jQuery('#org' + item).html()) || 0;
                        }
                        if (obj[key][item] !== 'Critical') {
                            subtotal += (obj[key][item] === 'N/A' ? 0 : parseFloat(obj[key][item]));
                            parameterTotal += parameterValue;
                        } else {
                            subtotal = 0;
                            parameterTotal += parameterValue;
                            break;
                        }
                    }
                }
                total += subtotal;
            }
        }

        // Update the DOM with the calculated totals
        jQuery('#scroable').text(parameterTotal);
        jQuery('#wfatal').text(total);
        jQuery('#wnfatal').text(total);

        var wfatalper = (total !== 0) ? (total / parameterTotal) * 100 : 0;
        jQuery('#wfatalper').text(wfatalper.toFixed(2) + '%');

        // Update grade based on wfatalper
        updateGrade(wfatalper);

        // Update category scores after overall totals change
        if (hasCategories) {
            updateCategoryScores();
        }
    }

    // Function to update the grade based on wfatalper
    function updateGrade(wfatalper) {
        var grade;
        if (wfatalper > 90) {
            grade = 'A';
        } else if (wfatalper >= 75) {
            grade = 'B';
        } else if (wfatalper >= 61) {
            grade = 'C';
        } else {
            grade = 'D';
        }
        jQuery('#grade').text(grade); // Update the grade element
    }

    // Initialize result object
    var result = {};
    @php
        foreach ($data->parameter as $item) {
    @endphp
    result[{{$item->id}}] = {};
    @php
        }
    @endphp

    // Function to update results based on input
    function resultFun(value, id, parameterId) {
        result[parameterId][id] = value;
        var total = 0;
        var parameterTotal = 0;

        for (var el in result[parameterId]) {
            if (result[parameterId].hasOwnProperty(el)) {
                var parameterValue = 0;
                if (result[parameterId][el] !== 'N/A') {
                    parameterValue = parseFloat(jQuery('#org' + el).html()) || 0;
                }
                if (result[parameterId][el] !== 'Critical') {
                    total += (result[parameterId][el] === 'N/A' ? 0 : parseFloat(result[parameterId][el]));
                    parameterTotal += parameterValue;
                } else {
                    parameterTotal += parameterValue;
                    total = 0;
                    break;
                }
            }
        }

        // Update the DOM with the results
        jQuery('#scroable' + parameterId).text(parameterTotal);
        jQuery('#wfatal' + parameterId).text(total);

        var wfatalper = (total !== 0) ? (total / parameterTotal) * 100 : 0;
        jQuery('#wfatalper' + parameterId).text(wfatalper.toFixed(2) + '%');

        // Update grade based on wfatalper
        updateGrade(wfatalper);

        // Call the total calculation function
        totalfun(result);
    }

    jQuery('.ratingSelect').on('change', function (e) {
        var id = jQuery(this).data('id');
        var parameterId = jQuery(this).data('parameterid');

        var value = parseInt(jQuery('#org' + id).html());
        var finalValue = value * (e.target.value / 100);
        console.log(finalValue, value)
        jQuery('#' + id).val('rating')
        // jQuery('#ratingSelect'+id).hide();
        // 	jQuery('#'+id).show()
        resultFun(finalValue, id, parameterId)
    });

    jQuery('.0bervation').on('change', function () {

    var id = jQuery(this).data('id');
    var parameterId = jQuery(this).data('parameterid');
    var selectedText = jQuery(this).find("option:selected").text().trim();

    let baseScore = parseFloat(jQuery('#org' + id).text()) || 0;

    // Disable error count by default
    jQuery('#errorCount' + id).val(0).prop('disabled', true);

    // N/A
    if (selectedText === 'N/A') {
        jQuery('#' + id).val('N/A');
        resultFun('N/A', id, parameterId);
        return;
    }

    // Critical
    if (selectedText === 'Critical') {
        jQuery('#' + id).val(0);
        resultFun(0, id, parameterId);
        return;
    }

    // Satisfactory
    if (selectedText === 'Satisfactory') {
        jQuery('#' + id).val(baseScore);
        resultFun(baseScore, id, parameterId);
        return;
    }

    // 🔴 Unsatisfactory
    // 🔴 Unsatisfactory
if (selectedText === 'Unsatisfactory') {
    var errorField = jQuery('#errorCount' + id);

    if (errorField.length === 0 || errorField.is('input[type="hidden"]')) {
        // Module 22 not enabled — score = 0
        jQuery('#' + id).val(0);
        resultFun(0, id, parameterId);
        return;
    }

    // Module 22 enabled — use dynamic score
    errorField.prop('disabled', false);

    var finalScore = resolveErrorCountScore(id);
    jQuery('#' + id).val(finalScore);
    resultFun(finalScore, id, parameterId);
}

});
/**
 * Resolves final score from error count select.
 * Handles both absolute scores (>=0) and deductions (<0).
 */
function resolveErrorCountScore(id) {
    var selectedOption = jQuery('#errorCount' + id).find('option:selected');
    var scoreVal = parseFloat(selectedOption.data('score'));
    return isNaN(scoreVal) ? (parseFloat(jQuery('#org' + id).text()) || 0) : scoreVal;
}
    jQuery('.error-count-select').on('change', function () {
    var id = jQuery(this).data('id');
    var parameterId = jQuery('#obs' + id).data('parameterid');
    var selectedText = jQuery('#obs' + id + ' option:selected').text().trim();

    if (selectedText !== 'Unsatisfactory') return;

    var finalScore = resolveErrorCountScore(id);
    jQuery('#' + id).val(finalScore);
    resultFun(finalScore, id, parameterId);
});

    // Common validation function to check for "Unsatisfactory" and validate remarks
        function validateObservationsAndRemarks() {
            var observation_rs = true;
            var validRemarks = true; // To check if remarks are filled when needed
        
            jQuery('.0bervation').each(function () {
                var id = jQuery(this).val();
                var txt1 = jQuery(this).children("option").filter(":selected").text();
                console.log(id);
            
                // Check if an observation is selected
                if (id == null || typeof (id) == 'undefined' || txt1.trim() == 'Choose type') {
                    observation_rs = false;
                }
            
                // If observation is Unsatisfactory, check if the corresponding remark is filled
                if (txt1.trim() === 'Unsatisfactory') {
                    var remarkId = jQuery(this).attr('id').replace('obs', 'remark');
                    var remarkValue = jQuery('#' + remarkId).val().trim();
                
                    if (remarkValue === '') {
                        validRemarks = false; // Remarks are not filled
                    }
                }
            });
        
            if (!observation_rs) {
                alert('Please choose an Observation');
                return false;
            }
        
            if (!validRemarks) {
                alert('Please fill in remarks for Unsatisfactory observations');
                return false;
            }
        
            return true; // All validations passed
        }
        
        // Submit button functionality
        jQuery(".submit").on("click", function (e) {
        
            var className = jQuery('#audit_for').attr('name');
            if (jQuery('#audit_for').val() == '') {
                alert('Please select ' + className);
                return false;
            }
        
            if (jQuery('#productSelect').val() == '') {
                alert('Please select product');
                return false;
            }

            const clientId = {{ auth()->user()->client_id }};
            if (clientId == 15) {
                if (jQuery('#sheet_select').val() === '') {
                    alert('Please select nature of sheet.');
                    return false;
                }
            }
            if (allocatedModules.includes(13)){
            if (jQuery('#collection_manager-select').val() == '' || jQuery('#collection_manager-select').val() == undefined) {
                alert('Please select collection manager');
                return false;
            }}
        
            // Validate observations and remarks
            if (validateObservationsAndRemarks()) {
                if (confirm("Are you sure to submit the audit? After submitting, you won't be able to edit it.")) {
                    // Call the function to send OTP
                    if (allocatedModules.includes(1)) {
                        sendOTP();
                    } else {
                        submitDataFun('submit');
                    }
                
                    // Disable the buttons temporarily
                  //  jQuery(".submit").prop('disabled', true);
                   // jQuery(".savebutton").prop('disabled', true);
                }
            }
        });
        
        
        // Save button functionality
        jQuery(".savebutton").on("click", function (e) {
        
            var className = jQuery('#audit_for').attr('name');
            if (jQuery('#audit_for').val() == '') {
                alert('Please select ' + className);
                return false;
            }
        
            if (jQuery('#productSelect').val() == '') {
                alert('Please select product');
                return false;
            }

            const clientId = {{ auth()->user()->client_id }};
            if (clientId == 15) {
                if (jQuery('#sheet_select').val() === '') {
                    alert('Please select nature of sheet.');
                    return false;
                }
            }

            if (allocatedModules.includes(13)){
            if (jQuery('#collection_manager-select').val() == '' || jQuery('#collection_manager-select').val() == undefined) {
                alert('Please select collection manager');
                return false;
            }}
        
            // Validate observations and remarks
            if (validateObservationsAndRemarks()) {
                if (confirm("Are you sure to save the audit?")) {
                    // Call save function here
                    submitDataFun('save');
                
                    // Disable the buttons temporarily
                  //  jQuery(".submit").prop('disabled', true);
                  //  jQuery(".savebutton").prop('disabled', true);
                }
            }
        });
        
        
            // jQuery('.agency').on('change', function(e) {
            //     gerProduct(e.target.value, 'agency')
            // })
            // Handle agency change to trigger editBranch if product is already selected
        jQuery('#audit_for').on('change', function() {
            var agencyId = jQuery(this).val();  // Get the selected agency
            var type = 'agency';
            
            if (agencyId) {
                // Call the getProduct function to get the product_id for this agency
                jQuery.ajax({
                    type: 'GET',
                    url: "{{ url('getProduct') }}/" + agencyId + '/' + type,
                    success: function(response) {
                        if (response.data && response.data.product_id) {
                            // Set the product dropdown to the product_id from the response
                            jQuery('#productSelect').val(response.data.product_id).trigger('change');
                            
                            // Call editBranch function after setting product selection
                            editBranch(agencyId, response.data.product_id, type);
                        } else {
                            console.log("No product found for the selected agency.");
                        }
                    },
                    error: function() {
                        alert("Something went wrong");
                    }
                });
            }
        });
        
        // Handle product change if selected manually by user
        jQuery('#productSelect').on('change', function(e) {
            var agencyId = jQuery('#audit_for').val();  // Get the selected agency
            var type = 'agency';
            var productId = e.target.value;  // Get the selected product ID
        
            if (agencyId && productId) {
                // Call the editBranch function with agencyId and productId
                editBranch(agencyId, productId, type);
            } else {
                console.log("Missing required parameters: agencyId or productId.");
            }
        });
        
            // Function to handle the AJAX request
        
            // Function to handle the AJAX request with loader
            function editBranch(id, product_id, type) {
            
                // Show the loader before the request starts
                jQuery('#loader').show();
            
                jQuery.ajax({
                    type: 'GET',
                    url: "{{ url('get_branch_detail') }}/" + id + '/' + type + '/' + product_id,
                    dataType: "text",
                    success: function (resultData) {
                        // Populate the data div with the result
                        jQuery('#data').html(resultData);
                        // Call getLocation if necessary
                        getLocation();
                    },
                    error: function () {
                        alert("Something went wrong");
                    },
                    complete: function () {
                        // Hide the loader after the request is complete
                        jQuery('#loader').hide();
                    }
                });
            }
            jQuery('.alertModal').on('click', function (e) {
                var subparameterId = jQuery(this).data('id')
                var parameterId = jQuery(this).data('parameterid')
                jQuery('#alertParameterId').val(parameterId)
                jQuery('#alertSubParameterId').val(subparameterId)
                var type = "{{$data->type}}"
                var typeid = ""
                var typelob = "{{$data->lob}}"
                switch (type) {
                    case 'branch':
                        typeid = jQuery('select[name=branch]').val();
                        break;
                    case 'agency':
                        typeid = jQuery('select[name=agency]').val();
                        break;
                    case 'yard':
                        typeid = jQuery('select[name=yard]').val();
                        break;
                }
                jQuery('#alerttype').val(type)
                jQuery('#alerttypeid').val(typeid)
                jQuery('#alertlob').val(typelob)
                console.log(parameterId)
                jQuery('#exampleModal').modal('show');
            
            })
        
        
        
            jQuery('.artifactModal').on('click', function (e) {
                var subparameterId = jQuery(this).data('id')
                var parameterId = jQuery(this).data('parameterid')
                jQuery('#artifactParameterId').val(parameterId)
                jQuery('#artifactSubParameterId').val(subparameterId)
                jQuery('#moreArtifact').empty()
                jQuery('#artifactModal').modal('show');
            })
            jQuery('#saveAlert').on('click', function (e) {
                var parid = jQuery('#alertParameterId').val()
                var subid = jQuery('#alertSubParameterId').val()
                var msg = jQuery('#msg').val()
                var lob = jQuery('#alertlob').val()
                var type = jQuery('#alerttype').val()
                var typeid = jQuery('#alerttypeid').val()
                var sheetID = "{{$data->id}}"
                jQuery('#alertParameterId').val('')
                jQuery('#alertSubParameterId').val('')
                jQuery('#msg').val('')
                jQuery('#exampleModal').modal('hide');
                var fileUpload = jQuery("#file").get(0);
                var files = fileUpload.files;
                var data = new FormData();
                // data.append('id', subid);
                // data.append('parameter_id', parid);
                // data.append('sheet_id', sheetID);
                // data.append('msg', msg);
                // data.append('lob', lob);
                // data.append('type', type);
                // data.append('typeid', typeid);
                // data.append('_token', "{{ csrf_token() }}");
                redalertData[subid] = {
                    'id': subid,
                    'parameter_id': parid,
                    'sheet_id': sheetID,
                    'msg': msg,
                    'lob': lob,
                    'type': type,
                    'typeid': typeid
                }
                for (var i = 0; i < files.length; i++) {
                    // data.append('file', files[i]);
                    redalertData[subid]['file'] = files[i]
                }
                console.log(redalertData)
                jQuery('#file').val('')
                // var saveData = jQuery.ajax({
                // 	type: 'post',
                // 	url: "{{url('red-alert')}}",
                // 	data: data,
                // 	processData: false,
                // 	contentType: false,
                // 	success: function(resultData) { 
                // 		console.log(resultData)
                // 		// window.location='{{ url("audited_list")}}'
                // 	}
                // });
                // saveData.error(function() { alert("Something went wrong"); });
            })
        
        // jQuery('#artifactAlert').on('click', function (e) {
        //     e.preventDefault(); // Prevent default action
            
        //     var parid = jQuery('#artifactParameterId').val();
        //     var subid = jQuery('#artifactSubParameterId').val();
        //     var sheetID = "{{$data->id}}";
            
        //     // Reset the input fields
        //     jQuery('#artifactParameterId').val('');
        //     jQuery('#artifactSubParameterId').val('');
        //     jQuery('#msg').val('');
            
        //     // Get the uploaded files
        //     var fileUpload = jQuery(".file").get();
        //     var totalFile = fileUpload.length;
        //     var data = new FormData();
            
        //     // Append form data
        //     data.append('id', subid);
        //     data.append('parameter_id', parid);
        //     data.append('sheet_id', sheetID);
        //     data.append('_token', "{{ csrf_token() }}");
        //     data.append('totalFile', totalFile);
            
        //     var valid = true; // To track validation status
        //     var allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp' , 'pdf']; // Allowed image extensions
            
        //     // Loop through each file and validate
        //     for (var i = 0; i < fileUpload.length; i++) {
        //         if (fileUpload[i].files.length > 0) {
        //             var file = fileUpload[i].files[0];
        //             var fileName = file.name;
        //             var fileExtension = fileName.split('.').pop().toLowerCase();
                    
        //             // Check if the file extension is valid
        //             if (!allowedExtensions.includes(fileExtension)) {
        //                 alert(`Invalid file type: ${fileName}. Please upload an image file.`);
        //                 valid = false;
        //                 break;
        //             }
                    
        //             // Append the valid file to FormData
        //             data.append('file' + i, file);
        //         }
        //     }
            
        //     // If validation fails, stop the function
        //     if (!valid) {
        //         return;
        //     }
            
        //     // Perform AJAX request if validation passes
        //     var saveData = jQuery.ajax({
        //         xhr: function () {
        //             var xhr = new window.XMLHttpRequest();
        //             xhr.upload.addEventListener("progress", function (evt) {
        //                 if (evt.lengthComputable) {
        //                     var percentComplete = evt.loaded / evt.total;
        //                     percentComplete = parseInt(percentComplete * 100);
        //                     jQuery('#ProgressContaint').show();
        //                     jQuery('#ProgressContaint').html(percentComplete + '% Complete');
        //                     if (percentComplete === 100) {
        //                         jQuery('#ProgressContaint').html(percentComplete + '% Complete');
        //                     }
        //                 }
        //             }, false);
        //             return xhr;
        //         },
        //         headers: {
        //             'X-CSRF-TOKEN': "{{ csrf_token() }}"
        //         },
        //         type: 'post',
        //         url: "{{url('artifact')}}",
        //         data: data,
        //         processData: false,
        //         contentType: false,
        //         success: function (resultData) {
        //             console.log(resultData);
        //             jQuery('#moreArtifact').empty();
        //             jQuery('.file').val('');
        //             ImgPreview(resultData.data, '.preview' + subid);
        //             jQuery('#artifactModal').modal('hide');
        //         },
        //         error: function () {
        //             alert("Something went wrong");
        //         }
        //     });
        // });

        jQuery('#artifactAlert').on('click', function (e) {
            e.preventDefault(); // Prevent default action
                
            var parid = jQuery('#artifactParameterId').val();
            var subid = jQuery('#artifactSubParameterId').val();
            var sheetID = "{{$data->id}}";
                
            // Reset the input fields
            jQuery('#artifactParameterId').val('');
            jQuery('#artifactSubParameterId').val('');
            jQuery('#msg').val('');
                
            // Get the uploaded files
            var fileUpload = jQuery(".file").get();
            var data = new FormData();
                
            // Append form data
            data.append('id', subid);
            data.append('parameter_id', parid);
            data.append('sheet_id', sheetID);
            data.append('_token', "{{ csrf_token() }}");
                
            var valid = true; // To track validation status
            var allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'pdf']; // Allowed image extensions
            var fileIndex = 0; // Counter for file naming in FormData
                
            // Loop through each file input
            for (var i = 0; i < fileUpload.length; i++) {
                var files = fileUpload[i].files; // Get all files from this input
                
                // Loop through all files in this input
                for (var j = 0; j < files.length; j++) {
                    var file = files[j];
                    var fileName = file.name;
                    var fileExtension = fileName.split('.').pop().toLowerCase();
                    
                    // Check if the file extension is valid
                    if (!allowedExtensions.includes(fileExtension)) {
                        alert(`Invalid file type: ${fileName}. Please upload an image file.`);
                        valid = false;
                        break;
                    }
                    
                    // Append the valid file to FormData
                    data.append('file' + fileIndex, file);
                    fileIndex++;
                }
                
                // Break outer loop if validation failed
                if (!valid) {
                    break;
                }
            }
            
            // If validation fails, stop the function
            if (!valid) {
                return;
            }
            
            // Add total file count
            data.append('totalFile', fileIndex);
            
            // Perform AJAX request if validation passes
            var saveData = jQuery.ajax({
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);
                            jQuery('#ProgressContaint').show();
                            jQuery('#ProgressContaint').html(percentComplete + '% Complete');
                            if (percentComplete === 100) {
                                jQuery('#ProgressContaint').html(percentComplete + '% Complete');
                            }
                        }
                    }, false);
                    return xhr;
                },
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                type: 'post',
                url: "{{url('artifact')}}",
                data: data,
                processData: false,
                contentType: false,
                success: function (resultData) {
                    console.log(resultData);
                    jQuery('#moreArtifact').empty();
                    jQuery('.file').val('');
                    ImgPreview(resultData.data, '.preview' + subid);
                    jQuery('#artifactModal').modal('hide');
                },
                error: function () {
                    alert("Something went wrong");
                }
            });
        });
            jQuery(document).on('click', '.close', function () {
                var id = jQuery(this).closest('.img-wrap').find('img').data('id');
                var data = {
                    'id': id,
                    '_token': '{{ csrf_token() }}'
                }
                var saveData = jQuery.ajax({
                    type: 'DELETE',
                    url: "{{url('artifact')}}/" + id,
                    data: data,
                    success: function (resultData) {
                        console.log(resultData)
                        jQuery('.preview' + id).remove();
                    }
                });
                //saveData.error(function() { alert("Something went wrong"); });
            });
        
            function ImgPreview(input, placeToInsertImagePreview) {
     var filesAmount = input.length;
     var image = '';
 
     input.map(function (item) {
         artifactData[item.id] = item.id;
 
         // Check if the file is a PDF
         const isPdf = item.file.toLowerCase().endsWith('.pdf');
 
         if (isPdf) {
             image += `<div class="img-wrap preview${item.id}" style="position: relative; display: inline-block; margin: 5px;">
                          <span class="close">&times;</span>
                          <a href="${item.file}" target="_blank" style="display: inline-block; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; background: #f9f9f9;">
                              <img src="{{ asset('public/images/pdf-icon.png') }}" style="width:100px; height:100px;" data-id="${item.id}">
                          </a>
                       </div>`;
         } else {
             // Assume it's an image
             image += `<div class="img-wrap preview${item.id}" style="position: relative; display: inline-block; font-size: 0;">
                          <span class="close">&times;</span>
                          <a href="${item.file}" target="_blank">
                              <img src="${item.file}" style="width:100px; height:100px;" data-id="${item.id}">
                          </a>
                       </div>`;
         }
     });
 
     jQuery(placeToInsertImagePreview).append(image);
 }
 
 // Helper function to extract file name from URL
 function getFileName(filePath) {
     return filePath.split('/').pop();
 }
 
        
            function changeUser(val) {
                var data = '';
                if (val.length > 3) {
                    var saveData = jQuery.ajax({
                        type: 'get',
                        url: "{{url('get_users')}}/" + val + '/Collection_Manager',
                        dataType: "text",
                        success: function (resultData) {
                            var obj = JSON.parse(resultData)
                            obj.data.forEach(function (item, index) {
                                data = data + '<option value="' + item.name + '" data-id="' + item.id +
                                    '" data-email="' + item.email + '">'
                            });
                            if (data == '') {
                                jQuery('#error').show()
                            } else {
                                jQuery('#error').hide()
                                jQuery('#adventure').html(data)
                            }
                        }
                    });
                    //saveData.error(function() { alert("Something went wrong"); });
                }
            }
        
            function selectUser(val) {
                var val = val
                console.log
                jQuery('#adventure option').filter(function () {
                    if (this.value == val) {
                        console.log(this,)
                        var email = jQuery(this).attr('data-email');
                        jQuery('input[name=Collection_Manager_email]').val(email)
                    
                    }
                });
                // console.log(xyz)
            }
        
            function changeAgencyManager(val) {
                var data = '';
                if (val.length > 3) {
                    var saveData = jQuery.ajax({
                        type: 'get',
                        url: "{{url('get_users')}}/" + val + '/Collection_Manager',
                        dataType: "text",
                        success: function (resultData) {
                            var obj = JSON.parse(resultData)
                            obj.data.forEach(function (item, index) {
                                data = data + '<option value="' + item.name + '" data-id="' + item.id +
                                    '" data-email="' + item.email + '">'
                            });
                            if (data == '') {
                                jQuery('#agency_error').show()
                            } else {
                                jQuery('#agency_error').hide()
                                jQuery('#agency_manager').html(data)
                            }
                        }
                    });
                    //	saveData.error(function() { alert("Something went wrong"); });
                }
            }
        
            function selectAgencyManager(val) {
                var val = val
                console.log
                jQuery('#agency_manager option').filter(function () {
                    if (this.value == val) {
                        console.log(this,)
                        var email = jQuery(this).attr('data-email');
                        jQuery('input[name=agency_manager_email]').val(email)
                    
                    }
                });
                // console.log(xyz)
            }
        
            function changeYardManager(val) {
                var data = '';
                if (val.length > 3) {
                    var saveData = jQuery.ajax({
                        type: 'get',
                        url: "{{url('get_users')}}/" + val + '/Collection_Manager',
                        dataType: "text",
                        success: function (resultData) {
                            var obj = JSON.parse(resultData)
                            obj.data.forEach(function (item, index) {
                                data = data + '<option value="' + item.name + '" data-id="' + item.id +
                                    '" data-email="' + item.email + '">'
                            });
                            if (data == '') {
                                jQuery('#yard_error').show()
                            } else {
                                jQuery('#yard_error').hide()
                                jQuery('#yard_manager').html(data)
                            }
                        }
                    });
                    //	saveData.error(function() { alert("Something went wrong"); });
                }
            }
        
            function selectYardManager(val) {
                var val = val
                console.log
                jQuery('#yard_manager option').filter(function () {
                    if (this.value == val) {
                        console.log(this,)
                        var email = jQuery(this).attr('data-email');
                        jQuery('input[name=yard_manager_email]').val(email)
                    
                    }
                });
                // console.log(xyz)
            }
            function submitDataFun(typesubmit) {
            var submitData = [];
            var parameters = {};
            var sub = {};
            var alertData = redalertData;
            
            // Show loader before starting the process
            jQuery('#loader').show();
            
            for (var el in result) {
                if (result.hasOwnProperty(el)) {
                    for (var row in result[el]) {
                        if (result[el].hasOwnProperty(row)) {
                            var ck = 0;
                            if (jQuery('#ackalert' + row).prop('checked') == true) {
                                ck = 1;
                            }
                             var baseScore = parseFloat(jQuery('#org' + row).text()) || 0;
                                var selectedScoreVal = parseFloat(jQuery('#errorCount' + row).find('option:selected').data('score'));
                                var finalErrorScore = isNaN(selectedScoreVal) ? baseScore : selectedScoreVal;
                                var error_score = baseScore - finalErrorScore;
                                if (error_score < 0) error_score = 0; // deduction for record keeping // deduction amount
                            sub[row] = {
                                'remark': jQuery('#remark' + row).val(),
                                'orignal_weight': jQuery('#org' + row).text(),
                                'temp_weight': result[el][row],
                                'score': jQuery('#' + row).val(),
                                'is_percentage': (jQuery('#' + row).val() == 'rating') ? 1 : 0,
                                'selected_per': jQuery('#ratingSelect' + row).val(),
                                'ackalert': ck,
                                'error': error_score,
                                'option': jQuery('#obs' + row + ' option:selected').text(),
                                'error_count': jQuery('#errorCount' + row).val(),
                            }
                        }
                    }
                
                    parameters[el] = {
                        'score': jQuery('#scroable' + el).text(),
                        'score_with_fatal': jQuery('#wfatal' + el).text(),
                        'score_without_fatal': jQuery('#wnfatal' + el).text(),
                        'temp_total_weightage': jQuery('#scroable').text(),
                        'parameter_weight': jQuery('#total' + el).text(),
                        'subs': sub
                    };
                    sub = {};
                }
            }
        
            // Capture dynamically added Level 4 and Level 5 values
            var level4Values = [];
            var level5Values = [];
            jQuery('#intimationUsersContainer .intimationUser').each(function() {
                level4Values.push(jQuery(this).find('select[name="level_4[]"]').val());
                level5Values.push(jQuery(this).find('select[name="level_5[]"]').val());
            });
        
            if (jQuery('#demogeo').val() == '') {
                jQuery('#loader').hide();  // Hide loader if validation fails
                alert("Please Allow Geo Tagging");
                return false;
            }

            // ==================== CATEGORY SCORING ADDITIONS START ====================
            var categoryScores = null;
            if (hasCategories) {
                categoryScores = getCategoryScoresForSubmission();
            }
            // ==================== CATEGORY SCORING ADDITIONS END ====================
        
            submitData.push({
                'qm_sheet_id': {{$data->id}},
                'geotag': jQuery('#demogeo').val(),
                // 'agency_image': jQuery('#agency_image').val(),
                'overall_score': jQuery('#wfatal').text(),
                'with_fatal_score_per': jQuery('#wfatalper').text(),
                'grade': jQuery('#grade').text(),
                'location': jQuery('.location').val(),
                'branch_id': jQuery('.branch').val(),
                'audit_cycle': jQuery('.audit_cycle').val(),
                'audit_date': jQuery('.audit_date').val(),
                'agency_id': jQuery('.agency').val(),
                'yard_id': jQuery('.yard').val(),
                'branch_repo_id': jQuery('.branch_repo').val(),
                'agency_repo_id': jQuery('.agency_repo').val(),
                'product_id': jQuery('.product').val(),
                'collection_manager_email': jQuery('input[name=Collection_Manager_email]').val(),
                'agency_manager_email': jQuery('input[name=agency_manager_email]').val(),
                'yard_manager_email': jQuery('input[name=yard_manager_email]').val(),
                'collection_manager_id': jQuery('#collection_manager-select').val() || '',
                'agency_manager': jQuery('input[name=agency_manager]').val(),
                'agency_phone': jQuery('select[name=agency_phone]').val(),
                'agency_email': jQuery('select[name=agency_email]').val(),
                'lavel_3': jQuery('#collection_manager-select').val() || '',
                'present_auditor': jQuery('#present_auditor').val(),
                'audit_sheet_type': jQuery('#sheet_select').val(),
                'lavel_4': level4Values,
                'lavel_5': level5Values,
                'sub_product': jQuery('#sub_product').val(),
                'status': typesubmit,
                'artifactIds': JSON.stringify(artifactData),
                'is_virtual_audit': jQuery('#virtualAudit').is(':checked') ? 1 : 0,
                'category_scores': categoryScores   // <-- ADDED
            });
        
            var ids = [];
            var saveData = jQuery.ajax({
                type: 'POST',
                url: "{{url('allocation/store_audit')}}",
                data: {
                    'submission_data': submitData,
                    'parameters': parameters,
                    "_token": "{{ csrf_token() }}"
                },
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                dataType: "text",
                success: function (result) {
                    var data = new FormData();
                    if (!jQuery.isEmptyObject(alertData)) {
                        for (var el in alertData) {
                            if (alertData.hasOwnProperty(el)) {
                                data.append('id' + el, alertData[el].id);
                                data.append('parameter_id' + el, alertData[el].parameter_id);
                                data.append('sheet_id' + el, alertData[el].sheet_id);
                                data.append('msg' + el, alertData[el].msg);
                                data.append('lob' + el, alertData[el].lob);
                                data.append('file' + el, alertData[el].file);
                                data.append('_token', "{{ csrf_token() }}");
                                data.append('type', alertData[el].type);
                                data.append('typeid', alertData[el].typeid);
                                ids.push(alertData[el].id);
                            }
                        }
                        data.append('ids', JSON.stringify(ids));
                        data.append('audit_id', JSON.parse(result).audit_id);
                        var saveAlert = jQuery.ajax({
                            type: 'post',
                            url: "{{url('red-alert')}}",
                            data: data,
                            dataType: "text",
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            processData: false,
                            contentType: false,
                            success: function (resultData) {
                                console.log(resultData);
                                window.location = '{{ url("submit_audited_list")}}';
                            }
                        });
                        saveAlert.error(function () {
                            alert("Something went wrong");
                            console.log('red-alert-error');
                        });
                    }
                    if(typesubmit == 'submit') {
                        window.location = '{{ url("submit_audited_list")}}';
                    } else {
                        window.location = '{{ url("save_audited_list")}}'
                    }
                    
                },
                complete: function (result) {
                    jQuery('#loader').hide();
                 //   jQuery(".submit").prop('disabled', false);
                   // jQuery(".savebutton").prop('disabled', false);
                }
            });
        }
        
        // ==================== CATEGORY SCORING ADDITIONS START ====================
        // Helper function to get category scores for submission
        function getCategoryScoresForSubmission() {
            if (!hasCategories) return null;
            
            var categoryRawScored = {};
            var categoryPossibleRaw = {};
            var categoryNames = {};
            var categoryWeights = {};
            
            for (var catId in categoriesMeta) {
                categoryRawScored[catId] = 0;
                categoryPossibleRaw[catId] = categoriesMeta[catId].total_possible_raw;
                categoryNames[catId] = categoriesMeta[catId].name;
                categoryWeights[catId] = categoriesMeta[catId].weight;
            }
            
            for (var paramId in result) {
                if (result.hasOwnProperty(paramId)) {
                    for (var subId in result[paramId]) {
                        if (result[paramId].hasOwnProperty(subId)) {
                            var catId = subParCategoryMap[subId];
                            if (catId && categoryRawScored.hasOwnProperty(catId)) {
                                var score = result[paramId][subId];
                                if (score === 'Critical' || score === 'N/A') {
                                    score = 0;
                                } else {
                                    score = parseFloat(score) || 0;
                                }
                                categoryRawScored[catId] += score;
                            }
                        }
                    }
                }
            }
            
            var categoriesArray = [];
            var totalScaledScore = 0;
            var totalWeightSum = 0;
            
            for (var catId in categoryRawScored) {
                var rawTotal = categoryRawScored[catId];
                var possibleRaw = categoryPossibleRaw[catId];
                var weight = categoryWeights[catId];
                var name = categoryNames[catId];
                
                var scaledScore = 0;
                var percentage = 0;
                if (possibleRaw > 0 && weight > 0) {
                    scaledScore = (rawTotal / possibleRaw) * weight;
                    percentage = (rawTotal / possibleRaw) * 100;
                }
                
                totalScaledScore += scaledScore;
                totalWeightSum += weight;
                
                categoriesArray.push({
                    category_id: catId,
                    name: name,
                    weight: weight,
                    raw_scored: rawTotal,
                    raw_possible: possibleRaw,
                    scaled_score: scaledScore,
                    percentage: percentage
                });
            }
            
            return {
                categories: categoriesArray,
                overall: {
                    total_weight: totalWeightSum,
                    total_scaled_score: totalScaledScore,
                    overall_percentage: totalWeightSum > 0 ? (totalScaledScore / totalWeightSum) * 100 : 0
                }
            };
        }
        // ==================== CATEGORY SCORING ADDITIONS END ====================
        
            function remarkIsFilled(result) {
                var remark = true;
                for (var el in result) {
                    if (result.hasOwnProperty(el)) {
                        if (jQuery.isEmptyObject(result[el])) {
                            remark = false;
                        }
                        for (var row in result[el]) {
                            if (result[el].hasOwnProperty(row)) {
                                var value = jQuery('#remark' + row).val();
                                if (value.trim().length == 0) {
                                    remark = false;
                                    break;
                                }
                            }
                        }
                    }
                }
                return remark;
            }
            var x = document.getElementById("demogeo");
        
            function getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition, showError);
                } else {
                    x.innerHTML = "Geolocation is not supported by this browser.";
                }
            }
        
            function showPosition(position) {
                var value = position.coords.latitude +
                    " " + position.coords.longitude;
                console.log(value)
                jQuery('#demogeo').val(value)
            }
        
            function showError(error) {
                var innerHTML = ''
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        innerHTML = "User denied the request for Geolocation."
                        break;
                    case error.POSITION_UNAVAILABLE:
                        innerHTML = "Location information is unavailable."
                        break;
                    case error.TIMEOUT:
                        innerHTML = "The request to get user location timed out."
                        break;
                    case error.UNKNOWN_ERROR:
                        innerHTML = "An unknown error occurred."
                        break;
                }
                jQuery('#demogeo').val(value)
            }
        
        
            // OTP functionality
        
            // function sendOTP() {
            //     var agencyMail = jQuery("#agency_mail").val(); // Assume agency_mail is the input field's ID
        
            //     jQuery.ajax({
            //         url: "{{ url('agency/send-otp') }}",
            //         type: 'POST',
            //         data: {
            //             email: agencyMail
            //         },
            //         dataType: "text",
            //         headers: {
            //             'X-CSRF-TOKEN': "{{ csrf_token() }}"
            //         },
            //         success: function(response) {
            //             // Assuming the OTP has been sent successfully
            //             openOtpPopup(); // Function to open the OTP popup/modal
            //         },
            //         error: function(error) {
            //             alert("Failed to send OTP. Please try again.");
            //             // Re-enable buttons in case of error
            //             jQuery(".submit").prop('disabled', false);
            //             jQuery(".savebutton").prop('disabled', false);
            //         }
            //     });
            // }
        
            function sendOTP() {
                // IDs to check for selected values
                var obsIds = ['obs42', 'obs62', 'obs82', 'obs133'];
                var selectedObsValue = null;
            
                // Loop through the IDs to find the selected value
                obsIds.forEach(function(id) {
                    var value = jQuery('#' + id).val();
                    if (value) {
                        selectedObsValue = value; // Assign the value if found
                        return false; // Exit loop after finding the first non-empty value
                    }
                });
                console.log('Collection-managerselectedd sendOTPx',selectedObsValue);
            
                var agencyMail = jQuery("#agency_mail").val(); // Get the email from the input field
                var audit_for = jQuery("#audit_for").val(); // Get the email from the input field
                var product_id = jQuery("#productSelect").val(); // Get the email from the input field
                var audit_cycle = jQuery('#audit_cycle').val();
                var level3 =jQuery('#collection_manager-select').val() || '';
                var present_auditor = jQuery('#present_auditor').val();
               
                var level4Values = [];
                var level5Values = [];
                jQuery('#intimationUsersContainer .intimationUser').each(function() {
                    level4Values.push(jQuery(this).find('select[name="level_4[]"]').val());
                    level5Values.push(jQuery(this).find('select[name="level_5[]"]').val());
                });
            
                if (selectedObsValue == 4 || selectedObsValue == 5) {
                    // Send OTP to Level 3 user (collection manager)
                    var collectionManagerSelected = jQuery("#collection_manager-select").val();
                    console.log('Collection-managerselecteddd',collectionManagerSelected);
                     // Get the collection manager selection
                }
            
                //  alert(product_id);
                // Validate email input
                if (agencyMail === '' || !validateEmail(agencyMail)) {
                    alert("Please enter a valid email address.");
                    return; // Stop the function if the email is not valid
                }
            
                // Prepare data similar to submitDataFun
                var parameters = {};
                var sub = {};
                for (var el in result) {
                    if (result.hasOwnProperty(el)) {
                        for (var row in result[el]) {
                            if (result[el].hasOwnProperty(row)) {
                                var ck = 0;
                                if (jQuery('#ackalert' + row).prop('checked') == true) {
                                    ck = 1;
                                }
                                  var baseScore = parseFloat(jQuery('#org' + row).text()) || 0;
                                var selectedScoreVal = parseFloat(jQuery('#errorCount' + row).find('option:selected').data('score'));
                                var finalErrorScore = isNaN(selectedScoreVal) ? baseScore : selectedScoreVal;
                                var error_score = baseScore - finalErrorScore;
                                if (error_score < 0) error_score = 0; // deduction for record keeping // deduction amount
                                sub[row] = {
                                    'remark': jQuery('#remark' + row).val(),
                                    'orignal_weight': jQuery('#org' + row).text(),
                                    'temp_weight': result[el][row],
                                    'score': jQuery('#' + row).val(),
                                    'is_percentage': (jQuery('#' + row).val() == 'rating') ? 1 : 0,
                                    'selected_per': jQuery('#ratingSelect' + row).val(),
                                    'ackalert': ck,
                                    'error_score': error_score,
                                    'option': jQuery('#obs' + row + ' option:selected').text(),
                                    'error_count': jQuery('#errorCount' + row).val(),
                                }
                            }
                        }
                    
                        parameters[el] = {
                            'score': jQuery('#scroable' + el).text(),
                            'score_with_fatal': jQuery('#wfatal' + el).text(),
                            'score_without_fatal': jQuery('#wnfatal' + el).text(),
                            'temp_total_weightage': jQuery('#scroable').text(),
                            'parameter_weight': jQuery('#total' + el).text(),
                            'subs': sub
                        }
                        sub = {}
                    }
                }

                // ==================== CATEGORY SCORING ADDITIONS START ====================
                var categoryScores = null;
                if (hasCategories) {
                    categoryScores = getCategoryScoresForSubmission();
                }
                // ==================== CATEGORY SCORING ADDITIONS END ====================
            
                // Show loader
                jQuery('#loader').show();
                // Prepare OTP data
                var otpData = {
                    'product_id': product_id,
                    'agency_email': agencyMail,
                    'agency_id': audit_for,
                    'temp_total_weightage': jQuery('#scroable').text(),
                    'overall_score': jQuery('#wfatal').text(),
                    'parameters': parameters,
                    'audit_cycle': audit_cycle,
                    'collection_manager': collectionManagerSelected,
                    '_token': "{{ csrf_token() }}",
                    'is_virtual_audit': jQuery('#virtualAudit').is(':checked') ? 1 : 0,
                    'lavel_3': level3,
                    'present_auditor': present_auditor,
                    'lavel_4': level4Values,
                    'lavel_5': level5Values,
                    'artifactIds': JSON.stringify(artifactData),
                    'agency_manager': jQuery('input[name=agency_manager]').val(),
                    'category_scores': categoryScores   // <-- ADDED
                };
            
                // Send OTP via AJAX
                jQuery.ajax({
                    url: "{{ url('agency/send-otp') }}",
                    type: 'POST',
                    data: otpData,
                    dataType: "text",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        jQuery('#loader').hide();
                    
                        openOtpPopup(); // Open the OTP popup/modal if the OTP is sent successfully
                          // If collection manager OTP is also sent, open its verification popup as well
                    if (response.collection_manager_otp_sent) {
                        openCollectionManagerOtpPopup(); // Open collection manager OTP verification
                    }
                    },
                    error: function (error) {
                        jQuery('#loader').hide();
                        alert("Failed to send OTP. Please try again.");
                        // jQuery(".submit").prop('disabled', false);
                        // jQuery(".savebutton").prop('disabled', false);
                    }
                });
            }
            function startResendTimer(buttonId, timerDisplayId, duration = 30) {
                let timer = duration;
                const button = jQuery(buttonId);
                const timerDisplay = jQuery(timerDisplayId);
            
                // Disable the button and show the timer
                button.prop('disabled', true);
                timerDisplay.text(`Please wait ${timer} seconds`).show();
            
                const interval = setInterval(() => {
                    timer--;
                    timerDisplay.text(`Please wait ${timer} seconds`);
                
                    if (timer <= 0) {
                        clearInterval(interval);
                        button.prop('disabled', false);
                        timerDisplay.text('').hide(); // Hide the timer display after countdown
                    }
                }, 1000);
            }
                let otpResendCooldown = false; // Cooldown flag for Resend OTP
                let collectionManagerOtpResendCooldown = false; // Cooldown flag for Collection Manager OTP
        
                function startResendTimer(buttonId, timerDisplayId) {
                    const cooldownTime = 30; // Cooldown time in seconds
                    let remainingTime = cooldownTime;
                
                    // Disable the resend button
                    jQuery(buttonId).prop('disabled', true);
                    otpResendCooldown = true;
                
                    // Show timer and update every second
                    const timerInterval = setInterval(function () {
                        if (remainingTime <= 0) {
                            clearInterval(timerInterval);
                            jQuery(timerDisplayId).hide();
                            jQuery(buttonId).prop('disabled', false);
                            otpResendCooldown = false; // Reset cooldown
                        } else {
                            jQuery(timerDisplayId).show().text(`Please wait ${remainingTime} seconds to resend OTP.`);
                            remainingTime--;
                        }
                    }, 1000);
                }
            
                jQuery(document).on('click', '#resendOtpButton', function () {
                    if (otpResendCooldown) {
                        alert("Please wait until the timer finishes to resend OTP.");
                        return;
                    }
                
                    const buttonId = '#resendOtpButton';
                    const timerDisplayId = '#resendOtpTimer';
                
                    startResendTimer(buttonId, timerDisplayId);
                
                    // Existing AJAX code for OTP resend
                    const agencyMail = jQuery("#agency_mail").val();
                    const audit_for = jQuery("#audit_for").val();
                    const product_id = jQuery("#productSelect").val();
                    const audit_cycle = jQuery('#audit_cycle').val();
                    var level3 =jQuery('#collection_manager-select').val() || '';
                    var present_auditor = jQuery('#present_auditor').val();
                    
                    var level4Values = [];
                    var level5Values = [];
                    jQuery('#intimationUsersContainer .intimationUser').each(function() {
                        level4Values.push(jQuery(this).find('select[name="level_4[]"]').val());
                        level5Values.push(jQuery(this).find('select[name="level_5[]"]').val());
                    });
                
                    if (agencyMail === '' || !validateEmail(agencyMail)) {
                        alert("Please enter a valid email address.");
                        return;
                    }
                
                        // Prepare data similar to submitDataFun
                        var parameters = {};
                        var sub = {};
                        for (var el in result) {
                        if (result.hasOwnProperty(el)) {
                            for (var row in result[el]) {
                                if (result[el].hasOwnProperty(row)) {
                                    var ck = 0;
                                    if (jQuery('#ackalert' + row).prop('checked') == true) {
                                        ck = 1;
                                    }
                                    sub[row] = {
                                        'remark': jQuery('#remark' + row).val(),
                                        'orignal_weight': jQuery('#org' + row).text(),
                                        'temp_weight': result[el][row],
                                        'score': jQuery('#' + row).val(),
                                        'is_percentage': (jQuery('#' + row).val() == 'rating') ? 1 : 0,
                                        'selected_per': jQuery('#ratingSelect' + row).val(),
                                        'ackalert': ck,
                                        'option': jQuery('#obs' + row + ' option:selected').text(),
                                        'error_count': jQuery('#errorCount' + row).val(),
                                    }
                                }
                            }
                            parameters[el] = {
                                'score': jQuery('#scroable' + el).text(),
                                'score_with_fatal': jQuery('#wfatal' + el).text(),
                                'score_without_fatal': jQuery('#wnfatal' + el).text(),
                                'temp_total_weightage': jQuery('#scroable').text(),
                                'parameter_weight': jQuery('#total' + el).text(),
                                'subs': sub
                            }
                            sub = {}
                        }
                        }

                        // ==================== CATEGORY SCORING ADDITIONS START ====================
                        var categoryScores = null;
                        if (hasCategories) {
                            categoryScores = getCategoryScoresForSubmission();
                        }
                        // ==================== CATEGORY SCORING ADDITIONS END ====================

                        var otpData = {
                            'product_id': product_id,
                            'agency_email': agencyMail,
                            'agency_id': audit_for,
                            'temp_total_weightage': jQuery('#scroable').text(),
                            'overall_score': jQuery('#wfatal').text(),
                            'parameters': parameters,
                            'audit_cycle': audit_cycle,
                            '_token': "{{ csrf_token() }}",
                            'lavel_3': level3,
                            'present_auditor': present_auditor,
                            'lavel_4': level4Values,
                            'lavel_5': level5Values,
                            'category_scores': categoryScores   // <-- ADDED
                        };
                    
                    
                    jQuery.ajax({
                        url: "{{ url('agency/resend-otp') }}",
                        type: 'POST',
                        data: otpData,
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            alert(response.message || "OTP sent successfully.");
                        },
                        error: function (xhr, status, error) {
                            console.error("Error:", xhr.responseText);
                            alert("Failed to resend OTP. Please try again.");
                        }
                    });
                });
            
                jQuery(document).on('click', '#collectionManagerResendOtpButton', function () {
                    if (collectionManagerOtpResendCooldown) {
                        alert("Please wait until the timer finishes to resend OTP.");
                        return;
                    }       
                
                    const buttonId = '#collectionManagerResendOtpButton';
                    const timerDisplayId = '#collectionManagerResendOtpTimer';
                
                    startResendTimer(buttonId, timerDisplayId);
                
                    // Existing AJAX code for Collection Manager OTP resend
                    const collectionManagerSelected = jQuery("#collection_manager-select").val();
                    const agencyMail = jQuery("#agency_mail").val();
                    const audit_for = jQuery("#audit_for").val();
                    const product_id = jQuery("#productSelect").val();
                    const audit_cycle = jQuery('#audit_cycle').val();
                
                    if (agencyMail === '' || !validateEmail(agencyMail)) {
                        alert("Please enter a valid email address.");
                        return;
                    }
                
                    // Prepare data similar to submitDataFun
                    var parameters = {};
                    var sub = {};
                    for (var el in result) {
                        if (result.hasOwnProperty(el)) {
                            for (var row in result[el]) {
                                if (result[el].hasOwnProperty(row)) {
                                    var ck = 0;
                                    if (jQuery('#ackalert' + row).prop('checked') == true) {
                                        ck = 1;
                                    }
                                    sub[row] = {
                                        'remark': jQuery('#remark' + row).val(),
                                        'orignal_weight': jQuery('#org' + row).text(),
                                        'temp_weight': result[el][row],
                                        'score': jQuery('#' + row).val(),
                                        'is_percentage': (jQuery('#' + row).val() == 'rating') ? 1 : 0,
                                        'selected_per': jQuery('#ratingSelect' + row).val(),
                                        'ackalert': ck,
                                        'option': jQuery('#obs' + row + ' option:selected').text(),
                                        'error_count': jQuery('#errorCount' + row).val(),
                                    }
                                }
                            }
                        
                            parameters[el] = {
                                'score': jQuery('#scroable' + el).text(),
                                'score_with_fatal': jQuery('#wfatal' + el).text(),
                                'score_without_fatal': jQuery('#wnfatal' + el).text(),
                                'temp_total_weightage': jQuery('#scroable').text(),
                                'parameter_weight': jQuery('#total' + el).text(),
                                'subs': sub
                            }
                            sub = {}
                        }
                    }

                    // ==================== CATEGORY SCORING ADDITIONS START ====================
                    var categoryScores = null;
                    if (hasCategories) {
                        categoryScores = getCategoryScoresForSubmission();
                    }
                    // ==================== CATEGORY SCORING ADDITIONS END ====================
                    
                    var otpData = {
                        'manager_id': collectionManagerSelected,
                        'product_id': product_id,
                        'agency_email': agencyMail,
                        'agency_id': audit_for,
                        'parameters': parameters,
                        'audit_cycle': audit_cycle,
                        '_token': "{{ csrf_token() }}",
                        'type': "collection_manager",
                        'category_scores': categoryScores   // <-- ADDED
                    };
                
                    jQuery.ajax({
                        url: "{{ url('collection-manager/resend-otp') }}",
                        type: 'POST',
                        data: otpData,
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            alert(response.message || "OTP sent successfully.");
                        },
                        error: function (xhr, status, error) {
                            console.error("Error:", xhr.responseText);
                            alert("Failed to resend OTP. Please try again.");
                        }
                    });
                });
</script>
<script>
    // Helper function to validate email format
    function validateEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }

    function openOtpPopup() {
        var otpModalElement = document.getElementById('otpModal');
        var otpModal = new bootstrap.Modal(otpModalElement, {
            backdrop: 'static',  // Disables closing the modal by clicking outside
            keyboard: false      // Disables closing the modal by pressing the escape key
        });
        otpModal.show();

        // Ensure the "Verify OTP" button is only bound once
        jQuery("#verifyOtpButton").off('click').on('click', function () {
            var enteredOtp = jQuery("#otpInput").val(); // Get the OTP entered by the user
            var agencyEmail = jQuery("#agency_mail").val(); // Get the email
            var auditFor = jQuery("#audit_for").val(); // Get the agency ID

            jQuery('#loader').show();
            jQuery.ajax({
                url: "{{ url('agency/verify-otp') }}",
                type: 'POST',
                data: {
                    otp: enteredOtp,
                    agency_id: auditFor, // Include agency_id
                    agency_email: agencyEmail,// Include agency_email
                    type: 'agency'

                },
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function (response) {
                    jQuery('#loader').hide();

                    if (response == "valid") {
                        // OTP is valid, close the modal and proceed with submission
                        otpModal.hide();
                        // IDs to check for selected values
                        var obsIds = ['obs42', 'obs62', 'obs82', 'obs133'];
                        var selectedObsValue = null;

                        // Loop through the IDs to find the selected value
                        obsIds.forEach(function(id) {
                            var value = jQuery('#' + id).val();
                            if (value) {
                                selectedObsValue = value; // Assign the value if found
                                return false; // Exit loop after finding the first non-empty value
                            }
                        });
                        console.log('Collection-managerselecteddd verifyOtpButton',selectedObsValue);                        
                        if (selectedObsValue == 4 || selectedObsValue == 5) {
                            // Send OTP to Level 3 user
                            openCollectionManagerOtpPopup(); // Open collection manager OTP verification
                            // Get the collection manager selection
                        }else{
                            submitDataFun('submit');
                            console.log("Collection manager otp not required  ");
                            
                        }

                    } else {
                        alert("Invalid OTP. Please try again.");
                    }
                },
                error: function (error) {
                    jQuery('#loader').hide();
                    alert("Failed to verify OTP. Please try again.");
                }
            });
        }); // Closing bracket for Verify OTP click handler
    }


    function openCollectionManagerOtpPopup() {
        var collectionManagerOtpModalElement = document.getElementById('collectionManagerOtpModal');
        var collectionManagerOtpModal = new bootstrap.Modal(collectionManagerOtpModalElement, {
            backdrop: 'static',  // Prevent closing the modal by clicking outside
            keyboard: false      // Prevent closing the modal by pressing the escape key
        });

        collectionManagerOtpModal.show();

        jQuery("#verifyCollectionManagerOtpButton").off('click').on('click', function() {
            var enteredOtp = jQuery("#collectionManagerOtpInput").val();
            var agencyEmail = jQuery("#agency_mail").val(); // Get the email
            var collectionManagerSelected = jQuery("#collection_manager-select").val();
            console.log('varifyotp', collectionManagerSelected);

            var auditFor = jQuery("#audit_for").val(); 
            jQuery('#loader').show();

            jQuery.ajax({
                url: "{{ url('agency/verify-otp') }}",
                type: 'POST',
                data: {
                    otp: enteredOtp,
                    agency_id: auditFor,
                    agency_email: agencyEmail,
                    cm_manager_id: collectionManagerSelected,
                    type: 'collection_manager'
                },
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(response) {
                    jQuery('#loader').hide();
                    if (response == "valid") {
                        // OTP is valid for collection manager
                        collectionManagerOtpModal.hide();
                        // Proceed to final submission
                        submitDataFun('submit');
                    } else {
                        
                        alert("Invalid OTP for Collection Manager. Please try again.");
                    }
                },
                error: function(error) {
                    jQuery('#loader').hide();

                    alert("Failed to verify OTP. Please try again.");
                }
            });
        });
    }



    jQuery('#productSelect').on('mousedown', function(event) {
        event.preventDefault(); // Prevents the selection from changing
    });
    jQuery('#sub_product').on('mousedown', function(event) {
        event.preventDefault(); // Prevents the selection from changing
    });


</script>

<!-- OTP Modal -->
<div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="otpModalLabel">Please Enter Agency OTP</h5>
            </div>
            <div class="modal-body">
                <lottie-player src="{{ asset('public/images/computer-otp-verification.json') }}" autoPlay loop
                style="width: 120px; height: 120px;margin:auto auto 20px"></lottie-player>
                <div class="mb-3">
                    <label for="otpInput" class="form-label">OTP has been sent to your registered email. Please enter it
                        below:</label>
                    <input type="text" class="form-control" id="otpInput" placeholder="Enter OTP">
                </div>
                <button type="button" class="btn btn-secondary" id="resendOtpButton">Resend OTP</button>
                <div id="resendOtpTimer" style="display: none; color: red; margin-top: 10px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="verifyOtpButton">Verify OTP</button>
            </div>
        </div>
    </div>
</div>
<!-- Collection manager otp -->
<div class="modal fade" id="collectionManagerOtpModal" tabindex="-1" aria-labelledby="collectionManagerOtpModalLabel" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background: #637c97">
        <h5 class="modal-title" id="collectionManagerOtpModalLabel">Please Enter Collection Manager OTP</h5>
      </div>
      <div class="modal-body">
        <lottie-player src="{{ asset('public/images/computer-otp-verification.json') }}" autoPlay loop
        style="width: 120px; height: 120px;margin:auto auto 20px;"></lottie-player>
        <div class="mb-3">
          <label for="collectionManagerOtpInput" class="form-label">OTP has been sent to registered Collection Manager email. Please enter it below:</label>
          <input type="text" class="form-control" id="collectionManagerOtpInput" placeholder="Enter OTP">
        </div>
        <button type="button" class="btn btn-secondary" id="collectionManagerResendOtpButton">Resend OTP TO Collection Manager</button>
        <div id="collectionManagerResendOtpTimer" style="display: none; color: red; margin-top: 10px;"></div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="verifyCollectionManagerOtpButton">Verify OTP</button>
      </div>
    </div>
  </div>
</div> 
<style>
    .modal-backdrop{
        pointer-events: none !important;
    }
</style>

<script>
    async function checkSpellingGrammar(text, resultDivId) {
        const response = await fetch("https://api.languagetoolplus.com/v2/check", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
                text: text,
                language: "en-US"
            })
        });

        const data = await response.json();
        let resultDiv = document.getElementById(resultDivId);
        resultDiv.innerHTML = '';

        if (data.matches && data.matches.length > 0) {
            data.matches.forEach(match => {
                resultDiv.innerHTML += `<div>❌ ${match.message} (<strong>${match.replacements.map(r => r.value).join(', ')}</strong>)</div>`;
            });
        } else {
            resultDiv.innerHTML = '<div style="color:green;">✅ No errors found.</div>';
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('textarea[id^="remark"]').forEach(textarea => {
            textarea.addEventListener("blur", function () {
                const id = this.id.replace('remark', 'remarkError');
                checkSpellingGrammar(this.value, id);
            });
        });
    });
    

    jQuery('.0bervation').on('change', function (e) {
        updateStep2Progress();
        var observationId = jQuery(this).data('id'); // sub_parameter_id
        var parameterId = jQuery(this).data('parameterid'); // parameter_id
        var selectedValue = jQuery(this).val();
        var agency_id = jQuery("#audit_for").val();
        var product_id = jQuery("#productSelect").val(); // Optional
        var selectedText = jQuery(this).children("option:selected").text();

        // Clear any previous inline error messages for this sub-parameter
        jQuery(`#error-msg-primary-${observationId}`).html('');
        jQuery(`#error-msg-secondary-${observationId}`).html('');

        if (selectedText.trim().toLowerCase() === "unsatisfactory") {

            // 🔵 First AJAX for 'check-last-observation'
            jQuery.ajax({
                url: "{{ url('check-last-observation') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    agency_id: agency_id,
                    parameter_id: parameterId,
                    product_id: product_id,
                    sub_parameter_id: observationId,
                    value: selectedValue,
                    observation: selectedText
                },
                success: function (response) {
                    if (response.status === 'error') {
                        jQuery(`#error-msg-primary-${observationId}`).html(response.message);
                    }
                }
            });

            // 🔴 Second AJAX for 'check-last-repeat-observation'
            jQuery.ajax({
                url: "{{ url('check-last-repeat-observation') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    agency_id: agency_id,
                    parameter_id: parameterId,
                    product_id: product_id,
                    sub_parameter_id: observationId,
                    value: selectedValue,
                    observation: selectedText
                },
                success: function (response) {
                    if (response.status === 'error') {
                        jQuery(`#error-msg-secondary-${observationId}`).html(response.message);
                    }
                }
            });
        }
    });
   jQuery(document).on('click', '.save-issues-btn', function () {
    var subParamId = jQuery(this).data('subparam');
    var selectedIssues = [];

    // Collect checked question IDs
    jQuery(`input[name='questions[${subParamId}][]']:checked`).each(function () {
        selectedIssues.push({
            id: jQuery(this).val(),
            text: jQuery(this).data('question-text')
        });
    });

    var remark = jQuery(`#extra_remark_${subParamId}`).val();
    var agency_id = jQuery("#audit_for").val();
    var cycle = jQuery('#audit_cycle').val();
    var product_id = jQuery("#productSelect").val();
    var parameter_id = jQuery(`.0bervation[data-id='${subParamId}']`).data('parameterid');

    jQuery.ajax({
        url: "{{ url('save-sub-parameter-issues') }}",
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            sub_parameter_id: subParamId,
            parameter_id: parameter_id,
            issues: selectedIssues,
            cycle: cycle,
            remark: remark,
            agency_id: agency_id,
            product_id: product_id
        },
        success: function (response) {
            if (response.status === 'success') {
                alert('Saved successfully!');
            } else {
                alert('Something went wrong!');
            }
        },
        error: function () {
            alert('Server error. Please try again.');
        }
    });
});



</script>

<!-- Flatpickr CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css" />
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#audit_date", {
            dateFormat: "Y-m-d",
            disableMobile: true,
            minDate: "today",
            defaultDate: "today",
            monthSelectorType: 'static', // disables dropdown for year/month
            disable: [
                function (date) {
                    // Disable Sundays (getDay() === 0)
                    return date.getDay() === 0;
                }
            ],
            onDayCreate: function (dObj, dStr, fp, dayElem) {
                // Highlight Sundays
                if (dayElem.dateObj.getDay() === 0) {
                    dayElem.classList.add('flatpickr-sunday');
                    dayElem.setAttribute('title', 'Sunday');
                }
            }
        });
    });
</script>

<style>
    .flatpickr-sunday {
        background-color: #ffe0e0 !important;
        color: #d00 !important;
        border-radius: 50% !important;
    }

    .flatpickr-calendar .flatpickr-monthDropdown-months,
    .flatpickr-calendar .numInputWrapper {
        display: none !important; /* hides dropdowns for month and year */
    }

    .flatpickr-current-month {
        justify-content: center !important;
    }
</style>
@endsection