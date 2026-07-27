@extends('layouts.master')
@section('css')

{{-- <link rel="stylesheet" href="{{URL::asset('base/style.bundle.css')}}"> --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" ; rel="stylesheet" />
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css" />
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<style>
.sp-row .row { margin-bottom: 15px; }
.flex-container { display: flex; align-items: center; }
.kt-font-bolder { font-weight: 600 !important; }
#seprator { margin: 2.5rem 0 0 0; }
.kt-separator.kt-separator--space-lg { margin: 2.5rem 0; }
.kt-separator.kt-separator--border-dashed { border-bottom: 1px dashed #ebedf2; }
.kt-separator { height: 0; margin: 20px 0; border-bottom: 1px solid #ebedf2; }
.kt-font-primary { color: #5867dd !important; }
.kt-font-bold { font-weight: 500 !important; }
.centerparameter { display: flex; justify-content: center; align-items: center }

/* Modal Background */
.modal-backdrop { background-color: rgba(0, 0, 0, 0.7); }
.modal-content { border-radius: 10px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); }
.modal-header { background-color: #007bff; color: white; border-bottom: none; }
.modal-title { font-size: 1.5rem; font-weight: bold; }
.modal-body { padding: 20px; }
.form-control { border-radius: 5px; border: 1px solid #ced4da; }
.btn { border-radius: 5px; }
.btn-secondary { background-color: #6c757d; border: none; }
.btn-secondary:hover { background-color: #5a6268; }
.btn-primary { background-color: #007bff; border: none; }
.btn-primary:hover { background-color: #0056b3; }
#resendOtpButton { margin-top: 10px; }

#loader {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background-color: rgba(255, 255, 255, 0.8); z-index: 9999;
    display: flex; justify-content: center; align-items: center;
}
#loader p { font-size: 24px; color: #333; font-weight: bold; }

/* Error count */
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
/* Category Scorecard Styles */
.category-scorecard-table {
    width: 100%;
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
.tatalScored{
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
$allocatedmodule = App\Helpers\Helper::allocatedmodulelist();

// Build category metadata for JS
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
?>

<div class="row">
    <div class="col-lg-12" style="margin-top:10x"></div>
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
                    <strong class="card-title">{{($data->lob=='commercial_vehicle')?'Commercial Vehicle':ucfirst($data->lob)}}
                        | {{ucfirst(str_replace('_',' ',$data->type))}}</strong>
                </div>

                <div class="card-body">
                    <div class="row">
                        <input type="hidden" value="{{$result->id}}" id="auditid" name="auditData">

                        @if($data->type=='agency')
                        <div class="col-md-3 form-group">
                            <label>Agency*</label>
                            <select name="agency" id="audit_for" class="form-control agency readonly">
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
                                <option value="{{ $item->id }}" {{ $item->id == $result->audit_cycle_id ? 'selected' : '' }}>
                                    {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 form-group">
                            <label>Audit Date*</label>
                            <input type="date" name="audit_date" value="{{$result->audit_date_by_aud}}"
                                class="form-control audit_date" id="audit_date" required="true" disabled>
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

                    <div class="row" id="data"></div>

                    <div class="row">
                        @if(in_array(13, $allocatedmodule))
                        <div class="col-md-4 form-group" id="collection_manager">
                            <label>Level 3</label>
                            {!! Form::select('lavel_3', $formattedUsers, $result->lavel_3 ?? '', ['id'=>'collection_manager-select', 'class' => 'form-control js-example-basic-single', 'readonly'=>'readonly']) !!}
                        </div>
                        @endif

                        <div class="col-md-4 form-group">
                            <label>Present Auditor</label>
                            {!! Form::text('present_auditor', $result->present_auditor ?? '', ['id'=>'present_auditor', 'class' => 'form-control js-example-basic-single', 'readonly'=>'readonly']) !!}
                        </div>

                        @if(auth()->user()->client_id == 15)
                        <div class="col-md-3 form-group" id="sheet">
                            <label for="sheet_select">Nature of Service<span class="text-danger">*</span></label>
                            <select name="sheet" id="sheet_select" class="form-control sheet" required>
                                <option value="">-- Select Nature of Service --</option>
                                <option value="field" {{ $result->audit_sheet_type == 'field' ? 'selected' : '' }}>Field</option>
                                <option value="telecalling" {{ $result->audit_sheet_type == 'telecalling' ? 'selected' : '' }}>Telecalling</option>
                                <option value="field_telecalling" {{ $result->audit_sheet_type == 'field_telecalling' ? 'selected' : '' }}>Field + Telecalling</option>
                            </select>
                        </div>
                        @endif
                    </div>

                    <div id="intimationUsersContainer">
                        @if(in_array(13, $allocatedmodule))
                        @php
                            $level4Values = explode(',', $result->lavel_4);
                            $level5Values = explode(',', $result->lavel_5);
                        @endphp
                        @foreach ($level4Values as $index => $level4)
                        <div class="row intimationUser">
                            <div class="col-md-3 form-group">
                                <label>Level 4</label>
                                {!! Form::select('level_4[]', $formattedUsers, $level4, ['class' => 'form-control select2', 'id'=>"level_4_{$index}", 'readonly' => 'readonly']) !!}
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Level 5</label>
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
                    <input type="hidden" name="virtual_audit" value="{{ $result->virtual_audit }}">
                </div>
                @endif
            </div>

            {{-- Audit Parameters Card --}}
            <div class="card">
                <div class="card-header">
                    <strong class="card-title">Audit</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 kt-font-bolder">Parameter</div>
                        <div class="col-md-10 kt-font-bolder">
                            <div class="row">
                                <div class="col-md-2 kt-font-bolder">Sub Parameter</div>
                                <div class="col-md-2 kt-font-bolder">Observation</div>
                                <div class="col-md-2 kt-font-bolder">Scored</div>
                                <div class="col-md-2 kt-font-bolder">Error Count</div>
                                <div class="col-md-2 kt-font-bolder">Remarks</div>
                                <div class="col-md-2 kt-font-bolder">Action</div>
                            </div>
                        </div>
                    </div>

                    <div id="seprator" class="kt-separator kt-separator--border-dashed kt-separator--space-lg"></div>

                    @php $total = 0; @endphp

                    <div id="accordion">
                        @foreach ($data->parameter as $item)
                        <div class="card">
                            <div class="card-header" id="heading{{$item->id}}" data-toggle="collapse"
                                data-target="#collapse{{$item->id}}" aria-controls="collapse{{$item->id}}" aria-expanded="false">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" data-toggle="collapse"
                                        data-target="#collapse{{$item->id}}" aria-controls="collapse{{$item->id}}" aria-expanded="false">
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
                                id="collapse{{$item->id}}" aria-labelledby="heading{{$item->id}}" data-parent="#accordion">

                                <div class="col-md-2 kt-font-bolder kt-font-primary flex-item centerparameter">
                                    {{$item->parameter}}
                                </div>

                                <div class="col-md-10 sp-row">
                                    @foreach ($item->qm_sheet_sub_parameter as $value)

                                    @php
                                        $errorScoring = [];
                                        if (!empty($value->error_scoring)) {
                                            $decoded = json_decode($value->error_scoring, true);
                                            if (is_array($decoded)) {
                                                $errorScoring = $decoded;
                                            }
                                        }
                                        $savedErrorCount = isset($resultSubPar[$value->id]) ? $resultSubPar[$value->id]->error_count : 0;
                                    @endphp

                                    <div class="row flex-container mb-2">

                                        <div class="col-md-2 kt-font-bold">
                                            {{$value->sub_parameter}}
                                            <i title="More info" class="la la-info-circle kt-font-warning sp-details-top"></i>
                                        </div>

                                        <div class="col-md-2">
                                            <select class="form-control 0bervation" id="obs{{$value->id}}"
                                                data-id="{{$value->id}}" data-parameterId="{{$item->id}}"
                                                data-point="{{$value->weight}}">

                                                <option value="">Choose type</option>

                                                @if(isset($resultSubPar[$value->id]) && $resultSubPar[$value->id]->option_selected == null)
                                                    @if($value->pass==1)<option value="{{$value->weight}}" {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->selected_option==$value->weight))?'selected':''}}>Satisfactory</option>@endif
                                                    @if($value->fail==1)<option value="0" {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_critical==0 && $resultSubPar[$value->id]->selected_option==0))?'selected':''}}>Unsatisfactory</option>@endif
                                                @else
                                                    @if($value->pass==1)<option value="{{$value->weight}}" {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->option_selected=='Satisfactory'))?'selected':''}}>Satisfactory</option>@endif
                                                    @if($value->fail==1)<option value="0" {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_critical==0 && $resultSubPar[$value->id]->option_selected=='Unsatisfactory'))?'selected':''}}>Unsatisfactory</option>@endif
                                                @endif

                                                @if($value->critical==1)<option value="Critical" {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_critical==1))?'selected':''}}>Critical</option>@endif
                                                @if($value->na==1)<option value="N/A" {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_critical==0 && $resultSubPar[$value->id]->selected_option=='N/A'))?'selected':''}}>N/A</option>@endif
                                                @if($value->pwd==1)<option value="{{round(($value->weight)/2,2)}}" {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->selected_option==round(($value->weight)/2,2)))?'selected':''}}>PWD</option>@endif
                                                @if($value->per==1)<option value="{{round(($value->weight))}}" data-type="rating" {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_percentage==1))?'selected':''}}>Percentage</option>@endif
                                            </select>

                                            <span style="display:none" id="org{{$value->id}}">{{$value->weight}}</span>
                                        </div>

                                        <div class="col-md-2">
                                            <select class="form-control ratingSelect" name="ratingSelect"
                                                id="ratingSelect{{$value->id}}"
                                                style="{{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_percentage!=1))?'display:none':'display:block'}}"
                                                data-id="{{$value->id}}" data-parameterId="{{$item->id}}">
                                                <option>select percentage</option>
                                                @for($counting=0; $counting<=100; $counting=$counting+5)
                                                <option value="{{$counting}}" {{(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->selected_per==$counting))?'selected':''}}>{{$counting}}%</option>
                                                @endfor
                                            </select>

                                            @if(isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_percentage!=1))
                                            <input type="text" id="{{$value->id}}" readonly="readonly" class="form-control"
                                                value="{{ (isset($resultSubPar[$value->id]) && ($resultSubPar[$value->id]->is_critical==1))?'Critical':($resultSubPar[$value->id]->score ?? '')}}"
                                                style="display: none">
                                            @else
                                            <input type="text" id="{{$value->id}}" readonly="readonly" class="form-control"
                                                value="rating" style="display: none;">
                                            @endif
                                        </div>

                                        @if(in_array(22, $allocatedmodule) && $value->use_error_count == 1)
                                        <div class="col-md-2">
                                            <label class="error-count-label">Error Count</label>
                                            <select class="form-control error-count-select"
                                                    id="errorCount{{$value->id}}"
                                                    name="error_count[{{$value->id}}]"
                                                    data-id="{{$value->id}}"
                                                    data-base-score="{{$value->weight}}"
                                                    disabled>

                                                <option value="0"
                                                        data-score="{{$value->weight}}"
                                                        {{ $savedErrorCount == 0 ? 'selected' : '' }}>
                                                    0 errors (Score: {{$value->weight}})
                                                </option>

                                                @foreach ($errorScoring as $rule)
                                                <option value="{{ $rule['error_count'] }}"
                                                        data-score="{{ $rule['score'] }}"
                                                        {{ $savedErrorCount == $rule['error_count'] ? 'selected' : '' }}>
                                                    {{ $rule['error_count'] }} error{{ (int)$rule['error_count'] > 1 ? 's' : '' }} (Score: {{ $rule['score'] }})
                                                </option>
                                                @endforeach

                                            </select>
                                        </div>
                                        @else
                                        <input type="hidden"
                                               id="errorCount{{$value->id}}"
                                               name="error_count[{{$value->id}}]"
                                               value="0"
                                               data-id="{{$value->id}}">
                                        @endif

                                        <div class="col-md-2"></div>

                                        <div class="col-md-2">
                                            <button class="btn btn-info btn-sm artifactModal mr-1"
                                                data-parameterid="{{$item->id}}" data-id="{{$value->id}}">Artifact</button>
                                        </div>

                                    </div>

                                    <div class="col-md-12 row">
                                        <div class="col-md-10">
                                            <textarea class="form-control" id="remark{{$value->id}}"
                                                value="{{ $resultSubPar[$value->id]->remark ?? ''}}">{{ $resultSubPar[$value->id]->remark ?? ''}}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12 row">
                                        <div class="col-md-10 preview{{$value->id}}">
                                            @foreach($value->artifact as $art)
                                            @php $extension = strtolower(pathinfo($art->file, PATHINFO_EXTENSION)); @endphp
                                            @if(in_array($art->id, $artifactIds))
                                            <div class="img-wrap art{{$art->id}}" style="position: relative;display: inline-block;font-size: 0;">
                                                <span class="close">&times;</span>
                                                <a href="{{ URL::asset('storage/app/'.$art->file) }}" target="_blank">
                                                    @if($extension === 'pdf')
                                                    <img src="{{ asset('public/images/pdf-icon.png') }}" data-id="{{ $art->id }}" style="width: 100px; height: 100px;">
                                                    @else
                                                    <img src="{{ URL::asset('storage/app/'.$art->file) }}" data-id="{{ $art->id }}" style="width: 100px; height: 100px;">
                                                    @endif
                                                </a>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2 voice-btn"
                                            data-id="{{ $value->id }}">🎤 Speak Remark</button>
                                    </div>

                                    <div id="seprator" class="kt-separator kt-separator--border-dashed"></div>

                                    @php $total = $total + $value->weight; @endphp
                                    @endforeach

                                    <span style="display:none" id="total{{$item->id}}">{{$total}}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Result Card --}}
            <div class="card">
                <div class="card-header">
                    <strong class="card-title">Result</strong>
                    <h5 class="kt-font-bolder kt-font-primary">Parameter Wise Score</h5>
                </div>
                <div class="card-body">
                    {{-- Parameter-wise scorecard (always shown) --}}
                    <div class="row" style="border-bottom: 1px solid rgb(204, 204, 204);">
                        <div class="col-lg-3 kt-font-bolder">Parameter</div>
                        <div class="col-lg-3 kt-font-bolder">Scorable</div>
                        <div class="col-lg-3 kt-font-bolder">Scored</div>
                        <div class="col-lg-2 kt-font-bolder">Scores%</div>
                        <div class="col-lg-1 kt-font-bolder">Grade</div>
                    </div>

                    @foreach ($data->parameter as $item)
                    <div class="row" style="border-bottom: 1px solid rgb(204, 204, 204); padding: 20px 0px; height: 100%;">
                        <div class="col-lg-3 kt-font-bold kt-font-primary">{{$item->parameter}}</div>
                        <div class="col-lg-3" id="scroable{{$item->id}}">0</div>
                        <div class="col-lg-3 kt-font-danger" id="wfatal{{$item->id}}">0</div>
                        <div class="col-lg-2" id="wfatalper{{$item->id}}">0</div>
                    </div>
                    @endforeach

                    {{-- Category Wise Scorecard (only shown if categories exist) --}}
                    

                    {{-- Overall score row (parameter-wise, unchanged) --}}
                    <div class="row" style="padding: 20px 0px; height: 100%;">
                        <div class="col-lg-3 kt-font-bold kt-font-success">Over All (Parameter)</div>
                        <div class="col-lg-3 kt-font-bold" id="scroable">0</div>
                        <div class="col-lg-3 kt-font-bold kt-font-danger" id="wfatal">0</div>
                        <div class="col-lg-2 kt-font-bold" id="wfatalper">0</div>
                        <div class="col-lg-1 kt-font-bold" id="grade"></div>
                    </div>
                </div>
                <div class="card-body">
                    @if($hasCategories)
                    <div class="category-section">
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 class="kt-font-bolder kt-font-primary">Final Score - Category Wise Scorecard</h5>
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
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-sm savebutton">
        <i class="fa fa-dot-circle-o"></i> Save
    </button>
    @if($result->is_qc_approved ==1)
    <button type="submit" class="btn btn-primary btn-sm submit">
        <i class="fa fa-dot-circle-o"></i> Submit
    </button>
    @endif
    <button type="reset" class="btn btn-danger btn-sm">
        <i class="fa fa-ban"></i> Reset
    </button>
