@extends('layouts.master')

@section('title', '| Create Issue Type')

@section('sh-detail')
Create New Issue Type
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <strong>Create Issue Type</strong>
    </div>

    <div class="card-body card-block">
       {!! Form::open([
           'route' => 'issue_types.store',
           'class' => 'form-horizontal',
           'role' => 'form',
           'data-toggle' => 'validator',
       ]) !!}

        <!-- Help Topic -->
        <div class="row form-group">
            <div class="col col-md-3"><label for="help_topic_id" class=" form-control-label">Help Topic</label></div>
            <div class="col-12 col-md-9">
                {!! Form::select('help_topic_id', $helpTopics->pluck('name', 'id'), null, ['class' => 'form-control', 'required' => true]) !!}
                @if ($errors->has('help_topic_id'))
                    <div class="alert alert-danger mt-2">
                        <strong>{{ $errors->first('help_topic_id') }}</strong>
                    </div>
                @endif
            </div>
        </div>

        <!-- Issue Type Name -->
        <div class="row form-group">
            <div class="col col-md-3"><label for="name" class=" form-control-label">Issue Type Name</label></div>
            <div class="col-12 col-md-9">
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter Issue Type Name', 'required' => true]) !!}
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

@section('js')
<!-- Include any necessary scripts here if needed -->
@endsection
