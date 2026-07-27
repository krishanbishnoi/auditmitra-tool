<!-- resources/views/support_tickets/show.blade.php -->

@extends('layouts.master')

@section('title', '| Support Ticket Details')

@section('sh-detail')
    View Support Ticket
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <strong>Support Ticket Details</strong>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card-body card-block">
        <div class="row">
            <div class="col col-md-6">
                <p><strong>ID:</strong> {{ $supportTicket->id }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col col-md-6">
                <p><strong>Help Topic:</strong> {{ $supportTicket->help_topic }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col col-md-6">
                <p><strong>Issue Type:</strong> {{ $supportTicket->issue_type }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col col-md-6">
                <p><strong>Subject:</strong> {{ $supportTicket->subject }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col col-md-6">
                <p><strong>Priority:</strong> {{ $supportTicket->priority }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col col-md-6">
                <p><strong>Description:</strong> {!!$supportTicket->description!!}</p>
            </div>
        </div>
        <div class="row">
            <div class="col col-md-6">
                <p><strong>Feedback:</strong> {{ $supportTicket->closure_feedback }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col col-md-6">
                <p><strong>Created At:</strong> {{ $supportTicket->created_at }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col col-md-6">
                <p><strong>Updated At:</strong> {{ $supportTicket->updated_at }}</p>
            </div>
        </div>
    </div>
    
    <div class="card-body card-block">
    <div class="row">
    <div class="col-md-6">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                Feedback
            </div>
            <div class="card-body">
                <p class="card-text">{{ $supportTicket->closure_feedback ?? 'No feedback provided.' }}</p>
            </div>
        </div>
    </div>
</div>
</div>

    <a href="{{ route('support_tickets.index') }}" class="btn btn-xs btn-info" title="Back">
        Back to Ticket List
    </a>
</div>
@endsection
