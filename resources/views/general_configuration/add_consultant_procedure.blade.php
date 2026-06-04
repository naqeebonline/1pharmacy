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

            <!-- Traffic sources -->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <div class="btn btn-primary add_new_record">Add Consultant Procedures</div>

                </div>

                <div class="card-body">

                    <div class="row">


                        <div class="col-12">

                            <div class="table-responsive" style="min-height: 200px">

                                <table id="users-list" class="table table-responsive table-striped data_mf_table table-condensed" >

                                    <thead>
                                    <tr>
                                        <th >Consultant Name</th>
                                        <th >Procedure</th>
                                        <th >Procedure Price</th>
                                        <th >Consultant Charges</th>
                                        <th >Consultant Share %</th>
                                        <th >Consultant Share Amount</th>
                                        <th>Pricing</th>

                                        <th  style="width: 10%">Action</th>
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

    <div class="modal fade my_modal" id="add_new_record_model" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="modal-content form-submit-event" id="from_submit">
                <input type="hidden" id="id" name="id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add Procedure Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Name<span class="asterisk">*</span></label>
                            <select class="form-select" required id="consultant_id" name="consultant_id">
                                <option value="" >Select Consultant...</option>
                                @foreach($consultant as $key => $value)
                                    <option value="{{$value->id}}"> {{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Name<span class="asterisk">*</span></label>
                            <select class="form-select" required id="procedure_type_id" name="procedure_type_id">
                                <option value="" >Select Procedure</option>
                                @foreach($procedure_type as $key => $value)
                                    <option value="{{$value->id}}"> {{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Amount<span class="asterisk">*</span></label>
                            <input type="number" required id="amount" name="amount" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Consultant Charges<span class="asterisk">*</span></label>
                            <input type="text" required id="consultant_charges" name="consultant_charges" class="form-control " placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Consultant Share %<span class="asterisk">*</span></label>
                            <input type="number" required id="consultant_share_percentage" name="consultant_share_percentage" class="form-control " placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3" style="pointer-events: none;">
                            <label for="nameBasic" class="form-label">Consultant Share Amount<span class="asterisk">*</span></label>
                            <input type="number" required id="consultant_share_amount" name="consultant_share_amount" class="form-control " readonly placeholder="" autocomplete="off">
                        </div>


                    </div>




                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close                </button>
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

        $("body").on("keyup",".calculate_tax",function (e) {
            var tax_deduction = $('#tax_deduction').val().trim();
            var sc_rate = $('#sc_rate').val().trim();
            if(tax_deduction == '' || sc_rate == ''){
                $("#net_rate").val(0);
                return false;
            }
            var tax = tax_deduction / 100;

            var deduction_amount = (sc_rate * tax);
            var net_rate = parseFloat(sc_rate) - parseFloat(deduction_amount);
            $("#net_rate").val(net_rate);
        });

        setTimeout(function () {
            $("#type").select2({dropdownParent: $('.my_modal')});
        },300);


        $("body").on("click",".add_new_record",function (e) {
            $("#id").val(0);
            $("#name").val('');
            $("#type").val('').trigger('change');
            $("#tax_deduction").val('');
            $("#sc_rate").val('');
            $("#net_rate").val('');
            $("#consultant_charges").val('');
            $("#consultant_share_percentage").val('');
            $("#consultant_share_amount").val('');
            $("#add_new_record_model").modal("show");

        });




        $("body").on("keyup","#consultant_share_percentage,#consultant_charges",function (e) {
            calculateAmount();
        });

        function calculateAmount(){
            var value = $("#consultant_share_percentage").val();
            var consultant_charges = $("#consultant_charges").val();
            if(value == ''){
                value = 0;
            }
            if(!consultant_charges){
                consultant_charges = 0;
            }
            var percentage_amount = ((consultant_charges) * (value)) / 100;

            $("#consultant_share_amount").val(percentage_amount);
        }

        $("body").on("click",".edit_record",function (e) {
            record_id = $(this).attr("data-id");
            var details = JSON.parse($(this).attr("data-details"));
            $("#id").val(details.id);
            $("#consultant_id").val(details.consultant_id).trigger('change');
            $("#procedure_type_id").val(details.procedure_type_id).trigger('change');
            $("#amount").val(details.amount);
            $("#consultant_charges").val(details.consultant_charges);
            $("#consultant_share_percentage").val(details.consultant_share_percentage);
            $("#consultant_share_amount").val(details.consultant_share_amount);

            $("#add_new_record_model").modal("show");
        });

        $("body").on("click","#submit_btn",function (e) {
            e.preventDefault();
            var consultant_id = $("#consultant_id").val();
            var procedure_type_id = $("#procedure_type_id").val();
            var amount = $("#amount").val();
            var consultant_charges = $("#consultant_charges").val();
            if(consultant_id == '' || procedure_type_id == '' || amount == '' || consultant_charges == ''){
                alert("Please fill all fields correctly");
                return false;
            }

            $("#from_submit").ajaxSubmit({
                url: '{{route('pos.save_consultant_procedures')}}',
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}'

                },
                success: function(response){
                    if(response.status == 'error'){
                        alert(response.message);
                        return false;
                    }
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

        $(document).ready(function (){

            district_id = "";
            police_station_id = [];
            leave_request_id = '';

            user_table = $('#users-list').DataTable({
                processing: true,
                serverSide: true,

                lengthMenu: [
                    [ 100, 250, 500, 1000 ],
                    [ '100', '250', '500', '1000']
                ],
                pageLength: 50,
                ajax: {
                    url: '{{route("pos.list_consultant_procedure")}}',
                    data: function (d) {
                        d.user_id = $('#attendance_user_filter').val();
                        /*d.attendance_date_from = $('#attendance_date_from').val();
                        d.attendance_date_to = $('#attendance_date_to').val();*/


                    }

                },

                columns: [

                    {data: 'consultant.name', name: 'consultant.name',searchable: true},
                    {data: 'procedure.name', name: 'procedure.name',searchable: true},
                    {data: 'amount', name: 'amount',searchable: true},
                    {data: 'consultant_charges', name: 'consultant_charges',searchable: true},
                    {data: 'consultant_share_percentage', name: 'consultant_share_percentage', orderable: false, searchable: false},
                    {data: 'consultant_share_amount', name: 'consultant_share_amount', orderable: false, searchable: false},

                    {data: 'pricing', name: 'pricing', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],

                responsive: true,
                processing: true,
                serverSide: true,
                searching:  true,
                sorting:    true,
                paging:     true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            $('#attendance_user_filter, #attendance_date_from, #attendance_date_to').on('change', function(e) {
                e.preventDefault();
                user_table.ajax.reload();
            });

            $("body").on("click",".delete_record",function (e) {
                var id  = $(this).attr("data-id");
                if (confirm('Are you sure to delete this record ?')) {
                    $.ajax({
                        type: 'post',
                        url: "{{ route('pos.deactivate_record') }}",
                        data: {
                            id: id,
                            table:"procedure_type",
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