</div>

{{-- Alert Modal --}}
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <textarea name="msg" id="msg" class="form-control" placeholder="Enter message" required></textarea>
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

{{-- Artifact Modal --}}
<div class="modal fade" id="artifactModal" tabindex="-1" role="dialog" aria-labelledby="artifactModalLabel" aria-hidden="true">
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
    position: absolute; top: 2px; right: 2px; z-index: 100;
    background-color: #FFF; padding: 5px 2px 2px; color: #000;
    font-weight: bold; cursor: pointer; opacity: .2;
    text-align: center; font-size: 22px; line-height: 10px; border-radius: 50%;
}
.img-wrap:hover .close { opacity: 1; }

</style>
@include('shared.table_css');
@endsection

@section('js')
<script>
    const allocatedModules = @json($allocatedmodule);
    const categoriesMeta = @json($categoriesMeta);
    const subParCategoryMap = @json($subParCategoryMap);
    const subParWeightMap = @json($subParWeightMap);
    const hasCategories = @json($hasCategories);
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) { console.warn("Speech Recognition not supported."); return; }

    document.querySelectorAll('.voice-btn').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const textarea = document.getElementById('remark' + id);
            if (!textarea) return;

            const recognition = new SpeechRecognition();
            recognition.lang = 'en-US';
            recognition.interimResults = false;
            recognition.maxAlternatives = 1;
            recognition.start();
            textarea.placeholder = "🎤 Listening...";

            recognition.onresult = function (event) {
                const transcript = event.results[0][0].transcript.trim();
                const existing = textarea.value.trim();
                let combined = existing ? existing + ' ' + transcript : transcript;
                combined = combined.charAt(0).toUpperCase() + combined.slice(1);
                textarea.value = combined;
            };
            recognition.onerror = function (event) { alert("Speech Recognition Error: " + event.error); };
            recognition.onend = function () { textarea.placeholder = "Enter Remark Here"; };
        });
    });
});
</script>

