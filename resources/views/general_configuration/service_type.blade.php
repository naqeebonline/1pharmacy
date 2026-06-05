@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<style>
    .table> :not(caption)>*>* {
        padding: 5px;
    }

    .service-type-card {
        min-height: calc(100vh - 200px);
        display: flex;
        flex-direction: column;
    }

    .service-type-card .card-body {
        flex: 1 1 auto;
    }

    .service-type-card .table-responsive {
        flex: 1 1 auto;
        min-height: 0;
    }

    .service-type-card .card-body .col-12.d-flex {
        flex: 1 1 auto;
    }

    #users-list {
        width: 100% !important;
    }
</style>

<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')

<div class="row">
    <div class="col-12">

        <!-- Traffic sources -->
        <div class="card service-type-card">
            <div class="card-header header-elements-inline">
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-primary add_new_record">Add Service Type</button>
                    <button type="button" class="btn btn-outline-secondary" id="print_all_services">Print All Services</button>
                </div>
            </div>

            <div class="card-body d-flex flex-column">

                <div class="row flex-grow-1">


                    <div class="col-12 d-flex flex-column">

                        <div class="table-responsive flex-grow-1">

                            <table id="users-list" class="table table-striped data_mf_table table-condensed w-100">

                                <thead>
                                    <tr>
                                        <th>Name</th>
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
        <!-- /traffic sources -->
    </div>
</div>

<div class="modal fade" id="add_new_record_model" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content form-submit-event" id="from_submit">
            <input type="hidden" id="id" name="id" value="0">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Add Service Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label">Name<span class="asterisk">*</span></label>
                        <input type="text" required id="market_name" name="name" class="form-control" placeholder="" autocomplete="off">
                    </div>
                </div>




            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close </button>
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
    $("body").on("click", ".add_new_record", function(e) {
        $("#id").val(0);
        $("#market_name").val('');
        $("#add_new_record_model").modal("show");

    });

    $("body").on("click", ".edit_record", function(e) {
        record_id = $(this).attr("data-id");
        var details = JSON.parse($(this).attr("data-details"));

        $("#id").val(record_id);

        $("#market_name").val(details.name);
        $("#add_new_record_model").modal("show");
    });

    $("body").on("click", "#submit_btn", function(e) {
        e.preventDefault();

        $("#from_submit").ajaxSubmit({
            url: "{{route('pos.save_service_type')}}",
            type: 'post',
            data: {
                _token: '{{ csrf_token() }}'

            },
            success: function(response) {
                $("#add_new_record_model").modal("hide");
                user_table.ajax.reload();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                //console.log();
                //alert("Status: " + textStatus); alert("Error: " + errorThrown);
                alert(JSON.parse(XMLHttpRequest.responseText).message);
            }
        });
    });

    $(document).ready(function() {

        district_id = "";
        police_station_id = [];
        leave_request_id = '';

        user_table = $('#users-list').DataTable({
            processing: true,
            serverSide: true,

            lengthMenu: [
                [100, 250, 500, 1000],
                ['100', '250', '500', '1000']
            ],
            pageLength: 50,
            ajax: {
                url: '{{route("pos.list_service_type")}}',
                data: function(d) {
                    d.user_id = $('#attendance_user_filter').val();
                    /*d.attendance_date_from = $('#attendance_date_from').val();
                    d.attendance_date_to = $('#attendance_date_to').val();*/


                }

            },

            columns: [

                {
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
            processing: true,
            serverSide: true,
            searching: true,
            sorting: true,
            paging: true,
            dom: 'Bfrtip',
            buttons: [
                'copy',
                'csv',
                'excel',
                {
                    text: 'Print All Services',
                    className: 'btn btn-secondary',
                    action: function() {
                        window.open("{{ route('pos.print_service_type') }}", '_blank');
                    }
                }
            ]
        });

        $('#attendance_user_filter, #attendance_date_from, #attendance_date_to').on('change', function(e) {
            e.preventDefault();
            user_table.ajax.reload();
        });

        $('#print_all_services').on('click', function() {
            window.open("{{ route('pos.print_service_type') }}", '_blank');
        });

        $("body").on("click", ".delete_record", function(e) {
            var id = $(this).attr("data-id");
            if (confirm('Are you sure to delete this record ?')) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.deactivate_record') }}",
                    data: {
                        id: id,
                        table: "service_type",
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(res) {
                        //user_table.dataTable.reload();
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