@extends('layouts.master')

@section('title', '| Add Role')

@section('content')

  <div class='card'>

    <div class="card-header"><strong>Add Permissions</strong>
    </div>
    <div class="card-body card-block ">
    {{ Form::open(array('url' => 'permissions')) }}

    <div class="form-group">
      {{ Form::label('name', 'Name') }}
      {{ Form::text('name', '', array('class' => 'form-control')) }}
    </div>


    <div class='form-group'>
      @if(!$roles->isEmpty())
      <h4><strong>Assign Permission to Roles</strong></h4>

      @foreach ($roles as $role)
        {{ Form::checkbox('roles[]',  $role->id ) }}
        {{ Form::label($role->name, ucfirst($role->name)) }}<br>

      @endforeach
      @endif
    </div>

    {{ Form::submit('Add', array('class' => 'btn btn-primary')) }}

    {{ Form::close() }}

  </div>
  </div>
@endsection
