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

    <div class="row mt-2">
        <div class="col-12">

            <!-- Traffic sources -->
            <div class="card">
                <div class="card-body">
                    <h5 >Cash Receipt Voucher</h5>
                    <form class=" form-submit-event" method="post" action="{{route('pos.save_cash_receipt_voucher')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 col-sm-4 mb-3">
                                <table class="table table-bordered">
                                    <tr>
                                        <td style="width: 30%">DR:</td>
                                        <td style="width: 70%">
                                            <select name="debit_head_id" required id="debit_head_id" class="form-select">
                                                <option value="">Select Head ----</option>
                                                @foreach ($finance_heads as $key => $value)
                                                    <option value="{{ $value->id }}">{{ strtoupper($value->name) }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Head</td>
                                        <td>
                                            <select name="credit_head_id" required id="credit_head_id" class="form-select">
                                                <option value="">Select Head ----</option>
                                                @foreach ($sub_heads as $key => $value)
                                                    <option value="{{ $value->id }}">{{ strtoupper($value->name) }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Amount</td>
                                        <td>
                                            <input type="number" class="form-control" id="amount" name="amount">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Remarks</td>
                                        <td>
                                            <textarea name="remarks" id="remarks" class="form-control"></textarea>
                                        </td>
                                    </tr>






                                    <tr>
                                        <td>Balance: Cr <span id="cr_amount_display">0</span></td>
                                        <td><button class="form-control btn btn-primary" type="submit">Post</button></td>
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
                                <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Voucher Number</th>
                                    <th>Voucher Type</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Remarks</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($vouchers as $key => $value)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{$value->voucher_number ?? ""}}</td>
                                        <td>{{ $value->voucher_type }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($value->voucher_date)->format('d-m-Y') }}
                                        </td>
                                        <td>
                                            {{ number_format($value->total_amount, 2) }}
                                        </td>
                                        <td>{{ $value->remarks }}</td>
                                        <td>
                                            @if(!$value->approved_by)
                                                <button class="btn btn-sm btn-success approve_entry" record-id="{{ $value->id }}" title="Approve">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>

                                                <button class="btn btn-sm btn-danger delete_entry" record-id="{{ $value->id }}" title="Delete">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif

                                            <a href="{{ route('pos.printDailyClosingVoucher', $value->id) }}" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-center mt-2">
                                {!! $vouchers->links() !!}
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
            $("#credit_head_id").select2();
            $("#debit_head_id").select2();
        },1000);
        $("body").on("change","#debit_head_id",function () {
            var value = $(this).val();
            $.ajax({
                type: 'post',
                url: "{{ route('pos.getBalance') }}",
                data: {
                    id: value,
                    _token: '{{ csrf_token() }}'

                },
                success: function(res) {
                    $("#cr_amount_display").text(res);
                }
            })
        });

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
