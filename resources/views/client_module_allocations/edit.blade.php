@extends('layouts.master')

@section('title', '| Modules')

@section('sh-detail')
    Edit
@endsection

@section('content')

<div class="card">
        <div class="card-header">
            <strong>Edit Module</strong> 
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
        <form action="{{ route('module_permissions.update', $modulePermission->id) }}" method="POST">
        @method('PUT')
             @include('module_permissions._form', ['buttonText' => 'Update'])
        </form>
        </div>

    </div>
@endsection