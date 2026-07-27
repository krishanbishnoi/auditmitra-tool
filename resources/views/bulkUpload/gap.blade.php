@extends('layouts.master')

@section('title', '| Upload')

@section('sh-detail')
    Create New
@endsection

@section('content')
<div id="loader"></div>
<div class="row" style="display:none;" id="myDiv">
    <div class="col-md-12">
    <div class="card w-100">
        <div class="card-header">
            <strong>Compliance show</strong> 
        </div>
        <div class="card-body card-block">
            {!! Form::open(
                     array(
                       'route' => 'getGap',
                       'class' => 'form-horizontal',
                       'role'=>'form',
                       'id'=>'gap',
                       'data-toggle'=>"validator",
                    //    'files' => true
                       )
                     ) !!}
            <div class="row">
                <div class="col-sm-3">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Choose lob</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <select name="lob" class="form-control" id="lob">
                            <option value="">Choose Lob Name</option>
                            <option value="collection" {{($lob=='collection')?'selected':''}}>Collection</option>
                            <option value="commercial_vehicle" {{($lob=='commercial_vehicle')?'selected':''}}>Commercial Vehicle</option>
                            <option value="rural" {{($lob=='rural')?'selected':''}}>Rural</option>
                            <option value="alliance" {{($lob=='alliance')?'selected':''}}>Alliance</option>
                            <option value="credit_card" {{($lob =='credit_card')?'selected':''}}>Credit Card</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class=" form-group">
                        <label for="text-input" class="form-control-label">Choose Branch</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <select class="form-control" id="branch" name="branch">
                            <option>Choose Branch Name</option>
                            {{-- @foreach($branchs as $data)
                                <option value="{{$data}}" {{($data==$branchId)?'selected':''}}>{{$data}}</option>
                            @endforeach --}}
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Choose Agency</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <select class="form-control" id="agency" name="agency">
                            <option>Choose Agency Name</option>
                        {{--    @foreach($agencies as $data)
                                <option value="{{$data}}" {{($data==$agencyId)?'selected':''}}>{{$data}}</option>
                            @endforeach
                        --}}
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">Choose date</label>
                    </div>
                </div>
                <div class="col-sm-4" style="float:right">
                    <div class="form-group">
                            <input type="text" name="date" class="form-control datepicker" placeholder="Choose From Date" value="{{$date ?? ''}}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class=" form-group">
                        <label for="text-input" class=" form-control-label">bucket</label>
                    </div>
                </div>
                <div class="col-sm-4" style="float:right">
                    <div class="form-group">
                            <input type="number" name="bucket" class="form-control" placeholder="enter bucket value" value="{{ $bucket ?? ''}}">
                    </div>
                </div>
            </div>
            
            
         <div class="card-footer">
                 <button type="submit" id="submit" class="btn btn-primary btn-sm">
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
@if(isset($allocation))
    <div class="col-lg-12">
        <div class="card  w-100">
            <div class="card-header">
            <strong class="card-title">{{$branchId}} Branch - Allocation GAP</strong>
            </div>
            <div class="card-body">
                <div>
                    @php 
                        $total=$allocation['total'];
                        $com=count($allocation['data']);
                        $per=($com!=0)?(($com/$total)*100):0;
                    @endphp
                    <span style="margin-right:10px">Total Data :</span><span style="margin-right:10px">{{$total}}</span>
                    <span style="margin-right:10px">Total Compliance :</span><span style="margin-right:10px">{{$com}}</span>
                    <span style="margin-right:10px">Total per :</span><span style="margin-right:10px">{{$per}}%</span>
                </div>
                <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                        <thead>
                            <tr>
                                <th>PRODUCTFLAG</th>
                                <th>BRANCH</th>
                                <th>REGION</th>
                                <th>Agency Code</th>
                                <th>Agency Name</th>
                                <th>Status</th>
                                <th>Date Stamp</th>
                                <th>Allocation GAP</th>
                                <th>Delay Allocation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allocation['data'] as $item)
                            {{-- {{dd($item)}} --}}
                            <tr>
                                <td>{{$item->product_flag}}</td>
                                <td>{{$item->branch}}</td>
                                <td>{{$item->region}}</td>
                                <td>{{$item->agency_code}}</td>
                                <td>{{$item->agency_name}}</td>
                                <td>{{$item->status}}</td>
                                <td>{{$item->date_stamp}}</td>
                                <td>{{($item->gap1!=null && $item->gap1!='')?$item->gap1.' Days':'0'}} </td>
                                <td>{{$item->agent_allocation_status}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>        
            </div>
        </div>
    </div>
@endif
@if(isset($dac))
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-header">
                <strong class="card-title">{{$branchId}} Branch - DAC GAP</strong>
            </div>
            <div class="card-body">
                <div>
                    @php 
                        $total=$dac['total'];
                        $com=count($dac['data']);
                        $per=($com!=0)?(($com/$total)*100):0;
                    @endphp
                    <span style="margin-right:10px">Total Data :</span><span style="margin-right:10px">{{$total}}</span>
                    <span style="margin-right:10px">Total Compliance :</span><span style="margin-right:10px">{{$com}}</span>
                    <span style="margin-right:10px">Total per :</span><span style="margin-right:10px">{{$per}}%</span>
                </div>
                <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_2">
                        <thead>
                                <tr>
                                        <th>Product</th>
                                        <th>Payment Id</th>
                                        <th>Branch</th>
                                        <th>Agency Id</th>
                                        <th>Agency Name</th>
                                        <th>Recipt No.</th>
                                        <th>Recipt Date.</th>
                                        <th>Deposit Date</th>
                                        <th>Finnone Update</th>
                                        <th>Delay deposition</th>
                                        <th>Delay deposition</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dac['data'] as $item)
                                    {{-- {{dd($item)}} --}}
                                    <tr>
                                        <td>{{$item->PRODUCT}}</td>
                                        <td>{{$item->PaymentId}}</td>
                                        <td>{{$item->BranchName}}</td>
                                        <td>{{$item->AgencyId}}</td>
                                        <td>{{$item->AgencyName}}</td>
                                        <td>{{$item->ReceiptNo}}</td>
                                        <td>{{$item->ReceiptDate}}</td>
                                        <td>{{$item->DepositDate}}</td>
                                        <td>{{$item->Finnone_Update}}</td>
                                        <td>{{$item->gap1}}</td>
                                        <td>{{$item->gap2}}</td>
                                    </tr>
                                    @endforeach
                        </tbody>
                    </table>        
            </div>
        </div>
    </div>
