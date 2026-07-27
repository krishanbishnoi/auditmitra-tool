@extends('layouts.master')

@section('title', '| Change Password')

@section('sh-detail')
Change Password
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">

        <!--begin::Portlet-->
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Change Password for {{ auth()->user()->name }}
                    </h3>
                </div>
            </div>

            <!--begin::Form-->
            <form method="POST" action="{{ route('change-password') }}" accept-charset="UTF-8" class="kt-form" role="form" data-toggle="validator">
                @csrf

                <div class="kt-portlet__body">

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="form-group">
                        <label>Current Password *</label>
                        <input type="password" name="current_password" class="form-control" required>
                        
                    </div>
                    

                    <div class="form-group">
                        <label>New Password *</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Confirm New Password *</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                </div>

                <div class="kt-portlet__foot">
                    <div class="kt-form__actions">
                        <button type="submit" class="btn btn-primary">Change Password</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                    
                </div>
            </form>
            <!--end::Form-->
        </div>
        <!--end::Portlet-->

    </div>
</div>
@endsection
