@extends('layouts.master')
@section('css')


{{-- <link rel="stylesheet" href="{{URL::asset('base/style.bundle.css')}}"> --}}
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

            <div class="card">

                <div class="card-header"
                    style="background-image: linear-gradient(to right, rgb(132, 94, 194), rgb(144, 109, 198), rgb(156, 125, 201), rgb(168, 140, 205), rgb(179, 156, 208));color:#fff">
                    
                    <strong
                        class="card-title">{{($data->lob=='commercial_vehicle')?'Commercial Vehicle':ucfirst($data->lob)}}
                        | {{ucfirst(str_replace('_',' ',$data->type))}}</strong>

                </div>

                <div class="card-body">

                    <div class="row">
                    <input type="hidden" value="{{$result->id}}" id="auditid" name="auditData">

                       @if($data->type=='agency')

                        <div class="col-md-3 form-group">

                            <label>Agency*</label>

                            <select name="agency"  id="audit_for" class="form-control agency readonly">

                                <option value="">Choose Agency</option>

                                @foreach ($agency as $item)

                                <option value="{{$item->id}}" {{($item->id==$result->agency_id)?'selected':''}}>
                                {{$item->agency_id}} {{$item->name}}</option>
                                @endforeach

                            </select>

                        </div>

                      
                        @endif
						<div class="col-md-3 form-group">
                            <label>Audit Cycle*</label>
                            <select name="audit_cycle" class="form-control audit_cycle js-example-basic-single"
                                id="audit_cycle" required="true" readonly>
                                @foreach ($cycle as $item)
                                <option value="{{ $item->id }}" 
                                    {{ $item->id == $result->audit_cycle_id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 form-group">
                            <label>Audit Date*</label>
                            <input type="date" name="audit_date" value="{{$result->audit_date_by_aud}}" class="form-control audit_date "
                                id="audit_date" required="true" disabled>
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
                        <div class="col-md-4 form-group" id="collection_manager">
                            <label>Level 3</label>
                            {!! Form::select('lavel_3', $formattedUsers,$result->lavel_3 ?? '', ['id'=>'collection_manager-select', 'class' => 'form-control js-example-basic-single','readonly'=>'readonly']) !!}
                        </div>
                        @endif

                        <div class="col-md-4 form-group">
                            <label>Present Auditor</label>
                            {!! Form::text('present_auditor',$result->present_auditor ?? '', ['id'=>'present_auditor', 'class' => 'form-control js-example-basic-single' ,'readonly'=>'readonly']) !!}
                        </div>

                         @if(auth()->user()->client_id == 15)
                        <div class="col-md-3 form-group" id="sheet">
                            <label for="sheet_select">Nature of Service<span class="text-danger">*</span></label>

                            <select name="sheet" id="sheet_select" class="form-control sheet" required>
                                <option value="">-- Select Nature of Service --</option>
                            
                                <option value="field"
                                    {{ $result->audit_sheet_type == 'field' ? 'selected' : '' }}>
                                    Field
                                </option>
                            
                                <option value="telecalling"
                                    {{ $result->audit_sheet_type == 'telecalling' ? 'selected' : '' }}>
                                    Telecalling
                                </option>
                            
                                <option value="field_telecalling"
                                    {{ $result->audit_sheet_type == 'field_telecalling' ? 'selected' : '' }}>
                                    Field + Telecalling
                                </option>
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
								@php
									// Explode level_4 and level_5 values
									$level4Values = explode(',', $result->lavel_4);
									$level5Values = explode(',', $result->lavel_5);

								@endphp

								@foreach ($level4Values as $index => $level4)
									<div class="row intimationUser">
										<div class="col-md-3 form-group">
											<label>Level 4</label>
											{!! Form::select('level_4[]', $Level_5, $level4, ['class' => 'form-control select2', 'id'=>"level_4_{$index}", 'readonly' => 'readonly']) !!}
										</div>
										<div class="col-md-3 form-group">
											<label>Level 5</label>
											{{-- Check if there's a matching index for level 5 --}}
											{!! Form::select('level_5[]', $Level_5, $level5Values[$index] ?? null, ['class' => 'form-control select2', 'id'=>"level_5_{$index}", 'readonly' => 'readonly']) !!}
										</div>
									</div>
								@endforeach
                        @endif
                    </div>
						</div>
                   @if(in_array(21, $allocatedmodule))   
                        <div class="col-md-3 form-group">
                            <label>
                                <input type="checkbox" id="virtualAudit" value="1"
                                    {{ isset($result) && $result->virtual_audit == 1 ? 'checked' : '' }} disabled>
                                Virtual Audit
                            </label>
                        
                            {{-- Hidden input to ensure value is submitted --}}
                            <input type="hidden" name="virtual_audit" value="{{ $result->virtual_audit }}">
                        </div>
                    @endif

            </div>

            <div class="card">

                <div class="card-header">

                    <strong class="card-title">Audit</strong>

                </div>

                <div class="card-body">



                    <div class="row">

                        <div class="col-md-2 kt-font-bolder">

                            Parameter

                        </div>

                        <div class="col-md-10 kt-font-bolder">

                            <div class="row">

                                <div class="col-md-2 kt-font-bolder">Sub Parameter</div>

                                <div class="col-md-2 kt-font-bolder">Observation</div>

                                <div class="col-md-2 kt-font-bolder">Scored</div>

                                <div class="col-md-2 kt-font-bolder">Remarks</div>

                                <div class="col-md-2 kt-font-bolder">Action</div>

                            </div>

                        </div>

                    </div>

                    <div id="seprator" class="kt-separator kt-separator--border-dashed kt-separator--space-lg"></div>

                    @php

                    $total=0;

                    @endphp
                    <div id="accordion">
                        @foreach ($data->parameter as $item)
                        <div class="card">

                            <div class="card-header" id="<?php echo 'heading'.$item->id ?>" data-toggle="collapse"
                                data-target="<?php echo '#collapse'.$item->id ?>"
                                aria-controls="<?php echo '#collapse'.$item->id ?>" aria-expanded="false">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" data-toggle="collapse"
                                        data-target="<?php echo '#collapse'.$item->id ?>"
                                        aria-controls="<?php echo '#collapse'.$item->id ?>" aria-expanded="false">
                                        {{$item->parameter}}
                                    </button>
                                    @if(isset($resultSubPar[$item->qm_sheet_sub_parameter[0]->id]))
                                    <button class="btn btn-link" style="float:right;">Filled</button>
                                    @else
                                    <button class="btn btn-link" style="float:right;">Not Filled</button>
                                    @endif

                                </h5>
                            </div>
                            <div class="row flex-container collapse"
                                style="border-bottom: 1px solid rgb(204, 204, 204); padding: 20px 0px; height: 100%;"
                                id="<?php echo 'collapse'.$item->id ?>"
                                aria-labelledby="<?php echo 'heading'.$item->id ?>" data-parent="#accordion">

                                <div class="col-md-2 kt-font-bolder kt-font-primary flex-item centerparameter">

                                    {{$item->parameter}}

                                </div>

                                <div class="col-md-10 sp-row">

                                    @foreach ($item->qm_sheet_sub_parameter as $value)

                                    <div class="row flex-container mb-2">

                                        <div class="col-md-2 kt-font-bold">

                                            {{$value->sub_parameter}} <i title="sdfdf"
                                                class="la la-info-circle kt-font-warning sp-details-top"></i>

                                        </div>

                                        <div class="col-md-2">


                                            <select class="form-control 0bervation" id="obs{{$value->id}}"
                                                data-id="{{$value->id}}" data-parameterId="{{$item->id}}"
                                                data-point="{{$value->weight}}">

                                                <option value="">Choose type</option>

                                                @if(isset($resultSubPar[$value->id]) &&
                                                $resultSubPar[$value->id]->option_selected==null)

                                                @if($value->pass==1)<option value="{{$value->weight}}"
                                                    {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->selected_option==$value->weight))?'selected':''}}>
                                                    Satisfactory</option>@endif

                                                @if($value->fail==1)<option value="0"
                                                    {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_critical==0 && $resultSubPar[$value->id]->selected_option==0))?'selected':''}}>
                                                    Unsatisfactory</option>@endif

                                                @else

                                                @if($value->pass==1)<option value="{{$value->weight}}"
                                                    {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->option_selected=='Satisfactory'))?'selected':''}}>
                                                    Satisfactory</option>@endif

                                                @if($value->fail==1)<option value="0"
                                                    {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_critical==0 && $resultSubPar[$value->id]->option_selected=='Unsatisfactory'))?'selected':''}}>
                                                    Unsatisfactory</option>@endif

                                                @endif

                                                @if($value->critical==1)<option value="Critical"
                                                    {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_critical==1))?'selected':''}}>
                                                    Critical</option>@endif

                                                @if($value->na==1)<option value="N/A"
                                                    {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_critical==0 && $resultSubPar[$value->id]->selected_option=='N/A'))?'selected':''}}>
                                                    N/A</option>@endif

                                                @if($value->pwd==1)<option value="{{round(($value->weight)/2,2)}}"
                                                    {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->selected_option==round(($value->weight)/2,2)))?'selected':''}}>
                                                    PWD</option>@endif

                                                @if($value->per==1)<option value="{{round(($value->weight))}}"
                                                    data-type="rating"
                                                    {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_percentage==1))?'selected':''}}>
                                                    Percentage</option>@endif

                                            </select>

                                            <span style="display:none" id="org{{$value->id}}">{{$value->weight}}</span>

                                        </div>

                                        <div class="col-md-2">

                                            <select class="form-control ratingSelect" name="ratingSelect"
                                                id="ratingSelect{{$value->id}}"
                                                style="{{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_percentage!=1))?'display:none':'display:block'}}"
                                                data-id="{{$value->id}}" data-parameterId="{{$item->id}}">

                                                <option>select percentage</option>

                                                @for($counting=0;$counting<=100;$counting=$counting+5) <option
                                                    value="{{$counting}}"
                                                    {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->selected_per==$counting))?'selected':''}}>
                                                    {{$counting}}%</option>

                                                    @endfor

                                            </select>

                                            @if(isset($resultSubPar[$value->id]) &&
                                            ($resultSubPar[$value->id]->is_percentage!=1))

                                            <input type="text" id="{{$value->id}}" readonly="readonly"
                                                class="form-control "
                                                value="{{ (isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_critical==1))?'Critical':($resultSubPar[$value->id]->score ?? '')}}" style="display: none">
                                                {{-- style="{{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_percentage==1))?'display:none':'display:block'}}"> --}}

                                            @else

                                            <input type="text" id="{{$value->id}}" readonly="readonly"
                                                class="form-control" value="rating" style="display: none;">
                                                {{-- style="{{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_percentage==1))?'display:none':'display:block'}} display:none;"> --}}

                                            @endif

                                        </div>
                                            @if(in_array(22, $allocatedmodule) && $value->use_error_count == 1)
                                            <div class="col-md-2">
                                            
                                                <label class="error-count-label">Error Count</label>
                                            
                                                <select class="form-control error-count-select"
                                                id="errorCount{{$value->id}}"
                                                name="error_count[{{$value->id}}]"
                                                data-id="{{$value->id}}"
                                                disabled>
                                            
                                                    <option value="0" {{ (isset($resultSubPar[$value->id]) && $resultSubPar[$value->id]->error_count == 0) ? 'selected' : '' }}>0</option>
                                                    <option value="1" {{ (isset($resultSubPar[$value->id]) && $resultSubPar[$value->id]->error_count == 1) ? 'selected' : '' }}>1</option>
                                                    <option value="2" {{ (isset($resultSubPar[$value->id]) && $resultSubPar[$value->id]->error_count == 2) ? 'selected' : '' }}>2</option>
                                                    <option value="3" {{ (isset($resultSubPar[$value->id]) && $resultSubPar[$value->id]->error_count >= 3) ? 'selected' : '' }}>3 or more</option>
                                            
                                                </select>
                                            
                                            </div>
                                            @else
                                            
                                            <input type="hidden"
                                                id="errorCount{{$value->id}}"
                                                name="error_count[{{$value->id}}]"
                                                value="0"
                                                data-id="{{$value->id}}">
                                            
                                            @endif
                                        <div class="col-md-2">

                                            {{-- <!-- <textarea class="form-control" id="remark{{$value->id}}"
                                            value="{{ $resultSubPar[$value->id]->remark}}">{{ $resultSubPar[$value->id]->remark ?? ''}}</textarea>
                                            --> --}}

                                        </div>

                                        <div class="col-md-3">

                                            <!-- <button class="btn btn-danger btn-sm alertModal"
                                                data-parameterid="{{$item->id}}" data-id="{{$value->id}}">Alert</button> -->

                                            <button class="btn btn-info btn-sm artifactModal mr-1"
                                                data-parameterid="{{$item->id}}"
                                                data-id="{{$value->id}}">Artifact</button>

                                            <button class="btn btn-warning btn-sm closureArtifactBtn ml-1"
                                                    data-subparameterid="{{$value->id}}"
                                                    style="display:none;">
                                                    Closure Details
                                                </button>

                                            <!-- <input type="checkbox" id="ackalert{{$value->id}}" data-id="{{$value->id}}"
                                                data-parameterId="{{$item->id}}" /> -->

                                        </div>

                                    </div>

                                    <div class="col-md-12 row">

                                        <!-- <div class="col-md-2">

											Remark

										</div> -->

                                        <div class="col-md-10">

                                            <textarea class="form-control" id="remark{{$value->id}}"
                                                value="{{ $resultSubPar[$value->id]->remark ?? ''}}">{{ $resultSubPar[$value->id]->remark ?? ''}}</textarea>

                                        </div>

                                    </div>
                                    

                                    <div class="col-md-12 row">

                                        <div class="col-md-10 preview{{$value->id}}">

                                            @foreach($value->artifact as $art)
                                            @php
										        $extension = strtolower(pathinfo($art->file, PATHINFO_EXTENSION));
										    @endphp
                                            @if(in_array($art->id,$artifactIds))

                                            <div class="img-wrap art{{$art->id}}"
                                                style="position: relative;display: inline-block;font-size: 0;">

                                                <span class="close">&times;</span>

                                                <a href="{{ URL::asset('storage/app/'.$art->file) }}" target="_blank">
									    			@if($extension === 'pdf')
									    			    <img src="{{ asset('public/images/pdf-icon.png') }}" data-id="{{ $art->id }}" style="width: 100px; height: 100px;">
									    			@else
									    			    <img src="{{ URL::asset('storage/app/'.$art->file) }}" data-id="{{ $art->id }}" style="width: 100px; height: 100px;">
														
														        {{-- <img src="{{ URL::asset('storage/app/'.$art->file) }}" style="width:100%;height:100%;object-fit:contain;"> --}}
														    
									    			@endif
												</a>

                                            </div>

                                            @endif

                                            @endforeach

                                        </div>
                                          {{-- Voice Preview --}}
                                 <div class="col-md-12 mt-2 d-flex align-items-center justify-content-end">
    
    <div class="preview{{ $value->id }}"></div>

    <select id="langSelect{{ $value->id }}" 
            class="form-control mb-2" 
            style="width:200px;">

        <option value="en-IN">English (India)</option>
        <option value="hi-IN">Hindi</option>
        <option value="ta-IN">Tamil</option>
        <option value="te-IN">Telugu</option>
        <option value="bn-IN">Bengali</option>
        <option value="mr-IN">Marathi</option>
        <option value="gu-IN">Gujarati</option>
        <option value="pa-IN">Punjabi</option>
    </select>

    <button type="button"
            class="btn btn-outline-secondary btn-sm voice-btn m-2"
            data-id="{{ $value->id }}">

        🎤 Speak Remark
    </button>

