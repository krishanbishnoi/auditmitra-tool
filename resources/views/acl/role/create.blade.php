@extends('layouts.master')

@section('title', '| Add Role')

@section('content')

  <div class='card'>

    <div class="card-header"><strong>Add Role</strong>
    </div>
    <div class="card-body card-block ">
    {{ Form::open(array('url' => 'roles')) }}

    <div class="form-group">
      {{ Form::label('name', 'Name') }}
      {{ Form::text('name', null, array('class' => 'form-control')) }}
    </div>

    <h5><b>Assign Permissions</b></h5>

    <div class='form-group'>
      @foreach ($permissions as $permission)
        {{ Form::checkbox('permissions[]',  $permission->id ) }}
        {{ Form::label($permission->name, ucfirst($permission->name)) }}<br>

      @endforeach
    </div>

    {{ Form::submit('Add', array('class' => 'btn btn-primary')) }}

    {{ Form::close() }}

  </div>
  </div>
@endsection