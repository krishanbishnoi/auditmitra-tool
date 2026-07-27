@extends('layouts.master')

@section('sh-title')
Audit Alert Box
@endsection

@section('sh-detail')
View Intimation Details
@endsection

@section('content')
<div class="card" id="kt_repeater_1">
    <div class="card-header">
        <h3 class="card-title">View Intimation Details</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
                <!-- Audit Creator -->
                <tr>
                    <th>Audit Creator</th>
                    <td>{{ $intimationDetails->user->name ?? 'N/A' }}</td>
                </tr>

                <!-- Audit Date -->
                <tr>
                    <th>Audit Date</th>
                    <td>{{ $intimationDetails->audit_date }}</td>
                </tr>

                <!-- Agency Name -->
                <tr>
                    <th>Agency Name</th>
                    @php 
												$agency_name = DB::table('agencies')->where('id', $intimationDetails->agency)->pluck('name')->first();
                                                $agency_loc = DB::table('agencies')->where('id', $intimationDetails->agency)->pluck('location')->first();
											@endphp
                    <td>{{ $agency_name ?? 'N/A' }} ({{$agency_loc?? ''}})</td>
                </tr>

                <!-- Agency Email -->
                <tr>
                    <th>Agency Email</th>
                    <td>{{ $intimationDetails->agency_email }}</td>
                </tr>

                <!-- Product Name -->
                <!-- <tr>
                    <th>Product Name</th>
                    <td>{{ $intimationDetails->product->name ?? 'N/A' }}</td>
                </tr> -->

                <!-- Auditor Name -->
                <tr>
                    <th>Auditor Name</th>
                    <td>{{ $intimationDetails->auditor ?? 'N/A' }}</td>
                </tr>

                <!-- Level 3 Users -->
                <tr>
                    <th>Level 3 Users</th>
                    <td>
                       
                            @foreach($level3Users as $user)
                               <li> {{ $user->name }} </li>
                            @endforeach
                       
                    </td>
                </tr>

                <tr>
                    <th>Level 4 Users</th>
                    <td>
                       
                            @foreach($level4Users as $user)
                            <li> {{ $user->name }} </li>
                            @endforeach
                        
                    </td>
                </tr>

                <tr>
                    <th>Level 5 Users</th>
                    <td>
                        
                            @foreach($level5Users as $user)
                            <li> {{ $user->name }} </li>
                            @endforeach
                      
                    </td>
                </tr>

                <!-- Process Review Month -->
                <tr>
                    <th>Process Review Month</th>
                    <td>{{ $intimationDetails->process_review_month }}</td>
                </tr>

                <!-- Description -->
                <tr>
                    <th>Description</th>
                    <td>{{ $intimationDetails->description }}</td>
                </tr>

                <!-- Created At -->
                <tr>
                    <th>Created At</th>
                    <td>{{ $intimationDetails->created_at }}</td>
                </tr>

                <!-- Updated At -->
                <tr>
                    <th>Updated At</th>
                    <td>{{ $intimationDetails->updated_at }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
