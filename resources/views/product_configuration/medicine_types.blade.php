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
                <div class="card-header header-elements-inline">
                    <div class="btn btn-primary add_new_record">Add Medicine Type</div>
                </div>

                <div class="card-body">

                    <div class="row">
                        <div class="col-12">

                            <div class="table-responsive" style="min-height: 200px">

                                <table id="medicine-types-list" class="table table-responsive table-striped data_mf_table table-condensed">

                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Value</th>
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

    <div class="modal fade" id="add_new_record_model" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="modal-content form-submit-event" id="from_submit">
                <input type="hidden" id="id" name="id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title" id="medicine_type_modal_title">Add Medicine Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="medicine_type_name" class="form-label">Name<span class="asterisk">*</span></label>
                            <input type="text" required id="medicine_type_name" name="name" class="form-control" placeholder="" autocomplete="off">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="medicine_type_value" class="form-label">Value<span class="asterisk">*</span></label>
                            <input type="text" required id="medicine_type_value" name="value" class="form-control" placeholder="" autocomplete="off">
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

        $("body").on("click", ".add_new_record", function (e) {
            $("#id").val(0);
            $("#medicine_type_name").val('');
            $("#medicine_type_value").val('');
            $("#medicine_type_modal_title").text('Add Medicine Type');
            $("#add_new_record_model").modal("show");
        });

        $("body").on("click", ".edit_record", function (e) {
            var record_id = $(this).attr("data-id");
            var details = JSON.parse($(this).attr("data-details"));

            $("#id").val(record_id);
            $("#medicine_type_name").val(details.name);
            $("#medicine_type_value").val(details.value);
            $("#medicine_type_modal_title").text('Edit Medicine Type');
            $("#add_new_record_model").modal("show");
        });

        $("body").on("click", "#submit_btn", function (e) {
            e.preventDefault();

            $("#from_submit").ajaxSubmit({
                url: "{{ route('pos.save_medicine_types') }}",
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    $("#add_new_record_model").modal("hide");
                    medicine_types_table.ajax.reload();
                },
                error: function (XMLHttpRequest) {
                    var msg = 'Save failed';
                    try {
                        msg = JSON.parse(XMLHttpRequest.responseText).message;
                    } catch (err) {}
                    alert(msg);
                }
            });
        });

        $(document).ready(function () {

            medicine_types_table = $('#medicine-types-list').DataTable({
                processing: true,
                serverSide: true,
                lengthMenu: [
                    [100, 250, 500, 1000],
                    ['100', '250', '500', '1000']
                ],
                pageLength: 50,
                ajax: {
                    url: '{{ route("pos.list_medicine_types") }}',
                    data: function (d) {
                    }
                },
                columns: [
                    {data: 'name', name: 'name', searchable: true},
                    {data: 'value', name: 'value', searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                responsive: true,
                searching: true,
                sorting: true,
                paging: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            $("body").on("click", ".delete_record", function (e) {
                var id = $(this).attr("data-id");
                if (confirm('Are you sure you want to delete this medicine type?')) {
                    $.ajax({
                        type: 'post',
                        url: "{{ route('pos.delete_medicine_type') }}",
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function () {
                            medicine_types_table.ajax.reload();
                        },
                        error: function (XMLHttpRequest) {
                            var msg = 'Delete failed';
                            try {
                                msg = JSON.parse(XMLHttpRequest.responseText).message;
                            } catch (err) {}
                            alert(msg);
                        }
                    });
                }
            });
        });

    </script>
@endpush
