@extends('layouts.master')

@section('content')
<!DOCTYPE html>
<html>
<head>
    <title>Create Mapping</title>
</head>
<body>

<h2>Create Mapping</h2>

@if(session('success'))
    <p style="color:green;">
        {{ session('success') }}
    </p>
@endif

<form action="{{ url('/mapping/store') }}" method="POST">

    @csrf

    <div>
        <label>Location</label>
        <input type="text" name="location">
    </div>

    <br>

    <div>
        <label>Campus Type</label>
        <input type="text" name="campus_type">
    </div>

    <br>

    <div>
        <label>Pillar</label>
        <input type="text" name="pillar">
    </div>

    <br>

    <div>
        <label>Touch Points</label>
        <input type="text" name="touch_points">
    </div>

    <br>

    <div>
        <label>Checkpoint Parameters</label>
        <input type="text" name="checkpoint_parameters">
    </div>

    <br>

    <button type="submit">
        Save Mapping
    </button>

</form>

</body>
</html>
@endsection