<script>
jQuery('#collection_manager-select').on('change', function(e) {
    var code = jQuery(this).data('code');
    var bucket = jQuery(this).data('bucket');
    jQuery('input[name=Collection_Manager_bucket]').val(bucket);
    jQuery('input[name=Collection_Managercode]').val(code);
});

jQuery(document).on('change', '.artifact', function(e) {
    if (e.target.files.length > 0) {
        jQuery('#moreArtifact').append('<input type="file" id="file" name="artifactfile[]" class="form-control-file artifact file">');
    }
});

jQuery(document).on('click', '.close', function() {
    var $imgWrap = jQuery(this).closest('.img-wrap');
    var id = $imgWrap.find('img').data('id');
    jQuery.ajax({
        type: 'DELETE',
        url: "{{url('artifact')}}/" + id,
        data: { 'id': id, '_token': '{{ csrf_token() }}' },
        success: function(resultData) { $imgWrap.remove(); jQuery('.art' + id).remove(); },
        error: function() { alert('Failed to delete the artifact. Please try again.'); }
    });
});

var redalertData = {};
var result = {};
var par = {};
var subpar = {};

@php
foreach($data->parameter as $item) { @endphp
    result[{{$item->id}}] = {};
@php }

foreach($resultSubPar as $k => $v) {
    $subValue = ($v->is_critical == 1) ? "Critical" : $v->score; @endphp
    subpar[{{$k}}] = {{$v->id}};
    resultFun('{{$subValue}}', {{$k}}, {{$v->parameter_id}});
@php }

