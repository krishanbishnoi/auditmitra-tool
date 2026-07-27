@extends('layouts.master')

@section('title', '| Upload')

@section('sh-detail')
    Create New
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
    <div class="card ">
        <div class="card-header">
            <strong>Bulk Upload</strong>
        </div>
        <div class="card-body card-block">
            {!! Form::open(
                     array(
                       'route' => 'upload.store',
                       'class' => 'form-horizontal',
                       'role'=>'form',
                       'data-toggle'=>"validator",
                       'files' => true)
                     ) !!}
            <div class="row">
                <div class="col-sm-3">
                    <div class=" form-group">
                        <label>Lob Name*</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <select name="lob" class="form-control">
                            <option value="">Choose Lob Name</option>
                            <option value="collection">Collection</option>
                            <option value="commercial_vehicle">Commercial Vehicle</option>
                            <option value="rural">Rural</option>
                            <option value="alliance">Alliance</option>
                            <option value="credit_card">Credit Card</option>
                          </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Dac Report</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class=" form-group">
                        <input type="file" id="dac_report" name="dac_report" class="form-control-file">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Allocation Report</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class=" form-group">
                        <input type="file" id="allocation_report" name="allocation_report" class="form-control-file">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Trail Intensity</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class=" form-group">
                        <input type="file" id="trail_intensity" name="trail_intensity" class="form-control-file">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Settlement_MIS</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class=" form-group">
                        <input type="file" id="report4" name="settlement_mis" class="form-control-file">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Adverse Bulk</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class=" form-group">
                        <input type="file" id="report5" name="adverse" class="form-control-file">
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
        jQuery(document).ready(function() {
            jQuery('.datepicker').datepicker({
                format: "mm-yyyy",
                viewMode: "months", 
                minViewMode: "months"
            });
    })
    </script>
@endsection