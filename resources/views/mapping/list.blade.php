@extends('layouts.master')

@section('content')

<h3>Mapping List</h3>

<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Location</th>
        <th>Campus Type</th>
        <th>Brand</th>
        <th>Pillar</th>
        <th>Touch Points</th>
        <th>Compliance/Experience</th>
        <th>Action</th>
    </tr>

    @foreach($mappings as $row)

    <tr>

        <td>{{ $row->id }}</td>

        <td>{{ $row->location }}</td>

        <td>{{ $row->campus_type }}</td>

        <td>
            @if($row->campus_type == 'Brand')
                {{ $row->brand }}
            @else
                -
            @endif
        </td>

        <td>{{ $row->pillar }}</td>

        <td>{{ $row->touch_points }}</td>

        <td>{{ $row->compliance_experience }}</td>

        <td>

            <a href="/mapping/{{ $row->id }}/parameters">
                Assign Parameters
            </a>

        </td>

    </tr>

    @endforeach

</table>

@endsection