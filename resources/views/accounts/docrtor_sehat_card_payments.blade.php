@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <style>
        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 7px;
            text-align: center;
            margin-bottom: 20px;
            float: left;
            margin-left: 43px;
            margin-top: -26px;


        }
        label{
            font-weight: bold;
        }

        input[type="checkbox"] {
             /* 1.5 times bigger */


        }
        .table > :not(caption) > * > * {padding: 5px;}

    </style>

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script> --}}
@endpush

@section('content')

    <div class="row">
        <div class="col-12">

            <!-- Traffic sources -->
                    <form method="post" id="from_submit">
                    <div class="row ">

                        <!-- Right Block: Form Inputs -->
                        <h4 style="color:white; font-weight: bold">Previous Balance: <span class="color:red" id="previous_balance_text"></span></h4>
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">

                                        <div class="col-md-3">
                                            <label for="doctor" class="form-label">Consultant</label>
                                            <select class="form-select" required id="consultant_id" name="consultant_id">
                                                <option value="">Select Consultant</option>
                                                @foreach($suppliers as $key => $value)
                                                    <option value="{{$value->id}}">{{$value->name}}</option>
                                                @endforeach
                                            <!-- Add options here -->
                                            </select>
                                        </div>


                                        <div class="col-md-3">
                                            <label for="date" class="form-label">Amount</label>
                                            <input type="number" required class="form-control" id="Amount" name="amount">
                                        </div>



                                        <div class="col-md-3">
                                            <label for="discount" class="form-label">Payment Type</label>
                                            <select name="payment_type_id" id="payment_type_id" class="form-select">
                                                <option value="">Select Payment Type...</option>
                                                @foreach($payment_type as $key => $value)
                                                <option value="{{$value->payment_type_id}}">{{$value->payment_type}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="discount" class="form-label">Payment Date</label>
                                            <input type="date" required class="form-control" value="{{date("Y-m-d")}}" id="dated" name="dated">
                                        </div>

                                        <div class="col-md-3">
                                            <label for="discount" class="form-label">Description</label>
                                            <input type="text" class="form-control" id="Description" name="description">
                                        </div>

                                       {{-- <div class="col-md-3">
                                            <label for="discount" class="form-label">Attachment</label>
                                            <input type="file"  class="form-control" id="attachment" name="attachment">
                                        </div>--}}



                                        <div class="col-md-12 mt-3">

                                            <button type="submit" id="submit_btn" class="btn btn-success">Save</button>
                                        </div>

                                    </div>
                                </div>
                            </div>




                        </div>
                    </div>
                    </form>


                    {{--<div class="row g-3">
                        <div class="col-md-3">
                            <input type="date" id="attendance_date_from" name="attendance_date_from" class="form-control"
                                   placeholder="Start date" autocomplete="off" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-3">
                            <input type="date" id="attendance_date_to" name="attendance_date_to" class="form-control"
                                   placeholder="End date" autocomplete="off" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-3">
                            <select class="form-select" id="attendance_user_filter" aria-label="Default select example">
                                <option value="">Select User</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <a class="btn btn-primary" href="">Export To Excel</a>
                        </div>


                    </div>--}}

                  <div class="card">
                      <div class="row">
                          <div class="col">

                              <div class="card mb-3">
                                  <div class="card-header border-bottom">
                                      <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                          <li class="nav-item">
                                              <button
                                                      class="nav-link active"
                                                      data-bs-toggle="tab"
                                                      data-bs-target="#all-payment-tab"
                                                      role="tab"
                                                      aria-selected="false"
                                              >
                                                  Payment Details
                                              </button>
                                          </li>

                                          <li class="nav-item">
                                              <button
                                                      class="nav-link "
                                                      data-bs-toggle="tab"
                                                      data-bs-target="#all-bills-tab"
                                                      role="tab"
                                                      aria-selected="true"
                                              >
                                                  Doctor Invoices
                                              </button>
                                          </li>


                                      </ul>
                                  </div>

                                  <div class="tab-content">

                                      <div class="tab-pane fade active show" id="all-payment-tab" role="tabpanel">
                                          <div class="table-responsive" style="min-height: 200px">

                                              <table id="payment_list" style="width: 100% !important" class="table table-responsive table-striped data_mf_table " >

                                                  <thead>
                                                  <tr>
                                                      <th >Consultant</th>
                                                      <th >Payment Type</th>
                                                      <th >Amount</th>
                                                      
                                                      <th >Dated</th>
                                                      <th >Created By</th>
                                                      <th >Description</th>

                                                      <th  style="width: 10%">Action</th>
                                                  </tr>
                                                  </thead>

                                                  <tbody>
                                                  </tbody>
                                              </table>
                                          </div>
                                      </div>


                                      <div class="tab-pane fade " id="all-bills-tab" role="tabpanel">
                                          <div class="table-responsive" style="min-height: 200px">

                                              <table id="doctor-sc-invoices" style="width: 100% !important;" class="table table-responsive table-striped data_mf_table table-condensed table-responsive" >

                                                  <thead>
                                                  <tr>
                                                      <th  style="width: 10%">Consultant</th>
                                                    
                                                      <th style="width: 10%">From Date</th>
                                                      <th style="width: 10%">To Date</th>
                                                      <th style="width: 10%">Amount</th>
                                                      <th style="width: 10%">Created By</th>
                                                    
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
                      </div>
                  </div>
            <!-- /traffic sources -->
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

         $(document).ready(function () {
             setTimeout(function () {

                 $('#consultant_id').select2();

             },1000);

             $("body").on("change","#consultant_id",function () {
                var id = $(this).val();
                 payment_list.destroy();
                 doctor_orders_table.destroy();
                loadPaymentData(id);
                 loadDoctorOrders(id);
                 get_prev_balance();
             });

             function get_prev_balance(e){

                 var value=$("#consultant_id").val();
                 var name=$('#consultant_id').select2('data')[0]['text'];
                 var serverBaseUrl = "";
                 //$("#page_title").text(name);
                 if(value!=''){
                     $.ajax({
                         type:"get",
                         url:"{{route("pos.consultant_sc_balance")}}/"+value,
                         success:function(response){

                             PreviousBalance = parseFloat(response).toFixed(2);

                             $("#previous_balance_text").text(PreviousBalance);
                           //  calculateBalance();

                         }
                     });
                 }else{
                     $("#prev_balance").val(0);
                 }
             }

             loadPaymentData("afg");
             loadDoctorOrders("afg");


             $("body").on("click","#submit_btn",function (e) {
                 e.preventDefault();

                 if($("#consultant_id").val() == ''){
                     alert("Please Select Doctor");
                     return false;
                 }
                 if($("#Amount").val() == ''){
                     alert("Please Enter Amount");
                     return false;
                 }
                 if($("#discount").val() == ''){
                     $("#discount").val(0);
                     alert("Please Enter Discount");
                     return false;
                 }
                 if($("#payment_type_id").val() == ''){
                     $("#discount").val(0);
                     alert("Please Select Payment Type");
                     return false;
                 }

                 if($("#Dated").val() == ''){
                     alert("Please Select Date");
                     return false;
                 }



                 $("#from_submit").ajaxSubmit({

                     url: '{{ route('pos.save_sc_payments') }}',
                     type: 'post',
                     data: {
                         _token: '{{ csrf_token() }}'

                     },
                     success: function(response){
                         $("#add_new_record_model").modal("hide");
                         alert("Payment Record Saved Successfully");
                         payment_list.destroy();
                         doctor_orders_table.destroy();
                         loadPaymentData($("#consultant_id").val());
                         loadDoctorOrders($("#consultant_id").val());
                         resetForm();
                     },
                     error: function(XMLHttpRequest, textStatus, errorThrown) {
                         //console.log();
                         //alert("Status: " + textStatus); alert("Error: " + errorThrown);
                         alert(JSON.parse(XMLHttpRequest.responseText).message);
                     }
                 });
             });
         });

         function resetForm() {
             //$("#SID").val('').trigger("change");
             $("#Amount").val('');
             $("#discount").val(0);
             $("#Dated").val('');
             $("#Description").val('');
             $("#InvoiceNo").val('');
             $("#payment_type_id").val('').trigger("change");
             $("#consultant_id").val('').trigger("change");

         }

         function loadPaymentData(id) {
             payment_list = $('#payment_list').DataTable({
                 processing: true,
                 serverSide: true,

                 lengthMenu: [
                     [ 100, 250, 500, 1000 ],
                     [ '100', '250', '500', '1000']
                 ],
                 pageLength: 50,
                 ajax: {
                     url: `{{ route('pos.get_sc_payments_to_doctors', ':id') }}`.replace(':id', id),
                     data: function (d) {
                        console.log("My Contole ",d);
                         d.user_id = $('#attendance_user_filter').val();
                         d.attendance_date_from = $('#attendance_date_from').val();
                         d.attendance_date_to = $('#attendance_date_to').val();


                     }

                 },

                 columns: [

                     {data: 'consultant.name', name: 'consultant.name',searchable: true},
                     {data: 'payment_type.payment_type', name: 'payment_type.payment_type',searchable: true},
                     {data: 'amount', name: 'amount',searchable: true},
                     {data: 'dated', name: 'dated',searchable: true},
                     {data: 'created_by.name', name: 'created_by',searchable: true},
                     {data: 'description', name: 'description',searchable: true},

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
         }

         function loadDoctorOrders(id) {
             doctor_orders_table = $('#doctor-sc-invoices').DataTable({
                 processing: true,
                 serverSide: true,

                 lengthMenu: [
                     [ 100, 250, 500, 1000 ],
                     [ '100', '250', '500', '1000']
                 ],
                 pageLength: 50,
                 ajax: {
                    url: `{{ route('pos.doctor_sc_invoices', ':id') }}`.replace(':id', id),
                     
                     data: function (d) {
                         d.user_id = $('#attendance_user_filter').val();
                         /*d.attendance_date_from = $('#attendance_date_from').val();
                         d.attendance_date_to = $('#attendance_date_to').val();*/


                     }

                 },

                 columns: [


                     
                     {data: 'consultant.name', name: 'consultant.name',searchable: true},
                     {data: 'from_date', name: 'from_date',searchable: true},
                     {data: 'to_date', name: 'to_date',searchable: true},
                     {data: 'total_amount', name: 'total_amount',searchable: true},
                     {data: 'created_by.name', name: 'created_by.name',searchable: true},
                
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
         }


         $("body").on("click",".delete_record",function (e) {
             var id  = $(this).attr("data-id");
             if (confirm('Are you sure to delete this record ?')) {
                 $.ajax({
                     type: 'post',
                     url: "{{ route('pos.delete_table_data_2') }}",
                     data: {
                         id: id,
                         table:"consultant_sc_payments_details",
                         _token: '{{ csrf_token() }}'

                     },
                     success: function(res) {
                         //user_table.dataTable.reload();
                         //window.location.reload();
                         payment_list.ajax.reload();
                     }
                 })
             } else {
                 alert('Why did you press cancel? You should have confirmed');
             }
         });

    </script>
@endpush