foreach($resultPar as $k => $v) { @endphp
    par[{{$k}}] = {{$v->id}};
@php } @endphp

function resolveErrorCountScore(id) {
    var errorField = jQuery('#errorCount' + id);
    var selectedOption = errorField.find('option:selected');
    var errorCount = parseInt(selectedOption.val()) || 0;
    var scoreVal = parseFloat(selectedOption.data('score'));
    if (errorCount === 0) return 0;
    return isNaN(scoreVal) ? 0 : scoreVal;
}

function sum(obj) {
    var sum = 0;
    for (var el in obj) { if (obj.hasOwnProperty(el)) { sum += parseFloat(obj[el]); } }
    return sum;
}

function totalFun() {
    var total = 0;
    var parmeterTotal = 0;
    for (var el in result) {
        if (result.hasOwnProperty(el)) {
            var subtotal = 0;
            for (var item in result[el]) {
                if (result[el].hasOwnProperty(item)) {
                    var paramterValue = (result[el][item] !== 'N/A') ? jQuery('#org' + item).html() : 0;
                    if (result[el][item] !== 'Critical') {
                        subtotal = parseFloat(subtotal) + parseFloat((result[el][item] === 'N/A' ? 0 : result[el][item]));
                        parmeterTotal = parmeterTotal + parseFloat(paramterValue);
                    } else {
                        subtotal = 0;
                        parmeterTotal = parmeterTotal + parseFloat(paramterValue);
                        break;
                    }
                }
            }
            total = parseFloat(subtotal) + parseFloat(total);
        }
    }
    jQuery('#scroable').text(parmeterTotal);
    jQuery('#wfatal').text(total);
    jQuery('#wnfatal').text(total);
    var wfatalper = (total !== 0) ? (total / parmeterTotal) * 100 : 0;
    jQuery('#wfatalper').text(wfatalper.toFixed(2) + '%');
    updateGrade(wfatalper);
}

function updateGrade(wfatalper) {
    var grade;
    if (wfatalper > 90) grade = 'A';
    else if (wfatalper >= 75) grade = 'B';
    else if (wfatalper >= 61) grade = 'C';
    else grade = 'D';
    jQuery('#grade').text(grade);
}

function resultFun(value, id, parameterId) {
    result[parameterId][id] = value;
    var total = 0;
    var parmeterTotal = 0;

    for (var el in result[parameterId]) {
        if (result[parameterId].hasOwnProperty(el)) {
            var paramterValue = (result[parameterId][el] !== 'N/A') ? jQuery('#org' + el).html() : 0;
            if (result[parameterId][el] !== 'Critical') {
                total = parseFloat(total) + parseFloat((result[parameterId][el] === 'N/A' ? 0 : result[parameterId][el]));
                parmeterTotal = parmeterTotal + parseFloat(paramterValue);
            } else {
                total = 0;
                parmeterTotal = parmeterTotal + parseFloat(paramterValue);
                break;
            }
        }
    }

    jQuery('#scroable' + parameterId).text(parmeterTotal);
    jQuery('#wfatal' + parameterId).text(total);
    var wfatalper = (total !== 0) ? (total / parmeterTotal) * 100 : 0;
    jQuery('#wfatalper' + parameterId).text(wfatalper.toFixed(2) + '%');
    totalFun(); // update parameter-wise overall
    if (hasCategories) {
        updateCategoryScores(); // update category-wise scorecard and its overall
    }
}

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
        html = '<tr><td colspan="4" class="text-center">No categories available</td>';
    }
    jQuery('#categoryScorecardBody').html(html);

    // Update overall row in category scorecard (footer)
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

jQuery('.ratingSelect').on('change', function(e) {
    var id = jQuery(this).data('id');
    var parameterId = jQuery(this).data('parameterid');
    var value = parseInt(jQuery('#org' + id).html());
    var finalValue = value * (e.target.value / 100);
    jQuery('#' + id).val('rating');
    resultFun(finalValue, id, parameterId);
});

jQuery('.0bervation').on('change', function(e) {
    var id = jQuery(this).data('id');
    var parameterId = jQuery(this).data('parameterid');
    var type = jQuery(this).find(':selected').data('type');
    var observation = jQuery(this).find(':selected').text().trim();
    var baseScore = parseFloat(jQuery('#org' + id).text()) || 0;

    var errorField = jQuery('#errorCount' + id);
    if (observation === 'Unsatisfactory') {
        if (errorField.is('select')) {
            errorField.prop('disabled', false);
        }
    } else {
        errorField.val('0').prop('disabled', true);
    }

    if (type === 'rating') {
        jQuery('#ratingSelect' + id).show();
        jQuery('#' + id).hide();
        jQuery('#' + id).val(e.target.value);
        jQuery('#ratingSelect' + id).attr('data-id', id);
        jQuery('#ratingSelect' + id).attr('data-parameterid', parameterId);
    } else {
        jQuery('#ratingSelect' + id).hide();
        jQuery('#' + id).show();

        if (observation === 'Critical') {
            jQuery('#' + id).val(0);
            resultFun(0, id, parameterId);
            return;
        }
        if (observation === 'N/A') {
            jQuery('#' + id).val('N/A');
            resultFun('N/A', id, parameterId);
            return;
        }
        if (observation === 'Unsatisfactory') {
            var errorField = jQuery('#errorCount' + id);
            if (errorField.length === 0 || errorField.is('input[type="hidden"]')) {
                jQuery('#' + id).val(0);
                resultFun(0, id, parameterId);
                return;
            }
            errorField.prop('disabled', false);
            var finalScore = resolveErrorCountScore(id);
            jQuery('#' + id).val(finalScore);
            resultFun(finalScore, id, parameterId);
        } else {
            jQuery('#' + id).val(baseScore);
            resultFun(baseScore, id, parameterId);
        }
    }
});