</div>

                                    </div>

                                    <div id="seprator" class="kt-separator kt-separator--border-dashed "></div>

                                    @php

                                    $total=$total+$value->weight;

                                    @endphp

                                    @endforeach

                                    <span style="display:none" id="total{{$item->id}}">{{$total}}</span>

                                </div>

                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- // result --}}

                    <div>



                    </div>

                    {{-- <div class="card-footer">

					<button type="submit" class="btn btn-primary btn-sm">

						<i class="fa fa-dot-circle-o"></i> Submit

					</button>

					<button type="reset" class="btn btn-danger btn-sm">

						<i class="fa fa-ban"></i> Reset

					</button>

				</div> --}}

                </div>

            </div>



            <div class="card">

                <div class="card-header">

                    <strong class="card-title">Result</strong>

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

                        <div class="col-lg-2" id="wfatalper{{$item->id}}">0</div>
                        <!-- <div class="col-lg-1 kt-font-bold" id="grade"></div> -->


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

                        <div class="col-lg-3 kt-font-bold kt-font-success">Over All</div>

                        <div class="col-lg-3 kt-font-bold" id="scroable">0</div>

                        <div class="col-lg-3 kt-font-bold kt-font-danger" id="wfatal">0</div>

                        <div class="col-lg-2 kt-font-bold" id="wfatalper">0</div>

                        <div class="col-lg-1 kt-font-bold" id="grade"></div>

                    </div>

                </div>

            </div>

        </div>



    </div>

    <button type="submit" class="btn btn-primary btn-sm savebutton">

        <i class="fa fa-dot-circle-o"></i> Save

    </button>

    <button type="submit" class="btn btn-primary btn-sm submit">

        <i class="fa fa-dot-circle-o"></i> Submit

    </button>

    <button type="reset" class="btn btn-danger btn-sm">

        <i class="fa fa-ban"></i> Reset

    </button>

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

