@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>{{ isset($assignment) ? 'Edit Legal Audit Assignment' : 'Assign Legal Audit' }}</h4>
        </div>

        <div class="card-body">
            <form method="POST"
                action="{{ isset($assignment) ? route('legal.audit.assign.update', $assignment->id) : route('legal.audit.assign.store') }}">
                @csrf

                <div class="form-group row">
                    <div class="col-md-6">
                        <label>Advocate</label>

                        @if (isset($assignment))
                            {{-- Show advocate but DO NOT allow editing --}}
                            <input type="text" class="form-control" value="{{ $assignment->advocate_name }}" readonly>

                            {{-- keep advocate_id for submit --}}
                            <input type="hidden" name="advocate_id" value="{{ $assignment->advocate_id }}">
                        @else
                            {{-- Create mode: selectable --}}
                            <select name="advocate_id" class="form-control" required>
                                <option value="">-- Select Advocate --</option>
                                @foreach ($advocates as $id => $name)
                                    <option value="{{ $id }}" {{ old('advocate_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>


                    <div class="col-md-6">
                        <label>Auditor</label>
                        <select name="auditor_id" class="form-control" required>
                            <option value="">-- Select Auditor --</option>
                            @foreach ($auditors as $id => $name)
                                <option value="{{ $id }}"
                                    {{ old('auditor_id', $assignment->auditor_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row mt-3">
                    <div class="col-md-6">
                        <label>Audit Date</label>
                        <input type="date" name="audit_date" class="form-control"
                            value="{{ old('audit_date', $assignment->audit_date ?? '') }}" required>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary btn-sm">
                        {{ isset($assignment) ? 'Update Assignment' : 'Assign Audit' }}
                    </button>
                    <a href="{{ route('legal.audit.assign.index') }}" class="btn btn-secondary btn-sm">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
