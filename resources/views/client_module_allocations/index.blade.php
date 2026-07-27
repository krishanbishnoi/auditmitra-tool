@extends('layouts.master')

@section('title', '| Client Module Allocation')

@section('content')

    <div class="row">
        <div class="col-lg-12" style="margin-top:10x">
        </div>
    </div>

    <div class="animated fadeIn">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <strong class="card-title">Module List </strong>                       
                    </div>
                    <div class="card-body">                        
                    <form action="{{ route('client_module_allocation.module_allocation_update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="clientId" value="{{$clientId}}" />
                        <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">
                            <thead>
                            <tr>
                                <th scope="col"><input type="checkbox" id="selectall" name="module_id_all" value="all" /></th>                         
                                <th scope="col">
                                    Module Name
                                </th>                               
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($moduleList as $alloc)
                                <tr scope="row">
                                    <td>
                                        <input class="modulebox" type="checkbox" name="module_id[{{$alloc->id}}]" value="{{$alloc->id}}" 
                                        <?php if(in_array($alloc->id,$allocations)) { echo "checked"; } ?> />
                                    </td>                                  
                                    <td>{{ $alloc->module_name }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fa fa-dot-circle-o"></i> Submit
                            </button>               
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')

<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"> -->

@endsection

@section('js')

<!-- <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script> -->

<script>

    // jQuery(document).on('ready', function () {
    //     jQuery('#kt_table_1').DataTable();
    // });

    // Select or Deselect all checkboxes
    $('#selectall').change(function () {
        if ($('#selectall').is(":checked")) {
            $('.modulebox').prop('checked', true);
        } else {
            $('.modulebox').prop('checked', false);
        }
    });

    // To reflect the state of the "Select All" checkbox based on individual checkboxes
    $('.modulebox').change(function () {
        // Check if all checkboxes are selected
        if ($('.modulebox:checked').length === $('.modulebox').length) {
            $('#selectall').prop('checked', true);
        } else {
            $('#selectall').prop('checked', false);
        }
    });

</script>

@endsection
