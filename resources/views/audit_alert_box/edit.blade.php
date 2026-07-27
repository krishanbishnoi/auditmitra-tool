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
                    'url' =>'audit_alert_box/'.Crypt::encrypt($data->id),
                    'class' => 'kt-form',
                    'data-toggle'=>"validator")
                  ) !!}
        {{-- <input type="hidden" name="company_id" value="{{AUth::User()->company_id}}"> --}}

        <div class="row">

          <div class="col-md-3 form-group">
            <label>Name*</label>
            <input type="text" name="name" class="form-control" required value="{{$data->name}}">
          </div>

          <div class="col-md-6 form-group">
                      <label>Message*</label>
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <textarea class="summernote" id="kt_summernote_1"style="min-width:250px" name="details">{!!$data->details!!}</textarea>
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
@include('shared.form_js')
<script src="/assets/app/custom/general/crud/forms/widgets/summernote.js" type="text/javascript"></script>
@endsection