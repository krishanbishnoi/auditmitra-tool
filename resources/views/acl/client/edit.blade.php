@extends('layouts.master')

@section('title', '| Clients')

@section('sh-detail')
    Edit
@endsection

@section('content')

    <div class="card">

        <!--begin::Portlet-->
        <div class="kt-portlet">
            <div class="card-header">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Details
                    </h3>
                </div>
            </div>

            <!--begin::Form-->
            {!! Form::model($data, [
                'method' => 'PATCH',
                'url' => 'client/' . Crypt::encrypt($data->id),
                'class' => 'kt-form',
                'data-toggle' => 'validator',
                'enctype' => 'multipart/form-data', // Added enctype for file upload
            ]) !!}

            <div class="card-body card-block">

                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Name*</label>
                        <input type="text" name="name" class="form-control" required value="{{ $data->name }}">
                    </div>
                    <div class="col-lg-6">
                        <label>Primary Email (as username)*</label>
                        <input type="text" readonly name="email" class="form-control" required
                            value="{{ $data->email }}">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Mobile No.*</label>
                        <input type="text" name="mobile" class="form-control" required value="{{ $data->mobile }}">
                    </div>
                    <div class="col-lg-6">
                        <label>Roles*</label>
                        {{ Form::select('role[]', $roles, $rdata, ['class' => 'form-control m-select2', 'id' => 'kt_select2_1', 'required' => 'required']) }}
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Logo</label>
                        @if ($data->logo)
                            <div>
                                <img src="{{ asset('storage/app/' . $data->logo) }}" alt="Client Logo"
                                    style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                            </div>
                        @endif
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>

                    <div class="col-lg-6">
                        <label for="color_code" class="form-control-label">Color Code</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" id="color_code" name="color_code" class="form-control form-control-color"
                                value="{{ old('color_code', $data->color_code ?? '#000000') }}"
                                onchange="document.getElementById('color_display').value = this.value"
                                style="max-width: 150px;">
                            <input type="text" id="color_display" class="form-control"
                                value="{{ old('color_code', $data->color_code ?? '#000000') }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-6">
                        <label for="levels">Approval Levels</label>
                        <div id="levels-container">
                            @if (is_array($data->levels))
                                @foreach ($data->levels as $key => $value)
                                    <div class="input-group mb-2 level-entry">
                                        <input type="text" name="levels[{{ $key }}]" class="form-control"
                                            value="{{ $value }}" placeholder="Enter Level Name">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger remove-level">Remove</button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2 level-entry">
                                    <input type="text" name="levels[Level 1]" class="form-control"
                                        placeholder="Enter Level Name">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger remove-level">Remove</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-success mt-2" id="add-level">Add Level</button>
                    </div>

                    <div class="col-lg-6">
                        <label for="ActionPlantat">Action Plan TAT</label>
                        <div class="input-group mb-2">
                            <input type="number" minlength="1" name="action_plan_tat" class="form-control"
                                placeholder="ActionPlan TAT in days">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-6">
                        <label for="start_date" class="form-label">Cycle Start Date</label>
                        <select name="start_date" id="start_date" class="form-control">
                            <option value="">-- Cycle Start Date --</option>
                            @for ($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}"
                                    {{ old('start_date', $data->cycle_start_date ?? '') == $i ? 'selected' : '' }}>
                                    {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                </option>
                            @endfor
                        </select>

                    </div>

                    <div class="col-lg-6">

                        <label for="end_date" class="form-label">Cycle End Date</label>
                        <select name="end_date" id="end_date" class="form-control">
                            <option value="">-- Cycle End Date --</option>
                            @for ($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}"
                                    {{ old('end_date', $data->cycle_end_date ?? '') == $i ? 'selected' : '' }}>
                                    {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                </option>
                            @endfor
                        </select>

                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-6">
                        <label for="ActionPlantat">Client Email</label>
                        <div class="input-group mb-2">
                            <input type="text" minlength="1" name="client_email" class="form-control"
                                placeholder="client_email used for pdf purpose">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="ActionPlantat">Non-Compliant(Baseline score)</label>
                        <div class="input-group mb-2">
                            <input type="number" minlength="1" name="negative_score_range" class="form-control"
                                placeholder="negative_score_range from 0 to 99">
                        </div>
                    </div>
                </div>
              

    {{-- new code edit fileds for the differnt clients --}}

                <div class="form-group row">
                    <div class="col-lg-6">
                        <label>Agency Name for Client</label>
                        <input type="text" name="agency_name_for_client" class="form-control" required 
                           value="{{ old('agency_name_for_client', $clientSettings['agency_name_for_client'] ?? '') }}">
                    </div>

                    <div class="col-lg-6">
                        <label>Agency Repo Name for Client</label>
                        <input type="text" name="agency_repo_name_for_client" class="form-control" required
                            value="{{ old('agency_repo_name_for_client', $clientSettings['agency_repo_name_for_client'] ?? '') }}">
                    </div>
                </div>


            <div class="form-group row">
                <div class="col-lg-6">
                    <label>Yard Name for Client</label>
                    <input type="text" name="yard_name_for_client" class="form-control" required 
                    value="{{ old('yard_name_for_client', $clientSettings['yard_name_for_client'] ?? '') }}">
                </div>

                <div class="col-lg-6">
                    <label>Yard Repo Name for Client</label>
                    <input type="text" name="yard_repo_name_for_client" class="form-control" required 
                    value="{{ old('yard_repo_name_for_client', $clientSettings['yard_repo_name_for_client'] ?? '') }}">
                </div>
            </div>

            <div class="form-group row">

                <div class="col-lg-6">
                    <label>Branch Name for Client</label>
                    <input type="text" name="branch_name_for_client" class="form-control" required
                        value="{{ old('branch_name_for_Client', $clientSettings['branch_name_for_client'] ?? '') }}">
                </div>

                <div class="col-lg-6">
                    <label>Branch Repo Name for Client</label>
                    <input type="text" name="branch_repo_name_for_client" class="form-control" required 
                    value="{{ old('branch_repo_name_for_client', $clientSettings['branch_repo_name_for_client'] ?? '') }}">
                </div>
            </div>

            {{-- end --}}

            <div class="form-group row">
                <div class="col-lg-6">
                    <label class="form-control-label">Audit Type</label>

                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="is_legal" name="is_legal" value="1"
                            {{ old('is_legal', $data->is_legal) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_legal">
                            Legal
                        </label>
                    </div>

                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="is_compliance" name="is_compliance"
                            value="1" {{ old('is_compliance', $data->is_compliance) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_compliance">
                            Compliance
                        </label>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa fa-dot-circle-o"></i> Submit
                </button>
                <button type="reset" class="btn btn-danger btn-sm">
                    <i class="fa fa-ban"></i> Reset
                </button>
            </div>

            {!! Form::close() !!}
            <!--end::Form-->
        </div>
        <!--end::Portlet-->

    </div>

@endsection

@section('js')
    <script>
        let levelCounter = {{ is_array($data->levels) ? count($data->levels) + 1 : 2 }};

        jQuery(document).ready(function() {
            jQuery('#add-level').on('click', function() {
                const html = `
                <div class="input-group mb-2 level-entry">
                    <input type="text" name="levels[Level ${levelCounter}]" class="form-control" placeholder="Enter Level Name">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-level">Remove</button>
                    </div>
                </div>`;
                jQuery('#levels-container').append(html);
                levelCounter++;
            });

            jQuery(document).on('click', '.remove-level', function() {
                jQuery(this).closest('.level-entry').remove();
            });
        });
    </script>
@endsection