<div class="modal fade" id="artifactModal" tabindex="-1" role="dialog" aria-labelledby="artifactModalLabel"
    aria-hidden="true">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="artifactModalLabel">Artifact</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <div class="row">

                    <div class="col-md-12 form-group">

                        <label>files</label>

                        <input type="hidden" name="artifactParameterId" id="artifactParameterId" value="" />

                        <input type="hidden" name="artifactSubParameterId" id="artifactSubParameterId" value="" />
                        <input type="file" id="file" name="artifactfile[]" class="form-control-file artifact file" accept="image/*,application/pdf" multiple>

                        <div id="moreArtifact"></div>

                        <div id="progress-bar">

                            <span id="ProgressContaint" style="display:none">0% Complete</span>

                        </div>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                <button type="button" class="btn btn-primary" id="artifactAlert">Save changes</button>

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

@endsection

@section('js')

<script>
    const allocatedModules = @json($allocatedmodule);
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const SpeechRecognition =
        window.SpeechRecognition || window.webkitSpeechRecognition;

    if (!SpeechRecognition) {
        alert("Your browser does not support Speech Recognition. Please use Google Chrome.");
        return;
    }

    document.querySelectorAll('.voice-btn').forEach(button => {

        button.addEventListener('click', function () {

            const id = this.dataset.id;

            const textarea = document.getElementById('remark' + id);

            if (!textarea) {
                alert("Remark box not found for ID: " + id);
                return;
            }

            // ✅ Get selected language for current row
            const selectedLang =
                document.getElementById('langSelect' + id)?.value || 'en-IN';

            const recognition = new SpeechRecognition();

            recognition.lang = selectedLang;
            recognition.interimResults = false;
            recognition.maxAlternatives = 1;

            recognition.start();

            textarea.placeholder = "🎤 Listening...";

            recognition.onresult = function (event) {

                const transcript =
                    event.results[0][0].transcript.trim();

                const existing = textarea.value.trim();

                let combined = existing
                    ? existing + ' ' + transcript
                    : transcript;

                combined =
                    combined.charAt(0).toUpperCase() + combined.slice(1);

                textarea.value = combined;
            };

            recognition.onerror = function (event) {
                alert("Error: " + event.error);
            };

            recognition.onend = function () {
                textarea.placeholder = "Enter Remark Here";
            };

        });

    });

});
</script>


