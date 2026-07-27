@extends('layouts.master')
@section('title', '| Users')

@section('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
@endsection

@section('content')
<div class="animated fadeIn">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong class="card-title">Audit Allocation Upload List</strong>
                    <div class="d-flex align-items-center gap-2">
                        <div id="exportButtons" class="mr-2"></div>
                        <a class="btn btn-info btn-sm mr-2" href="{{ route('auditallocationBulkUpload') }}">Audit Allocation Upload</a>
                        <button id="bulk_delete_btn" class="btn btn-danger btn-sm" style="display: none;">Bulk Delete</button>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <table class="table table-bordered table-hover" id="kt_table_1">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select_all" /></th>
                                <th>Sr.No.</th>
                                <th class="font-weight-bold">Final Agency Name</th>
                                <th>Agency Code</th>
                                <th>Type</th>
                                <th>Product</th>
                                <th>Location</th>
                                <th>Process Review Agency</th>
                                <th>Process Review Period</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditAllocations as $row)
                                <tr>
                                    <td><input type="checkbox" class="select_item" value="{{ $row->id }}" /></td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="font-weight-bold">{{ $row->final_agency_name }}</td>
                                    <td>{{ $row->agency_code }}</td>
                                    <td>{{ $row->type_of_agency }}</td>
                                    <td>{{ $row->product }}</td>
                                    <td>{{ $row->location }}</td>
                                    <td>{{ $row->process_review_agency }}</td>
                                    <td>{{ $row->process_review_period }}</td>
                                    <td>{{ $row->created_at->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ url('audit_allocation/' . Crypt::encrypt($row->id)) }}" class="btn btn-success btn-sm" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <form action="{{ route('audit_allocation.destroy', Crypt::encrypt($row->id)) }}" method="POST" onsubmit="return confirmDelete();">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- jQuery + DataTables + Export Buttons -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
    $(document).ready(function () {
        let selectedIds = [];

        // Initialize DataTable with search bar, export buttons, pagination
        let table = $('#kt_table_1').DataTable({
            dom: '<"top d-flex justify-content-between mb-2"<"d-flex"l><"ml-auto"f>>rt<"bottom d-flex justify-content-between mt-2"ip>',
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    title: 'Audit_Allocation_Report',
                    exportOptions: {
                        columns: ':not(:first-child):not(:last-child)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    title: 'Audit_Allocation_Report',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':not(:first-child):not(:last-child)'
                    },
                    customize: function (doc) {
                        doc.defaultStyle.fontSize = 8;
                    }
                }
            ]
        });

        // Move export buttons to custom div
        table.buttons().container().appendTo('#exportButtons');

        // Checkbox select/deselect logic
        $('#select_all').on('change', function () {
            $('.select_item').prop('checked', $(this).prop('checked'));
            updateSelectedIds();
        });

        $(document).on('change', '.select_item', function () {
            updateSelectedIds();
        });

        function updateSelectedIds() {
            selectedIds = $('.select_item:checked').map(function () {
                return $(this).val();
            }).get();

            $('#bulk_delete_btn').toggle(selectedIds.length > 0);
        }

        $('#bulk_delete_btn').on('click', function () {
            if (confirm('Are you sure you want to delete the selected records?')) {
                $.ajax({
                    url: "{{ route('auditallocation.bulkDelete') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    success: function (response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function () {
                        alert('An error occurred while processing your request.');
                    }
                });
            }
        });
    });

    function confirmDelete() {
        return confirm('Are you sure you want to delete this item?');
    }
</script>
@endsection
