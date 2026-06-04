@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<style>
    .consultant-type-card .data-table-wrapper {
        min-height: 280px;
    }

    .consultant-type-card #users-list {
        width: 100% !important;
    }

    .consultant-type-card .dt-buttons {
        margin-bottom: 1rem;
    }

    .consultant-type-card .card-header {
        padding: 1.25rem 1.5rem;
    }

    .consultant-type-card .card-body {
        padding: 1.5rem;
    }

    .consultant-type-card .card-title {
        font-size: 1.125rem;
        font-weight: 600;
    }
</style>

<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card consultant-type-card shadow-sm">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h5 class="card-title mb-1">Consultant Types</h5>
                    <p class="text-muted mb-0">Review, add, and maintain the consultant categories available across the system.</p>
                </div>
                <button type="button" class="btn btn-primary add_new_record">
                    Add Consultant Type
                </button>
            </div>
            <div class="card-body">
                <div class="data-table-wrapper">
                    <div class="table-responsive">
                        <table id="users-list" class="table table-striped table-hover align-middle mb-0 w-100">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th style="width: 130px;">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_new_record_model" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content form-submit-event" id="from_submit">
            <input type="hidden" id="id" name="id" value="0">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Add Consultant Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label">Name<span class="asterisk">*</span></label>
                        <input type="text" required id="market_name" name="name" class="form-control" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" id="submit_btn" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/js/jquery.form.min.js') }}"></script>
<script>
    $("body").on("click", ".add_new_record", function() {
        $("#id").val(0);
        $("#market_name").val('');
        $("#add_new_record_model").modal("show");
    });

    $("body").on("click", ".edit_record", function() {
        const record_id = $(this).attr("data-id");
        const details = JSON.parse($(this).attr("data-details"));

        $("#id").val(record_id);
        $("#market_name").val(details.name);
        $("#add_new_record_model").modal("show");
    });

    $("body").on("click", "#submit_btn", function(e) {
        e.preventDefault();

        $("#from_submit").ajaxSubmit({
            url: "{{ route('pos.save_consultant_type') }}",
            type: 'post',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function() {
                $("#add_new_record_model").modal("hide");
                user_table.ajax.reload();
            },
            error: function(XMLHttpRequest) {
                alert(JSON.parse(XMLHttpRequest.responseText).message);
            }
        });
    });

    $(document).ready(function() {
        let district_id = "";
        let police_station_id = [];
        let leave_request_id = '';

        user_table = $('#users-list').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            lengthMenu: [
                [100, 250, 500, 1000],
                ['100', '250', '500', '1000']
            ],
            pageLength: 50,
            ajax: {
                url: '{{ route("pos.list_consultant_type") }}',
                data: function(d) {
                    d.user_id = $('#attendance_user_filter').val();
                }
            },
            columns: [{
                    data: 'name',
                    name: 'name',
                    searchable: true
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            responsive: true,
            searching: true,
            sorting: true,
            paging: true,
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        });

        $('#attendance_user_filter, #attendance_date_from, #attendance_date_to').on('change', function(e) {
            e.preventDefault();
            user_table.ajax.reload();
        });

        $("body").on("click", ".delete_record", function() {
            const id = $(this).attr("data-id");
            if (confirm('Are you sure to delete this record ?')) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.deactivate_record') }}",
                    data: {
                        id: id,
                        table: "consultant_type",
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        window.location.reload();
                    }
                })
            } else {
                alert('Why did you press cancel? You should have confirmed');
            }
        });
    });
</script>
@endpush