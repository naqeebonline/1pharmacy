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
                    <div class="btn btn-primary add_new_record">Add General Expenses</div>

                </div>

                <div class="card-body">

                    <div class="row">


                        <div class="col-12">

                            <div class="table-responsive" style="min-height: 200px">

                                <table id="users-list"
                                    class="table table-responsive table-striped data_mf_table table-condensed">

                                    <thead>
                                        <tr>
                                             
                                            <th>Expense </th>
                                            <th>Sub Expense </th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Description</th>


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
                    <h5 class="modal-title" id="exampleModalLabel1">Add General Expenses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Select Expense<span class="asterisk">*</span></label>
                            <select name="ExpenseID" id="ExpenseID" class="form-select">
                                <option disabled selected value="">Select Expense</option>
                                @forelse ($expense as $item)
                                    <option value="{{ $item->ExpenseID }}">{{ $item->ExpenseTitle }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Select Sub Expense<span
                                    class="asterisk">*</span></label>
                            <select disabled name="ESID" id="ESID" class="form-select">
                                <option disabled selected value="">Select Sub Expense</option>

                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Amount<span class="asterisk">*</span></label>
                            <input type="text" required id="Amount" name="Amount" class="form-control" placeholder=""
                                autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Date<span class="asterisk">*</span></label>
                            <input type="date" required id="Dated" name="Dated" class="form-control" placeholder=""
                                autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Description<span class="asterisk">*</span></label>
                            <textarea name="Description" id="Description" class="form-control" rows="3"></textarea>

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
        $("body").on("change", "#ExpenseID", function(e) {
            let expenseID = $("#ExpenseID").val();

            $.ajax({
                url: "{{ route('pos.get_sub_expanses') }}",
                type: "post",
                data: {
                    id: expenseID,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    console.log(response);

                    $("#ESID").find("option").not(":first").remove();

                    if (response.data.length > 0) {
                        $("#ESID").removeAttr("disabled");
                        $.each(response.data, function(key, item) {
                            $("#ESID").append(
                                `<option value="${item.ESID}">${item.ESTitle}</option>`
                            )
                        });
                    } else {
                        $("#ESID").attr("disabled", "true");
                    }


                }
            });

            // $("#id").val(0);
            // $("#ExpenseID").val('');
            // $("#ESTitle").val('');
            // $("#add_new_record_model").modal("show");

        });

        $("body").on("click", ".add_new_record", function(e) {
            $("#id").val(0);
            $("#ExpenseID").val('');
            $("#ESID").find("option").not(":first").remove();
            $("#ESID").val('');
            $("#Amount").val('');
            $("#Dated").val('');
            $("#Description").val('');
            $("#add_new_record_model").modal("show");

        });

        $("body").on("click", ".edit_record", function(e) {
            record_id = $(this).attr("data-id");
            var details = JSON.parse($(this).attr("data-details"));

             $("#id").val(record_id);
            
            
            $("#ESID").val(details.ESID);
            $("#Amount").val(details.Amount);
            $("#Dated").val(details.Dated);
            $("#Description").val(details.Description);
            $("#add_new_record_model").modal("show");
        });

        $("body").on("click", "#submit_btn", function(e) {
            e.preventDefault();

            $("#from_submit").ajaxSubmit({
                url: '{{ route('pos.save_general_expanses') }}',
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
                    url: '{{ route('pos.general_expanses_list') }}',
                    data: function(d) {
                        d.user_id = $('#attendance_user_filter').val();
                        d.attendance_date_from = $('#attendance_date_from').val();
                        d.attendance_date_to = $('#attendance_date_to').val();


                    }

                },

                columns: [
                    
                    {
                        data: 'expense_name',
                        name: 'expense_name',
                        searchable: false
                    },
                    {
                        data: 'sub_expenses.ESTitle',
                        name: 'sub_expenses.ESTitle',
                        searchable: true
                    },
                    {
                        data: 'Amount',
                        name: 'Amount',
                        searchable: true
                    },
                    
                    {
                        data: 'Dated',
                        name: 'Dated',
                        searchable: true
                    },
                    {
                        data: 'Description',
                        name: 'Description',
                        searchable: true
                    },
                     

                    // {
                    //     data: 'expense.ExpenseTitle',
                    //     name: 'expense.ExpenseTitle',
                    //     searchable: true
                    // },
                    // {
                    //     data: 'ESTitle',
                    //     name: 'ESTitle',
                    //     searchable: true
                    // },


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

            $("body").on("click", ".delete_record", function(e) {
                var id = $(this).attr("data-id");
                if (confirm('Are you sure to delete this record ?')) {
                    $.ajax({
                        type: 'post',
                        url: "{{ route('pos.delete-table-data') }}",
                        data: {
                            id: id,
                            table: "market",
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
