@extends('layouts.master')



@section('title', '| Clients')


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- @section('sh-detail')

Users

@endsection -->



@section('content')
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div class="row">

    <div class="col-lg-12" style="margin-top:10x">

    </div>

</div>

<div class="animated fadeIn">

    <div class="row">

        <div class="col-lg-12">

            <div class="card">


                <div class="card-body">

                    <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">

                        <thead>

                            <tr>

                                <th scope="col">#</th>

                                <th scope="col">

                                    Name

                                </th>



                                <th scope="col">

                                    Email

                                </th>

                                <th scope="col">

                                    Phone

                                </th>




                                <th scope="col">

                                    Actions

                                </th>


                            </tr>

                        </thead>

                        <tbody>
                            @foreach($data as $row)
                            @if(($row->roles->isNotEmpty() && $row->roles->first()->name != 'Quality Auditor') || $row->is_approved == 1)
                            <tr scope="row">
                                <td>{{$loop->iteration}}</td>

                                <td>{{$row->name}}</td>






                                <td>{{$row->email}}</td>

                                <td>{{$row->mobile}}</td>


                                <td nowrap>
                                    
                                    <a href="{{ route('masterqa.client_list', $row->id) }}" class="btn btn-sm btn-secondary" title="Auditor Assign">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>


                                
                            </tr>
                            @endif
                            @endforeach
                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection

@section('css')

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">

@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>
    jQuery(document).on('ready', function() {

        jQuery('#kt_table_1').DataTable();

    })
</script>

@endsection