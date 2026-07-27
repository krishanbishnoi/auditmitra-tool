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
                <strong>Create Agency Repo</strong> form
            </div>

            <div class="card-body card-block">
                {!! Form::open(
                array(
                'route' => 'agencyrepo.store',
                'class' => 'form-horizontal',
                'role'=>'form',
                'data-toggle'=>"validator")
                ) !!}
                <div class="row">
                    <div class="col-md-6">
                        <div class=" form-group">
                            <label for="text-input" class=" form-control-label">Agency Repo Name</label>
                            <input type="text" id="text-input" name="name" placeholder="Agency Repo Name"
                                class="form-control" value="{{old('name')}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="multiple-select" class="form-control-label">Select Product</label>
                            {!! Form::select('product_id', $products, null, ['id' => 'product_id', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class=" form-group">
                            <label for="agency_id" class=" form-control-label">Agency Repo id</label>
                            <input type="text" id="agency_id" name="agency_id" placeholder="Agency Repo id"
                                class="form-control" value="{{old('agency_id')}}">
                        </div>
                    </div>
                    <div class="col-md-6">
            <div class="form-group">
                <label for="email" class="form-control-label">Email</label>
                <div id="email_container">
                    <input type="text" id="email" name="emails[]" placeholder="email" class="form-control" value="{{old('email')}}">
                </div>
                <button type="button" class="btn btn-success mt-2" id="addEmailBtn">Add More Email</button>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label for="mobile_number" class="form-control-label">Mobile Number</label>
                <div id="mobile_container">
                    <input type="text" id="mobile_number" name="mobile_numbers[]" placeholder="Mobile Number"
                        class="form-control" value="{{old('mobile_number')}}">
                </div>
                <button type="button" class="btn btn-success mt-2" id="addMobileBtn">Add More Mobile</button>
            </div>
        </div>
                    <!-- <div class="col-md-6">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Branch Name</label>
                        <select name="branch_name" data-placeholder="Choose a Branch..." class="standardSelect form-control" tabindex="1">
                            <option value="" label="Branch Name"></option>
                            @foreach($branch as $k=>$item)
                            <option value="{{$item->id}}">{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> -->
                   
                    <div class="col-md-6">
                        <div class=" form-group">
                            <label for="agency_manager" class=" form-control-label">Agency Repo Manger</label>
                            <!-- <input type="text" id="agency_manager" name="agency_manager" placeholder="Agency Manger" class="form-control" value="{{old('agency_manager')}}"> -->
                            <select name="agency_manager" data-placeholder="Choose a Agency Manager..."
                                class="standardSelect form-control" tabindex="3">
                                <option value="" label="Agency Manger"></option>
                                @foreach($user as $k=>$item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                  
                    <div class="col col-md-6">
                        <div class="form-group">
                            <label for="multiple-select" class=" form-control-label">Regions
                                    </label>
                            <select class="form-control" name="region_id" id="country">
                                <option value="">Choose Region</option>
                                @foreach ($regions as $country)
                                    <option value="{{$country->id}}">
                                        {{$country->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col col-md-6">
                        <div class="form-group">
                            <label for="multiple-select" class=" form-control-label">State
                                    </label>
                                <select class="form-control" name="state" id="state">
                                </select>

                        </div>
                    </div>
                    <div class="col col-md-6">
                        <div class="form-group">
                            <label for="multiple-select" class=" form-control-label">City
                                    </label>

                                <select class="form-control" name="city_id" id="city">
                                </select>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class=" form-group">
                            <label for="location" class=" form-control-label">Location</label>
                            <input type="text" id="location" name="location" placeholder="Location" class="form-control"
                                value="{{old('location')}}">
                        </div>
                    </div>
                
                    <div class="col-md-6">
                        <div class=" form-group">
                            <label for="address" class=" form-control-label">Address</label>
                            <input type="text" id="address" name="address" placeholder="Address" class="form-control"
                                value="{{old('address')}}">
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
                </form>
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