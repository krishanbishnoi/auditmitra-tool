@extends('layouts.master')

@section('css')

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

@endsection

@section('content')

 <!-- Content -->

 <input type="hidden" name="url" id="url" value={{url('/')}}>

 <input type="hidden" name="token" id="token" value={{@csrf_token()}}>

    <div class="content" style="font-size: 13px !important;">

        <!-- Animated -->

        <div class="animated fadeIn">

            <!-- Widgets  -->

            <div class="card">

                <div class="card-header">

                 <form action="{{route('dashboard')}}" method="post"> 

                        @csrf

                        <div class="row">

                            <div class="col-md-3"><h4>My Dashboard</h4></div>

                            <div class="col-md-9">

                                <label for="lob">Line of Business</label>

                                <select class="text-right" id="Productlob" name="productlob">

                                    <option value="all" {{(isset($old['productlob']) && $old['productlob']=='all')?'selected':''}}>All</option>

                                    <option value="collection" {{(isset($old['productlob']) && $old['productlob']=='collection')?'selected':''}}>IDFC First Collection</option>

                                    <option value="commercial_vehicle" {{(isset($old['productlob']) && $old['productlob']=='commercial_vehicle')?'selected':''}}>IDFC First Commercial Vehicle</option>

                                    <option value="rural" {{(isset($old['productlob']) && $old['productlob']=='rural')?'selected':''}}>IDFC First Rural</option>

                                    <option value="alliance" {{(isset($old['productlob']) && $old['productlob']=='alliance')?'selected':''}}>IDFC First Alliance</option>

                                    <option value="credit_card" {{(isset($old['productlob']) && $old['productlob']=='credit_card')?'selected':''}}>IDFC Credit Card</option>

                                </select>

                                <label for="audit_cycle">Audit Cycle</label>

                                <select class="text-right " name="lob_audit_cycle" id="lob_audit_cycle">

                                    <option value="current" {{(isset($old['lob_audit_cycle']) && $old['lob_audit_cycle']=='current')?'selected':''}}>Current Audit Cycle</option>

                                <option value="last_2" {{(isset($old['lob_audit_cycle']) && $old['lob_audit_cycle']=='last_2')?'selected':''}}>Last 2 Audit Cycle</option>

                                <option value="last_3" {{(isset($old['lob_audit_cycle']) && $old['lob_audit_cycle']=='last_3')?'selected':''}}>Last 3 Audit Cycle</option>

                                <option value="last_4" {{(isset($old['lob_audit_cycle']) && $old['lob_audit_cycle']=='last_4')?'selected':''}}>Last 4 Audit Cycle</option>

                                <option value="custom" {{(isset($old['lob_audit_cycle']) && $old['lob_audit_cycle']=='custom')?'selected':''}}>Custom Audit Cycle</option>

                                </select>

                            <input type="text" style="{{(isset($old['lob_audit_cycle']) && $old['lob_audit_cycle']=='custom')?'':'display:none;'}}" value="{{(isset($old['lob_audit_cycle_custom']))? $old['lob_audit_cycle_custom']:''}}" name="lob_audit_cycle_custom" id="lob_audit_cycle_custom"/>

                                <input type="submit" class="text-right ml-2" value="Show Result">

                            </div>

                        </div>

                     </form> 

                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-lg-3 col-md-6">

                            <div class="card">

                                <div class="card-body">

                                    <div class="stat-widget-five text-center">

                                        <div class="text-center dib">

                                            <div class="stat-heading">Total Audits</div>

                                            <div class="stat-text"><span class="count">{{ $totalAudit ?? 0 }}</span></div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>



                        <div class="col-lg-3 col-md-6">

                            <div class="card">

                                <div class="card-body">

                                    <div class="stat-widget-five text-center">

                                        <div class="text-center dib">

                                            <div class="stat-heading">Total Pending To Approved</div>

                                            <div class="stat-text"><span class="count">{{ $totalpending ?? 0}}</span></div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>



                        <div class="col-lg-3 col-md-6">

                            <div class="card">

                                <div class="card-body">

                                    <div class="stat-widget-five text-center">

                                        <div class="text-center dib">

                                            <div class="stat-heading">Audit Approved</div>

                                            <div class="stat-text"><span class="count">{{ $totalApproved ?? 0 }}</span></div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-3 col-md-6">

                            <div class="card">

                                <div class="card-body">

                                    <div class="stat-widget-five text-center">

                                        <div class="text-center dib">

                                            <div class="stat-heading">Audit Approved Without Changes</div>

                                            <div class="stat-text"><span class="count">{{ $totalpass ?? 0 }}</span></div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-3 col-md-6">

                            <div class="card">

                                <div class="card-body">

                                    <div class="stat-widget-five text-center">

                                        <div class="text-center dib">

                                            <div class="stat-heading">Audit Approved With Changes</div>

                                            <div class="stat-text"><span class="count">{{$totalpassChange ?? 0}}</span></div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-3 col-md-6">

                            <div class="card">

                                <div class="card-body">

                                    <div class="stat-widget-five text-center">

                                        <div class="text-center dib">

                                            <div class="stat-heading">Audit Saved</div>

                                            <div class="stat-text"><span class="count">{{$totalsaved ?? 0}}</span></div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-3 col-md-6">

                            <div class="card">

                                <div class="card-body">

                                    <div class="stat-widget-five text-center">

                                        <div class="text-center dib">

                                            <div class="stat-heading">Audit Failed</div>

                                            <div class="stat-text"><span class="count">{{$totalfaild ?? 0}}</span></div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12">

                        <table class="table table-striped  table-hover table-checkable" id="kt_table_1">

                                    <thead style="background-color: rgba(0,0,0,.03);">

                                        <tr>

                                            <th>Audit Name</th>

                                            <th>Submited Date</th>

                                            <th>Days Passed Since Audit Submited</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        @foreach ($totalPendingList as $item)

                                        @php 

                                        $created = \carbon\Carbon::parse($item->created_at);

                                        $now = \carbon\Carbon::now();

                                        $difference = ($created->diff($now)->days < 1)

                                            ? '0 Day'

                                            : $created->diffForHumans($now);



                                        @endphp

                                        <tr>

                                            <td>{{$item->qmsheet->name ?? ''}}</td>

                                            <td>{{$item->created_at}}</td>

                                            <td>{{$difference}}</td>

                                        </tr>

                                        @endforeach

                                    </tbody>

                                </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- .animated -->

    </div>

<!-- /.content -->

    <div class="clearfix"></div>

@endsection



@section('js')

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

{{-- @include('shared.table_js'); --}}

<script>

	jQuery(document).on('ready',function(){

		jQuery('#kt_table_1').DataTable();

	})

</script>

@endsection

