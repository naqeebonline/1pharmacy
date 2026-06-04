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
                <div class="card-body">
                    <form class=" form-submit-event" id="patient_register">
                        <div class="row" style="background-color: #2790b2; padding: 5px">


                            <div class="col-md-4 col-sm-4 mt-1 float-right">

                                <select id="admission_id" name="admission_id" required class="form-control select2">
                                    <option value="">Please Select Patient...</option>
                                    <?php foreach($admitted_patients as $key => $value){ ?>
                                    <option  value="<?php echo $value->id; ?>"><?php echo $value->patient->name." - ".$value->patient->mr_no; ?> Status:({{$value->admission_status}}) </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <br>
                        <div class="row">
                            <input type="hidden" name="list_products" id="list_products">

                            <div class="col-md-4" >
                                <select  id="product_id" class="form-select">
                                    <option value="">Select Products...</option>
                                    @foreach ($products as $value)
                                        @if(($value->ProductName != '-' || $value->ProductName != '') && $value->avaliable_quantity != 0)
                                            <option purchase_price="{{$value->PurchasePrice}}" product_name="{{ $value->ProductName }}" value="{{ $value->ProductID }}"> {{ $value->ProductName }} - ( {{$value->generic_name?->name}} ) | PS: {{$value->pack_size }} Qty: {{$value->avaliable_quantity}} </option>
                                        @endif
                                    @endforeach
                                </select>
                                <br>
                                <br>
                                <input type="number" class="form-control" id="quanity" value="1" placeholder="quanity">
                                <br>

                                <a href="javascript:void(0)" id="add_item"  class="btn btn-warning"> Add Item</a>
                            </div>

                            <div class="col-md-8" >
                                <br>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr style="background-color: #b8e8f7">
                                            <th style="width: 30%">Name</th>
                                            <th>Quanity</th>

                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="inv_body">

                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>






                        </div>

                        <div class="row">
                            <div class="text-left mt-3 float-right">
                                <button class="btn btn-success" style="float: right" id="submit_btn">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            {{-- LISTIN PATIENTS --}}
            <div class="card my-5">
                <div class="card-body">
                    <h5 class="card-title">Ward Request For Medicine to Pharmacy </h5>

                        <div class="row">
                            <div class="col-md-2">
                                <label>From Date</label>
                                <input type="date" class="form-control" value="{{date("Y-m-d")}}" id="filter_from_date">
                            </div>
                            <div class="col-md-2">
                                <label>To Date</label>
                                <input type="date" class="form-control" value="{{date("Y-m-d")}}" id="filter_to_date">
                            </div>



                            <div class="col-md-3 mt-4">
                                <div class="btn btn-primary print_all_details">Print</div>
                            </div>
                        </div>

                    <div class="table-responsive" style="min-height: 200px">


                        <table id="patient-list" style="width: 100% !important"
                               class="table table-responsive table-striped data_mf_table ">

                            <thead>
                            <tr>
                                <th style="display: none">Patient</th>
                                <th>Invoice#</th>
                                <th>Patient</th>

                                <th>Requested By</th>
                                <th>Requested On</th>
                                <th>Status</th>

                                <th style="width: 10%">Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            </tbody>
                        </table>
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
                    <h5 class="modal-title" id="exampleModalLabel1">Patients</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                     <table class="table table-bordered">
                         <tr>
                             <th>Serial</th>
                             <th>Name</th>
                             <th>Father Name</th>
                             <th></th>
                         </tr>
                         <tbody id="prev_patients">

                         </tbody>


                     </table>

                    </div>




                </div>

            </form>
        </div>
    </div>


    <div class="modal fade my_modal" id="patient_admission_edit_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="modal-content form-submit-event" id="cancel_admission_form">
                <input type="hidden" id="cancel_admission_id" name="id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Update Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

               -


                    </div>




                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close                </button>
                    <div id="update_record" class="btn btn-primary">Update</div>
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
        registered_patients = [];
        list_products = [];
        setTimeout(function() {
            $(".select2").select2();
            $("#district_id").select2();
            $("#product_id").select2();
            $("#location_id").select2();

            $("#edit_consultant_id").select2({dropdownParent: $('.my_modal')});
            $("#edit_opd_type_id").select2({dropdownParent: $('.my_modal')});

            let time = Date.now();
            $("#invoice_no").val(time);
        }, 1000);


        $("body").on("change", "#product_id", function(e) {
            setTimeout(function () {
                $("#quanity").focus();
            },100);
        });

        $("body").on("click", ".remove_item", function(e) {
            var id = $(this).attr("item_id");
            removeItem(id);
        });

        $("body").on("change", "#discount_percentage", function(e) {
            load_investigation();
        });

        $("body").on("click", "#add_item", function(e) {
            var product_id =  $("#product_id").val();
            var quanity =  $("#quanity").val();
            if(quanity == '' || quanity == 0){
                quanity = 1;
            }
            if(product_id == ""){
                alert("Please select Product");
                return false;
            }
            let rate = $("#product_id").find("option:selected").attr("sale_price");
            let product_name = $("#product_id").find("option:selected").attr("product_name");

            const exists = list_products.some(item => item.product_id == product_id);
            if (!exists) {
                list_products.push({product_id:product_id,product_name:product_name,quanity:quanity});
                load_investigation();
            } else {
                alert("Investigation already exist");

            }

            $("#product_id").select2('open');
            $("#product_id").focus();


            e.preventDefault(); // Prevent the default form submission
        });

        $('form').on('keydown', function (e) {
            if (e.key === 'Enter') {
                var product_id =  $("#product_id").val();
                var quanity =  $("#quanity").val();
                if(quanity == '' || quanity == 0){
                    quanity = 1;
                }
                if(product_id == ""){
                    alert("Please select Product");
                    return false;
                }
                let rate = $("#product_id").find("option:selected").attr("sale_price");
                let product_name = $("#product_id").find("option:selected").attr("product_name");

                const exists = list_products.some(item => item.product_id == product_id);
                if (!exists) {
                    list_products.push({product_id:product_id,product_name:product_name,quanity:quanity});
                    load_investigation();
                } else {
                    alert("Investigation already exist");

                }



               // $("#investigation_id").val("").trigger("change");
                $("#product_id").select2('open');
                $("#product_id").focus();


                e.preventDefault(); // Prevent the default form submission
            }
        });

        function load_investigation(){
            $("#inv_body").html("");


            var total_bill_amount = 0;
            var total_discount = 0;
            var net_bill_amount = 0;
            list_products.forEach(function (value,key) {

               var html = `<tr>
                                <td>${value.product_name}</td>
                                <td>${value.quanity}</td>

                                <td ><a href="javascript:void(0)" class="remove_item" item_id='${value.product_id}' style="color:red">X remove</a></td>

                                `;
                $("#inv_body").append(html);
            });

            setTimeout(function () {
                $("#total_bill_amount").html(total_bill_amount);
                $("#net_bill_amount").html(net_bill_amount);
                $("#total_bill_discount").html(total_discount);
                $("#list_products").val(JSON.stringify(list_products));

            },100);
        }

        function removeItem(id){
            list_products = list_products.filter(item => item.product_id != id);
            load_investigation();
        }




        $("body").on("click", ".print_all_details", function(e) {
            var from_date = $("#filter_from_date").val();
            var to_date = $("#filter_to_date").val();
            var filter_opd_type_id = $("#filter_opd_type_id").val();
            var filter_consultant_id = $("#filter_consultant_id").val();
            if(from_date == ''){
                from_date = 'nill';
            }
            if(to_date == ''){
                to_date = 'nill';
            }
            if(filter_opd_type_id == ''){
                filter_opd_type_id = 0;
            }
            if(filter_consultant_id == ''){
                filter_consultant_id = 0;
            }
            var url = "{{route('pos.print_all_appointments')}}/"+from_date+"/"+to_date+"/"+filter_opd_type_id+"/"+filter_consultant_id;
            var newWindow = window.open(url, '_blank', 'width=1200,height=800');
            newWindow.focus();

        });

        function calculateDOB(years, months, days) {
            // Get the current date
            const currentDate = new Date();

            // Create a new date object for the calculation
            const dob = new Date(currentDate);

            // Subtract years, months, and days
            dob.setFullYear(dob.getFullYear() - years); // Subtract years
            dob.setMonth(dob.getMonth() - months);      // Subtract months
            dob.setDate(dob.getDate() - days);          // Subtract days


            const date = new Date(dob);

            // Format the date as d-m-Y
            const day = String(date.getDate()).padStart(2, '0'); // Get day and add leading zero
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Get month (0-indexed, so add 1) and add leading zero
            const year = date.getFullYear(); // Get the full year

            // Combine into the desired format
            var formattedDate = `${year}-${month}-${day}`;

            return formattedDate;
        }

        function calculateAgeDetails(birthDate) {
            const currentDate = new Date(); // Current date
            const birth = new Date(birthDate); // Convert input to Date object

            // Calculate the differences
            let years = currentDate.getFullYear() - birth.getFullYear();
            let months = currentDate.getMonth() - birth.getMonth();
            let days = currentDate.getDate() - birth.getDate();
            if (days < 0) {
                months -= 1;
                const prevMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 0); // Last day of the previous month
                days += prevMonth.getDate(); // Add days from the previous month
            }
            if (months < 0) {
                years -= 1;
                months += 12;
            }
            $("#age").val(years);
            $("#months").val(months);
            $("#days").val(days);
        }




        $("body").on("click", "#update_record", function() {
            var id = $("#edit_id").val();
            var opd_type_id =  $("#edit_opd_type_id").val();
            var consultant_id = $("#edit_consultant_id").val();
            if(id == '' || opd_type_id == '' || consultant_id == ''){
                alert("Please Fill all fields correctly");
                return false;
            }

            $.ajax({
                type: 'post',
                url: "{{ route('pos.update_appointment') }}",
                data: {
                    id: id,
                    opd_type_id: opd_type_id,
                    consultant_id: consultant_id,

                    _token: '{{ csrf_token() }}'

                },
                success: function(res) {
                    $("#patient_admission_edit_modal").modal("hide");
                    user_table.ajax.reload();
                }
            })
        });

        $("body").on("keyup", "#days,#months,#age", function() {
            var year = $("#age").val();
            var months = $("#months").val();
            var days = $("#days").val();
            if(year == ''){
                year = 0;
            }
            if(months == ''){
                months = 0;
            }
            if(days == ''){
                days = 0;
            }

            var dob = calculateDOB(year,months,days);
            $("#dob").val(dob);
        });



        $("body").on("change", "#dob", function() {
            calculateAgeDetails($(this).val());
        });
        $("body").on("blur", "#contact_no", function() {


            var contact_no = $("#contact_no").val();
            var id = $("#id").val();
            if ((contact_no.length > 8)) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.get_patient_by_cnic') }}",
                    data: {

                        contact_no: contact_no,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                       // console.log(res);
                        if (res.status) {

                            $("#prev_patients").html('');
                            registered_patients = res.data;
                            if(registered_patients.length > 0){
                                $.each(registered_patients, function(index, value) {
                                    var html = `<tr>
                                            <td>${index + 1}</td>
                                            <td>${value.name}</td>
                                            <td>${value.father_husband_name}</td>
                                            <td><div class="btn btn-success select_patient" data-id='${index}' >Select</div></td>

                                            `;
                                    $("#prev_patients").append(html);

                                });
                                $("#add_new_record_model").modal("show");
                            }
                        }else{
                            registered_patients = [];
                            reset_fields();
                        }

                    }
                });

            }
        });

        $("body").on("blur", "#mr_number", function() {
            var mr_number = $("#mr_number").val();


            var id = $("#id").val();
            if ((mr_number.length > 3 || contact_no.length > 8)) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.get_patient_by_cnic') }}",
                    data: {
                        mr_number: mr_number,

                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        // console.log(res);
                        if (res.status) {

                            $("#prev_patients").html('');
                            registered_patients = res.data;
                            if(registered_patients.length > 0){
                                $.each(registered_patients, function(index, value) {
                                    var html = `<tr>
                                            <td>${index + 1}</td>
                                            <td>${value.name}</td>
                                            <td>${value.father_husband_name}</td>
                                            <td><div class="btn btn-success select_patient" data-id='${index}' >Select</div></td>

                                            `;
                                    $("#prev_patients").append(html);

                                });
                                $("#add_new_record_model").modal("show");
                            }
                        }else{
                            registered_patients = [];
                            reset_fields();
                        }

                    }
                });

            }
        });

        $("body").on("change","#filter_from_date,#filter_to_date,#filter_opd_type_id,#filter_consultant_id",function () {
            user_table.ajax.reload();
        });



        user_table = $('#patient-list').DataTable({
            processing: true,
            serverSide: true,

            pageLength: 20,
            ajax: {
                url: "{{ route('pos.get_list_ward_request') }}",
                 data: function (d) {
                     d.from_date = $('#filter_from_date').val();
                     d.to_date = $('#filter_to_date').val();
                     d.opd_type_id = $('#filter_opd_type_id').val();
                     d.consultant_id = $('#filter_consultant_id').val();

                 }
            },

            columns: [
                {
                    data: 'patient.name',
                    visible: false
                },
                {
                    data: 'invoice_no',
                    name: 'invoice_no',
                    searchable: true
                },
                {
                    data: null, // Use null because you're creating a custom render
                    name: 'patient',
                    searchable: false, // If you want it searchable, additional backend support is required
                    render: function (data, type, row) {
                        // Combine first_name and last_name to display full_name
                        return '<b style="font-size:12px;color:green;">'+row.patient.name + '</b> <br><b style="font-size:12px;color:red;">' + row.patient.mr_no +'</b>';
                    }
                },


                {
                    data: 'user.name',
                    name: 'user.name',
                    searchable: true
                },
                {
                    data: 'requested_at',
                    name: 'requested_at',
                    searchable: true
                },

                {
                    data: null, // Use null because you're creating a custom render
                    name: 'status',
                    searchable: false, // If you want it searchable, additional backend support is required
                    render: function (data, type, row) {
                        // Combine first_name and last_name to display full_name
                        if(row.status == 0){
                            return '<b class="badge badge-warning" style="font-size:12px;color:red;">Pending</b>';
                        }else{
                            return '<b class="badge badge-success" style="font-size:12px;color:green;">Issued</b>';
                        }

                    }
                },



                {
                    data: 'actions',
                    name: 'actions',
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
            // buttons: [
            //     'copy', 'csv', 'excel', 'pdf', 'print'
            // ]
        });


        $("body").on("click", ".edit_record", function(e) {
            record_id = $(this).attr("data-id");
            var details = JSON.parse($(this).attr("data-details"));
            $("#edit_id").val(details.id);
            $("#edit_opd_type_id").val(details.opd_type_id).trigger('change');
            $("#edit_consultant_id").val(details.consultant_id).trigger('change');
            $("#patient_admission_edit_modal").modal('show');

        });


        //$("body").on("click", "#submit_btn", function(e) {
        $("#patient_register").on("submit", function(e) {

            e.preventDefault();

            let isValid = true;

            var list_products = $("#list_products").val();
            if(list_products == '[]' || list_products == ''){
                alert("Please Add Medicine");
                return false;
            }

            // Clear previous error messages
            $(".error-message").remove();
            $(".is-invalid").removeClass("is-invalid");

            // Validate required fields
            $(this).find("[required]").each(function() {
                if (!$(this).val().trim()) {
                    isValid = false;
                    $(this).addClass("is-invalid"); // Highlight invalid field
                    $(this).after(
                        `<span class="error-message text-danger">This field is required.</span>`
                    ); // Show error message
                }
            });

            if (isValid) {

                $("#patient_register").ajaxSubmit({
                    url: '{{ route('pos.save_ward_request') }}',
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(response) {
                        if(response.status =='empty'){
                            alert(response.message);
                            return false;
                        }
                       // alert(response.appointment_id);
                          var url = "";
                        //window.open(url, '_blank');

                        reset_fields();
                        alert("Request Sent Successfully");
                        user_table.ajax.reload();
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        //console.log();
                        //alert("Status: " + textStatus); alert("Error: " + errorThrown);
                        alert(JSON.parse(XMLHttpRequest.responseText).message);
                    }
                });
            }
        });

        $("body").on("click", ".delete_record", function(e) {
            var id = $(this).attr("data-id");
            if (confirm('Are you sure to delete this record ?')) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.deactivate_record') }}",
                    data: {
                        id: id,
                        table: "ward_request",
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(res) {
                        // user_table.dataTable.reload();
                        window.location.reload();
                    }
                })
            } else {
                alert('Why did you press cancel? You should have confirmed');
            }
        });

        function reset_fields() {
            $("#id").val(0);
            $("#contact_no").val('');
            $("#admission_id").val('').trigger("change");
            $("#product_id").val('').trigger("change");
            $("#quantity").val('1');
            $("#dob").val('');
            $("#father_husband_name").val('');
            $("#g4no").val(0);
            $("#age").val('');
            $("#months").val('');
            $("#days").val('');
            $("#gender").val('');
            $("#id").val('');
            $("#location_id").val('').trigger("change");
            $("#cnic").val('');
            $("#cnic").val('');
            $("#name").val('');
            $("#regdate").val('{{date("Y-m-d")}}');
            //$("#relation_id").val('');
            $("#sc_ref_no").val('');
            let time = Date.now();
            $("#discount_percentage").val('');
            $("#invoice_no").val(time);
            list_products = [];
            load_investigation();
        }
    </script>
@endpush
