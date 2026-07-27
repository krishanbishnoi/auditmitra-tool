@extends('layouts.master')
@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"> -->
@endsection
@section('content')
 <!-- Content -->
 <div>
  


<!-- Modal -->
<div class="row">

  <div class="col-lg-12">

  <div class="card">

      <div class="card-header">

          <strong>QA-QC Report</strong>

      </div>

      <div class="card-body card-block">

            <form method="POST" action="{{route('internalexcel')}}" autocomplete="off">

                        <div class="row">

                            @csrf

                            <div class="col-md-3 form-group">

                                <label>Lob Name*</label>

                                <select name="lob" class="form-control">

                                  <option value="">Choose Lob Name</option>

                                  <option value="collection">Collection</option>

                                  <option value="commercial_vehicle">Commercial Vehicle</option>

                                  <option value="rural">Rural</option>

                                  <option value="alliance">Alliance</option>

                                  <option value="credit_card">Credit Card</option>

                                </select>

                            </div>

                            <div class="col-md-3 form-group">

                                <label>Select Branch</label>

                                <select class="form-control" name="branch_name" id="branch_name"  value="{{old('branch_name')}}">

                                    <option value="">All</option>
                                    
                                    @foreach ($branch as $item)

                                        <option value="{{$item->id}}" {{($item->id == old('branch_name'))?'selected':''}} >{{$item->name}}</option>

                                    @endforeach

                                </select>

                            </div>

                            <div class="col-md-3 form-group">

                                <label>Start Date*</label>

                                <input name="start_date" type="text" data-date-format="yyyy-mm-dd" class="form-control datepicker" required />

                            </div>

                            <div class="col-md-3 form-group">

                                <label>End Date*</label>

                                <input name="end_date" type="text" data-date-format="yyyy-mm-dd" class="form-control datepicker"/>

                            </div>
                        </div>
                    
                 <button type="submit" class="btn btn-primary">Download</button>
               
        </form>
    </div>
</div>
</div>
</div>
    

 <!-- end -->

 <!-- <a class="btn btn-sm btn-primary float-right" href="{{route('dump-excel')}}">Dump Download</a> -->
 </div>

        <!-- /.content -->
        <div class="clearfix"></div>
@endsection
@section('js')
<!-- <script src="{{URL::asset('js/highmaps.js')}}"></script>
<script src="{{URL::asset('js/exporting.js')}}"></script>
<script src="{{URL::asset('js/in-all.js')}}"></script> -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{URL::asset('js/dashboard.js')}}"></script>
<!-- <script src="https://code.highcharts.com/modules/pareto.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script> -->
<script>
     jQuery(document).ready(function() {

        jQuery('.datepicker').datepicker({
            dateFormat: "yyyy-mm-dd"
        });
        
       
    })
   
</script>

@endsection
