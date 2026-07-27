@extends('layouts.master')

@section('sh-title')
Audit Alert Box
@endsection

@section('sh-detail')
Create New
@endsection

@section('content')

<div class="card">
  <div class="card-header">
      <strong>Audit Alert Box</strong>
  </div>
  <div class="card-body card-block">

      <!--begin::Form-->
      
        {!! Form::model($data,
                  array(
                  'method' => 'PATCH',
                    'url' =>'beat_plan/'.Crypt::encrypt($data->id),
                    'class' => 'kt-form',
                    'data-toggle'=>"validator")
                  ) !!}
        {{-- <input type="hidden" name="company_id" value="{{AUth::User()->company_id}}"> --}}

        <div class="row">
          <div class="col-md-3 form-group">
            <label>Name*</label>
          <input type="text" id="name" name="name" class="form-control" placeholder="Enter Beat plan name" value="{{$data->name}}" >
          </div>
           
          </div> 
          <div id="kt_repeater_qm_sheet">
            <div class="form-group  row" id="kt_repeater_qm_sheet">
              <div data-repeater-list="subs" class="col-lg-12">
                
                    @foreach($data->sub as $value)
                    <div data-repeater-item class="form-group align-items-center">
                  
                      <div class="row">
                    <input type="hidden" name="sub_id" value="{{$value->id}}">
                    <div class="col-md-3 form-group">
                      <label>Date*</label>
                    <input type="text" id="date" name="date" class="form-control datepicker" placeholder="Choose dates" value="{{$value->date}}" >
                  </div>
                  <div class="col-md-3 form-group">
                      <label>Branch*</label>
                      <select name="branch_id" id="branchName" data-placeholder="Choose a Branch..." class="standardSelect form-control" tabindex="2">
                        <option value="" label="Branch Name"></option>
                        @foreach($branch as $k=>$item)
                        <option value="{{$item->id}}" {{($value->branch_id==$item->id)?'selected':''}}>{{$item->name}}</option>
                        @endforeach
                      </select>
                  </div>
      
                <div class="col-md-3 form-group">
                  <label>Description</label>
                <textarea class="form-control"  id="" name="description">{{$value->description}}</textarea>
                </div>
                <div class="col-md-3">
                  <div data-repeater-delete="" class="btn-sm btn btn-danger btn-pill">
                    <span>
                      <i class="la la-trash-o"></i>
                      <span>Delete</span>
                    </span>
                  </div>
                </div>
                </div>
                </div>
                    @endforeach


                    <div data-repeater-item class="form-group align-items-center">
                  
                      <div class="row">
                    <input type="hidden" name="sub_id" value="">
                  <div class="col-md-3 form-group">
                      <label>Date*</label>
                      <input type="text" id="date" name="date" class="form-control datepicker" placeholder="Choose dates" >
                  </div>
                  <div class="col-md-3 form-group">
                      <label>Branch*</label>
                      <select name="branch_id" id="branchName" data-placeholder="Choose a Branch..." class="standardSelect form-control" tabindex="2">
                        <option value="" label="Branch Name"></option>
                        @foreach($branch as $k=>$item)
                        <option value="{{$item->id}}">{{$item->name}}</option>
                        @endforeach
                      </select>
                  </div>
      
                <div class="col-md-3 form-group">
                  <label>Description</label>
                    <textarea class="form-control"  id="" name="description"></textarea>
                </div>
                <div class="col-md-3">
                  <div data-repeater-delete="" class="btn-sm btn btn-danger btn-pill">
                    <span>
                      <i class="la la-trash-o"></i>
                      <span>Delete</span>
                    </span>
                  </div>
                </div>
                  </div>

                  
                </div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-lg-4">
                <div data-repeater-create="" class="btn btn-primary btn-sm">
                  <span>
                    <i class="fa fa-plus"></i>
                    <span>Add</span>
                  </span>
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

      <!--end::Form-->
    </div>

    <!--end::Portlet-->
  </div>
</div>

@endsection

@section('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script> --}}
<script>
  jQuery(document).ready(function() {
    jQuery('.datepicker').datepicker({
      format: "dd-mm-yyyy",
      minDate: new Date()
    });
  })
  
  jQuery('body').on('focus',".datepicker", function(){
    jQuery(this).datepicker({
      format: "dd-mm-yyyy",
      minDate: new Date()
    });
  })
  </script>
    @include('shared.form_js')
@endsection