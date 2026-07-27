@extends('layouts.master')

@section('sh-title')
    {{ $qm_sheet_data->name }}
@endsection

@section('sh-detail')
    Update Parameter
@endsection

@section('sh-toolbar')
    <div class="kt-subheader__toolbar">
        <div class="kt-subheader__wrapper">
            <a href="/qm_sheet/{{ Crypt::encrypt($qm_sheet_data->id) }}/list_parameter"
                class="btn btn-label-success btn-bold">
                List All Parameter
            </a>
        </div>
    </div>
@endsection

@section('content')
    @php
        $allocatedmodule = App\Helpers\Helper::allocatedmodulelist();
    @endphp

    <style>
        /* ── Brand tokens ── */
        :root {
            --cp-primary:       #4f46e5;
            --cp-primary-dark:  #4338ca;
            --cp-primary-light: #eef2ff;
            --cp-primary-mid:   #c7d2fe;
            --cp-success:       #059669;
            --cp-success-light: #ecfdf5;
            --cp-success-mid:   #6ee7b7;
            --cp-danger:        #dc2626;
            --cp-danger-light:  #fef2f2;
            --cp-danger-mid:    #fecaca;
            --cp-amber:         #d97706;
            --cp-amber-light:   #fffbeb;
            --cp-amber-mid:     #fde68a;
            --cp-gray-50:       #f9fafb;
            --cp-gray-100:      #f3f4f6;
            --cp-gray-200:      #e5e7eb;
            --cp-gray-400:      #9ca3af;
            --cp-gray-600:      #6b7280;
            --cp-gray-800:      #374151;
            --cp-gray-900:      #1a1a2e;
        }

        /* .cp-page { max-width: 960px; margin: 0 auto; padding: 1.5rem 1rem; } */

        /* ── Page header ── */
        .cp-page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }
        .cp-page-header h1 {
            font-size: 20px;
            font-weight: 700;
            color: var(--cp-gray-900);
            margin: 0 0 3px;
        }
        .cp-page-header p { font-size: 13px; color: var(--cp-gray-600); margin: 0; }

        .cp-btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 500;
            padding: 7px 14px;
            border-radius: 8px;
            border: 1px solid var(--cp-primary-mid);
            background: var(--cp-primary-light);
            color: var(--cp-primary);
            text-decoration: none;
            transition: background .15s;
        }
        .cp-btn-back:hover { background: #e0e7ff; text-decoration: none; color: var(--cp-primary-dark); }

        /* ── Card ── */
        .cp-card {
            background: #fff;
            border: 1px solid var(--cp-gray-200);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
        }

        .cp-card-section {
            padding: 1.5rem;
            border-bottom: 1px solid var(--cp-gray-100);
        }
        .cp-card-section:last-of-type { border-bottom: none; }

        .cp-section-label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--cp-primary);
            background: var(--cp-primary-light);
            border: 1px solid var(--cp-primary-mid);
            border-radius: 20px;
            padding: 3px 10px;
            margin-bottom: 1.1rem;
        }

        /* ── Form grid ── */
        .cp-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .cp-form-grid.full { grid-template-columns: 1fr; }
        @media (max-width: 640px) { .cp-form-grid { grid-template-columns: 1fr; } }

        .cp-form-group { display: flex; flex-direction: column; gap: 5px; }
        .cp-form-group label { font-size: 13px; font-weight: 500; color: var(--cp-gray-800); }
        .cp-form-group label .req { color: var(--cp-danger); margin-left: 2px; }

        .cp-input-row { display: flex; gap: 6px; align-items: center; }
        .cp-input-row select,
        .cp-input-row input,
        .cp-input-row textarea { flex: 1; min-width: 0; }

        .cp-input-row select,
        .cp-form-group select,
        .cp-form-group input[type="number"],
        .cp-form-group input[type="text"],
        .cp-form-group textarea {
            height: 36px;
            padding: 0 10px;
            font-size: 13px;
            color: var(--cp-gray-800);
            background: #fff;
            border: 1px solid var(--cp-gray-200);
            border-radius: 8px;
            transition: border-color .15s, box-shadow .15s;
            width: 100%;
            appearance: none;
            -webkit-appearance: none;
        }
        .cp-form-group textarea {
            height: 36px;
            min-height: 36px;
            padding: 8px 10px;
            resize: vertical;
        }
        .cp-form-group select:focus,
        .cp-form-group input:focus,
        .cp-form-group textarea:focus,
        .cp-input-row select:focus {
            outline: none;
            border-color: var(--cp-primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
        }

        .cp-form-group select,
        .cp-input-row select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 30px;
        }

        /* ── + add buttons ── */
        .cp-btn-add-small {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px dashed var(--cp-success-mid);
            background: var(--cp-success-light);
            color: var(--cp-success);
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all .15s;
            text-decoration: none;
            line-height: 1;
            font-weight: 600;
        }
        .cp-btn-add-small:hover {
            background: #d1fae5;
            border-color: var(--cp-success);
            color: #065f46;
        }

        /* ── Repeater items ── */
        .cp-repeater-item {
            background: #fafbff;
            border: 1px solid #dde3f8;
            border-left: 3px solid var(--cp-primary);
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1rem;
        }

        .cp-repeater-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.1rem;
        }
        .cp-repeater-header .cp-sub-label {
            font-size: 13px;
            font-weight: 700;
            color: var(--cp-primary-dark);
            background: var(--cp-primary-light);
            padding: 3px 10px;
            border-radius: 20px;
            border: 1px solid var(--cp-primary-mid);
        }

        .cp-repeater-actions { display: flex; align-items: center; gap: 8px; }

        .cp-btn-delete {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 500;
            color: var(--cp-danger);
            border: 1px solid var(--cp-danger-mid);
            border-radius: 6px;
            padding: 5px 11px;
            background: var(--cp-danger-light);
            cursor: pointer;
            transition: background .15s;
            text-decoration: none;
        }
        .cp-btn-delete:hover { background: #fee2e2; color: var(--cp-danger); text-decoration: none; }

        .cp-btn-sub-question {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 500;
            color: var(--cp-success);
            border: 1px solid var(--cp-success-mid);
            border-radius: 6px;
            padding: 5px 11px;
            background: var(--cp-success-light);
            cursor: pointer;
            transition: background .15s;
            text-decoration: none;
        }
        .cp-btn-sub-question:hover { background: #d1fae5; color: var(--cp-success); text-decoration: none; }

        .cp-divider { height: 1px; background: #e8ecf8; margin: 1rem 0; }

        /* ── Checkbox pills ── */
        .cp-checkbox-row { display: flex; flex-wrap: wrap; gap: 8px; }
        .cp-checkbox-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border: 1px solid var(--cp-gray-200);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            color: var(--cp-gray-600);
            cursor: pointer;
            transition: all .15s;
            background: #fff;
            user-select: none;
        }
        .cp-checkbox-pill:hover { border-color: var(--cp-primary-mid); color: var(--cp-primary-dark); background: var(--cp-primary-light); }
        .cp-checkbox-pill input[type="checkbox"] { width: 13px; height: 13px; accent-color: var(--cp-primary); cursor: pointer; }

        /* ── Error scoring ── */
        .cp-error-section {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            background: var(--cp-amber-light);
            border: 1px solid var(--cp-amber-mid);
            border-radius: 8px;
        }
        .cp-error-section-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--cp-amber);
            margin-bottom: .75rem;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .cp-error-col-labels {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 8px;
            font-size: 11px;
            font-weight: 600;
            color: var(--cp-amber);
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 6px;
        }
        .cp-error-row {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 8px;
            align-items: center;
            margin-bottom: 6px;
        }
        .cp-error-row input {
            height: 34px;
            padding: 0 10px;
            font-size: 13px;
            border: 1px solid #fde68a;
            border-radius: 6px;
            width: 100%;
            background: #fff;
        }
        .cp-error-row input:focus { outline: none; border-color: var(--cp-amber); box-shadow: 0 0 0 3px rgba(217,119,6,.1); }

        .cp-btn-add-row {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 500;
            color: var(--cp-amber);
            border: 1px dashed var(--cp-amber-mid);
            border-radius: 6px;
            padding: 5px 12px;
            background: #fff;
            cursor: pointer;
            margin-top: 4px;
            transition: background .15s;
        }
        .cp-btn-add-row:hover { background: #fef3c7; }

        .cp-btn-remove-row {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--cp-danger-mid);
            border-radius: 6px;
            background: var(--cp-danger-light);
            color: var(--cp-danger);
            cursor: pointer;
            font-size: 13px;
            transition: background .15s;
        }
        .cp-btn-remove-row:hover { background: #fee2e2; }

        /* ── Add sub-parameter button ── */
        .cp-add-sub {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            width: 100%;
            padding: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--cp-primary);
            border: 1.5px dashed var(--cp-primary-mid);
            border-radius: 8px;
            background: var(--cp-primary-light);
            cursor: pointer;
            margin-top: .25rem;
            transition: background .15s, border-color .15s;
        }
        .cp-add-sub:hover { background: #e0e7ff; border-color: var(--cp-primary); }

        /* ── Footer ── */
        .cp-card-footer {
            padding: 1.25rem 1.5rem;
            background: var(--cp-gray-50);
            border-top: 1px solid var(--cp-gray-200);
            display: flex;
            gap: 8px;
        }
        .cp-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 22px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            background: var(--cp-primary);
            color: #fff;
            cursor: pointer;
            transition: background .15s;
        }
        .cp-btn-primary:hover { background: var(--cp-primary-dark); }

        .cp-btn-reset {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 500;
            border-radius: 8px;
            border: 1px solid var(--cp-gray-200);
            background: #fff;
            color: var(--cp-gray-600);
            cursor: pointer;
            transition: background .15s;
        }
        .cp-btn-reset:hover { background: var(--cp-gray-100); }

        .cp-scoring-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--cp-success);
            background: var(--cp-success-light);
            border: 1px solid var(--cp-success-mid);
            border-radius: 20px;
            padding: 3px 10px;
            display: inline-block;
            margin-bottom: .6rem;
            text-transform: uppercase;
            letter-spacing: .06em;
        }
    </style>

    <div class="cp-page">

        <div class="cp-page-header">
            <div>
                <h1>Edit parameter</h1>
                <p>Update parameters and sub-parameters for this QM sheet</p>
            </div>
            <a href="{{ url('/qm_sheet/' . Crypt::encrypt($qm_sheet_data->id) . '/list_parameter') }}"
                class="cp-btn-back">
                &#8592; All parameters
            </a>
        </div>

        <div class="cp-card">

            {!! Form::open([
                'route' => 'update_parameter',
                'class' => 'kt-form',
                'role'  => 'form',
                'data-toggle' => 'validator',
            ]) !!}

            <input type="hidden" name="qm_sheet_id" value="{{ $qm_sheet_data->id }}">
            <input type="hidden" name="parameter_id" value="{{ $param_data->id }}">

            {{-- ── Sheet info section ── --}}
            <div class="cp-card-section">
                <div class="cp-section-label">Sheet info</div>
                <div class="cp-form-grid">

                    @if (in_array(25, $allocatedmodule))
                        <div class="cp-form-group">
                            <label>Category <span class="req">*</span></label>
                            <select name="category_id" required>
                                <option value="">— Select category —</option>
                                @foreach ($category as $cat)
                                    <option value="{{ $cat->id }}" {{ $param_data->category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="cp-form-group">
                        <label>Parameter <span class="req">*</span></label>
                        <input type="text" name="parameter" required value="{{ $param_data->parameter }}">
                    </div>

                    @if (in_array(27, $allocatedmodule))
                        @if (count($locations) > 0)
                            <div class="cp-form-group">
                                <label>Location <span class="req">*</span></label>
                                <select name="locations" id="locations" required>
                                    <option value="">— Select location —</option>
                                    @foreach ($locations as $l)
                                        <option value="{{ $l }}" {{ $param_data->location == $l ? 'selected' : '' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if (count($campusTypes) > 0)
                            <div class="cp-form-group">
                                <label>Campus type <span class="req">*</span></label>
                                <select name="campusTypes" id="campusTypes" required>
                                    <option value="">— Select campus type —</option>
                                    @foreach ($campusTypes as $ct)
                                        <option value="{{ $ct }}" {{ $param_data->campus_type == $ct ? 'selected' : '' }}>{{ $ct }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    @endif

                </div>
            </div>

            {{-- ── Sub-parameters section ── --}}
            <div class="cp-card-section">
                <div class="cp-section-label">Sub-parameters</div>

                <div id="kt_repeater_qm_sheet">
                    <div class="form-group row" id="kt_repeater_qm_sheet">
                        <div data-repeater-list="subs" class="col-lg-12">

                            {{-- ── Existing sub-parameters ── --}}
                            @foreach ($param_data->qm_sheet_sub_parameter as $kksp => $vvsp)
                                <div data-repeater-item class="cp-repeater-item">

                                    <input type="hidden" name="sp_pm_id" value="{{ $vvsp->id }}">

                                    <div class="cp-repeater-header">
                                        <span class="cp-sub-label">Sub-parameter #{{ $kksp + 1 }}</span>
                                        <div class="cp-repeater-actions">
                                            <a href="{{ route('create_question', ['sub_param_id' => $vvsp->id]) }}"
                                                class="cp-btn-sub-question">
                                                + Add sub question
                                            </a>
                                            <div data-repeater-delete class="cp-btn-delete">
                                                &#128465; Remove
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Sub-parameter name, weightage, details --}}
                                    <div class="cp-form-grid">
                                        <div class="cp-form-group" style="grid-column: 1 / -1;">
                                            <label>Sub-parameter name</label>
                                            <input type="text" name="sub_parameter"
                                                placeholder="Sub-parameter name"
                                                value="{{ $vvsp->sub_parameter }}">
                                        </div>
                                        <div class="cp-form-group">
                                            <label>Weightage</label>
                                            <input type="number" name="weight"
                                                placeholder="e.g. 10"
                                                value="{{ $vvsp->weight }}">
                                        </div>
                                        <div class="cp-form-group">
                                            <label>Details</label>
                                            <textarea name="details" rows="1"
                                                placeholder="Sub-parameter description or notes...">{{ $vvsp->details }}</textarea>
                                        </div>
                                    </div>

                                    <div class="cp-divider"></div>

                                    {{-- Parameter type, risk, compliance, pillar, touchpoint --}}
                                    <div class="cp-form-grid">
                                        @if (in_array(24, $allocatedmodule))
                                            <div class="cp-form-group">
                                                <label>Parameter type <span class="req">*</span></label>
                                                <select name="parameter_type" class="parameter_type">
                                                    <option value="not_required" {{ $vvsp->parameter_type == 'not_required' ? 'selected' : '' }}>Not required</option>
                                                    <option value="internal"     {{ $vvsp->parameter_type == 'internal'     ? 'selected' : '' }}>Internal</option>
                                                    <option value="external"     {{ $vvsp->parameter_type == 'external'     ? 'selected' : '' }}>External</option>
                                                </select>
                                            </div>
                                        @endif

                                        <div class="cp-form-group">
                                            <label>Risk level <span class="req">*</span></label>
                                            <select name="risk_level" class="risk_level">
                                                <option value="">— Select risk level —</option>
                                                <option value="Low"    {{ $vvsp->Severity == 'Low'    ? 'selected' : '' }}>Low</option>
                                                <option value="Medium" {{ $vvsp->Severity == 'Medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="High"   {{ $vvsp->Severity == 'High'   ? 'selected' : '' }}>High</option>
                                                <option value="Zero Tolerance"   {{ $vvsp->Severity == 'Zero Tolerance'   ? 'selected' : '' }}>Zero Tolerance</option>
                                            </select>
                                        </div>

                                        @if (in_array(27, $allocatedmodule) && count($compliance_experience) > 0)
                                            <div class="cp-form-group">
                                                <label>Compliance / Experience <span class="req">*</span></label>
                                                <select name="compliance_experience" class="compliance_experience">
                                                    <option value="" disabled selected>— Select —</option>
                                                    @foreach ($compliance_experience as $item)
                                                        <option value="{{ $item }}" {{ $vvsp->compliance_experience == $item ? 'selected' : '' }}>{{ $item }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif

                                        @if (in_array(27, $allocatedmodule) && count($pillar) > 0)
                                            <div class="cp-form-group">
                                                <label>Pillar <span class="req">*</span></label>
                                                <select name="pillar">
                                                    <option value="">— Select pillar —</option>
                                                    @foreach ($pillar as $item)
                                                        <option value="{{ $item }}" {{ $vvsp->pillar == $item ? 'selected' : '' }}>{{ $item }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif

                                        @if (in_array(27, $allocatedmodule) && count($touch_points) > 0)
                                            <div class="cp-form-group">
                                                <label>Touch point <span class="req">*</span></label>
                                                <select name="touchpoint">
                                                    <option value="">— Select touch point —</option>
                                                    @foreach ($touch_points as $tp)
                                                        <option value="{{ $tp }}" {{ $vvsp->touch_point == $tp ? 'selected' : '' }}>{{ $tp }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="cp-divider"></div>

                                    {{-- Scoring options --}}
                                    <div class="cp-scoring-label">Scoring options</div>
                                    <div class="cp-checkbox-row">
                                        <label class="cp-checkbox-pill">
                                            <input type="checkbox" name="s_pass" value="1" {{ $vvsp->pass ? 'checked' : '' }}> Satisfactory
                                        </label>
                                        <label class="cp-checkbox-pill">
                                            <input type="checkbox" name="s_fail" value="1" {{ $vvsp->fail ? 'checked' : '' }}> Unsatisfactory
                                        </label>
                                        <label class="cp-checkbox-pill">
                                            <input type="checkbox" name="s_critical" value="1" {{ $vvsp->critical ? 'checked' : '' }}> Critical
                                        </label>
                                        <label class="cp-checkbox-pill">
                                            <input type="checkbox" name="s_na" value="1" {{ $vvsp->na ? 'checked' : '' }}> N/A
                                        </label>
                                        <label class="cp-checkbox-pill">
                                            <input type="checkbox" name="s_pwd" value="1" {{ $vvsp->pwd ? 'checked' : '' }}> PWD
                                        </label>
                                        <label class="cp-checkbox-pill">
                                            <input type="checkbox" name="s_per" value="1" {{ $vvsp->per ? 'checked' : '' }}> Percentage
                                        </label>
                                        <label class="cp-checkbox-pill">
                                            <input type="checkbox" name="s_collection" value="1" {{ $vvsp->collection_manager ? 'checked' : '' }}> OTP for Collection Manager
                                        </label>
                                        <label class="cp-checkbox-pill">
                                            <input type="checkbox" name="s_regulatory" value="1" {{ $vvsp->is_regulatory_param ? 'checked' : '' }}> Is Regulatory type
                                        </label>
                                        <label class="cp-checkbox-pill">
                                            <input type="checkbox" name="error_scoring" class="error_scoring_checkbox" value="1" {{ $vvsp->use_error_count ? 'checked' : '' }}> Error Scoring
                                        </label>
                                    </div>

                                    {{-- Error scoring section --}}
                                    <div class="cp-error-section error_scoring_section"
                                        style="{{ $vvsp->use_error_count ? 'display:block;' : 'display:none;' }}">
                                        <div class="cp-error-section-title">Error count scoring</div>
                                        <div class="cp-error-col-labels">
                                            <span>Error count</span>
                                            <span>Score</span>
                                            <span></span>
                                        </div>
                                        <div class="error_scoring_container">
                                            @if ($vvsp->error_scoring)
                                                @foreach (json_decode($vvsp->error_scoring, true) as $row)
                                                    <div class="cp-error-row">
                                                        <input type="number" class="error-count" value="{{ $row['error_count'] }}" readonly>
                                                        <input type="number" class="error-score" value="{{ $row['score'] }}" placeholder="Enter score">
                                                        <button type="button" class="cp-btn-remove-row remove-error-row">&times;</button>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <button type="button" class="cp-btn-add-row add_error_row">
                                            + Add error count
                                        </button>
                                        <input type="hidden" name="error_scoring_json" class="error_scoring_json" value="{{ $vvsp->error_scoring }}">
                                    </div>

                                </div>
                            @endforeach

                            {{-- ── New (empty) repeater template ── --}}
                            <div data-repeater-item class="cp-repeater-item" style="display:none;">

                                <input type="hidden" name="sp_pm_id">

                                <div class="cp-repeater-header">
                                    <span class="cp-sub-label">New sub-parameter</span>
                                    <div class="cp-repeater-actions">
                                        <div data-repeater-delete class="cp-btn-delete">
                                            &#128465; Remove
                                        </div>
                                    </div>
                                </div>

                                <div class="cp-form-grid">
                                    <div class="cp-form-group" style="grid-column: 1 / -1;">
                                        <label>Sub-parameter name</label>
                                        <input type="text" name="sub_parameter" placeholder="Sub-parameter name">
                                    </div>
                                    <div class="cp-form-group">
                                        <label>Weightage</label>
                                        <input type="number" name="weight" placeholder="e.g. 10">
                                    </div>
                                    <div class="cp-form-group">
                                        <label>Details</label>
                                        <textarea name="details" rows="1" placeholder="Sub-parameter description or notes..."></textarea>
                                    </div>
                                </div>

                                <div class="cp-divider"></div>

                                <div class="cp-form-grid">
                                    @if (in_array(24, $allocatedmodule))
                                        <div class="cp-form-group">
                                            <label>Parameter type <span class="req">*</span></label>
                                            <select name="parameter_type" class="parameter_type">
                                                <option value="not_required" selected>Not required</option>
                                                <option value="internal">Internal</option>
                                                <option value="external">External</option>
                                            </select>
                                        </div>
                                    @endif
                                    <div class="cp-form-group">
                                        <label>Risk level <span class="req">*</span></label>
                                        <select name="risk_level" class="risk_level">
                                            <option value="" disabled selected>— Select risk level —</option>
                                            <option value="Low">Low</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">High</option>
                                        </select>
                                    </div>
                                    @if (in_array(27, $allocatedmodule) && count($compliance_experience) > 0)
                                        <div class="cp-form-group">
                                            <label>Compliance / Experience <span class="req">*</span></label>
                                            <select name="compliance_experience" class="compliance_experience">
                                                <option value="" disabled selected>— Select —</option>
                                                @foreach ($compliance_experience as $item)
                                                    <option value="{{ $item }}">{{ $item }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    @if (in_array(27, $allocatedmodule) && count($pillar) > 0)
                                        <div class="cp-form-group">
                                            <label>Pillar <span class="req">*</span></label>
                                            <select name="pillar">
                                                <option value="">— Select pillar —</option>
                                                @foreach ($pillar as $item)
                                                    <option value="{{ $item }}">{{ $item }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    @if (in_array(27, $allocatedmodule) && count($touch_points) > 0)
                                        <div class="cp-form-group">
                                            <label>Touch point <span class="req">*</span></label>
                                            <select name="touchpoint">
                                                <option value="">— Select touch point —</option>
                                                @foreach ($touch_points as $tp)
                                                    <option value="{{ $tp }}">{{ $tp }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>

                                <div class="cp-divider"></div>

                                <div class="cp-scoring-label">Scoring options</div>
                                <div class="cp-checkbox-row">
                                    <label class="cp-checkbox-pill"><input type="checkbox" name="s_pass" value="1"> Satisfactory</label>
                                    <label class="cp-checkbox-pill"><input type="checkbox" name="s_fail" value="1"> Unsatisfactory</label>
                                    <label class="cp-checkbox-pill"><input type="checkbox" name="s_critical" value="1"> Critical</label>
                                    <label class="cp-checkbox-pill"><input type="checkbox" name="s_na" value="1"> N/A</label>
                                    <label class="cp-checkbox-pill"><input type="checkbox" name="s_pwd" value="1"> PWD</label>
                                    <label class="cp-checkbox-pill"><input type="checkbox" name="s_per" value="1"> Percentage</label>
                                    <label class="cp-checkbox-pill"><input type="checkbox" name="s_collection" value="1"> OTP for Collection Manager</label>
                                    <label class="cp-checkbox-pill"><input type="checkbox" name="s_regulatory" value="1"> Is Regulatory type</label>
                                    <label class="cp-checkbox-pill"><input type="checkbox" name="error_scoring" class="error_scoring_checkbox" value="1"> Error Scoring</label>
                                </div>

                                <div class="cp-error-section error_scoring_section">
                                    <div class="cp-error-section-title">Error count scoring</div>
                                    <div class="cp-error-col-labels">
                                        <span>Error count</span>
                                        <span>Score</span>
                                        <span></span>
                                    </div>
                                    <div class="error_scoring_container"></div>
                                    <button type="button" class="cp-btn-add-row add_error_row">
                                        + Add error count
                                    </button>
                                    <input type="hidden" name="error_scoring_json" class="error_scoring_json">
                                </div>

                            </div>

                        </div>
                    </div>

                    {{-- Add sub-parameter button --}}
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <div data-repeater-create class="cp-add-sub">
                                + Add sub-parameter
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Footer ── --}}
            <div class="cp-card-footer">
                <button type="submit" class="cp-btn-primary">
                    &#10003; Update
                </button>
                <button type="reset" class="cp-btn-reset">
                    &#8635; Reset
                </button>
            </div>

            {!! Form::close() !!}
        </div>

    </div>
@endsection

@section('js')
    @include('shared.form_js')

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Toggle error scoring section
            document.addEventListener("change", function(e) {
                if (e.target.classList.contains("error_scoring_checkbox")) {
                    const parent = e.target.closest('[data-repeater-item]');
                    const section = parent.querySelector(".error_scoring_section");
                    const container = parent.querySelector(".error_scoring_container");

                    if (e.target.checked) {
                        section.style.display = "block";
                        if (container.children.length === 0) addRow(parent);
                    } else {
                        section.style.display = "none";
                        container.innerHTML = "";
                        updateJSON(parent);
                    }
                }
            });

            // Add / Remove row
            document.addEventListener("click", function(e) {
                if (e.target.classList.contains("add_error_row")) {
                    const parent = e.target.closest('[data-repeater-item]');
                    addRow(parent);
                }
                if (e.target.classList.contains("remove-error-row")) {
                    const parent = e.target.closest('[data-repeater-item]');
                    e.target.closest(".cp-error-row").remove();
                    reIndex(parent);
                    updateJSON(parent);
                }
            });

            // Input change → update JSON
            document.addEventListener("input", function(e) {
                if (e.target.classList.contains("error-score")) {
                    const parent = e.target.closest('[data-repeater-item]');
                    updateJSON(parent);
                }
            });

            function addRow(parent) {
                const container = parent.querySelector(".error_scoring_container");
                const count = container.children.length + 1;
                const row = document.createElement("div");
                row.classList.add("cp-error-row");
                row.innerHTML = `
                    <input type="number" class="error-count" value="${count}" readonly>
                    <input type="number" class="error-score" placeholder="Enter score">
                    <button type="button" class="cp-btn-remove-row remove-error-row">&times;</button>
                `;
                container.appendChild(row);
                updateJSON(parent);
            }

            function reIndex(parent) {
                parent.querySelectorAll(".error-count").forEach((c, i) => c.value = i + 1);
            }

            function updateJSON(parent) {
                const rows = parent.querySelectorAll(".cp-error-row");
                const jsonField = parent.querySelector(".error_scoring_json");
                let data = [];
                rows.forEach(row => {
                    data.push({
                        error_count: row.querySelector(".error-count").value,
                        score: row.querySelector(".error-score").value
                    });
                });
                jsonField.value = JSON.stringify(data);
            }

            // Initialize existing rows on page load
            document.querySelectorAll('[data-repeater-item]').forEach(parent => {
                updateJSON(parent);
            });

            // Reset new repeater items when added
            document.addEventListener('click', function(e) {
                if (e.target.matches('[data-repeater-create], [data-repeater-create] *')) {
                    setTimeout(() => {
                        const items = document.querySelectorAll('[data-repeater-item]');
                        const lastItem = items[items.length - 1];
                        if (!lastItem) return;

                        const checkbox = lastItem.querySelector('.error_scoring_checkbox');
                        if (checkbox) checkbox.checked = false;

                        const section = lastItem.querySelector('.error_scoring_section');
                        if (section) section.style.display = 'none';

                        const container = lastItem.querySelector('.error_scoring_container');
                        if (container) container.innerHTML = '';

                        const jsonField = lastItem.querySelector('.error_scoring_json');
                        if (jsonField) jsonField.value = '';
                    }, 200);
                }
            });
        });
    </script>
@endsection
