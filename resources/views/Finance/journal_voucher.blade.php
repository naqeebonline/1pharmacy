@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@section('content')
    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5>Journal Voucher</h5>
                    <form id="jv_form" method="POST" action="{{ route('pos.save_journal_voucher') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <tr>
                                        <td style="width: 10%">Head:</td>
                                        <td style="width: 30%">
                                            <select id="head_id" class="form-select">
                                                <option value="">Select Head ----</option>
                                                @foreach ($finance_heads as $head)
                                                    <option value="{{ $head->id }}">{{ strtoupper($head->name) }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="width: 10%">
                                            <label><input type="radio" name="type" value="debit" checked> Debit</label>
                                            <label><input type="radio" name="type" value="credit"> Credit</label>
                                        </td>
                                        <td style="width: 20%">
                                            <input type="number" id="amount" class="form-control" placeholder="Amount">
                                        </td>
                                        <td style="width: 20%">
                                            <textarea id="remarks" class="form-control"></textarea>
                                        </td>
                                        <td style="width: 10%">
                                            <button type="button" id="add_entry" class="btn btn-success btn-sm">Add</button>
                                        </td>
                                    </tr>

                                </table>
                            </div>
                        </div>

                        <h6 class="mt-3">JV Entries</h6>
                        <table class="table table-striped" id="jv_table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Head</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Debit:</th>
                                <th id="total_debit">0</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Total Credit:</th>
                                <th id="total_credit">0</th>
                                <th colspan="2"></th>
                            </tr>
                            </tfoot>
                        </table>

                        <input type="hidden" name="entries" id="entries_data">
                        <button type="submit" id="post_btn" class="btn btn-primary" disabled>Post Voucher</button>
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

                                            <a href="{{ route('pos.printJournalVoucher', $value->id) }}" target="_blank" class="btn btn-sm btn-primary">
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
@endsection


@push('scripts')
    <script>
        let entries = [];
        let counter = 1;
       setTimeout(function () {
           $("#head_id").select2();
       },2000);
        function renderTable() {
            let tbody = $("#jv_table tbody");
            tbody.empty();

            let totalDebit = 0, totalCredit = 0;

            entries.forEach((entry, index) => {
                let row = `<tr>
            <td>${index+1}</td>
            <td>${entry.head_name}</td>
            <td>${entry.type.toUpperCase()}</td>
            <td>${entry.amount}</td>
            <td>${entry.remarks}</td>
            <td><button type="button" class="btn btn-danger btn-sm remove_entry" data-index="${index}">X</button></td>
        </tr>`;
                tbody.append(row);

                if(entry.type === 'debit') totalDebit += parseFloat(entry.amount);
                if(entry.type === 'credit') totalCredit += parseFloat(entry.amount);
            });

            $("#total_debit").text(totalDebit.toFixed(2));
            $("#total_credit").text(totalCredit.toFixed(2));

            // Enable Post button only if balanced
            if(totalDebit > 0 && totalDebit === totalCredit){
                $("#post_btn").prop("disabled", false);
            } else {
                $("#post_btn").prop("disabled", true);
            }

            // update hidden field for Laravel
            $("#entries_data").val(JSON.stringify(entries));
        }

        // Add entry
        $("#add_entry").on("click", function(){
            let head_id = $("#head_id").val();
            let head_name = $("#head_id option:selected").text();
            let type = $("input[name='type']:checked").val();
            let amount = $("#amount").val();
            let remarks = $("#remarks").val();

            if(!head_id || !amount){
                alert("Please select head and enter amount");
                return;
            }

            entries.push({head_id, head_name, type, amount, remarks});
            renderTable();

            // Reset inputs
            $("#amount").val("");
            $("#remarks").val("");
        });

        // Remove entry
        $(document).on("click", ".remove_entry", function(){
            let index = $(this).data("index");
            entries.splice(index, 1);
            renderTable();
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
