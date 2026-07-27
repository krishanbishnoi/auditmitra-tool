@extends('layouts.master')

@section('title', '| Auditor Assign')

@section('sh-detail')
    Edit New
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <strong>Edit Auditor Assign</strong> form
        </div>
        <div class="card-body card-block">
            
            {!! Form::model($data,
                      array(
                      'method' => 'PATCH',
                        'url' =>'auditor_assign/'.Crypt::encrypt($data->id),
                        'class' => 'kt-form',
                        'data-toggle'=>"validator")
                      ) !!}

                <div class="card">
                    <div class="card-header" style="background-image: linear-gradient(to right, rgb(132, 94, 194), rgb(144, 109, 198), rgb(156, 125, 201), rgb(168, 140, 205), rgb(179, 156, 208));color:#fff">
                        <strong class="card-title">Auditors Details</strong>
                    </div>      
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class=" form-group">
                                <label for="text-input" class="form-control-label font-weight-bold">Auditor Name</label>
                                <input type="text" id="text-input" name="auditor_name" placeholder="Auditor Name" class="form-control" value="{{$data->auditor_name}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class=" form-group">
                                <label for="text-input" class=" form-control-label font-weight-bold">Auditor Email ID</label>
                                <input type="text" id="text-input" name="auditor_email" placeholder="Auditor Email ID" class="form-control" value="{{$data->auditor_email}}" tabindex="2">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class=" form-group">
                                <label for="text-input" class=" form-control-label font-weight-bold">Audit Date</label>
                                <input type="text" id="text-input" name="" placeholder="Audit Date" class="form-control" value="" tabindex="2">
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-header" style="background-image: linear-gradient(to right, rgb(132, 94, 194), rgb(144, 109, 198), rgb(156, 125, 201), rgb(168, 140, 205), rgb(179, 156, 208));color:#fff">
                        <strong class="card-title">Collection | Agency Details</strong>
                    </div>   
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class=" form-group">
                                <label for="text-input" class=" form-control-label font-weight-bold">Agency Name</label>
                                <input type="text" id="text-input" name="final_agency_name" placeholder="Agency Name" class="form-control" value="{{$data->final_agency_name}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class=" form-group">
                                <label for="text-input" class=" form-control-label font-weight-bold">Type of Agency</label>
                                <input type="text" id="text-input" name="type_of_agency" placeholder="Type of Agency" class="form-control" value="{{$data->type_of_agency}}" tabindex="2">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class=" form-group">
                                <label for="text-input" class=" form-control-label font-weight-bold">Sub Product</label>
                                <input type="text" id="text-input" name="sub_product" placeholder="Sub Product" class="form-control" value="{{$data->sub_product}}" tabindex="2">
                            </div>
                        </div>
                    </div>
                </div> 

                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class=" form-group">
                            <label for="text-input" class=" form-control-label font-weight-bold">Product</label>
                            <input type="text" id="text-input" name="product" placeholder="Product" class="form-control" value="{{$data->product}}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class=" form-group">
                            <label for="text-input" class=" form-control-label font-weight-bold">Location</label>
                            <input type="text" id="text-input" name="location" placeholder="Location" class="form-control" value="{{$data->location}}" tabindex="2">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class=" form-group">
                            <label for="text-input" class=" form-control-label font-weight-bold">State</label>
                            <input type="text" id="text-input" name="state" placeholder="State" class="form-control" value="{{$data->state}}" tabindex="2">
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class=" form-group">
                            <label for="text-input" class=" form-control-label font-weight-bold">Region</label>
                            <input type="text" id="text-input" name="region" placeholder="Region" class="form-control" value="{{$data->region}}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class=" form-group">
                            <label for="text-input" class=" form-control-label font-weight-bold">Process Review Agency</label>
                            <input type="text" id="text-input" name="process_review_agency" placeholder="Process Review Agency" class="form-control" value="{{$data->process_review_agency}}" tabindex="2">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class=" form-group">
                            <label for="text-input" class=" form-control-label font-weight-bold">Process Review Agency ID</label>
                            <input type="text" id="text-input" name="process_review_agency_id" placeholder="Process Review Agency ID" class="form-control" value="{{$data->process_review_agency_id}}" tabindex="2">
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class=" form-group">
                            <label for="text-input" class=" form-control-label font-weight-bold">Process Review Period</label>
                            <input type="text" id="text-input" name="process_review_period" placeholder="Process Review Period" class="form-control" value="{{$data->process_review_period}}">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class=" form-group">
                            <label for="text-input" class="form-control-label font-weight-bold">Agency Address</label>
                            <input type="text" id="text-input" name="agency_address" placeholder="Agency Address" class="form-control" value="{{$data->agency_address}}" tabindex="2">
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-group ml-1">
                            <label for="contact-textarea" class="form-control-label font-weight-bold">Agency Contact</label>
                            <textarea id="text-input" name="contact" placeholder="Contact" class="form-control" tabindex="2" rows="3">{{ $data->contact }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contact-textarea" class="form-control-label font-weight-bold">Agency Email</label>
                            <textarea id="text-input" name="agency_email" placeholder="Agency Email" class="form-control" tabindex="2" rows="3">{{ $data->agency_email }}</textarea>
                        </div>
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    <script>


    </script>
@endsection