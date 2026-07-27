@extends('layouts.master')

@section('title', '| Edit State')

@section('content')
<div class="card">
    <div class="card-header">
        <strong>Edit State</strong>
        <a href="{{ route('location.state_view') }}" class="btn btn-sm btn-secondary float-right">Back to List</a>
    </div>

    <div class="card-body card-block">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('location.updateState', $state->id) }}" method="POST" class="form-horizontal">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col col-md-4">
                    <div class="form-group">
                        <label for="region_id" class="form-control-label">Region</label>
                        <select name="region_id" id="region_id" class="form-control" required>
                            <option value="">Choose Region</option>
                            @foreach ($regions as $region)
                                <option value="{{ $region->id }}" {{ $region->id == $state->region_id ? 'selected' : '' }}>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col col-md-4">
                    <div class="form-group">
                        <label for="state" class="form-control-label">State Name</label>
                        <input type="text" id="state" name="state" class="form-control" value="{{ old('state', $state->name) }}" required>
                    </div>
                </div>
            </div>

           <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>
@endsection
