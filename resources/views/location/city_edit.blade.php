@extends('layouts.master')

@section('title', '| Edit City')

@section('content')
<div class="card">
    <div class="card-header">
        <strong>Edit City</strong>
        <a href="{{ route('location.city_view') }}" class="btn btn-sm btn-secondary float-right">Back to List</a>
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

        <form action="{{ route('location.city_update', Crypt::encrypt($city->id)) }}" method="POST" class="form-horizontal">
            @csrf
            @method('PUT')

            <div class="row">
               

                <div class="col col-md-4">
                    <div class="form-group">
                        <label for="state" class="form-control-label">State</label>
                        <select name="state" id="state" class="form-control" required>
                            <option value="">Choose State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" {{ old('state', $city->state_id) == $state->id ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col col-md-4">
                    <div class="form-group">
                        <label for="city" class="form-control-label">City Name</label>
                        <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $city->name) }}" required>
                    </div>
                </div>
            </div>

            
               <button type="submit" class="btn btn-primary">Save Changes</button>
            
        </form>
    </div>
</div>
@endsection
