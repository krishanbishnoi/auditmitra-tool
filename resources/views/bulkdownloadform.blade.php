@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header text-white text-center fw-bold rounded-top-4"
                     style="background: linear-gradient(90deg, #007bff, #6610f2);">
                    Download Report PDF
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('audit.downloadBulkReports') }}" method="GET">
                        @csrf

                        {{-- Cycle Dropdown --}}
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-4 text-md-end">
                                <label for="cycle" class="form-label fw-semibold mb-0">Select Cycle</label>
                            </div>
                            <div class="col-md-8">
                                <select name="cycle" id="cycle" class="form-select rounded-3" required>
                                    <option value="">-- Select Cycle --</option>
                                    @foreach($cycles as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Sheet Type Dropdown --}}
                        <div class="row mb-4 align-items-center">
                            <div class="col-md-4 text-md-end">
                                <label for="sheet_type" class="form-label fw-semibold mb-0">Select Sheet Type</label>
                            </div>
                            <div class="col-md-8">
                                <select name="sheet_type" id="sheet_type" class="form-select rounded-3" required>
                                    <option value="">-- Select Sheet Type --</option>
                                    <option value="checksheet_pdf">Checksheet</option>
                                    <option value="audit_result_pdf">Audit Result</option>
                                    <option value="closure_pdf">Audit Closure</option>
                                </select>
                            </div>
                        </div>

                        {{-- Download Button Centered --}}
                        <div class="text-center">
                            <button type="submit" class="btn btn-success px-5 py-2 rounded-3 shadow-sm fw-semibold">
                                <i class="bi bi-download me-2"></i>Download PDF
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-footer bg-light text-center rounded-bottom-4">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Select cycle and sheet type to download your report.
                    </small>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
