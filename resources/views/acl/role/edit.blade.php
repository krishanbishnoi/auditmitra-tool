@extends('layouts.master')

@section('title', '| Edit Role')

@section('content')


    <div class='card'>
        <div class="card-header">
            <h1><i class='fa fa-key'></i> Edit Role: {{$role->name}}</h1>
            <hr>
        </div>
        <div class="card-body card-block ">
            {{ Form::model($role, array('route' => array('roles.update',Crypt::encrypt($role->id) ), 'method' => 'PUT')) }}

            <div class="form-group">
                {{ Form::label('name', 'Role Name') }}
                {{ Form::text('name', null, array('class' => 'form-control')) }}
            </div>

            <h5><b>Assign Permissions</b></h5>
            @foreach ($permissions as $permission)

                {{Form::checkbox('permissions[]',  $permission->id, $role->permissions ) }}
                {{Form::label($permission->name, ucfirst($permission->name)) }}<br>

            @endforeach
            <br>
            {{ Form::submit('Edit', array('class' => 'btn btn-primary')) }}

            {{ Form::close() }}
        </div>
    </div>

@endsection