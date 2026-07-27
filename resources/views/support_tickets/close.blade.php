@extends('layouts.master')

@section('title', '| Close Support Ticket')

@section('content')
<div class="container mt-4">
    <h2>Close Ticket #{{ $ticket->id }} - {{ $ticket->subject }}</h2>

    <!-- Display Validation Errors -->
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('support_tickets.close', $ticket->id) }}">
        @csrf
        <div class="mb-3">
            <label for="feedback" class="form-label">Optional Feedback before closing the ticket:</label>
            <textarea name="feedback" id="feedback" class="form-control" rows="5">{{ old('feedback') }}</textarea>
        </div>
        <a href="{{ route('support_tickets.index') }}" class="btn btn-secondary">Back to Tickets</a>
        <button type="submit" class="btn btn-primary">Submit & Close Ticket</button>
    </form>
</div>
@endsection