<script>



jQuery('#collection_manager-select').on('change',function(e){

	var code =jQuery(this).data('code');

	var bucket =jQuery(this).data('bucket');

	jQuery('input[name=Collection_Manager_bucket]').val(bucket)

	jQuery('input[name=Collection_Managercode]').val(code)

})

jQuery(document).on('change', '.artifact', function(e){

	console.log(e)

	if(e.target.files.length>0){

		jQuery('#moreArtifact').append('<input type="file" id="file" name="artifactfile[]" class="form-control-file artifact file">')

	}

})



jQuery(document).on('click', '.close', function() {
    var $imgWrap = jQuery(this).closest('.img-wrap');
    var id = $imgWrap.find('img').data('id');

    var data = {
        'id': id,
        '_token': '{{ csrf_token() }}'
    };

    var saveData = jQuery.ajax({
        type: 'DELETE',
        url: "{{url('artifact')}}/" + id,
        data: data,
        success: function(resultData) { 
            console.log(resultData);
            
            // Remove the image wrapper element
            $imgWrap.remove();
            
            // Also remove any elements with the class 'art' + id
            jQuery('.art' + id).remove();
        },
        error: function(xhr, status, error) {
            console.error('Delete error:', error);
            alert('Failed to delete the artifact. Please try again.');
        }
    });
});

