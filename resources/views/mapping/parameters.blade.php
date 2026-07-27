@extends('layouts.master')

@section('content')
    <h3>Assign Parameters</h3>

    <form method="POST" action="/mapping/{{ $mapping->id }}/parameters/store">

        @csrf

        <select name="parameters[]" multiple class="form-control" style="height:400px;">

            @foreach ($parameters as $parameter)
                <option value="{{ $parameter->id }}"
                    {{ in_array($parameter->id, $assignedParameters) ? 'selected' : '' }}>

                    {{ $parameter->parameter }}
                    ({{ $parameter->id }})
                </option>
            @endforeach

        </select>

        <br>

        <button type="submit" class="btn btn-primary">

            Save Parameters

        </button>

    </form>
@endsection
