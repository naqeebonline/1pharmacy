@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <style>
        .table> :not(caption)>*>* {
            padding: 5px;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')
    <div class="row">
        <div class="col-12">

            <!-- Traffic sources -->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <div class="btn btn-primary add_new_record">Investigation Sub Category</div>

                </div>

                <div class="card-body">

                    <div class="row">


                        <div class="col-12">

                            <div class="table-responsive" style="min-height: 200px">

                                <table id="users-list"
                                    class="table table-responsive table-striped data_mf_table table-condensed">

                                    <thead>
                                        <tr>
                                            <th>Main Category</th>
                                            <th>Name</th>
                                            <th>Is ICT</th>
                                            <th>Purchase Price</th>
                                            <th>Sale Price</th>
                                            <th>Test Type</th>
                                            <th>Report Headings</th>
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
                    <h5 class="modal-title" id="exampleModalLabel1">Add Investigation Sub Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Investigation Main Category<span
                                    class="asterisk">*</span></label>
                            <select name="investigation_category_id" id="investigation_category_id" class="form-select">
                                <option disabled selected value="">Main Category...</option>
                                @forelse ($inv_main_category as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Name<span class="asterisk">*</span></label>
                            <input type="text" required id="name" name="name" class="form-control" placeholder=""
                                autocomplete="off">
                        </div>



                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Purchase Rate<span class="asterisk">*</span></label>
                            <input type="text" required id="price" name="price" class="form-control" placeholder=""
                                autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Sale Price<span class="asterisk">*</span></label>
                            <input type="text" required id="sale_price" name="sale_price" class="form-control" placeholder=""
                                   autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Select Type...<span class="asterisk">*</span></label>
                            <select name="is_parameter" id="is_parameter" class="form-select">
                                <option value="">Select Type...</option>
                                <option selected="selected" value="1">Parmeter</option>
                                <option value="0">Textual</option>

                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Result Text Format (For X-Ray and Ultra sound etc)<span class="asterisk">*</span></label>
                            <textarea class="form-control" id="result_text" name="result_text"></textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <input type="checkbox" value="1" name="is_ict" id="is_ict"
                                    class="form-check-input" />
                                <label for="is_ict" class="ms-1" style="font-weight: bold">Is ICT</label>
                            </div>
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


    <div class="modal fade" id="add_report_heading_model" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="modal-content form-submit-event">
                <input type="hidden" id="investigation_sub_category_id" name="investigation_sub_category_id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add Report Heading</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Index Number<span class="asterisk">*</span></label>
                            <input type="number" required id="index_number" name="index_number" class="form-control" placeholder=""
                                   autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Heading Name<span class="asterisk">*</span></label>
                            <input type="text" required id="heading_name" name="heading_name" class="form-control" placeholder=""
                                   autocomplete="off">
                        </div>





                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close </button>
                    <a type="submit" id="save_report_heading" class="btn btn-primary">Save</a>
                </div>
        </from>
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
        $("body").on("change", "#is_parameter", function(e) {
            var value = $(this).val();
            if(value == 0){
                $("#result_text").val('');
                $("#result_text_div").show();
            }else{
                $("#result_text").val('');
                $("#result_text_div").hide();
            }
        });
        $("body").on("click", ".add_new_record", function(e) {
            $("#id").val(0);
            $("#name").val('');
            $("#investigation_category_id").val('');
            $("#is_parameter").val('');
            $("#price").val('');
            $("#sale_price").val('');
            $("#result_text").val('');
            $("#is_ict").prop('checked', false);

            $("#add_new_record_model").modal("show");

        });

        $("body").on("click", ".edit_record", function(e) {
            record_id = $(this).attr("data-id");
            var details = JSON.parse($(this).attr("data-details"));

            $("#id").val(record_id);

            $("#name").val(details.name);
            $("#investigation_category_id").val(details.investigation_category_id);
            $("#is_parameter").val(details.is_parameter);
            $("#price").val(details.price);
            $("#sale_price").val(details.sale_price);
            $("#result_text").val(details.result_text);
            details.is_ict == 1 ? $("#is_ict").prop('checked', true) : $("#is_ict").prop('checked', false);
            $("#add_new_record_model").modal("show");
        });

        $("body").on("click", "#submit_btn", function(e) {
            e.preventDefault();

            $("#from_submit").ajaxSubmit({
                url: '{{ route('pos.save_investigation_sub_category') }}',
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}'

                },
                success: function(response) {
                    $("#id").val(0);

                    $("#name").val("");
                    $("#investigation_category_id").val("").trigger('change');
                    $("#is_parameter").val("").trigger('change');
                    $("#price").val("");
                    $("#sale_price").val("");

                    $("#is_ict").prop('checked', false);
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
                    url: '{{ route('pos.list_investigation_sub_category') }}',
                    data: function(d) {
                        d.user_id = $('#attendance_user_filter').val();
                        /*d.attendance_date_from = $('#attendance_date_from').val();
                        d.attendance_date_to = $('#attendance_date_to').val();*/


                    }

                },

                columns: [

                    {
                        data: 'main_category.name',
                        name: 'main_category.name',
                        searchable: true
                    },
                    {
                        data: 'name',
                        name: 'name',
                        searchable: true
                    },
                    {
                        data: 'is_ict',
                        name: 'is_ict',
                        searchable: true
                    },
                    {
                        data: 'price',
                        name: 'price',
                        searchable: true
                    },
                    {
                        data: 'sale_price',
                        name: 'sale_price',
                        searchable: true
                    },
                    {
                        data: 'test_type',
                        name: 'test_type',
                        searchable: true
                    },
                    {
                        data: 'report_heading',
                        name: 'report_heading',
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
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            $('#attendance_user_filter, #attendance_date_from, #attendance_date_to').on('change', function(e) {
                e.preventDefault();
                user_table.ajax.reload();
            });

            $("body").on("click", ".add_report_heading", function(e) {
                var investigation_sub_category_id = $(this).attr("data-id");


                var index_number = $("#index_number").val('');
                var heading_name = $("#heading_name").val('');
                var investigation_sub_category_id = $("#investigation_sub_category_id").val(investigation_sub_category_id);
               $("#add_report_heading_model").modal('show');
            });


            $("body").on("click", "#save_report_heading", function(e) {
               var index_number = $("#index_number").val();
               var heading_name = $("#heading_name").val();
               var investigation_sub_category_id = $("#investigation_sub_category_id").val();
               if(index_number == '' || heading_name == ''){
                   alert("Please fill all fields");
                   return false;
               }

                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.save_report_heading') }}",
                    data: {
                        id: 0,
                        index_number:index_number,
                        name:heading_name,
                        investigation_sub_category_id:investigation_sub_category_id,
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(res) {
                        user_table.ajax.reload();
                        $("#add_report_heading_model").modal('hide');
                       alert("Record Added Successfully");
                    }
                })
            });

            $("body").on("click", ".delete_record", function(e) {
                var id = $(this).attr("data-id");
                if (confirm('Are you sure to delete this record ?')) {
                    $.ajax({
                        type: 'post',
                        url: "{{ route('pos.deactivate_record') }}",
                        data: {
                            id: id,
                            table: "investigation_sub_category",
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
