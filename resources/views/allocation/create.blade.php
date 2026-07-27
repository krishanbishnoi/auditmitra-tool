@extends('layouts.master')

@section('sh-title')
QM - Allocation
@endsection

@section('sh-detail')
Allocation New
@endsection

@section('content')

<div class="row">
  <div class="col-lg-12">
  <div class="card">
      <div class="card-header">
          <strong>Sheet Allocation</strong>
      </div>
      <div class="card-body card-block">

      <!--begin::Form-->
      
        {!! Form::open(
                  array(
                    'route' => 'allocation.store', 
                    'class' => 'form-horizontal',
                    'role'=>'form',
                    'data-toggle'=>"validator")
                  ) !!}
        <div class="row">

          <div class="col-md-6 form-group">
            <label>Sheet*</label>
            <select class="form-control" name="sheet_id" id="sheet_id" required>
                <option selected>--Choose sheet--</option>
               @foreach($sheet as $data)
               <option value="{{$data->id}}">{{$data->name}}</option>
                @endforeach
            </select>
          </div>
          <div class="col-md-6 form-group">
            <label>User*</label>
            <select class="form-control" name="user_id" id="user_id" required>
                <option selected>--Choose user--</option>
               @foreach($user as $data)
               <option value="{{$data->id}}">{{$data->name}}</option>
                @endforeach
            </select>
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
{{-- @include('shared.form_js') --}}
@endsection