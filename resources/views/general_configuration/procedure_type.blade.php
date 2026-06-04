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
                    <div class="btn btn-primary add_new_record">Add Procedure Type</div>

                </div>

                <div class="card-body">

                    <div class="row">


                        <div class="col-12">

                            <div class="table-responsive" style="min-height: 200px">

                                <table id="users-list" class="table table-responsive table-striped data_mf_table table-condensed" >

                                    <thead>
                                    <tr>
                                        <th >Name</th>
                                        <th >Type</th>
                                        <th >Sehat Card Rate</th>
                                        <th >Tax Deduction (%age)</th>
                                        <th >Net Amount</th>
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
                            <select class="form-select" required id="type" name="type">
                                <option value="" >Select Type</option>
                                <option value="Surgical" >Surgical</option>
                                <option value="Medical" >Medical</option>
                                <option value="NICU" >NICU</option>
                                <option value="GYNAECOLOGY" >GYNAECOLOGY</option>
                                <option value="ORTHOPAEDIC" >ORTHOPAEDIC</option>
                                <option value="EAR" >EAR</option>
                                <option value="NOSE" >NOSE</option>
                                <option value="THROAT" >THROAT</option>
                                <option value="Gastroentrology" >Gastroentrology</option>
                                <option value="UROLOGY" >UROLOGY</option>
                                <option value="ENDOCRINE" >ENDOCRINE</option>
                                <option value="ENDOSCOPIC PROCEDURES" >ENDOSCOPIC PROCEDURES</option>
                                <option value="Dental" >Dental</option>
                                <option value="OPHTHALMOLOGY" >OPHTHALMOLOGY</option>
                                <option value="PAEDIATRIC" >PAEDIATRIC</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Name<span class="asterisk">*</span></label>
                            <input type="text" required id="name" name="name" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Sehat Card Rate<span class="asterisk">*</span></label>
                            <input type="text" required id="sc_rate" name="sc_rate" class="form-control calculate_tax" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Tax Deduction %<span class="asterisk">*</span></label>
                            <input type="number" required id="tax_deduction" name="tax_deduction" class="form-control calculate_tax" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Net Rate<span class="asterisk">*</span></label>
                            <input type="number" required id="net_rate" style="pointer-events: none;" name="net_rate" class="form-control" placeholder="" autocomplete="off">
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
            $("#add_new_record_model").modal("show");

        });

        $("body").on("click",".edit_record",function (e) {
            record_id = $(this).attr("data-id");
            var details = JSON.parse($(this).attr("data-details"));
            $("#id").val(details.id);
            $("#name").val(details.name);
            $("#type").val(details.type).trigger('change');
            $("#tax_deduction").val(details.tax_deduction);
            $("#sc_rate").val(details.sc_rate);
            $("#net_rate").val(details.net_rate);
            $("#add_new_record_model").modal("show");
        });

        $("body").on("click","#submit_btn",function (e) {
            e.preventDefault();

            $("#from_submit").ajaxSubmit({
                url: '{{route('pos.save_procedure_type')}}',
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}'

                },
                success: function(response){
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
                    url: '{{route("pos.list_procedure_type")}}',
                    data: function (d) {
                        d.user_id = $('#attendance_user_filter').val();
                        /*d.attendance_date_from = $('#attendance_date_from').val();
                        d.attendance_date_to = $('#attendance_date_to').val();*/


                    }

                },

                columns: [

                    {data: 'name', name: 'name',searchable: true},
                    {data: 'type', name: 'type',searchable: true},
                    {data: 'sc_rate', name: 'sc_rate',searchable: true},
                    {data: 'tax_deduction', name: 'tax_deduction',searchable: true},
                    {data: 'net_rate', name: 'net_rate',searchable: true},
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