@extends('layouts.master')

@section('title', '| Auditor Assign')

@section('sh-detail')
    Show Auditor Assign
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <strong>Show Auditor Assign Details</strong>
            </div>
            <div class="card-body card-block">
                
                <form method="GET" class="kt-form">
                    <div class="card">
                        <div class="card-header" style="background-image: linear-gradient(to right, rgb(132, 94, 194), rgb(144, 109, 198), rgb(156, 125, 201), rgb(168, 140, 205), rgb(179, 156, 208)); color: #fff">
                            <strong class="card-title">Auditor Details</strong>
                        </div>      
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="auditor_name" class="form-control-label font-weight-bold">Auditor Name</label>
                                    <input type="text" id="auditor_name" name="auditor_name" class="form-control" value="{{ $data->auditor_name }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="auditor_email" class="form-control-label font-weight-bold">Auditor Email ID</label>
                                    <input type="text" id="auditor_email" name="auditor_email" class="form-control" value="{{ $data->auditor_email }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="audit_date" class="form-control-label font-weight-bold">Audit Date</label>
                                    <input type="text" id="audit_date" name="audit_date" class="form-control" value="{{ $data->audit_date }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" style="background-image: linear-gradient(to right, rgb(132, 94, 194), rgb(144, 109, 198), rgb(156, 125, 201), rgb(168, 140, 205), rgb(179, 156, 208)); color: #fff">
                            <strong class="card-title">Collection | Agency Details</strong>
                        </div>   
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="final_agency_name" class="form-control-label font-weight-bold">Agency Name</label>
                                    <input type="text" id="final_agency_name" name="final_agency_name" class="form-control" value="{{ $data->final_agency_name }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type_of_agency" class="form-control-label font-weight-bold">Type of Agency</label>
                                    <input type="text" id="type_of_agency" name="type_of_agency" class="form-control" value="{{ $data->type_of_agency }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sub_product" class="form-control-label font-weight-bold">Sub Product</label>
                                    <input type="text" id="sub_product" name="sub_product" class="form-control" value="{{ $data->sub_product }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div> 

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="product" class="form-control-label font-weight-bold">Product</label>
                                <input type="text" id="product" name="product" class="form-control" value="{{ $data->product }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="location" class="form-control-label font-weight-bold">Location</label>
                                <input type="text" id="location" name="location" class="form-control" value="{{ $data->location }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="state" class="form-control-label font-weight-bold">State</label>
                                <input type="text" id="state" name="state" class="form-control" value="{{ $data->state }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="region" class="form-control-label font-weight-bold">Region</label>
                                <input type="text" id="region" name="region" class="form-control" value="{{ $data->region }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="process_review_agency" class="form-control-label font-weight-bold">Process Review Agency</label>
                                <input type="text" id="process_review_agency" name="process_review_agency" class="form-control" value="{{ $data->process_review_agency }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="process_review_agency_email" class="form-control-label font-weight-bold">Process Review Agency Email</label>
                                <input type="text" id="process_review_agency_email" name="process_review_agency_email" class="form-control" value="{{ $data->process_review_agency_email }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="process_review_period" class="form-control-label font-weight-bold">Process Review Period</label>
                                <input type="text" id="process_review_period" name="process_review_period" class="form-control" value="{{ $data->process_review_period }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="agency_address" class="form-control-label font-weight-bold">Agency Address</label>
                                <input type="text" id="agency_address" name="agency_address" class="form-control" value="{{ $data->agency_address }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group ml-1">
                                <label for="contact" class="form-control-label font-weight-bold">Agency Contact</label>
                                <textarea id="contact" name="contact" class="form-control" rows="3" readonly>{{ $data->contact }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="agency_email" class="form-control-label font-weight-bold">Agency Email</label>
                                <textarea id="agency_email" name="agency_email" class="form-control" rows="3" readonly>{{ $data->agency_email }}</textarea>
                            </div>
                        </div>    
                    </div>      
                    
                </form>
            </div>
            
            <div class="card-footer">
                <a href="{{ route('auditor_assign.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
@endsection
