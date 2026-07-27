@extends('layouts.master')

@section('content')

<style>
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

    .cp-page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }
    .cp-page-header h1 { font-size: 20px; font-weight: 700; color: var(--cp-gray-900); margin: 0 0 3px; }
    .cp-page-header p  { font-size: 13px; color: var(--cp-gray-600); margin: 0; }

    .cp-btn-back {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; font-weight: 500; padding: 7px 14px;
        border-radius: 8px; border: 1px solid var(--cp-primary-mid);
        background: var(--cp-primary-light); color: var(--cp-primary);
        text-decoration: none; transition: background .15s;
    }
    .cp-btn-back:hover { background: #e0e7ff; color: var(--cp-primary-dark); text-decoration: none; }

    .cp-card {
        background: #fff;
        border: 1px solid var(--cp-gray-200);
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
    }
    .cp-card-section { padding: 1.5rem; border-bottom: 1px solid var(--cp-gray-100); }
    .cp-card-section:last-of-type { border-bottom: none; }

    .cp-section-label {
        display: inline-flex; align-items: center;
        font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
        color: var(--cp-primary); background: var(--cp-primary-light);
        border: 1px solid var(--cp-primary-mid);
        border-radius: 20px; padding: 3px 10px; margin-bottom: 1.1rem;
    }
    .cp-section-label-amber {
        display: inline-flex; align-items: center;
        font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
        color: var(--cp-amber); background: var(--cp-amber-light);
        border: 1px solid var(--cp-amber-mid);
        border-radius: 20px; padding: 3px 10px; margin-bottom: 1.1rem;
    }

    .cp-form-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 1rem; }
    .cp-form-group:last-child { margin-bottom: 0; }
    .cp-form-group label { font-size: 13px; font-weight: 500; color: var(--cp-gray-800); }
    .cp-form-group label .req { color: var(--cp-danger); margin-left: 2px; }

    .cp-form-group select,
    .cp-form-group input[type="text"] {
        height: 36px; padding: 0 10px; font-size: 13px;
        color: var(--cp-gray-800); background: #fff;
        border: 1px solid var(--cp-gray-200); border-radius: 8px;
        width: 100%; appearance: none; -webkit-appearance: none;
        transition: border-color .15s, box-shadow .15s;
    }
    .cp-form-group select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 10px center; padding-right: 30px;
    }
    .cp-form-group select:focus,
    .cp-form-group input[type="text"]:focus {
        outline: none; border-color: var(--cp-primary);
        box-shadow: 0 0 0 3px rgba(99,102,241,.12);
    }

    /* Sub-parameter input rows */
    .cp-sub-fields-label {
        font-size: 13px; font-weight: 500; color: var(--cp-gray-800); margin-bottom: 6px;
    }
    .cp-sub-fields-label .req { color: var(--cp-danger); margin-left: 2px; }

    .cp-input-row {
        display: flex; gap: 8px; align-items: center; margin-bottom: 8px;
    }
    .cp-input-row input {
        flex: 1; height: 36px; padding: 0 10px; font-size: 13px;
        color: var(--cp-gray-800); background: #fff;
        border: 1px solid var(--cp-gray-200); border-radius: 8px;
        transition: border-color .15s, box-shadow .15s;
    }
    .cp-input-row input:focus {
        outline: none; border-color: var(--cp-primary);
        box-shadow: 0 0 0 3px rgba(99,102,241,.12);
    }
    .cp-btn-remove-field {
        width: 36px; height: 36px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        border: 1px solid var(--cp-danger-mid); border-radius: 8px;
        background: var(--cp-danger-light); color: var(--cp-danger);
        cursor: pointer; font-size: 14px; transition: background .15s;
    }
    .cp-btn-remove-field:hover { background: #fee2e2; }

    .cp-btn-add-another {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 12px; font-weight: 500;
        color: var(--cp-primary); border: 1px dashed var(--cp-primary-mid);
        border-radius: 8px; padding: 7px 14px;
        background: var(--cp-primary-light); cursor: pointer;
        margin-top: 4px; transition: background .15s; width: 100%;
        justify-content: center;
    }
    .cp-btn-add-another:hover { background: #e0e7ff; }

    .cp-card-footer {
        padding: 1.25rem 1.5rem;
        background: var(--cp-gray-50);
        border-top: 1px solid var(--cp-gray-200);
        display: flex; gap: 8px;
    }
    .cp-btn-primary {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 22px; font-size: 13px; font-weight: 600;
        border-radius: 8px; border: none;
        background: var(--cp-primary); color: #fff;
        cursor: pointer; transition: background .15s;
    }
    .cp-btn-primary:hover { background: var(--cp-primary-dark); }

    .cp-alert-success {
        background: var(--cp-success-light); border: 1px solid var(--cp-success-mid);
        color: var(--cp-success); padding: 10px 14px; border-radius: 8px;
        font-size: 13px; font-weight: 500; margin-bottom: 1rem;
    }

    /* Existing sub-parameters panel */
    .cp-existing-panel {
        background: var(--cp-gray-50);
        border: 1px solid var(--cp-gray-200);
        border-radius: 10px;
        padding: 1rem;
        min-height: 60px;
    }
    .cp-existing-placeholder {
        font-size: 13px; color: var(--cp-gray-400); text-align: center; padding: .5rem 0;
    }
    .cp-existing-item {
        display: flex; align-items: center; gap: 8px;
        padding: 7px 10px; border-radius: 8px;
        background: #fff; border: 1px solid var(--cp-gray-200);
        font-size: 13px; color: var(--cp-gray-800);
        margin-bottom: 6px;
    }
    .cp-existing-item:last-child { margin-bottom: 0; }
    .cp-existing-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: var(--cp-primary); flex-shrink: 0;
    }
    .cp-existing-loading { font-size: 13px; color: var(--cp-gray-400); text-align: center; padding: .5rem 0; }
    .cp-existing-error   { font-size: 13px; color: var(--cp-danger); text-align: center; padding: .5rem 0; }
    .cp-existing-empty   { font-size: 13px; color: var(--cp-gray-400); text-align: center; padding: .5rem 0; }
</style>

<div class="cp-page">

    <div class="cp-page-header">
        <div>
            <h1>Add sub-parameters</h1>
            <p>Select a parameter and add one or more sub-parameters to it</p>
        </div>
        {{-- <a href="{{ route('sub-parameters.index') }}" class="cp-btn-back">&#8592; All sub-parameters</a> --}}
    </div>

    @if(session('success'))
        <div class="cp-alert-success">&#10003; {{ session('success') }}</div>
    @endif

    <div class="cp-card">
        <form action="{{ route('sub-parameters.store') }}" method="POST">
            @csrf

            {{-- ── Parameter select ── --}}
            <div class="cp-card-section">
                <div class="cp-section-label">Parent parameter</div>

                <div class="cp-form-group">
                    <label for="parameter_id">Select parameter <span class="req">*</span></label>
                    <select name="parameter_id" id="parameter_id" required>
                        <option value="">— Choose parameter —</option>
                        @foreach($parameters as $parameter)
                            <option value="{{ $parameter->id }}">{{ $parameter->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- ── Sub-parameter inputs ── --}}
            <div class="cp-card-section">
                <div class="cp-section-label">Sub-parameters</div>

                <div class="cp-sub-fields-label">
                    Sub-parameter names <span class="req">*</span>
                </div>

                <div id="sub-parameter-wrapper">
                    <div class="cp-input-row">
                        <input type="text" name="sub_parameters[]"
                            placeholder="Enter sub-parameter name" required>
                        <button type="button" class="cp-btn-remove-field remove-field"
                            title="Remove">&times;</button>
                    </div>
                </div>

                <button type="button" id="add-sub-parameter" class="cp-btn-add-another">
                    + Add another sub-parameter
                </button>
            </div>

            {{-- ── Existing sub-parameters preview ── --}}
            <div class="cp-card-section">
                <div class="cp-section-label-amber">Existing sub-parameters</div>
                <div class="cp-existing-panel" id="existing-sub-parameters">
                    <div class="cp-existing-placeholder">
                        Select a parameter above to preview its existing sub-parameters.
                    </div>
                </div>
            </div>

            <div class="cp-card-footer">
                <button type="submit" class="cp-btn-primary">&#10003; Create sub-parameters</button>
            </div>

        </form>
    </div>

</div>

<script>
    // ── Load existing sub-parameters on parameter change ──
    const parameterSelect = document.getElementById('parameter_id');
    const subParamList    = document.getElementById('existing-sub-parameters');
    const getRoute        = `{{ route('get.subparameters', ':id') }}`;

    parameterSelect.addEventListener('change', function () {
        const paramId = this.value;

        if (!paramId) {
            subParamList.innerHTML = '<div class="cp-existing-placeholder">Select a parameter above to preview its existing sub-parameters.</div>';
            return;
        }

        subParamList.innerHTML = '<div class="cp-existing-loading">Loading...</div>';

        fetch(getRoute.replace(':id', paramId))
            .then(r => r.json())
            .then(data => {
                if (data.length === 0) {
                    subParamList.innerHTML = '<div class="cp-existing-empty">No sub-parameters found for this parameter.</div>';
                } else {
                    subParamList.innerHTML = data.map(item =>
                        `<div class="cp-existing-item">
                            <span class="cp-existing-dot"></span>
                            ${item.name}
                        </div>`
                    ).join('');
                }
            })
            .catch(() => {
                subParamList.innerHTML = '<div class="cp-existing-error">Error loading sub-parameters.</div>';
            });
    });

    // ── Add another field ──
    document.getElementById('add-sub-parameter').addEventListener('click', function () {
        const wrapper  = document.getElementById('sub-parameter-wrapper');
        const row      = document.createElement('div');
        row.className  = 'cp-input-row';
        row.innerHTML  = `
            <input type="text" name="sub_parameters[]"
                placeholder="Enter sub-parameter name" required>
            <button type="button" class="cp-btn-remove-field remove-field"
                title="Remove">&times;</button>
        `;
        wrapper.appendChild(row);
        row.querySelector('input').focus();
    });

    // ── Remove field ──
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-field')) {
            const wrapper = document.getElementById('sub-parameter-wrapper');
            // keep at least one row
            if (wrapper.querySelectorAll('.cp-input-row').length > 1) {
                e.target.closest('.cp-input-row').remove();
            }
        }
    });
</script>

@endsection