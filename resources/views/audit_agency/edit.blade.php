@extends('layouts.master')

@section('title', '| Audit Agency Edit')

@section('sh-detail')
    Edit New
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <strong>Edit Audit {{$agencyLabel}} Form</strong> 
            </div>
        <div class="card-body card-block">
            
            {!! Form::model($data,
                      array(
                      'method' => 'PATCH',
                        'url' =>'audit_agency/'.Crypt::encrypt($data->id),
                        'class' => 'kt-form',
                        'data-toggle'=>"validator")
                      ) !!}

            <div class="row form-group">
                <div class="col col-md-6">
                    <label for="text-input" class="form-control-label font-weight-bold">Name</label>
                    <input type="text" id="text-input" name="name" placeholder="{{$agencyLabel}} Name" class="form-control" value="{{$data->name}}" required>
                </div>
                <div class="col col-md-6">
                    <label for="email-input" class="form-control-label font-weight-bold">Email</label>
                    <input type="email" id="email-input" value="{{$data->email}}" name="email" placeholder="Enter Email" class="form-control" required>
                </div>
            </div>

            <div class="row form-group">
                <div class="col col-md-6">
                    <label for="mobile-input" class="form-control-label font-weight-bold">Contact</label>
                    <input type="text" id="mobile-input" name="mobile" placeholder="Enter Mobile" class="form-control" value="{{$data->mobile}}" required>
                </div>
                <div class="col col-md-6">
                    <label for="agency_admin-input" class="form-control-label font-weight-bold">Admin Name</label>
                    <input type="text" id="agency_admin-input" name="agency_admin" placeholder="{{$agencyLabel}} Admin Name" class="form-control" value="{{$data->agency_admin}}">
                </div>
            </div>

            <div class="row form-group">
                <div class="col col-md-6">
                    <label for="agency_admin_email_one-input" class="form-control-label font-weight-bold">Admin Email - First</label>
                    <input type="text" id="agency_admin_email_one-input" name="agency_admin_email_one" placeholder="{{$agencyLabel}} Admin Email - First" class="form-control" value="{{$data->agency_admin_email_one}}">
                </div>

                <div class="col col-md-6">
                    <label for="agency_admin_email_two-input" class="form-control-label font-weight-bold">Admin Email - Second (If any)</label>
                    <input type="text" id="agency_admin_email_two-input" name="agency_admin_email_two" placeholder="{{$agencyLabel}} Admin Email - Second" class="form-control" value="{{$data->agency_admin_email_two}}">
                </div>
            </div>

            <input type="hidden" name="old_password" value="{{ $data->password }}">

            <div class="row form-group">
                <div class="col col-md-6">
                    <label for="password-input" class="form-control-label font-weight-bold">New Password (Leave blank to keep current)</label>
                    <input type="password" id="password-input" name="password" placeholder="New Password (Min 6 Digits)" class="form-control">
                    <input type="checkbox" id="show-password"> Show New Password
                </div>
            </div>


            <div class="col col-md-6" style="display:none;">
                <label for="multiple-select" class="form-control-label">Select Role</label>
                <select name="role[]" id="multiple-select" class="form-control">
                    @foreach($roles as $k => $v)
                        <option value="{{ $v->id }}" {{ $v->name === 'Admin' ? 'selected' : '' }}>{{ $v->name }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="default_role" value="Admin">
            </div>

            <div class="col col-md-6" style="display:none;">
                <label for="check-input" class="form-control-label">Password Type</label>
                <input type="hidden" id="check-input2" name="auto" value="manual" checked>
                <span>Manual</span>
            </div>
            
            <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa fa-dot-circle-o"></i> Submit
                </button>
                <button type="reset" class="btn btn-danger btn-sm">
                    <i class="fa fa-ban"></i> Reset
                </button>
            </div>
            </form>
        </div>

    </div>
</div>


@endsection
@section('js')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    
    <script>
        document.getElementById('show-password').addEventListener('change', function() {
            const newPasswordInput = document.getElementById('password-input');
            newPasswordInput.type = this.checked ? 'text' : 'password';
        });
    </script>
@endsection