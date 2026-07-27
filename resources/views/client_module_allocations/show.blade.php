@extends('layouts.master')

@section('title', '| Modules')

@section('sh-detail')
    View
@endsection

@section('content')
<div class="card">
        <div class="card-header">
            <strong>Module Details</strong> 
        </div>
        @if ($errors->any())
        <div>
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
                <p><strong>ID:</strong> {{ $modulePermission->id }}</p>
                </div>
            </div>
            <div class="row">            
                <div class="col col-md-6">
                <p><strong>Module Name:</strong> {{ $modulePermission->module_name }}</p>
                </div>
            </div>
            <div class="row">            
                <div class="col col-md-6">
                <p><strong>Created At:</strong> {{ $modulePermission->created_at }}</p>
                </div>
            </div>
            <div class="row">            
                <div class="col col-md-6">
                <p><strong>Updated At:</strong> {{ $modulePermission->updated_at }}</p>
                </div>
            </div>
        </div>
        <a href="{{ route('module_permissions.index') }}" class="btn btn-xs btn-info" title="Back">
            Back to List
        </a>
</div>

@endsection
