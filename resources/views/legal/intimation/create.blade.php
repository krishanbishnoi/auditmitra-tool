@extends('layouts.master')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Legal Audit Intimation</h5>
                </div>

                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/legal/legal-audit/intimation') }}">
                        @csrf

                        {{-- Advocate + Audit --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Advocate & Assigned Audit</label>
                            <select name="legal_audit_assign_id" class="form-select" required>
                                <option value="">Select Advocate & Audit</option>
                                @foreach ($audits as $audit)
                                    <option value="{{ $audit->legal_audit_assign_id }}"
                                        data-advocate="{{ $audit->advocate_id }}">
                                        {{ $audit->advocate_name }} (Audit ID: {{ $audit->legal_audit_assign_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <input type="hidden" name="advocate_id" id="advocate_id">

                        {{-- Advocate Email --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Advocate Email</label>
                            <input type="email"
                                   name="advocate_email"
                                   class="form-control"
                                   placeholder="Enter advocate email"
                                   required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Audit Date</label>
                                <input type="date"
                                       name="audit_date"
                                       class="form-control"
                                       required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Auditor Name</label>
                                <input type="text"
                                       name="auditor_name"
                                       class="form-control"
                                       placeholder="Enter auditor name"
                                       required>
                            </div>
                        </div>

                        <div class="text-end">
                            <button class="btn btn-primary px-4">
                                Send Intimation
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.querySelector('select[name="legal_audit_assign_id"]')
        .addEventListener('change', function () {
            let option = this.options[this.selectedIndex];
            document.getElementById('advocate_id').value =
                option.getAttribute('data-advocate');
        });
</script>
@endsection
