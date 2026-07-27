@extends('layouts.master')

@section('title', '| Modules')

@section('sh-detail')
    create
@endsection

@section('content')

<div class="card">
        <div class="card-header">
            <strong>Create Module</strong> 
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
        <form action="{{ route('module_permissions.store') }}" method="POST">
             @include('module_permissions._form', ['buttonText' => 'Create'])
        </form>
        </div>
</div>
@endsection
