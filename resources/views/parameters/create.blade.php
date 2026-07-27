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
        --cp-gray-50:       #f9fafb;
        --cp-gray-100:      #f3f4f6;
        --cp-gray-200:      #e5e7eb;
        --cp-gray-400:      #9ca3af;
        --cp-gray-600:      #6b7280;
        --cp-gray-800:      #374151;
        --cp-gray-900:      #1a1a2e;
    }

    /* .cp-page { max-width: 960px; margin: 0 auto; padding: 1.5rem 1rem; } */

    .cp-page-header { margin-bottom: 1.5rem; }
    .cp-page-header h1 { font-size: 20px; font-weight: 700; color: var(--cp-gray-900); margin: 0 0 3px; }
    .cp-page-header p  { font-size: 13px; color: var(--cp-gray-600); margin: 0; }

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
        display: inline-flex;
        align-items: center;
        font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
        color: var(--cp-primary);
        background: var(--cp-primary-light);
        border: 1px solid var(--cp-primary-mid);
        border-radius: 20px;
        padding: 3px 10px;
        margin-bottom: 1.1rem;
    }
    .cp-section-label-green {
        display: inline-flex;
        align-items: center;
        font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
        color: var(--cp-success);
        background: var(--cp-success-light);
        border: 1px solid var(--cp-success-mid);
        border-radius: 20px;
        padding: 3px 10px;
        margin-bottom: 1.1rem;
    }

    .cp-form-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 0; }
    .cp-form-group label { font-size: 13px; font-weight: 500; color: var(--cp-gray-800); }
    .cp-form-group label .req { color: var(--cp-danger); margin-left: 2px; }
    .cp-form-group input {
        height: 36px; padding: 0 10px; font-size: 13px;
        color: var(--cp-gray-800); background: #fff;
        border: 1px solid var(--cp-gray-200); border-radius: 8px;
        transition: border-color .15s, box-shadow .15s; width: 100%;
    }
    .cp-form-group input:focus {
        outline: none; border-color: var(--cp-primary);
        box-shadow: 0 0 0 3px rgba(99,102,241,.12);
    }
    .cp-form-group .cp-error { font-size: 12px; color: var(--cp-danger); }

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

    .cp-btn-cancel {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 18px; font-size: 13px; font-weight: 500;
        border-radius: 8px; border: 1px solid var(--cp-gray-200);
        background: #fff; color: var(--cp-gray-600);
        text-decoration: none; transition: background .15s;
    }
    .cp-btn-cancel:hover { background: var(--cp-gray-100); color: var(--cp-gray-800); text-decoration: none; }

    .cp-alert-success {
        background: var(--cp-success-light); border: 1px solid var(--cp-success-mid);
        color: var(--cp-success); padding: 10px 14px; border-radius: 8px;
        font-size: 13px; font-weight: 500; margin-bottom: 1rem;
    }

    /* Table */
    .cp-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .cp-table thead tr { background: var(--cp-gray-50); border-bottom: 1px solid var(--cp-gray-200); }
    .cp-table thead th {
        padding: 10px 14px; font-weight: 600; color: var(--cp-gray-600);
        text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em;
    }
    .cp-table tbody tr { border-bottom: 1px solid var(--cp-gray-100); transition: background .1s; }
    .cp-table tbody tr:hover { background: var(--cp-gray-50); }
    .cp-table tbody tr:last-child { border-bottom: none; }
    .cp-table tbody td { padding: 10px 14px; color: var(--cp-gray-800); vertical-align: middle; }

    .cp-badge-gray {
        display: inline-flex; align-items: center;
        padding: 2px 8px; border-radius: 20px;
        font-size: 11px; font-weight: 600;
        background: var(--cp-gray-100); color: var(--cp-gray-600);
        border: 1px solid var(--cp-gray-200);
    }

    .cp-actions { display: flex; gap: 6px; flex-wrap: wrap; }
    .cp-action-btn {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 12px; font-weight: 500; padding: 4px 10px;
        border-radius: 6px; border: 1px solid; cursor: pointer;
        text-decoration: none; transition: background .15s; white-space: nowrap;
        background: transparent;
    }
    .cp-action-btn-edit   { color: var(--cp-primary); border-color: var(--cp-primary-mid); background: var(--cp-primary-light); }
    .cp-action-btn-edit:hover { background: #e0e7ff; color: var(--cp-primary); text-decoration: none; }
    .cp-action-btn-danger { color: var(--cp-danger); border-color: var(--cp-danger-mid); background: var(--cp-danger-light); }
    .cp-action-btn-danger:hover { background: #fee2e2; color: var(--cp-danger); text-decoration: none; }
    .cp-action-btn-success{ color: var(--cp-success); border-color: var(--cp-success-mid); background: var(--cp-success-light); }
    .cp-action-btn-success:hover{ background: #d1fae5; color: var(--cp-success); text-decoration: none; }

    .cp-empty { text-align: center; padding: 2rem; color: var(--cp-gray-400); font-size: 13px; }
</style>

<div class="cp-page">

    <div class="cp-page-header">
        <h1>{{ isset($editParam) ? 'Edit parameter' : 'Add parameter' }}</h1>
        <p>Manage parameters used across QM sheets</p>
    </div>

    @if(session('success'))
        <div class="cp-alert-success">&#10003; {{ session('success') }}</div>
    @endif

    {{-- ── Form card ── --}}
    <div class="cp-card">
        <form method="POST" action="{{ isset($editParam) ? route('parameters.update', $editParam->id) : route('parameters.store') }}">
            @csrf
            @if(isset($editParam)) @method('PUT') @endif

            <div class="cp-card-section">
                <div class="cp-section-label">{{ isset($editParam) ? 'Edit parameter' : 'New parameter' }}</div>

                <div class="cp-form-group">
                    <label for="name">Parameter name <span class="req">*</span></label>
                    <input type="text" name="name" id="name" required
                        placeholder="e.g. Communication"
                        value="{{ old('name', $editParam->name ?? '') }}">
                    @error('name')
                        <span class="cp-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="cp-card-footer">
                <button type="submit" class="cp-btn-primary">
                    &#10003; {{ isset($editParam) ? 'Update' : 'Save' }}
                </button>
                @if(isset($editParam))
                    <a href="{{ route('parameters.create') }}" class="cp-btn-cancel">&#8592; Cancel</a>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Table card ── --}}
    <div class="cp-card">
        <div class="cp-card-section">
            <div class="cp-section-label-green">Existing parameters</div>

            <table class="cp-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Parameter name</th>
                        <th>Created at</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parameters as $index => $param)
                        <tr>
                            <td><span class="cp-badge-gray">{{ $index + 1 }}</span></td>
                            <td>{{ $param->name }}</td>
                            <td>{{ $param->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="cp-actions">
                                    <a href="{{ route('parameters.edit', $param->id) }}"
                                        class="cp-action-btn cp-action-btn-edit">&#9998; Edit</a>

                                    <form method="POST" action="{{ route('parameters.destroy', $param->id) }}"
                                        style="display:inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="cp-action-btn cp-action-btn-danger"
                                            onclick="if(confirm('Are you sure you want to delete this parameter?')) this.closest('form').submit()">
                                            &#128465; Delete
                                        </button>
                                    </form>

                                    <a href="{{ route('sub-parameters.create') }}"
                                        class="cp-action-btn cp-action-btn-success">+ Sub-parameter</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="cp-empty">No parameters found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection