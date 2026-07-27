@extends('layouts.master')

@section('title', '| Create CMS Page')

@section('sh-detail')
Create New CMS Page
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <strong>Create CMS Page</strong> form
            </div>
            <div class="card-body card-block">
                {!! Form::open(['route' => 'cms.store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'data-toggle' => 'validator']) !!}
                
                {{-- Optional: Error summary --}}
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="form-control-label">Page Name</label>
                            <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter Page Name" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title" class="form-control-label">Title</label>
                            <input id="title" type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Enter Title" required>
                            @error('title')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="content" class="form-control-label">Description</label>
                            <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                            @error('content')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="file" class="form-control-label">Upload Image/File</label>
                            <input id="file" type="file" name="file" class="form-control-file @error('file') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            @error('file')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-sm d-inline-flex align-items-center justify-content-center px-3 py-2" style="min-width: 110px;">
                        <i class="fa fa-dot-circle-o me-1"></i> Submit
                    </button>
                    <a href="{{ route('cms.index') }}" class="btn btn-secondary btn-sm d-inline-flex align-items-center justify-content-center px-3 py-2" style="min-width: 110px;">
                        <i class="fa fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    let editor;

    ClassicEditor
        .create(document.querySelector('#content'))
        .then(newEditor => {
            editor = newEditor;
        })
        .catch(error => {
            console.error(error);
        });

    // Custom validation for CKEditor content
    document.querySelector('form').addEventListener('submit', function (e) {
        const content = editor.getData().trim();
        if (!content) {
            alert('The description field is required.');
            e.preventDefault(); // stop form submission
        }
    });
</script>
@endsection
