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
                        <div class="row">
                            <div class="col-md-2 col-sm-4 mb-3">
                                <label for="nameBasic" class="form-label">User<span class="asterisk">*</span></label>

                                <select name="user_id" required id="user_id" class="form-select">
                                    <option value="">Select User ---</option>
                                    @foreach ($users as $key => $value)
                                        <option value="{{ $value->id }}" {{ ($value->id == $user_id) ? "selected" : ""}}>{{ $value->name }}</option>
                                    @endforeach
                                </select>


                            </div>

                            <div class="col-md-3 col-sm-4 mb-3" >
                                <label for="nameBasic" class="form-label">Closing Date<span class="asterisk">*</span></label>
                                <input  type="date" required id="closing_date" value="{{date("Y-m-d")}}" name="closing_date" class="form-control"
                                        placeholder="" autocomplete="off">
                            </div>


                            <div class="col-md-3 col-sm-4 mt-4">
                                <button class="btn btn-success" id="show_button">Show</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>


        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">

            <!-- Traffic sources -->
            <div class="card">
                <div class="card-body">
                    <form class=" form-submit-event" method="post" action="{{route('pos.post_daily_closing')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" required name="user_id" value="{{$user_id}}">
                        <input type="hidden" name="closing_date" value="{{$closing_date}}">
                        <div class="row">
                            <div class="col-md-6 col-sm-4 mb-3">
                                <a target="_blank" style="float: right" href="{{route('pos.view_details')}}?user_id={{$user_id}}&closing_date={{$closing_date}}">View Report</a>
                                <table class="table table-bordered">
                                    

                                    

                                    <tr>
                                        <td>Pharmacy Sale</td>
                                        <td><span style="color: green;">Sale on Cash: {{$data->received_amount ?? 0}} &nbsp;&nbsp;&nbsp; <br><span style="color: red">Reg Customer Sale: ({{ ($in_patient_sale) }})</span></td>
                                    </tr>

                                    <tr>
                                        <td >Pharmacy Return<br><p style="font-size: 10px; color: red">( Return taken in other users or posted bills )</p></td>
                                        <td>- {{$pharmacy_return ?? 0}}</td>
                                    </tr>
                                    
                                    <tr>
                                        <td >Payment Received From Registered Customers<br><p style="font-size: 10px; color: green">( This amount is already posted to Cash at Office )</p></td>
                                        <td> {{$payments_received_from_customer ?? 0}}</td>
                                    </tr>

                                
                                    
                                    

                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td><strong>{{ ($data->received_amount) + ($payment_received_from_customer) - ($pharmacy_return)}}</strong></td>
                                    </tr>
                                    @if(getUserRole() == 'Super Admin' || getUserRole() == 'Finance')
                                    <tr>
                                        <td>Cash Closing</td>
                                        <td>
                                            <select class="form-control" required id="finance_head_id" name="finance_head_id">
                                                <option value="">Select Head...</option>
                                                @foreach($finance_heads as $key => $value)
                                                    <option value="{{$value->id}}">{{$value->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td></td>
                                        <td><button class="form-control btn btn-primary" type="submit">Post</button></td>
                                    </tr>
                                        @endif
                                </table>




                            </div>


                            <div class="col-md-6 col-sm-b mb-3">
                                <p style="text-align: center; font-weight:bold;font-size: 12px;color:red;">Pharmacy Return By User</p>
                                <table class="table table-strap">
                                    <tr>
                                        <th style="width: 10%">S.No</th>
                                        <th style="width: 20%">Product Name</th>
                                        <th style="width: 10%">Return Qty</th>
                                        <th style="width: 10%">Unit Price</th>
                                        <th style="width: 10%">Amount</th>
                                        <th style="width: 10%">Return By</th>
                                    </tr>
                                    <?php $total_return=0; ?>
                                    @foreach($pharmacy_item_returns as $key => $value)
                                        <?php $total_return = ($total_return) + (($value->ReturnQuantity) * ($value->UnitePrice)); ?>
                                    @if($value->admission_id == 0 || $value->admission_id == "null")

                                     <tr>
                                        <td style="width: 5%">{{$key + 1}}</td>
                                        <td style="width: 15%">{{$value->product->ProductName ?? ''}}</td>
                                        <td style="width: 10%">{{$value->ReturnQuantity}}</td>
                                        <td style="width: 10%">{{$value->UnitePrice}}</td>
                                        <td style="width: 10%">{{($value->ReturnQuantity) * ($value->UnitePrice)}}</td>
                                        <td style="width: 10%">{{$value->return_by_user->name ?? ''}}</td>
                                       
                                    </tr>
                                    @endif
                                   @endforeach
                                   <tr>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td><b>Total:</b></td>
                                       <td><b>{{$total_return}}</b></td>
                                       <td></td>
                                   </tr>
                                </table>
                            </div>

                        
                        </div>
                    </form>
                </div>
            </div>



        </div>
    </div>


    <div class="row mt-2">
        <div class="col-12">

            <!-- Traffic sources -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 mb-3">
                            <table class="table table-striped">
                                <tr>
                                    <th>S.No</th>
                                    <th>Voucher#</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Voucher Date</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>

                                @foreach($voucher as $key => $value)
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{$value->voucher_number}}</td>
                                        <td>{{$value->voucher_type}}</td>
                                        <td>{{$value->total_amount}}</td>
                                        <td>{{$value->voucher_date}}</td>
                                        <td>{{$value->user->name ?? ''}}</td>
                                        <td>

                                            @if(!$value->approved_by)
                                                <button class="btn btn-sm btn-success approve_entry" record-id="{{$value->id}}" title="Approve">
                                                    <i class="fas fa-check-circle "></i>
                                                </button>

                                                <!-- Delete button -->
                                                <button class="btn btn-sm btn-danger delete_entry" record-id="{{$value->id}}" title="Delete">
                                                    <i class="fas fa-times "></i>
                                                </button>
                                            @endif



                                                <a href="{{ route('pos.printDailyClosingVoucher', $value->id) }}" target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-print"></i> Cloing report
                                                </a>
                                        </td>
                                    </tr>

                                @endforeach
                            </table>

                            <div class="d-flex justify-content-center mt-2">
                                {!! $voucher->links() !!}
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

@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.form.min.js') }}"></script>

    <script type="text/javascript">
        setTimeout(function () {
            $("#user_id").select2();
            $("#finance_head_id").select2();
        },1000);


        $("body").on("click",".approve_entry",function () {
            var value = $(this).attr('record-id');
            if (confirm('Are you sure to approve this record ?')) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.approve_transaction_entry') }}",
                    data: {
                        id: value,
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(res) {
                        window.location.reload();
                    }
                })
            } else {
                $("#discharge_patient").show();
                //alert('Why did you press cancel? You should have confirmed');
            }
        });

        $("body").on("click",".delete_entry",function () {
            var value = $(this).attr('record-id');
            if (confirm('Are you sure to delete this record ?')) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.delete_transaction_entry') }}",
                    data: {
                        id: value,
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(res) {
                        window.location.reload();
                    }
                })
            } else {
                $("#discharge_patient").show();
                //alert('Why did you press cancel? You should have confirmed');
            }
        });
    </script>
@endpush
