@extends('layouts.master')

@section('title', '| Edit Role')

@section('content')


    <div class='card'>
        <div class="card-header">
            <h1><i class='fa fa-key'></i> Edit Permission : {{$permission->name}}</h1>
            <hr>
        </div>
        <div class="card-body card-block ">
            {{ Form::model($permission, array('route' => array('permissions.update', Crypt::encrypt($permission->id)), 'method' => 'PUT')) }}

            <div class="form-group">
                {{ Form::label('name', 'Permission  Name') }}
                {{ Form::text('name', null, array('class' => 'form-control')) }}
            </div>

            {{ Form::submit('Edit', array('class' => 'btn btn-primary')) }}

            {{ Form::close() }}
        </div>
    </div>

@endsection