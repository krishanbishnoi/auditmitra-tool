@extends('layouts.master')

@section('title', '| Create Help Topic')

@section('sh-detail')
Create New Help Topic
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <strong>Create Help Topic</strong>
    </div>

    <div class="card-body card-block">
       {!! Form::open([
           'route' => 'help_topics.store',
           'class' => 'form-horizontal',
           'role' => 'form',
           'data-toggle' => 'validator',
       ]) !!}

        <!-- Help Topic Name -->
        <div class="row form-group">
            <div class="col col-md-3"><label for="name" class=" form-control-label">Help Topic Name</label></div>
            <div class="col-12 col-md-9">
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter Help Topic Name', 'required' => true]) !!}
                @if ($errors->has('name'))
                    <div class="alert alert-danger mt-2">
                        <strong>{{ $errors->first('name') }}</strong>
                    </div>
                @endif
            </div>
        </div>

        <!-- Submit and Reset Buttons -->
        <div class="card-footer">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa fa-dot-circle-o"></i> Create
            </button>
            
        </div>

        {!! Form::close() !!}
    </div>
</div>

@endsection