var redalertData={};

	var result={};

	var par={};

	var subpar={};

	@php

	foreach($data->parameter as $item)

	{

		@endphp

		result[{{$item->id}}]={};

		@php

	}

	

	foreach($resultSubPar as $k=>$v){

		$subValue=($v->is_critical==1)?"Critical":$v->score;

		@endphp	

		subpar[{{$k}}]={{$v->id}}

		resultFun('{{$subValue}}', {{$k}},{{$v->parameter_id}})

	@php

	}

	foreach($resultPar as $k=>$v){

	@endphp

		par[{{$k}}]={{$v->id}}

	@php

	}

	@endphp

	function sum( obj ) {

		var sum = 0;

		for( var el in obj ) {

			if( obj.hasOwnProperty( el ) ) {

				sum += parseFloat( obj[el] );

			}

		}

		return sum;

	}

	function totalfun( obj ){

		var total=0;

		var parmeterTotal=0;

		for( var el in obj ) {

			if( obj.hasOwnProperty( el ) ) {

				var subtotal=0

				for( var item in obj[el] ) {

				if( obj[el].hasOwnProperty( item ) ) {

					if(obj[el][item]!='N/A'){

						paramterValue=jQuery('#org'+item).html();

					}

					else{

						paramterValue=0;

					}

					if(obj[el][item]!='Critical'){

						subtotal=parseFloat(subtotal)+parseFloat((obj[el][item]=='N/A'?0:obj[el][item]));

						parmeterTotal=parmeterTotal+parseFloat(paramterValue);

					}

					else{

						subtotal=0;

						parmeterTotal=parmeterTotal+parseFloat(paramterValue);

						break;

					}

				}

			}

			total=parseFloat(subtotal)+parseFloat(total);

				// total +=sum(obj[el])

			}

		}

		jQuery('#scroable').text(parmeterTotal)

		jQuery('#wfatal').text(total)

		jQuery('#wnfatal').text(total)

		var wfatalper=(total!=0)?(total/parmeterTotal)*100:0;

		// var wnfatalper=(total!=0)?(total/total)*100:0;

		jQuery('#wfatalper').text(wfatalper.toFixed(2)+'%')

		// jQuery('#wnfatalper').text(wnfatalper+'%')
        updateGrade(wfatalper);

	}
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

	var total=0;

	function resultFun(value, id,parameterId){

		console.log(value, id,parameterId)

		result[parameterId][id]=value;

		var total=0;

		var parmeterTotal=0;

			for( var el in result[parameterId] ) {

				if( result[parameterId].hasOwnProperty( el ) ) {

					var paramterValue=0;

					if(result[parameterId][el]!='N/A'){

						paramterValue=jQuery('#org'+el).html();

					}

					else{

							paramterValue=0;

						}

					if(result[parameterId][el]!='Critical'){

						total=parseFloat(total)+parseFloat((result[parameterId][el]=='N/A'?0:result[parameterId][el]));

						parmeterTotal=parmeterTotal+parseFloat(paramterValue);

					}

					else{

						total=0;

						parmeterTotal=parmeterTotal+parseFloat(paramterValue);

						break;

					}

				}

			}

			console.log(total)

		jQuery('#scroable'+parameterId).text(parmeterTotal)

		jQuery('#wfatal'+parameterId).text(total)

		// jQuery('#wnfatal'+parameterId).text(total)

		var wfatalper=(total!=0)?(total/parmeterTotal)*100:0;

		// var wnfatalper=(total!=0)?(total/total)*100:0;

		jQuery('#wfatalper'+parameterId).text(wfatalper.toFixed(2)+'%')

		// jQuery('#wnfatalper'+parameterId).text(wnfatalper+'%')

		totalfun(result)

		

	}

	jQuery('.ratingSelect').on('change',function(e){

		var id =jQuery(this).data('id');

		console.log(id)

		var parameterId =jQuery(this).data('parameterid');



		var value=parseInt(jQuery('#org'+id).html());

		var finalValue=value*(e.target.value/100);

		console.log(finalValue,value)

		jQuery('#'+id).val('rating')

		// jQuery('#ratingSelect'+id).hide();

		// 	jQuery('#'+id).show()

		resultFun(finalValue, id,parameterId)

	});

	jQuery('.0bervation').on('change', function(e){

    var id = jQuery(this).data('id');
    var parameterId = jQuery(this).data('parameterid');
    var type = jQuery(this).find(':selected').data('type');
    var observation = jQuery(this).find(':selected').text().trim();

    let baseScore = parseFloat(jQuery('#org'+id).text()) || 0;

    let errorCountElement = jQuery('#errorCount'+id);

    // ✅ Check if error count dropdown actually exists (NOT hidden input)
    let isErrorCountDropdown = errorCountElement.is('select');

    // Enable only if dropdown exists
    if(observation === 'Unsatisfactory' && isErrorCountDropdown){
        errorCountElement.prop('disabled', false);
    } else {
        errorCountElement.val(0).prop('disabled', true);
    }

    if(type=='rating'){
        jQuery('#ratingSelect'+id).show();
        jQuery('#'+id).hide();
        jQuery('#'+id).val(e.target.value);
        jQuery('#ratingSelect'+id).attr('data-id',id);
        jQuery('#ratingSelect'+id).attr('data-parameterid',parameterId);
    }
    else{

        jQuery('#ratingSelect'+id).hide();
        jQuery('#'+id).show();

        if(observation === 'Critical'){
            jQuery('#'+id).val(0);
            resultFun(0,id,parameterId);
            return;
        }

        if(observation === 'N/A'){
            jQuery('#'+id).val('N/A');
            resultFun('N/A',id,parameterId);
            return;
        }

        // ✅ MAIN FIX HERE
        if(observation === 'Unsatisfactory'){

            // ❌ If NO error count dropdown → FORCE ZERO
            if(!isErrorCountDropdown){
                jQuery('#'+id).val(0);
                resultFun(0,id,parameterId);
                return;
            }

            // ✅ If dropdown exists → apply deduction
            let errorCount = parseInt(errorCountElement.val()) || 0;

            let deduction = 0;
            if(errorCount == 1) deduction = 2.5;
            else if(errorCount == 2) deduction = 5;
            else if(errorCount >= 3) deduction = 10;

            let finalScore = baseScore - deduction;
            if(finalScore < 0) finalScore = 0;

            jQuery('#'+id).val(finalScore);
            resultFun(finalScore,id,parameterId);

        }else{

            jQuery('#'+id).val(baseScore);
            resultFun(baseScore,id,parameterId);

        }

    }

});
    jQuery(document).on('change', '.error-count-select', function(){

    let id = jQuery(this).data('id');
    let parameterId = jQuery('#obs'+id).data('parameterid');

    let observation = jQuery('#obs'+id+' option:selected').text().trim();

    if(observation !== 'Unsatisfactory') return;

    let baseScore = parseFloat(jQuery('#org'+id).text()) || 0;
    let errorCount = parseInt(jQuery(this).val()) || 0;

    let deduction = 0;

    if(errorCount == 1) deduction = 2.5;
    else if(errorCount == 2) deduction = 5;
    else if(errorCount >= 3) deduction = 10;

    let finalScore = baseScore - deduction;

    if(finalScore < 0) finalScore = 0;

    jQuery('#'+id).val(finalScore);

    resultFun(finalScore,id,parameterId);

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
        }
    }


    // Validate observations and remarks
    if (validateObservationsAndRemarks()) {
        if (confirm("Are you sure to submit the audit? After submitting, you won't be able to edit it.")) {

            // Call the function to send OTP
            if (allocatedModules.includes(1)) {
                sendOTP();
            } else {
                submitDataFun('submit');
            }


            // // Disable the buttons temporarily
            // jQuery(".submit").prop('disabled', true);
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
        }
    }


    // Validate observations and remarks
    if (validateObservationsAndRemarks()) {
        if (confirm("Are you sure to save the audit?")) {
            // Call save function here
            submitDataFun('save');

            // Disable the buttons temporarily
            // jQuery(".submit").prop('disabled', true);
            // jQuery(".savebutton").prop('disabled', true);
        }
    }
});

	jQuery(document).ready(function() {
        var type = "{{$data->type}}";

        if (type === 'agency') {  // Ensure it only runs for 'agency'
            var agencyId = "{{$result->agency_id}}";
            var productId = "{{$result->product_id}}";

            // Call getProduct to populate the product dropdown for the agency
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

// Product selection change handler, only for agency
jQuery('#productSelect').on('change', function(e) {
    var agencyId = "{{$result->agency_id}}";
    var type = 'agency';
    var productId = e.target.value;

    if (agencyId && productId) {
        editBranch(agencyId, productId, type);
    } else {
        console.log("Missing required parameters: agencyId or productId.");
    }
});
function editBranch(id,product_id,type){
		
	
        // var auditid='null';
         var auditid=document.getElementById("auditid").value;
   
           var saveData = jQuery.ajax({
               
   
               type: 'get',
   
               url: "{{url('get_branch_detail_qc')}}/"+id+'/'+type+'/'+auditid+'/'+product_id,
               //url: "http://rbl.qdegrees.com/get_branch_detail_qc/"+id+'/'+type+'/'+product_id,
               //url: "{{URL::to('get_branch_detail_qc')}}/"+id+'/'+type+'/'+product_id,
               
   
               dataType: "text",
   
               success: function(resultData) { 
   
                   console.log(resultData)
   
                   jQuery('#data').html(resultData)
   
                   jQuery('#collection_manager-select').val("{{$result->collection_manager_id}}")
   
                   var code =jQuery('#collection_manager-select').find(':selected').data('code');
   
                   var bucket =jQuery('#collection_manager-select').find(':selected').data('bucket');
   
                   jQuery('input[name=Collection_Manager_bucket]').val(bucket)
   
                   jQuery('input[name=Collection_Managercode]').val(code)
   
                   
   
                   @if($result->collection_manager_email != '')
   
                       var html='<div>\
   
                           <div>\
   
                           {{$result->collectionuser->name}} ({{$result->collection_manager_email}}) change by {{$result->qa_qtl_detail->name}}\
   
                           </div>\
   
                           <div>\
   
                               <button class="btn btn-sm btn-success" onclick="SaveData(`{{$result->collection_manager_email}}`,{{$result->id}},`collection`)">Accept</button>\
   
                               <button class="btn btn-sm btn-danger" onclick="RejectData(`{{$result->collection_manager_email}}`,{{$result->id}},`collection`)">Reject</button>\
   
                           </div>\
   
                       </div>'
   
                       jQuery('#error').show();
   
                       jQuery('.error').show();
   
                       jQuery('#error').html(html)
   
                   @endif
   
                   @if($result->agency_manager_email != '')
   
                   var htmla='<div>\
   
                           <div>\
   
                               {{$result->agencyuser->name}} ({{($result->agency_manager_email)}}) change by {{$result->qa_qtl_detail->name}}\
   
                           </div>\
   
                           <div>\
   
                               <button class="btn btn-sm btn-success"  onclick="SaveData(`{{$result->agency_manager_email}}`,{{$result->id}},`agency`);">Accept</button>\
   
                               <button class="btn btn-sm btn-danger" onclick="RejectData(`{{$result->agency_manager_email}}`,{{$result->id}},`agency`);">Reject</button>\
   
                           </div>\
   
                       </div>'
   
                       jQuery('#agency_error').show();
   
                       jQuery('.agency_error').show();
   
                       jQuery('#agency_error').html(htmla)
   
                   @endif
   
                   @if($result->yard_manager_email != '')
   
                   var htmlb='<div>\
   
                           <div>\
   
                               {{$result->yarduser->name}} ({{($result->yard_manager_email)}}) change by {{$result->qa_qtl_detail->name}}\
   
                           </div>\
   
                           <div>\
   
                               <button class="btn btn-sm btn-success"  onclick="SaveData(`{{$result->yard_manager_email}}`,{{$result->id}},`yard`)">Accept</button>\
   
                               <button class="btn btn-sm btn-danger" onclick="RejectData(`{{$result->yard_manager_email}}`,{{$result->id}},`yard`)">Reject</button>\
   
                           </div>\
   
                       </div>'
   
                       jQuery('#yard_error').show();
   
                       jQuery('.yard_error').show();
   
                       jQuery('#yard_error').html(htmlb)
   
                   @endif
   
               }
   
           });
   
           saveData.error(function() { alert("Something went wrong"); });
   
       }
   

	jQuery('.alertModal').on('click',function(e){

		var subparameterId =jQuery(this).data('id')

		var parameterId =jQuery(this).data('parameterid')

		jQuery('#alertParameterId').val(parameterId)

		jQuery('#alertSubParameterId').val(subparameterId)

		console.log(parameterId)

		jQuery('#exampleModal').modal('show');

	})

	jQuery('.artifactModal').on('click',function(e){

		var subparameterId =jQuery(this).data('id')

		var parameterId =jQuery(this).data('parameterid')

		jQuery('#artifactParameterId').val(parameterId)

		jQuery('#artifactSubParameterId').val(subparameterId)

		jQuery('#moreArtifact').empty()

		jQuery('#artifactModal').modal('show');

	})

	jQuery('#saveAlert').on('click',function(e){

		var parid=jQuery('#alertParameterId').val()

		var subid=jQuery('#alertSubParameterId').val()

		var msg=jQuery('#msg').val()

		var lob=jQuery('#alertlob').val()

		var type=jQuery('#alerttype').val()

		var typeid=jQuery('#alerttypeid').val()

		var sheetID="{{$data->id}}"

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

			redalertData[subid]={'id':subid

			,'parameter_id':parid,'sheet_id':sheetID,'msg':msg,'lob':lob,'type':type,'typeid':typeid

			}

            for (var i = 0; i < files.length; i++) {

                // data.append('file', files[i]);

				redalertData[subid]['file']=files[i]

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

    jQuery('#artifactAlert').on('click', function (e) {
    e.preventDefault(); // Prevent default behavior

    var parid = jQuery('#artifactParameterId').val();
    var subid = jQuery('#artifactSubParameterId').val();
    var msg = jQuery('#msg').val();
    var sheetID = "{{$data->id}}";

    // Reset the input fields
    jQuery('#artifactParameterId').val('');
    jQuery('#artifactSubParameterId').val('');
    jQuery('#msg').val('');

    var fileInputs = jQuery(".file").get();
    console.log(fileInputs);

    var allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'pdf'];
    var valid = true;
    var totalFiles = 0;
    var allFiles = [];

    // Collect all files from all file inputs
    for (var i = 0; i < fileInputs.length; i++) {
        if (fileInputs[i].files.length > 0) {
            // Handle multiple files from each input
            for (var j = 0; j < fileInputs[i].files.length; j++) {
                var file = fileInputs[i].files[j];
                var fileName = file.name;
                var fileExtension = fileName.split('.').pop().toLowerCase();

                // Validate the file extension
                if (!allowedExtensions.includes(fileExtension)) {
                    alert(`Invalid file type: ${fileName}. Please upload an image or PDF file.`);
                    valid = false;
                    break;
                }

                allFiles.push(file);
                totalFiles++;
            }

            if (!valid) {
                break;
            }
        }
    }

    // If validation fails, stop the process
    if (!valid) {
        return;
    }

    // If no files selected
    if (totalFiles === 0) {
        alert('Please select at least one file to upload.');
        return;
    }

    var data = new FormData();
    data.append('id', subid);
    data.append('audit_id', "{{$result->id}}");
    data.append('parameter_id', parid);
    data.append('sheet_id', sheetID);
    data.append('_token', "{{ csrf_token() }}");
    data.append('totalFile', totalFiles);

    // Append all collected files to FormData
    for (var i = 0; i < allFiles.length; i++) {
        data.append('file' + i, allFiles[i]);
    }

    var saveData = jQuery.ajax({
        xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    percentComplete = parseInt(percentComplete * 100);
                    jQuery('#ProgressContaint').html(percentComplete + '% Complete');
                    if (percentComplete === 100) {
                        jQuery('#ProgressContaint').html('Upload Complete - Processing...');
                    }
                }
            }, false);
            return xhr;
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
            jQuery('#ProgressContaint').html('');
        },
        error: function (xhr, status, error) {
            console.error('Upload error:', error);
            alert("Something went wrong during upload. Please try again.");
            jQuery('#ProgressContaint').html('');
        }
    });
});

	function ImgPreview(input, placeToInsertImagePreview) {

		// if (input) {

			var filesAmount = input.length;

			var image=''

			input.map(function(item){

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

			})

			jQuery(placeToInsertImagePreview).append(image)	

		// }

}

	function submitDataFun(type){

	    var submitData=[];

		var parameters={}

		var sub={}

		var alertData=redalertData;

		console.log(result)

		for( var el in result ) {

			if( result.hasOwnProperty( el ) ) {

				for( var row in result[el] ) {

					if( result[el].hasOwnProperty( row ) ) {
					    
					    var ck=0;
                        if(jQuery('#ackalert'+row).prop('checked') == true){
                             ck=1;
                        }

						sub[row]={

							'id':subpar[row],

							'remark':jQuery('#remark'+row).val(),

							'orignal_weight':jQuery('#org'+row).text(),

							'temp_weight':result[el][row],

							'score':jQuery('#'+row).val(),

							'is_percentage':(jQuery('#'+row).val()=='rating')?1:0,

							'selected_per':jQuery('#ratingSelect'+row).val(),
                            'ackalert':ck,
							'option':jQuery('#obs'+row+' option:selected').text(),

                            'error_count': jQuery('#errorCount'+row).val(),

						}

					}

				}

				

				parameters[el]={

				'id':par[el],

				'score':jQuery('#scroable'+el).text(),

				'score_with_fatal':jQuery('#wfatal'+el).text(),

				'score_without_fatal':jQuery('#wnfatal'+el).text(),

				'temp_total_weightage':jQuery('#scroable').text(),

				'parameter_weight':jQuery('#total'+el).text(),

				'subs':sub

			}

			sub={}

			}

		}
        

		submitData.push({

			'id':"{{$result->id}}",

			'qm_sheet_id':"{{$data->id}}",

			// 'overall_score':jQuery('#scroable').text(),

			'overall_score':jQuery('#wfatal').text(),

			'with_fatal_score_per':jQuery('#wfatalper').text(),
            'grade': jQuery('#grade').text(),

			'branch_id':jQuery('.branch').val(),

			'agency_id':jQuery('.agency').val(),

			'yard_id':jQuery('.yard').val(),

			'branch_repo_id':jQuery('.branch_repo').val(),

			'agency_repo_id':jQuery('.agency_repo').val(),

			'product_id':jQuery('.product').val(),

			'collection_manager_email':jQuery('input[name=Collection_Manager_email]').val(),

			'agency_manager_email':jQuery('input[name=agency_manager_email]').val(),

			'yard_manager_email':jQuery('input[name=yard_manager_email]').val(),

			
			'collection_manager_id':jQuery('#collection_manager-select').val(),

            'agency_manager': jQuery('input[name=agency_manager]').val(),
            'agency_phone': jQuery('select[name=agency_phone]').val(),
            'agency_email': jQuery('select[name=agency_email]').val(),
            'lavel_4': jQuery('#collection_manager-select').val(),
            'lavel_4': jQuery('#lavel_4').val(),
            'lavel_5': jQuery('#lavel_5').val(),
            'present_auditor': jQuery('#present_auditor').val(),
            'audit_sheet_type': jQuery('#sheet_select').val(),

			'status':type
		})

		var ids=[];

		var saveData = jQuery.ajax({

			type: 'POST',

			url: "{{url('allocation/update_audit')}}",

			data: {'submission_data':submitData,'parameters':parameters,

			"_token":"{{ csrf_token() }}"

			},

			dataType: "text",

			success: function(result) { 

				

				console.log(result)

				var data = new FormData();

				if(jQuery.isEmptyObject(alertData)==false){

				for( var el in alertData ) {

					if( alertData.hasOwnProperty( el ) ) {

							data.append('id'+el, alertData[el].id);

							data.append('parameter_id'+el,  alertData[el].parameter_id);

							data.append('sheet_id'+el,  alertData[el].sheet_id);

							data.append('msg'+el,  alertData[el].msg);

							data.append('lob'+el,  alertData[el].lob);

							data.append('file'+el,  alertData[el].file);

							data.append('_token', "{{ csrf_token() }}");

							data.append('type',  alertData[el].type);

							data.append('typeid',  alertData[el].typeid);

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

					processData: false,

					contentType: false,

					success: function(resultData) { 

						console.log(resultData)

						// 						window.location = '{{ url("auditor_list/1")}}'

					}

				});

				saveAlert.error(function() { alert("Something went wrong");console.log('red-alert-error'); });

			}

				if(type == 'submit') {
                    window.location = '{{ url("submit_audited_list")}}';
                } else {
                    window.location = '{{ url("save_audited_list")}}'
                }

			}

		});

		saveData.error(function() { alert("Something went wrong");console.log('audit save-error'); });

		console.log(parameters)

}

function remarkIsFilled(result){

	var remark=true;

	for( var el in result ) {

		if( result.hasOwnProperty( el ) ) {

			if(jQuery.isEmptyObject(result[el])){

					remark=false;

			}

			for( var row in result[el] ) {

				if( result[el].hasOwnProperty( row ) ) {

					var value=jQuery('#remark'+row).val();

					if(value.trim().length==0){

						remark=false;

						break;

					}

				}

			}

		}

	}

	return remark;

}
  //sendotp work
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
                        sub[row] = {
                            'remark': jQuery('#remark' + row).val(),
                            'orignal_weight': jQuery('#org' + row).text(),
                            'temp_weight': result[el][row],
                            'score': jQuery('#' + row).val(),
                            'is_percentage': (jQuery('#' + row).val() == 'rating') ? 1 : 0,
                            'selected_per': jQuery('#ratingSelect' + row).val(),
                            'ackalert': ck,
                            'option': jQuery('#obs' + row + ' option:selected').text(),
                            'error_count': jQuery('#errorCount'+row).val(),
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
            'agency_manager': jQuery('input[name=agency_manager]').val(),
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
                                'error_count': jQuery('#errorCount'+row).val(),
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
                var otpData = {
                    'product_id': product_id,
                    'agency_email': agencyMail,
                    'temp_total_weightage': jQuery('#scroable').text(),
                    'overall_score': jQuery('#wfatal').text(),
                    'agency_id': audit_for,
                    'parameters': parameters,
                    'audit_cycle': audit_cycle,
                    '_token': "{{ csrf_token() }}",
                    'lavel_3': level3,
                    'present_auditor': present_auditor,
                    'lavel_4': level4Values,
                    'lavel_5': level5Values,
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
                                'error_count': jQuery('#errorCount'+row).val(),
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
            var otpData = {
                'manager_id': collectionManagerSelected,
                'product_id': product_id,
                'agency_email': agencyMail,
                'agency_id': audit_for,
                'parameters': parameters,
                'audit_cycle': audit_cycle,
                '_token': "{{ csrf_token() }}",
                'type': "collection_manager"
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
    jQuery('#intimationUsersContainer').on('mousedown', function(event) {
        event.preventDefault(); // Prevents the selection from changing
    });
    jQuery('#collection_manager').on('mousedown', function(event) {
        event.preventDefault(); // Prevents the selection from changing
    });
    
    jQuery('#audit_cycle').on('mousedown', function(event) {
        event.preventDefault(); // Prevents the selection from changing
    });
    jQuery('#audit_for').on('mousedown', function(event) {
        event.preventDefault(); // Prevents the selection from changing
    });

    jQuery('select[name="agency"], select[name="level_4[]"], select[name="level_5[]"], select[name="product"], select[name="sub_product[]"]').each(function() {
    if (!$(this).attr('id')) { // Check if id is not present
        $(this).on('mousedown', function(event) {
            event.preventDefault(); // Prevent default behavior
        });
        $(this).css({
            'pointer-events': 'none',
            'cursor': 'not-allowed'
        });
    }
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



<script>
  // Disable the select element when editing
  const selectElement = document.getElementById('audit_for');
  const selectedValue = selectElement.value;

  // Check if the selected value matches the agency ID, and then disable the select
  if (selectedValue) {
    selectElement.disabled = true; // Disable the entire select to prevent changes
  }
</script>

<script>
     if (allocatedModules.includes(29)){
    jQuery.ajax({

    type:'POST',

    url:"{{ url('/audit/get-closure-artifact-data') }}",

    data:{
        agency_id: jQuery('#audit_for').val(),
        _token:"{{ csrf_token() }}"
    },

    success:function(res){

        closureArtifactData = {};

        jQuery('.closureArtifactBtn').hide();

        res.forEach(function(item){

            closureArtifactData[item.sub_parameter_id] = item;

            jQuery(
                '.closureArtifactBtn[data-subparameterid="' +
                item.sub_parameter_id +
                '"]'
            ).show();

        });

    }

});
jQuery(document).on('click','.closureArtifactBtn',function(){

    let subParameterId = jQuery(this).data('subparameterid');

    let data = closureArtifactData[subParameterId];

    if(!data){
        return;
    }

    let artifactLink = '';

    if(data.artifact){
        artifactLink =
        '<a href="'+data.artifact+'" target="_blank">View Artifact</a>';
    }

    let html = `
        <table class="table table-bordered">

            <tr>
                <th>Justification</th>
                <td>${data.justification ?? ''}</td>
            </tr>

            <tr>
                <th>Action Taken</th>
                <td>${data.action_taken ?? ''}</td>
            </tr>

            <tr>
                <th>Approval Status</th>
                <td>${data.approval_status ?? ''}</td>
            </tr>

            <tr>
                <th>Artifact</th>
                <td>${artifactLink}</td>
            </tr>

        </table>
    `;

    jQuery('#closureModalBody').html(html);

    jQuery('#closureModal').modal('show');

});}
</script>

@endsection