jQuery(document).on('change', '.error-count-select', function() {
    var id = jQuery(this).data('id');
    var parameterId = jQuery('#obs' + id).data('parameterid');
    var observation = jQuery('#obs' + id + ' option:selected').text().trim();
    if (observation !== 'Unsatisfactory') return;
    var finalScore = resolveErrorCountScore(id);
    jQuery('#' + id).val(finalScore);
    resultFun(finalScore, id, parameterId);
});

function validateObservationsAndRemarks() {
    var observation_rs = true;
    var validRemarks = true;

    jQuery('.0bervation').each(function () {
        var id = jQuery(this).val();
        var txt1 = jQuery(this).children("option").filter(":selected").text();

        if (id == null || typeof(id) == 'undefined' || txt1.trim() == 'Choose type') {
            observation_rs = false;
        }
        if (txt1.trim() === 'Unsatisfactory') {
            var remarkId = jQuery(this).attr('id').replace('obs', 'remark');
            if (jQuery('#' + remarkId).val().trim() === '') {
                validRemarks = false;
            }
        }
    });

    if (!observation_rs) { alert('Please choose an Observation'); return false; }
    if (!validRemarks) { alert('Please fill in remarks for Unsatisfactory observations'); return false; }
    return true;
}

jQuery(".submit").on("click", function (e) {
    var className = jQuery('#audit_for').attr('name');
    if (jQuery('#audit_for').val() == '') { alert('Please select ' + className); return false; }
    if (jQuery('#productSelect').val() == '') { alert('Please select product'); return false; }

    const clientId = {{ auth()->user()->client_id }};
    if (clientId == 15 && jQuery('#sheet_select').val() === '') { alert('Please select nature of sheet.'); return false; }

    if (allocatedModules.includes(13)) {
        if (!jQuery('#collection_manager-select').val()) { alert('Please select collection manager'); return false; }
    }

    if (validateObservationsAndRemarks()) {
        if (confirm("Are you sure to submit the audit? After submitting, you won't be able to edit it.")) {
            if (allocatedModules.includes(1)) { sendOTP(); } else { submitDataFun('submit'); }
        }
    }
});

jQuery(".savebutton").on("click", function (e) {
    var className = jQuery('#audit_for').attr('name');
    if (jQuery('#audit_for').val() == '') { alert('Please select ' + className); return false; }
    if (jQuery('#productSelect').val() == '') { alert('Please select product'); return false; }

    const clientId = {{ auth()->user()->client_id }};
    if (clientId == 15 && jQuery('#sheet_select').val() === '') { alert('Please select nature of sheet.'); return false; }

    if (allocatedModules.includes(13)) {
        if (!jQuery('#collection_manager-select').val()) { alert('Please select collection manager'); return false; }
    }

    if (validateObservationsAndRemarks()) {
        if (confirm("Are you sure to save the audit?")) { submitDataFun('save'); }
    }
});

jQuery(document).ready(function() {
    var type = "{{$data->type}}";
    if (type === 'agency') {
        var agencyId = "{{$result->agency_id}}";
        jQuery.ajax({
            type: 'GET',
            url: "{{ url('getProduct') }}/" + agencyId + '/' + type,
            success: function(response) {
                if (response.data && response.data.product_id) {
                    jQuery('#productSelect').val(response.data.product_id).trigger('change');
                    editBranch(agencyId, response.data.product_id, type);
                }
            },
            error: function() { alert("Something went wrong"); }
        });
    }

    setTimeout(function() {
        totalFun();
        if (hasCategories) {
            updateCategoryScores();
        }
    }, 500);
});

jQuery('#productSelect').on('change', function(e) {
    var agencyId = "{{$result->agency_id}}";
    var productId = e.target.value;
    if (agencyId && productId) { editBranch(agencyId, productId, 'agency'); }
});

function editBranch(id, product_id, type) {
    var auditid = document.getElementById("auditid").value;
    jQuery.ajax({
        type: 'get',
        url: "{{url('get_branch_detail_qc')}}/" + id + '/' + type + '/' + auditid + '/' + product_id,
        dataType: "text",
        success: function(resultData) {
            jQuery('#data').html(resultData);
            jQuery('#collection_manager-select').val("{{$result->collection_manager_id}}");
            var code = jQuery('#collection_manager-select').find(':selected').data('code');
            var bucket = jQuery('#collection_manager-select').find(':selected').data('bucket');
            jQuery('input[name=Collection_Manager_bucket]').val(bucket);
            jQuery('input[name=Collection_Managercode]').val(code);

            @if($result->collection_manager_email != '')
            var html = '<div><div>{{$result->collectionuser->name}} ({{$result->collection_manager_email}}) change by {{$result->qa_qtl_detail->name}}</div><div><button class="btn btn-sm btn-success" onclick="SaveData(`{{$result->collection_manager_email}}`,{{$result->id}},`collection`)">Accept</button><button class="btn btn-sm btn-danger" onclick="RejectData(`{{$result->collection_manager_email}}`,{{$result->id}},`collection`)">Reject</button></div></div>';
            jQuery('#error').show(); jQuery('.error').show(); jQuery('#error').html(html);
            @endif

            @if($result->agency_manager_email != '')
            var htmla = '<div><div>{{$result->agencyuser->name}} ({{$result->agency_manager_email}}) change by {{$result->qa_qtl_detail->name}}</div><div><button class="btn btn-sm btn-success" onclick="SaveData(`{{$result->agency_manager_email}}`,{{$result->id}},`agency`);">Accept</button><button class="btn btn-sm btn-danger" onclick="RejectData(`{{$result->agency_manager_email}}`,{{$result->id}},`agency`);">Reject</button></div></div>';
            jQuery('#agency_error').show(); jQuery('.agency_error').show(); jQuery('#agency_error').html(htmla);
            @endif

            @if($result->yard_manager_email != '')
            var htmlb = '<div><div>{{$result->yarduser->name}} ({{$result->yard_manager_email}}) change by {{$result->qa_qtl_detail->name}}</div><div><button class="btn btn-sm btn-success" onclick="SaveData(`{{$result->yard_manager_email}}`,{{$result->id}},`yard`)">Accept</button><button class="btn btn-sm btn-danger" onclick="RejectData(`{{$result->yard_manager_email}}`,{{$result->id}},`yard`)">Reject</button></div></div>';
            jQuery('#yard_error').show(); jQuery('.yard_error').show(); jQuery('#yard_error').html(htmlb);
            @endif
        }
    });
    jQuery.error = function() { alert("Something went wrong"); };
}

