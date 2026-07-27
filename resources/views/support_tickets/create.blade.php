@extends('layouts.master')

@section('title', '| Create Support Ticket')

@section('sh-detail')
Create New Support Ticket
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <strong>Create Support Ticket</strong>
    </div>

    <div class="card-body card-block">
       {!! Form::open([
           'route' => 'support_tickets.store',
           'class' => 'form-horizontal',
           'role' => 'form',
           'data-toggle' => 'validator',
           'files' => true  // <-- for file upload if necessary
       ]) !!}

        <!-- Help Topic -->
        <div class="row form-group">
            <div class="col col-md-3"><label for="help_topic" class=" form-control-label">Help Topic</label></div>
            <div class="col-12 col-md-9">
                <select name="help_topic" id="help_topic" class="form-control" required>
                    <option value="">Select Help Topic</option>
                    @foreach($helpTopics as $helpTopic)
                        <option value="{{ $helpTopic->id }}" {{ old('help_topic') == $helpTopic->id ? 'selected' : '' }}>{{ $helpTopic->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Issue Type -->
        <div class="row form-group">
            <div class="col col-md-3"><label for="issue_type" class=" form-control-label">Issue Type</label></div>
            <div class="col-12 col-md-9">
                <select name="issue_type" id="issue_type" class="form-control" required>
                    <option value="">Select Issue Type</option>
                </select>
            </div>
        </div>

        <!-- Subject -->
        <div class="row form-group">
            <div class="col col-md-3"><label for="subject" class=" form-control-label">Subject</label></div>
            <div class="col-12 col-md-9">
                <input type="text" id="subject" name="subject" placeholder="Subject" class="form-control" value="{{ old('subject') }}" required>
            </div>
        </div>

        <!-- Priority -->
        <div class="row form-group">
            <div class="col col-md-3"><label for="priority" class=" form-control-label">Priority</label></div>
            <div class="col-12 col-md-9">
                <select name="priority" id="priority" class="form-control" required>
                    <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                    <option value="Medium" {{ old('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                    <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                </select>
            </div>
        </div>

        <!-- Description -->
        <div class="row form-group">
            <div class="col col-md-3"><label for="description" class=" form-control-label">Description</label></div>
            <div class="col-12 col-md-9">
                <textarea id="description" name="description" placeholder="Describe the issue" class="form-control">{{ old('description') }}</textarea>
            </div>
        </div>

        <!-- Submit and Reset Buttons -->
        <div class="card-footer">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa fa-dot-circle-o"></i> Create
            </button>

            <button type="reset" class="btn btn-danger btn-sm">
                <i class="fa fa-ban"></i> Reset
            </button>
        </div>

        {!! Form::close() !!}
    </div>
</div>

@endsection

@section('js')

<!-- CKEditor 5 CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/38.1.0/classic/ckeditor.js"></script>

<!-- jQuery (necessary for AJAX) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let editorInstance;

    jQuery(function() {
        // Initialize CKEditor
        ClassicEditor
            .create(document.querySelector('#description'))
            .then(editor => {
                editorInstance = editor;
            })
            .catch(error => {
                console.error(error);
            });

        // Update Issue Types based on selected Help Topic using AJAX
        $('#help_topic').change(function() {
            var helpTopicId = $(this).val();

            // Clear previous issue types
            $('#issue_type').empty();
            $('#issue_type').append('<option value="">Select Issue Type</option>'); // Default option

            if (helpTopicId) {
                $.ajax({
                    url: '{{ route("get.issue_types") }}', // Route to fetch issue types
                    method: 'GET',
                    data: { help_topic_id: helpTopicId },
                    success: function(response) {
                        // Populate issue types dropdown
                        $.each(response.issue_types, function(index, issueType) {
                            $('#issue_type').append('<option value="' + issueType.id + '">' + issueType.name + '</option>');
                        });
                    }
                });
            }
        });
    });
</script>

@endsection
