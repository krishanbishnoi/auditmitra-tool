@extends('layouts.master')

@section('title', 'Client Wise Quality Auditor Mapping')

@section('content')

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">

            <div class="card">

                <div class="card-header">
                    <h4 class="mb-0">Client Wise Quality Auditor Mapping</h4>
                </div>

                <form action="{{ route('masterqa.save') }}" method="POST">
                    @csrf

                    <div class="card-body">

                        <div class="alert alert-info">
                            <strong>Master QA:</strong> {{ $masterQA->name }}
                        </div>

                        <input type="hidden" name="masterqa_id" value="{{ $id }}">

                        @foreach($users as $client)

                            <div class="row mb-4 align-items-center">

                                <div class="col-md-4">
                                    <label class="font-weight-bold mb-0">
                                        {{ $client->name }}
                                    </label>
                                </div>

                                <div class="col-md-8">
                                    <select class="form-control select2"
                                            name="quality_auditor[{{ $client->id }}]">

                                        <option value="">Select Quality Auditor</option>

                                        @forelse($client->quality_auditors as $auditor)

                                            <option value="{{ $auditor->id }}"
                                                {{ (isset($selectedAuditors[$client->id]) && $selectedAuditors[$client->id] == $auditor->id) ? 'selected' : '' }}>
                                                {{ $auditor->name }}
                                            </option>

                                        @empty

                                            <option value="">No Quality Auditor Found</option>

                                        @endforelse

                                    </select>
                                </div>

                            </div>

                            <hr>

                        @endforeach

                    </div>

                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success">
                            Save Mapping
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>

</div>

@endsection

@section('js')

<script>
$(document).ready(function () {

    $('.select2').select2({
        width: '100%'
    });

});
</script>

@endsection