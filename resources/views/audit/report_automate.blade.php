@extends('layouts.master')
@section('sh-title')
Automated Report Section
@endsection
@section('sh-detail')
Call
@endsection

@section('content')
<style type="text/css">
#SUMMARY:focus {     
    background-color:tomato;    
}	
#COLLECTION:focus {     
    background-color:tomato;    
}	
#GAPS:focus {     
    background-color:tomato;    
}	
</style>

<div class="row">
	<div class="col-lg-12" style="margin-top:10x">
        @if (session('status'))
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <i class="material-icons">close</i>
                        </button>
                        <span>{{ session('status') }}</span>
                      </div>
                    </div>
                  </div>
                @endif
	</div>
</div>
<?php
$user=Auth::user();

?>


<div class="animated fadeIn">
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header">
					<strong class="card-title">Automated Report Section</strong>	
				</div>
				<div class="card-body card-block">
					<form target="_blank" method="GET" action="{{url('/reportAutomationData')}}" autocomplete="off">
						<div class="row">
                            
                            <div class="col-md-3 form-group">
                                <label>Select Branch*</label>
                                <select class="form-control" id="branch" name="branch" required />
                                @foreach($branchList as $b)
                                	<option value="<?=$b->id?>"><?=$b->name;?></option>
                                @endforeach
                            	</select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Start Date*</label>
                                <input name="start_date" id="start_date" type="date" data-date-format="yyyy-mm-dd" class="form-control datepicker" required />
                            </div>
                            <div class="col-md-3 form-group">
                                <label>End Date*</label>
                                <input name="end_date" id="end_date" type="date" data-date-format="yyyy-mm-dd" class="form-control datepicker" required/>
                            </div>
                            
                            <div class="col-md-3 form-group">
								<input name="search" type="submit" class="btn btn-sm btn-primary mt-4" value="Search"/>
							</div>
                        </div>
					</form>
				</div>
			</div>
		</div>
	</div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <strong class="card-title">Data Uploader</strong>    
                </div>
                <div class="card-body card-block">
                    <form method="POST" enctype='multipart/form-data' action="{{url('/reportDataUploader')}}" autocomplete="off">
                        <div class="row">
                            @csrf
                            
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>ACR Data Upload</label>
                                <input name="acr_report" id="acr_report" type="file"  class="form-control" />
                                <a style="color:blue;" class="form-control" href="{{asset('acr_report_sample.csv')}}">Sample File Download</a>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>DAC Uploader</label>
                                <input name="dac_uploader" id="dac_uploader" type="file"  class="form-control" />
                                <a style="color:blue;" class="form-control" href="{{asset('dac_uploader.csv')}}">Sample File Download</a>
                            </div>
                            
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Score Upload</label>
                                <input name="score_upload" id="score_upload" type="file"  class="form-control" />
                                <a style="color:blue;" class="form-control" href="{{asset('score_upload.csv')}}">Sample File Download</a>
                            </div>
                        </div>
                        
                        <div class="row">
                            
                            <div class="col-md-6 form-group">
                                <label>Branch Id's</label>
                                <a style="color:blue;" class="form-control" href="{{asset('branch.csv')}}">Download</a>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label>Product Id's</label>
                                <a style="color:blue;" class="form-control" href="{{asset('product_id.csv')}}">Download</a>
                            </div>
                        </div>
                        
                        <!--<div class="row">
                            <div class="col-md-6 form-group">
                                <label>Receipt cut in Non-Day light hour Data Upload</label>
                                <input name="receipt_cut" id="receipt_cut" type="file"  class="form-control" />
                                <a style="color:blue;" class="form-control" href="{{asset('receipt_cut_data.csv')}}">Sample File Download</a>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Delay in Secondary Allocation Data Upload</label>
                                <input name="sec_allocation" id="sec_allocation" type="file"  class="form-control" />
                                <a style="color:blue;" class="form-control" href="{{asset('delay_secondary_allocation_data.csv')}}">Sample File Download</a>
                            </div>
                        </div>-->

                        

                        <div class="row">                            
                            <div class="col-md-6 form-group">
                                <input name="search" type="submit" class="btn btn-sm btn-primary mt-6" value="Upload"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

	

</div>



@endsection