jQuery('.alertModal').on('click', function(e) {
    jQuery('#alertParameterId').val(jQuery(this).data('parameterid'));
    jQuery('#alertSubParameterId').val(jQuery(this).data('id'));
    jQuery('#exampleModal').modal('show');
});

jQuery('.artifactModal').on('click', function(e) {
    jQuery('#artifactParameterId').val(jQuery(this).data('parameterid'));
    jQuery('#artifactSubParameterId').val(jQuery(this).data('id'));
    jQuery('#moreArtifact').empty();
    jQuery('#artifactModal').modal('show');
});

jQuery('#saveAlert').on('click', function(e) {
    var parid = jQuery('#alertParameterId').val();
    var subid = jQuery('#alertSubParameterId').val();
    var msg = jQuery('#msg').val();
    var sheetID = "{{$data->id}}";
    jQuery('#alertParameterId').val(''); jQuery('#alertSubParameterId').val(''); jQuery('#msg').val('');
    jQuery('#exampleModal').modal('hide');
    var files = jQuery("#file").get(0).files;
    redalertData[subid] = { 'id': subid, 'parameter_id': parid, 'sheet_id': sheetID, 'msg': msg };
    for (var i = 0; i < files.length; i++) { redalertData[subid]['file'] = files[i]; }
    jQuery('#file').val('');
});

jQuery('#artifactAlert').on('click', function(e) {
    e.preventDefault();
    var parid = jQuery('#artifactParameterId').val();
    var subid = jQuery('#artifactSubParameterId').val();
    var sheetID = "{{$data->id}}";
    jQuery('#artifactParameterId').val(''); jQuery('#artifactSubParameterId').val(''); jQuery('#msg').val('');

    var fileInputs = jQuery(".file").get();
    var allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'pdf'];
    var valid = true;
    var allFiles = [];

    for (var i = 0; i < fileInputs.length; i++) {
        for (var j = 0; j < fileInputs[i].files.length; j++) {
            var file = fileInputs[i].files[j];
            var ext = file.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(ext)) { alert('Invalid file type: ' + file.name); valid = false; break; }
            allFiles.push(file);
        }
        if (!valid) break;
    }
    if (!valid) return;
    if (allFiles.length === 0) { alert('Please select at least one file to upload.'); return; }

    var data = new FormData();
    data.append('id', subid);
    data.append('audit_id', "{{$result->id}}");
    data.append('parameter_id', parid);
    data.append('sheet_id', sheetID);
    data.append('_token', "{{ csrf_token() }}");
    data.append('totalFile', allFiles.length);
    for (var i = 0; i < allFiles.length; i++) { data.append('file' + i, allFiles[i]); }

    jQuery.ajax({
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var pct = parseInt((evt.loaded / evt.total) * 100);
                    jQuery('#ProgressContaint').show().html(pct + '% Complete');
                }
            }, false);
            return xhr;
        },
        type: 'post', url: "{{url('artifact')}}", data: data, processData: false, contentType: false,
        success: function(resultData) {
            jQuery('#moreArtifact').empty(); jQuery('.file').val('');
            ImgPreview(resultData.data, '.preview' + subid);
            jQuery('#artifactModal').modal('hide'); jQuery('#ProgressContaint').html('');
        },
        error: function() { alert("Something went wrong during upload."); jQuery('#ProgressContaint').html(''); }
    });
});