@endif
@if(isset($trail))
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-header">
                <strong class="card-title">{{$branchId}} Branch - Trail Intensity GAP</strong>
            </div>
            <div class="card-body">
                <div>
                    @php 
                        $total=$trail['total'];
                        $com=count($trail['data']);
                        $per=($com!=0)?(($com/$total)*100):0;
                    @endphp
                    <span style="margin-right:10px">Total Data :</span><span style="margin-right:10px">{{$total}}</span>
                    <span style="margin-right:10px">Total Compliance :</span><span style="margin-right:10px">{{$com}}</span>
                    <span style="margin-right:10px">Total per :</span><span style="margin-right:10px">{{$per}}%</span>
                </div>
                <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_3">
                        <thead>
                            <tr>
                                    <th>PRODUCTFLAG</th>
                                    <th>BRANCH</th>
                                    <th>REGION</th>
                                    <th>Agency Code</th>
                                    <th>Agency Name</th>
                                    <th>Status</th>
                                    <th>Date Stamp</th>
                                    <th>Attempts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trail['data'] as $item)
                            {{-- {{dd($item)}} --}}
                            <tr>
                                <td>{{$item->product_flag_1}}</td>
                                <td>{{$item->branch}}</td>
                                <td>{{$item->region}}</td>
                                <td>{{$item->agency_code}}</td>
                                <td>{{$item->agency_name}}</td>
                                <td>{{$item->status}}</td>
                                <td>{{$item->date_stamp}}</td>
                                <td>{{$item->attempts}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>        
            </div>
        </div>
    </div>
@endif
@if(isset($adverseBulk))
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-header">
                <strong class="card-title">{{$branchId}} Branch - Adverse Bulk</strong>
            </div>
            <div class="card-body">
                <div>
                    @php 
                        $total=$adverseBulk['total'];
                        $com=count($adverseBulk['data']);
                        $per=($com!=0)?(($com/$total)*100):0;
                    @endphp
                    <span style="margin-right:10px">Total Data :</span><span style="margin-right:10px">{{$total}}</span>
                    <span style="margin-right:10px">Total Compliance :</span><span style="margin-right:10px">{{$com}}</span>
                    <span style="margin-right:10px">Total per :</span><span style="margin-right:10px">{{$per}}%</span>
                </div>
                <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_4">
                        <thead>
                            <tr>
                                <th>AGRMNT ID</th>
                                <th>PRODUCT FLAG</th>
                                <th>PRODUCT FLAG_Q</th>
                                <th>BRANCH</th>
                                <th>BOM POS</th>
                                <th>Agency Code</th>
                                <th>Agency Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($adverseBulk['data'] as $item)
                            {{-- {{dd($item)}} --}}
                            <tr>
                                <td>{{$item->AGRMNTID}}</td>
                                <td>{{$item->PRODUCTFLAG}}</td>
                                <td>{{$item->PRODUCTFLAG_Q}}</td>
                                <td>{{$item->BRANCH }}</td>
                                <td>{{$item->month_BOM_POS}}</td>
                                <td>{{$item->month_Agent_Code}}</td>
                                <td>{{$item->month_Agency_Name}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>        
            </div>
        </div>
    </div>
@endif
@if(isset($settlement))
    <div class="col-lg-12">
        <div class="card w-100">
            <div class="card-header">
                <strong class="card-title">{{$branchId}} Branch - Settlement</strong>
            </div>
            <div class="card-body">
                <div>
                    @php 
                        $total=$settlement['total'];
                        $com=count($settlement['data']);
                        $per=($com!=0)?(($com/$total)*100):0;
                    @endphp
                    <span style="margin-right:10px">Total Data :</span><span style="margin-right:10px">{{$total}}</span>
                    <span style="margin-right:10px">Total Compliance :</span><span style="margin-right:10px">{{$com}}</span>
                    <span style="margin-right:10px">Total per :</span><span style="margin-right:10px">{{$per}}%</span>
                </div>
                <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_5">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>REQUEST NO</th>
                                <th>LOAN NO</th>
                                <th>CUSTOMERNAME</th>
                                <th>BRANCH</th>
                                <th>STATE</th>
                                <th>PRODUCT </th>
                                <th>SCHEME DESC</th>
                                <th>PENALTY</th>
                                <th>LOANAMT</th>
                                <th>EMI</th>
                                <th>SETTLEMENTAMT</th>
                                <th>REQUEST_DATE</th>
                                <th>REQUESTED_BY</th>
                                <th>SETTLEMENTSTART_DATE</th>
                                <th>SETTLEMENTEND_DATE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($settlement['data'] as $item)
                            {{-- {{dd($item)}} --}}
                            <tr>
                                <td>{{$item->Month}}</td>
                                <td>{{$item->REQUEST_NO}}</td>
                                <td>{{$item->LOAN_NO}}</td>
                                <td>{{$item->CUSTOMERNAME}}</td>
                                <td>{{$item->BRANCH}}</td>
                                <td>{{$item->STATE}}</td>
                                <td>{{$item->PRODUCT_1}}</td>
                                <td>{{$item->SCHEMEDESC}}</td>
                                <td>{{$item->PENALTY}}</td>
                                <td>{{$item->LOANAMT}}</td>
                                <td>{{$item->EMI}}</td>
                                <td>{{$item->SETTLEMENTAMT}}</td>
                                <td>{{$item->REQUESTED_BY}}</td>
                                <td>{{$item->SETTLEMENTSTART_DATE}}</td>
                                <td>{{$item->SETTLEMENTEND_DATE}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>        
            </div>
        </div>
    </div>
@endif
</div>

@endsection
@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
/* Center the loader */
#loader {
  position: absolute;
  left: 60%;
  top: 60%;
  z-index: 1;
  /* margin: -75px 0 0 -75px; */
  border: 2px solid #f3f3f3;
  border-radius: 50%;
  border-top: 2px solid #3498db;
  width: 60px;
  height: 60px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}



</style>
@endsection

@section('js')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
    <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        jQuery('#branch').on('change',function(e){
            // jQuery('#gap').submit();
            getAgency(e.target.value,'')
        })
        jQuery('#lob').on('change',function(e){
             getBranch(e.target.value,'')
        })
        jQuery('#submit').on('click',function(e){
            // e.preventDefault();
            // jQuery('#gap').submit();
            document.getElementById("loader").style.display = "block";
            document.getElementById("myDiv").style.display = "none";
        })
        
        jQuery(document).ready(function() {
            document.getElementById("loader").style.display = "none";
            document.getElementById("myDiv").style.display = "block";
            jQuery('#kt_table_1').DataTable();
            jQuery('#kt_table_2').DataTable();
            jQuery('#kt_table_3').DataTable();
            jQuery('#kt_table_4').DataTable();
            jQuery('#kt_table_5').DataTable();
            jQuery('.datepicker').daterangepicker();
            @if(isset($branchId) && $branchId!='')
                getBranch('{{$lob}}','{{$branchId}}')
            @endif
            @if(isset($agencyId))
                getAgency('{{$branchId}}','{{$agencyId}}')
            @endif
    })
    function getAgency(name,selected){
        var saveAlert = jQuery.ajax({
            type: 'get',
            url: "{{url('get-agencies-upload')}}/"+name,
            accept: "application-json",
            processData: false,
            contentType: false,
            success: function(resultData) { 
                // console.log(resultData)
                var html='<option>Choose Agency Name</option>'
                for( var val in resultData.data ) {
                    if( resultData.data.hasOwnProperty( val ) ) {
                        html=html+`<option value="${resultData.data[val]}" ${(resultData.data[val]==selected)?'selected':''} >${resultData.data[val]}</option>`
                    }
                }
                jQuery('#agency').html(html)
            }
        });
        saveAlert.error(function() { alert("Something went wrong"); });

    }
    function getBranch(lob,selected){
        var saveAlert = jQuery.ajax({
            type: 'get',
            url: "{{url('get-branch-upload')}}/"+lob,
            accept: "application-json",
            processData: false,
            contentType: false,
            success: function(resultData) { 
                // console.log(resultData)
                var html='<option>Choose branch Name</option>'
                for( var val in resultData ) {
                    if( resultData.hasOwnProperty( val ) ) {
                        html=html+`<option value="${resultData[val]}" ${(resultData[val]==selected)?'selected':''} >${resultData[val]}</option>`
                    }
                }
                jQuery('#branch').html(html)
            }
        });
        saveAlert.error(function() { alert("Something went wrong"); });

    }
    </script>
@endsection