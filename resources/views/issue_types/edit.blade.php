@extends('layouts.master')

@section('title', '| Edit Issue Type')

@section('sh-detail')
Edit Issue Type
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <strong>Edit Issue Type</strong>
    </div>

    <div class="card-body card-block">
       {!! Form::model($issueType, [
           'route' => ['issue_types.update', $issueType->id],
           'method' => 'PUT',
           'class' => 'form-horizontal',
           'role' => 'form',
           'data-toggle' => 'validator',
       ]) !!}

        <!-- Help Topic -->
        <div class="row form-group">
            <div class="col col-md-3"><label for="help_topic_id" class=" form-control-label">Help Topic</label></div>
            <div class="col-12 col-md-9">
                {!! Form::select('help_topic_id', $helpTopics->pluck('name', 'id'), $issueType->help_topic_id, ['class' => 'form-control', 'required' => true]) !!}
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
                <i class="fa fa-dot-circle-o"></i> Update
            </button>

            <a href="{{ route('issue_types.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-ban"></i> Cancel
            </a>
        </div>

        {!! Form::close() !!}
    </div>
</div>

@endsection
