@extends('layouts.master')

@section('title', '| Agency')

@section('sh-detail')
Create New
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <strong>Create Agency</strong> form
            </div>

            <div class="card-body card-block">
                 {!! Form::open(['route' => 'agency.store', 'class' => 'form-horizontal', 'role' => 'form', 'data-toggle' => 'validator']) !!}
                <div class="row">
                    <div class="col-md-6">
                        <div class=" form-group">
                            <label for="text-input" class=" form-control-label">Agency Name</label>
                            <input type="text" id="text-input" name="name" placeholder="Agency Name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="multiple-select" class="form-control-label">Select Product</label>
                            {!! Form::select('product_id', $products, null, ['id' => 'product_id', 'class' => 'form-control']) !!}
                            @error('product_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class=" form-group">
                            <label for="agency_id" class=" form-control-label">Agency id</label>
                            <input type="text" id="agency_id" name="agency_id" placeholder="Agency id"
                                class="form-control @error('agency_id') is-invalid @enderror" value="{{ old('agency_id') }}">
                            @error('agency_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-control-label">Email</label>
                            <div id="email_container">
                                <input type="text" id="email" name="emails[]" placeholder="email"
                                    class="form-control @error('emails.0') is-invalid @enderror" value="{{ old('emails.0') }}">
                                @error('emails.0')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <button type="button" class="btn btn-success mt-2" id="addEmailBtn">Add More Email</button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mobile_number" class="form-control-label">Mobile Number</label>
                            <div id="mobile_container">
                                <input type="text" id="mobile_number" name="mobile_numbers[]" placeholder="Mobile Number"
                                    class="form-control @error('mobile_numbers.0') is-invalid @enderror" value="{{ old('mobile_numbers.0') }}">
                                @error('mobile_numbers.0')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <button type="button" class="btn btn-success mt-2" id="addMobileBtn">Add More Mobile</button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class=" form-group">
                            <label for="agency_manager" class=" form-control-label">Agency Manager</label>
                            <select name="agency_manager" class="standardSelect form-control" tabindex="3">
                                <option value="">Choose Agency Manager</option>
                                @foreach($user as $item)
                                    <option value="{{ $item->id }}" {{ old('agency_manager') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error('agency_manager')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col col-md-6">
                        <div class="form-group">
                            <label for="region_id" class="form-control-label">Regions</label>
                            <select class="form-control" name="region_id" id="country">
                                <option value="">Choose Region</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('region_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col col-md-6">
                        <div class="form-group">
                            <label for="state" class="form-control-label">State</label>
                            <select class="form-control" name="state" id="state"></select>
                            @error('state')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col col-md-6">
                        <div class="form-group">
                            <label for="city_id" class="form-control-label">City</label>
                            <select class="form-control" name="city_id" id="city"></select>
                            @error('city_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="location" class="form-control-label">Location</label>
                            <input type="text" id="location" name="location" placeholder="Location"
                                class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}">
                            @error('location')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address" class="form-control-label">Address</label>
                            <input type="text" id="address" name="address" placeholder="Address"
                                class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}">
                            @error('address')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-dot-circle-o"></i> Submit
                    </button>
                    <button type="reset" class="btn btn-danger btn-sm">
                        <i class="fa fa-ban"></i> Reset
                    </button>
                </div>
                {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>


@endsection
@section('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script>
$(function() {
    $(".sizes").select2();

});
jQuery(document).ready(function() {
    jQuery(".standardSelect").chosen({
        disable_search_threshold: 10,
        no_results_text: "Oops, nothing found!",
        width: "100%"
    });
})
</script>
<script>

jQuery('#country').change(function () {
            var cid = jQuery(this).val();
            if (cid) {
                jQuery.ajax({
                    type: "get",
                    url: " {{url('/getStates')}}/" + cid,
                    success: function (res) {
                        if (res) {
                            jQuery("#state").empty();
                            jQuery("#city").empty();
                            jQuery("#state").append('<option>Select State</option>');
                            jQuery.each(res, function (key, value) {
                                jQuery("#state").append('<option value="' + key + '">' + value + '</option>');
                            });
                        }
                    }

                });
            }
        });
        jQuery('#state').change(function () {
            var sid = jQuery(this).val();
            if (sid) {
                jQuery.ajax({
                    type: "get",
                    url: "{{url('/getCities')}}/" + sid,
                    success: function (res) {
                        if (res) {
                            jQuery("#city").empty();
                            jQuery("#city").append('<option>Select City</option>');
                            jQuery.each(res, function (key, value) {
                                jQuery("#city").append('<option value="' + key + '">' + value + '</option>');
                            });
                        }
                    }

                });
            }
        });

        document.getElementById('addEmailBtn').addEventListener('click', function() {
        var emailContainer = document.getElementById('email_container');
        var newInput = document.createElement('input');
        newInput.type = 'text';
        newInput.name = 'emails[]';
        newInput.placeholder = 'email';
        newInput.className = 'form-control mt-2';
        emailContainer.appendChild(newInput);
    });

    document.getElementById('addMobileBtn').addEventListener('click', function() {
        var mobileContainer = document.getElementById('mobile_container');
        var newInput = document.createElement('input');
        newInput.type = 'text';
        newInput.name = 'mobile_numbers[]';
        newInput.placeholder = 'Mobile Number';
        newInput.className = 'form-control mt-2';
        mobileContainer.appendChild(newInput);
    });
    </script>
@endsection