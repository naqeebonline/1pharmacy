@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <style>
        .table > :not(caption) > * > * {padding: 5px;}
    </style>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h4 class="card-title">OPD Type Configuration</h4>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('pos.create_opd_type') }}" class="btn btn-primary">
                                <i class="bx bx-plus"></i> Add New OPD Type
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive" style="min-height: 200px">
                                <table id="opd-types-list" class="table table-responsive table-striped data_mf_table table-condensed">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Fees</th>
                                            <th>Medicine</th>
                                            <th>Labs</th>
                                            <th>Products</th>
                                            <th>Investigations</th>
                                            <th style="width: 10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
 <script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script>
        $(document).ready(function() {
            var user_table = $('#opd-types-list').DataTable({
                ajax: {
                    url: "{{ route('pos.list_opd_types') }}",
                    dataSrc: "data"
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                        searchable: true
                    },
                    {
                        data: 'name',
                        name: 'name',
                        searchable: true
                    },
                    {
                        data: 'fees',
                        name: 'fees',
                        searchable: true,
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'including_medicine',
                        name: 'including_medicine',
                        searchable: false,
                        render: function(data) {
                            return data == 1 
                                ? '<span class="badge bg-success">Yes</span>' 
                                : '<span class="badge bg-secondary">No</span>';
                        }
                    },
                    {
                        data: 'including_labs',
                        name: 'including_labs',
                        searchable: false,
                        render: function(data) {
                            return data == 1 
                                ? '<span class="badge bg-success">Yes</span>' 
                                : '<span class="badge bg-secondary">No</span>';
                        }
                    },
                    {
                        data: 'products_count',
                        name: 'products_count',
                        searchable: false
                    },
                    {
                        data: 'investigations_count',
                        name: 'investigations_count',
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                searching: true,
                sorting: true,
                paging: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            // Delete OPD type
            $("body").on("click", ".delete_opd_type", function(e) {
                e.preventDefault();
                var opd_type_id = $(this).attr("data-id");
                
                if (confirm("Are you sure you want to delete this OPD type?")) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ url('delete_opd_type') }}/" + opd_type_id,
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.status) {
                                alert(res.message);
                                user_table.ajax.reload();
                            } else {
                                alert(res.message);
                            }
                        },
                        error: function(xhr) {
                            alert('Error deleting OPD type');
                        }
                    });
                }
            });
        });
    </script>
@endpush
