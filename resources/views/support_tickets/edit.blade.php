@extends('layouts.master')

@section('title', '| Edit Support Ticket')

@section('sh-detail')
Edit Support Ticket
@endsection

@section('content')

<div class="card">

    <div class="card-header">
        <strong>Edit Support Ticket</strong>
    </div>

    <div class="card-body card-block">
        {!! Form::model($supportTicket, [
            'route' => ['support_tickets.update', $supportTicket->id],
            'method' => 'PUT',
            'class' => 'form-horizontal',
            'role' => 'form',
            'data-toggle' => 'validator'
        ]) !!}

        <!-- Help Topic -->
        <div class="row form-group">
            <div class="col col-md-3"><label for="help_topic" class="form-control-label">Help Topic</label></div>
            <div class="col-12 col-md-9">
                <input type="text" id="help_topic" name="help_topic" value="{{ old('help_topic', $supportTicket->help_topic) }}" placeholder="Help Topic" class="form-control" required>
            </div>
        </div>

        <!-- Issue Type -->
        <div class="row form-group">
            <div class="col col-md-3"><label for="issue_type" class="form-control-label">Issue Type</label></div>
            <div class="col-12 col-md-9">
                <input type="text" id="issue_type" name="issue_type" value="{{ old('issue_type', $supportTicket->issue_type) }}" placeholder="Issue Type" class="form-control" required>
            </div>
        </div>

        <!-- Subject -->
        <div class="row form-group">
            <div class="col col-md-3"><label for="subject" class="form-control-label">Subject</label></div>
            <div class="col-12 col-md-9">
                <input type="text" id="subject" name="subject" value="{{ old('subject', $supportTicket->subject) }}" placeholder="Subject" class="form-control" required>
            </div>
        </div>

        <!-- Priority -->
        <div class="row form-group">
            <div class="col col-md-3"><label for="priority" class="form-control-label">Priority</label></div>
            <div class="col-12 col-md-9">
                <select name="priority" id="priority" class="form-control" required>
                    <option value="Low" {{ old('priority', $supportTicket->priority) == 'Low' ? 'selected' : '' }}>Low</option>
                    <option value="Medium" {{ old('priority', $supportTicket->priority) == 'Medium' ? 'selected' : '' }}>Medium</option>
                    <option value="High" {{ old('priority', $supportTicket->priority) == 'High' ? 'selected' : '' }}>High</option>
                </select>
            </div>
        </div>

        <!-- Description -->
        <div class="row form-group">
            <div class="col col-md-3"><label for="description" class="form-control-label">Description</label></div>
            <div class="col-12 col-md-9">
                <textarea id="description" name="description" placeholder="Describe the issue" class="form-control" required>{{ old('description', $supportTicket->description) }}</textarea>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="card-footer">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa fa-dot-circle-o"></i> Update
            </button>

            <a href="{{ route('support_tickets.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left"></i> Cancel
            </a>
        </div>

        {!! Form::close() !!}
    </div>

</div>

@endsection
