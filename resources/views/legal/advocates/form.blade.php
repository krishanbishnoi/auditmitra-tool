@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>{{ isset($advocate) ? 'Edit Advocate' : 'Create Advocate' }}</h4>
    </div>

    <div class="card-body">
        <form method="POST"
              action="{{ isset($advocate)
                        ? route('legal.advocates.update', $advocate->id)
                        : route('legal.advocates.store') }}">
            @csrf

            <div class="form-group">
                <label>Advocate Name</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ old('name', $advocate->name ?? '') }}"
                       required>
            </div>

            <button type="submit" class="btn btn-primary">
                {{ isset($advocate) ? 'Update' : 'Create' }}
            </button>

            <a href="{{ route('legal.advocates.index') }}" class="btn btn-secondary">
                Back
            </a>
        </form>
    </div>
</div>
@endsection
