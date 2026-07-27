@extends('layouts.master')

@section('sh-title')
Audit Alert Box
@endsection

@section('sh-detail')
Create New
@endsection

@section('content')
<div class="card" id="kt_repeater_1">
    <br/>
    <div class="card-header">
        <strong>Beat Plan</strong>
    </div>
    <div class="card-body card-block">
      
        {!! Form::open(
                  array(
                    'route' => 'beat_plan.store', 
                    'class' => 'kt-form',
                    'role'=>'form',
                    'data-toggle'=>"validator")
                  ) !!}
      <!--begin::Form-->
        {{-- <input type="hidden" name="company_id" value="{{AUth::User()->company_id}}"> --}}

        <br/>
        <div class="row">
          <div class="col-md-3 form-group">
            <label>Name*</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Enter Beat plan name" >
          </div>
           
          </div> 
          <div id="kt_repeater_1">
            <div class="form-group  row" id="kt_repeater_1">
              <div data-repeater-list="subs" class="col-lg-12">
                <div data-repeater-item class="form-group align-items-center">
                  <div class="row">
                  
                    <div class="col-md-3 form-group">
                      <label>From Date*</label>
                      <input type="text" id="date" name="date" class="form-control datepicker" placeholder="Choose dates" autocomplete="off" required>
                  </div>
                  
                    <div class="col-md-3 form-group">
                      <label>To Date*</label>
                      <input type="text" id="to_date" name="to_date" class="form-control datepicker" placeholder="Choose to dates" autocomplete="off" required>
                    </div>
                    
                <div class="col-md-3 form-group">
                    <label>Branch*</label>
                    <select name="branch_id" id="branchName" data-placeholder="Choose a Branch..." class="standardSelect form-control branchName" tabindex="2" onchange="GetSelectedBranchValue(this.value,this.name)" required>
                      <option value="" label="Branch Name"></option>
                      @foreach($branch as $k=>$item)
                      <option value="{{$item->id}}">{{$item->name}}</option>
                      @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 form-group">
                  <label>Agencies*</label>
                  <select class="form-control agencies" id="agencies" name="agencies" required>
                  </select>
                  
                    
                </div>
                
                <div class="col-md-3 form-group">
                  <label>Collection Managers*</label>
                  <select class="form-control" name="collection_manager" id="collection_manager" required>
                      <option selected>--Choose Collection Manager--</option>
                      @foreach($user as $data)
                      <option value="{{$data->id}}">{{$data->name}}</option>
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
  });
  jQuery('body').on('focus',".datepicker", function(){
    jQuery(this).datepicker({
      format: "dd-mm-yyyy",
      minDate: new Date()
    });
  })
  
  //added by nisha 
  
    function GetSelectedBranchValue(id,name)
        {
          var nameforagencies = name; 
          nameforagencies = nameforagencies.replace("branch_id", "agencies"); 
          
              if (id && nameforagencies) {
                  getAgency(id,nameforagencies);
                }
        }
    function getAgency(id,nameforagencies){
    
        jQuery.ajax({
                    type: "get",
                    url: " {{url('get_agencies')}}/"+id,
                    success: function (res) {
                        var data='<option>Choose Agency</option>';
                        if (res) {
                            var obj=res
                            obj.data.forEach(function(item, index){
                                data=data+'<option value="'+item.id+'" >'+item.name+'</option>'
                            });  
                        }
                        jQuery('.agencies').html(data);
                        
                    }
                });
     }
  
    
      
  </script>
    @include('shared.form_js')
@endsection