function ImgPreview(input, placeToInsertImagePreview) {
    var image = '';
    input.map(function(item) {
        const isPdf = item.file.toLowerCase().endsWith('.pdf');
        if (isPdf) {
            image += `<div class="img-wrap preview${item.id}" style="position: relative; display: inline-block; margin: 5px;"><span class="close">&times;</span><a href="${item.file}" target="_blank"><img src="{{ asset('public/images/pdf-icon.png') }}" style="width:100px; height:100px;" data-id="${item.id}"></a></div>`;
        } else {
            image += `<div class="img-wrap preview${item.id}" style="position: relative; display: inline-block; font-size: 0;"><span class="close">&times;</span><a href="${item.file}" target="_blank"><img src="${item.file}" style="width:100px; height:100px;" data-id="${item.id}"></a></div>`;
        }
    });
    jQuery(placeToInsertImagePreview).append(image);
}
function getCategoryScores() {
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
function submitDataFun(type) {
    var submitData = [];
    var parameters = {};
    var sub = {};
    var alertData = redalertData;

    for (var el in result) {
        if (result.hasOwnProperty(el)) {
            for (var row in result[el]) {
                if (result[el].hasOwnProperty(row)) {
                    var ck = jQuery('#ackalert' + row).prop('checked') ? 1 : 0;
                    var baseScore = parseFloat(jQuery('#org' + row).text()) || 0;
                    var selectedScoreVal = parseFloat(jQuery('#errorCount' + row).find('option:selected').data('score'));
                    var finalErrorScore = isNaN(selectedScoreVal) ? baseScore : selectedScoreVal;
                    var error_score = baseScore - finalErrorScore;
                    if (error_score < 0) error_score = 0;

                    sub[row] = {
                        'id': subpar[row],
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
                    };
                }
            }

            parameters[el] = {
                'id': par[el],
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

    submitData.push({
        'id': "{{$result->id}}",
        'qm_sheet_id': "{{$data->id}}",
        'overall_score': jQuery('#wfatal').text(),
        'with_fatal_score_per': jQuery('#wfatalper').text(),
        'grade': jQuery('#grade').text(),
        'branch_id': jQuery('.branch').val(),
        'agency_id': jQuery('.agency').val(),
        'yard_id': jQuery('.yard').val(),
        'branch_repo_id': jQuery('.branch_repo').val(),
        'agency_repo_id': jQuery('.agency_repo').val(),
        'product_id': jQuery('.product').val(),
        'collection_manager_email': jQuery('input[name=Collection_Manager_email]').val(),
        'agency_manager_email': jQuery('input[name=agency_manager_email]').val(),
        'yard_manager_email': jQuery('input[name=yard_manager_email]').val(),
        'collection_manager_id': jQuery('#collection_manager-select').val(),
        'agency_manager': jQuery('input[name=agency_manager]').val(),
        'agency_phone': jQuery('select[name=agency_phone]').val(),
        'agency_email': jQuery('select[name=agency_email]').val(),
        'lavel_4': jQuery('#lavel_4').val(),
        'lavel_5': jQuery('#lavel_5').val(),
        'present_auditor': jQuery('#present_auditor').val(),
        'audit_sheet_type': jQuery('#sheet_select').val(),
        'status': type
    });

    // ADD CATEGORY SCORES TO SUBMISSION
    var categoryScores = getCategoryScores();
    if (categoryScores) {
        submitData[0].category_scores = categoryScores;
    }

    var ids = [];
    jQuery.ajax({
        type: 'POST',
        url: "{{url('allocation/update_audit')}}",
        data: { 'submission_data': submitData, 'parameters': parameters, "_token": "{{ csrf_token() }}" },
        dataType: "text",
        success: function(result) {
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
                jQuery.ajax({
                    type: 'post', url: "{{url('red-alert')}}", data: data, dataType: "text",
                    processData: false, contentType: false,
                    success: function(resultData) { console.log(resultData); }
                });
            }
            if (type == 'submit') { window.location = '{{ url("submit_audited_list")}}'; }
            else { window.location = '{{ url("save_audited_list")}}'; }
        },
        error: function() { alert("Something went wrong"); }
    });
}

function remarkIsFilled(result) {
    var remark = true;
    for (var el in result) {
        if (result.hasOwnProperty(el)) {
            if (jQuery.isEmptyObject(result[el])) { remark = false; }
            for (var row in result[el]) {
                if (result[el].hasOwnProperty(row) && jQuery('#remark' + row).val().trim().length == 0) {
                    remark = false; break;
                }
            }
        }
    }
    return remark;
}

function sendOTP() {
    var obsIds = ['obs42', 'obs62', 'obs82', 'obs133'];
    var selectedObsValue = null;
    var collectionManagerSelected = '';
    obsIds.forEach(function(id) {
        var value = jQuery('#' + id).val();
        if (value) { selectedObsValue = value; return false; }
    });

    var agencyMail = jQuery("#agency_mail").val();
    var audit_for = jQuery("#audit_for").val();
    var product_id = jQuery("#productSelect").val();
    var audit_cycle = jQuery('#audit_cycle').val();
    var level3 = jQuery('#collection_manager-select').val() || '';
    var present_auditor = jQuery('#present_auditor').val();
    var level4Values = [], level5Values = [];
    jQuery('#intimationUsersContainer .intimationUser').each(function() {
        level4Values.push(jQuery(this).find('select[name="level_4[]"]').val());
        level5Values.push(jQuery(this).find('select[name="level_5[]"]').val());
    });

    if (selectedObsValue == 4 || selectedObsValue == 5) {
        collectionManagerSelected = jQuery("#collection_manager-select").val();
    }

    if (agencyMail === '' || !validateEmail(agencyMail)) { alert("Please enter a valid email address."); return; }

    var parameters = {}, sub = {};
    for (var el in result) {
        if (result.hasOwnProperty(el)) {
            for (var row in result[el]) {
                if (result[el].hasOwnProperty(row)) {
                    sub[row] = {
                        'remark': jQuery('#remark' + row).val(),
                        'orignal_weight': jQuery('#org' + row).text(),
                        'temp_weight': result[el][row],
                        'score': jQuery('#' + row).val(),
                        'is_percentage': (jQuery('#' + row).val() == 'rating') ? 1 : 0,
                        'selected_per': jQuery('#ratingSelect' + row).val(),
                        'ackalert': jQuery('#ackalert' + row).prop('checked') ? 1 : 0,
                        'option': jQuery('#obs' + row + ' option:selected').text(),
                        'error_count': jQuery('#errorCount' + row).val(),
                    };
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

    // GET CATEGORY SCORES FOR OTP
    var categoryScores = getCategoryScores();

    jQuery('#loader').show();
    jQuery.ajax({
        url: "{{ url('agency/send-otp') }}",
        type: 'POST',
        data: {
            'product_id': product_id,
            'agency_email': agencyMail,
            'agency_id': audit_for,
            'temp_total_weightage': jQuery('#scroable').text(),
            'overall_score': jQuery('#wfatal').text(),
            'parameters': parameters,
            'audit_cycle': audit_cycle,
            'collection_manager': collectionManagerSelected,
            '_token': "{{ csrf_token() }}",
            'lavel_3': level3,
            'present_auditor': present_auditor,
            'lavel_4': level4Values,
            'lavel_5': level5Values,
            'agency_manager': jQuery('input[name=agency_manager]').val(),
            'category_scores': categoryScores   // <-- ADDED
        },
        dataType: "text",
        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
        success: function(response) {
            jQuery('#loader').hide();
            openOtpPopup();
            if (response.collection_manager_otp_sent) { openCollectionManagerOtpPopup(); }
        },
        error: function() { jQuery('#loader').hide(); alert("Failed to send OTP. Please try again."); }
    });
}

let otpResendCooldown = false;
let collectionManagerOtpResendCooldown = false;

function startResendTimer(buttonId, timerDisplayId) {
    const cooldownTime = 30;
    let remainingTime = cooldownTime;
    jQuery(buttonId).prop('disabled', true);
    otpResendCooldown = true;
    const timerInterval = setInterval(function() {
        if (remainingTime <= 0) {
            clearInterval(timerInterval);
            jQuery(timerDisplayId).hide();
            jQuery(buttonId).prop('disabled', false);
            otpResendCooldown = false;
        } else {
            jQuery(timerDisplayId).show().text('Please wait ' + remainingTime + ' seconds to resend OTP.');
            remainingTime--;
        }
    }, 1000);
}

jQuery(document).on('click', '#resendOtpButton', function() {
    if (otpResendCooldown) { alert("Please wait until the timer finishes to resend OTP."); return; }
    startResendTimer('#resendOtpButton', '#resendOtpTimer');
    const agencyMail = jQuery("#agency_mail").val();
    const audit_for = jQuery("#audit_for").val();
    const product_id = jQuery("#productSelect").val();
    const audit_cycle = jQuery('#audit_cycle').val();
    if (agencyMail === '' || !validateEmail(agencyMail)) { alert("Please enter a valid email address."); return; }
    jQuery.ajax({
        url: "{{ url('agency/resend-otp') }}", type: 'POST',
        data: { 'product_id': product_id, 'agency_email': agencyMail, 'agency_id': audit_for, 'audit_cycle': audit_cycle, '_token': "{{ csrf_token() }}" },
        dataType: "json", headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
        success: function(response) { alert(response.message || "OTP sent successfully."); },
        error: function() { alert("Failed to resend OTP. Please try again."); }
    });
});

jQuery(document).on('click', '#collectionManagerResendOtpButton', function() {
    if (collectionManagerOtpResendCooldown) { alert("Please wait until the timer finishes to resend OTP."); return; }
    startResendTimer('#collectionManagerResendOtpButton', '#collectionManagerResendOtpTimer');
    const collectionManagerSelected = jQuery("#collection_manager-select").val();
    const agencyMail = jQuery("#agency_mail").val();
    const audit_for = jQuery("#audit_for").val();
    const product_id = jQuery("#productSelect").val();
    const audit_cycle = jQuery('#audit_cycle').val();
    if (agencyMail === '' || !validateEmail(agencyMail)) { alert("Please enter a valid email address."); return; }
    jQuery.ajax({
        url: "{{ url('collection-manager/resend-otp') }}", type: 'POST',
        data: { 'manager_id': collectionManagerSelected, 'product_id': product_id, 'agency_email': agencyMail, 'agency_id': audit_for, 'audit_cycle': audit_cycle, '_token': "{{ csrf_token() }}", 'type': "collection_manager" },
        dataType: "json", headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
        success: function(response) { alert(response.message || "OTP sent successfully."); },
        error: function() { alert("Failed to resend OTP. Please try again."); }
    });
});
</script>

<script>
function validateEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}

function openOtpPopup() {
    var otpModal = new bootstrap.Modal(document.getElementById('otpModal'), { backdrop: 'static', keyboard: false });
    otpModal.show();
    jQuery("#verifyOtpButton").off('click').on('click', function() {
        jQuery('#loader').show();
        jQuery.ajax({
            url: "{{ url('agency/verify-otp') }}", type: 'POST',
            data: { otp: jQuery("#otpInput").val(), agency_id: jQuery("#audit_for").val(), agency_email: jQuery("#agency_mail").val(), type: 'agency' },
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            success: function(response) {
                jQuery('#loader').hide();
                if (response == "valid") {
                    otpModal.hide();
                    var obsIds = ['obs42', 'obs62', 'obs82', 'obs133'];
                    var selectedObsValue = null;
                    obsIds.forEach(function(id) { var v = jQuery('#' + id).val(); if (v) { selectedObsValue = v; return false; } });
                    if (selectedObsValue == 4 || selectedObsValue == 5) { openCollectionManagerOtpPopup(); }
                    else { submitDataFun('submit'); }
                } else { alert("Invalid OTP. Please try again."); }
            },
            error: function() { jQuery('#loader').hide(); alert("Failed to verify OTP. Please try again."); }
        });
    });
}

function openCollectionManagerOtpPopup() {
    var cmModal = new bootstrap.Modal(document.getElementById('collectionManagerOtpModal'), { backdrop: 'static', keyboard: false });
    cmModal.show();
    jQuery("#verifyCollectionManagerOtpButton").off('click').on('click', function() {
        jQuery('#loader').show();
        jQuery.ajax({
            url: "{{ url('agency/verify-otp') }}", type: 'POST',
            data: { otp: jQuery("#collectionManagerOtpInput").val(), agency_id: jQuery("#audit_for").val(), agency_email: jQuery("#agency_mail").val(), cm_manager_id: jQuery("#collection_manager-select").val(), type: 'collection_manager' },
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            success: function(response) {
                jQuery('#loader').hide();
                if (response == "valid") { cmModal.hide(); submitDataFun('submit'); }
                else { alert("Invalid OTP for Collection Manager. Please try again."); }
            },
            error: function() { jQuery('#loader').hide(); alert("Failed to verify OTP. Please try again."); }
        });
    });
}

jQuery('#productSelect').on('mousedown', function(e) { e.preventDefault(); });
jQuery('#audit_cycle').on('mousedown', function(e) { e.preventDefault(); });
jQuery('#audit_for').on('mousedown', function(e) { e.preventDefault(); });
jQuery('#collection_manager').on('mousedown', function(e) { e.preventDefault(); });
jQuery('#intimationUsersContainer').on('mousedown', function(e) { e.preventDefault(); });

jQuery('select[name="agency"], select[name="level_4[]"], select[name="level_5[]"], select[name="product"]').each(function() {
    if (!$(this).attr('id')) {
        $(this).on('mousedown', function(e) { e.preventDefault(); });
        $(this).css({ 'pointer-events': 'none', 'cursor': 'not-allowed' });
    }
});

const selectElement = document.getElementById('audit_for');
if (selectElement && selectElement.value) { selectElement.disabled = true; }
</script>

<!-- OTP Modal -->
<div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="otpModalLabel">Please Enter Agency OTP</h5>
            </div>
            <div class="modal-body">
                <lottie-player src="{{ asset('public/images/computer-otp-verification.json') }}" autoPlay loop style="width: 120px; height: 120px;margin:auto auto 20px"></lottie-player>
                <div class="mb-3">
                    <label for="otpInput" class="form-label">OTP has been sent to your registered email. Please enter it below:</label>
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

<!-- Collection Manager OTP Modal -->
<div class="modal fade" id="collectionManagerOtpModal" tabindex="-1" aria-labelledby="collectionManagerOtpModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #637c97">
                <h5 class="modal-title" id="collectionManagerOtpModalLabel">Please Enter Collection Manager OTP</h5>
            </div>
            <div class="modal-body">
                <lottie-player src="{{ asset('public/images/computer-otp-verification.json') }}" autoPlay loop style="width: 120px; height: 120px;margin:auto auto 20px;"></lottie-player>
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

@endsection