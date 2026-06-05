@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<style>
    .table> :not(caption)>*>* {
        padding: 5px;
    }

    .select2-container--default .select2-selection--single {
        min-height: 28px !important;
        height: 28px !important;
        padding: 0 8px !important;
        font-size: 0.92rem !important;
        border-radius: 0.2rem !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 24px !important;
        padding-left: 0 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 28px !important;
    }

    /* Professional Form Layout Styling */
    .patient-form-section {
        background: #f8fafc;
        border-left: 4px solid #3b82f6;
        padding: 15px;
        border-radius: 0 8px 8px 0;
        margin-bottom: 15px;
    }

    .investigation-section {
        background: #f0fdf4;
        border-left: 4px solid #10b981;
        padding: 15px;
        border-radius: 0 8px 8px 0;
        height: fit-content;
    }

    .section-header {
        font-size: 0.9rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
    }

    .section-header i {
        margin-right: 6px;
        color: #3b82f6;
    }

    .investigation-section .section-header i {
        color: #10b981;
    }

    .form-label {
        font-size: 0.8rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.3rem;
    }

    .mr-search-bar {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .investigation-table {
        font-size: 0.85rem;
        margin-top: 10px;
    }

    .investigation-table th {
        background-color: #dcfce7;
        color: #166534;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 8px 6px;
    }

    .investigation-table td {
        padding: 6px;
        font-size: 0.8rem;
    }

    .btn-save {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        border-radius: 6px;
        padding: 8px 20px;
        font-weight: 600;
        color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-save:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-1px);
    }

    .age-group {
        background: #fef3f2;
        border: 1px solid #fecaca;
        border-radius: 6px;
        padding: 1px;
    }

    .age-group label {
        font-size: 0.7rem;
        color: #dc2626;
        font-weight: 600;
        margin: 0 4px;
    }

    .age-group .form-control-sm {
        width: 45px !important;
        height: 24px;
        padding: 2px 4px;
        font-size: 0.75rem;
        text-align: center;
    }
</style>

<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12">

        <!-- Patient Investigation Form -->
        <div class="card">
            <div class="card-body">
                <form class="form-submit-event" id="patient_register">
                    <!-- Hidden Fields -->
                    <input type="hidden" required id="id" name="id" value="0" class="form-control form-control-sm id_class" />
                    <input type="hidden" name="list_investigations" id="list_investigations">
                    <input type="hidden" name="invoice_no" id="invoice_no">
                    <input type="hidden" name="selected_appointment_id" id="selected_appointment_id" value="0">

                    <!-- MR Number Search Bar -->
                    <div class="mr-search-bar">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" id="mr_number" value="" class="form-control form-control-sm" placeholder="Enter MR Number" autocomplete="off">
                            </div>
                            <div class="col-md-5">
                                <select id="appointment_id" name="appointment_id" class="form-control form-control-sm">
                                    <option value="">Select Appointment (Last 24 Hours)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Main Form Layout -->
                    <div class="row">

                        <!-- Patient Information Section (Left Side) -->
                        <div class="col-md-6">
                            <div class="patient-form-section">
                                <div class="section-header">
                                    <i class="fas fa-user"></i>Patient Information
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="contact_no" class="form-label">Contact Number<span class="asterisk">*</span></label>
                                        <input type="text" required id="contact_no" oninput="if(this.value.length > 11) this.value = this.value.slice(0, 11);" name="contact_no" class="form-control form-control-sm" placeholder="03xxxxxxxxx" autocomplete="off">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Patient Name<span class="asterisk">*</span></label>
                                        <input type="text" required id="name" name="name" class="form-control form-control-sm" placeholder="Enter patient name" autocomplete="off">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Gender<span class="asterisk">*</span></label>
                                        <select name="gender" id="gender" class="form-control form-control-sm">
                                            <option selected value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label text-center w-100" style="color: #dc2626; font-weight: 600; font-size: 0.75rem;">Patient Age</label>
                                        <div class="age-group d-flex align-items-center justify-content-center">
                                            <label>YY:</label>
                                            <input type="text" id="age" name="age" required class="form-control form-control-sm" pattern="\d*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="3" title="Only digits are allowed" />
                                            <label>MM:</label>
                                            <input type="text" id="months" value="0" name="months" class="form-control form-control-sm" pattern="\d*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="2" title="Only digits are allowed" />
                                            <label>DD:</label>
                                            <input type="text" id="days" value="0" name="days" class="form-control form-control-sm" pattern="\d*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="2" title="Only digits are allowed" />
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="location_id" class="form-label">Location<span class="asterisk">*</span></label>
                                        <select name="location_id" required id="location_id" class="form-select form-select-sm">
                                            <option value="">Select Location</option>
                                            @foreach ($locations as $location)
                                            @if($location->id == "53")
                                            <option value="{{ $location->id }}" selected>{{ $location->name }}</option>
                                            @else
                                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="consultant_id" class="form-label">Consultant<span class="asterisk">*</span></label>
                                        <select name="consultant_id" id="consultant_id" class="form-select form-select-sm">
                                            <option value="0">Select Consultant</option>
                                            @foreach ($consultants as $value)
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="discount_percentage" class="form-label">Discount<span class="asterisk">*</span></label>
                                        <select name="discount_percentage" id="discount_percentage" required class="form-select form-select-sm">
                                            <option value="0" selected>0%</option>
                                            <option value="2">2%</option>
                                            <option value="5">5%</option>
                                            <option value="7">7%</option>
                                            <option value="10">10%</option>
                                            <option value="15">15%</option>
                                            <option value="20">20%</option>
                                            <option value="25">25%</option>
                                            <option value="30">30%</option>
                                            <option value="40">40%</option>
                                            <option value="50">50%</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Investigation Management Section (Right Side) -->
                        <div class="col-md-6">
                            <div class="investigation-section">
                                <div class="section-header">
                                    <i class="fas fa-flask"></i>Investigation Management
                                </div>

                                <!-- Investigation Selection -->
                                <div class="row">
                                    <div class="col-md-8 mb-2">
                                        <label for="investigation_id" class="form-label">Select Investigation<span class="asterisk">*</span></label>
                                        <select id="investigation_id" class="form-select form-select-sm">
                                            <option value="">Choose Investigation...</option>
                                            @foreach ($investigation as $value)
                                            <option sale_price="{{$value->sale_price}}" investigation_name="{{ $value->name }}" value="{{ $value->id }}">{{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Frequency Input -->
                                    <div class="col-md-4 mb-2">
                                        <label for="frequency" class="form-label">Frequency</label>
                                        <input type="number" class="form-control form-control-sm" id="frequency" value="1" placeholder="Enter frequency" min="1">
                                    </div>
                                </div>

                                <!-- Investigation Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered investigation-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 40%">Name</th>
                                                <th style="width: 15%">Freq</th>
                                                <th style="width: 20%">Amount</th>
                                                <th style="width: 15%">Disc</th>
                                                <th style="width: 15%">Total</th>
                                                <th style="width: 10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="inv_body">
                                            <!-- Dynamic investigation rows -->
                                        </tbody>
                                        <tfoot>
                                            <tr style="background-color: #f3f4f6; font-weight: bold;">
                                                <td colspan="2">Total:</td>
                                                <td><span id="total_bill_amount">0</span></td>
                                                <td><span id="total_bill_discount">0</span></td>
                                                <td></td>
                                            </tr>
                                            <tr style="background-color: #ecfdf5; font-weight: bold; color: #166534;">
                                                <td colspan="2">Net Amount:</td>
                                                <td colspan="2"><span id="net_bill_amount">0</span></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" id="cnic" name="cnic" class="form-control form-control-sm" value="0" placeholder="" autocomplete="off" pattern="\d*" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="13" title="Only digits are allowed" />
                    <input type="hidden" value="-" id="father_husband_name" name="father_husband_name" class="form-control form-control-sm" placeholder="" autocomplete="off">
                    <input type="hidden" name="district_id" id="district_id" class="form-control form-control-sm" value="22">
                    <input type="date" required id="dob" name="dob" class="form-control form-control-sm" style="display: none;" autocomplete="off">

                    <!-- Submit Button -->
                    <div class="text-center mt-3">
                        <button class="btn btn-save" id="submit_btn" type="submit">
                            <i class="fas fa-save me-1"></i>Save Investigation
                        </button>
                    </div>
            </div>
            </form>
        </div>
    </div>


    {{-- LISTIN PATIENTS --}}
    <div class="card my-2">
        <div class="card-body">
            <h5 class="card-title">General Patient Investigations</h5>

            <div class="row">
                <div class="col-md-2">
                    <label>From Date</label>
                    <input type="date" class="form-control form-control-sm" value="{{date("Y-m-d")}}" id="filter_from_date">
                </div>
                <div class="col-md-2">
                    <label>To Date</label>
                    <input type="date" class="form-control form-control-sm" value="{{date("Y-m-d")}}" id="filter_to_date">
                </div>
                <div class="col-md-2">
                    <label>Investigations</label>
                    <select class="form-select form-select-sm" id="investigation_sub_category_id">
                        <option value="">View--All</option>
                        @foreach($investigation_sub_category as $key => $value)
                        <option value="{{$value->id}}">{{$value->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label>Users</label>
                    <select class="form-select form-select-sm" id="created_by">
                        <option value="">View--All</option>
                        @foreach($users as $key => $value)
                        <option value="{{$value->id}}">{{$value->name}}</option>
                        @endforeach
                    </select>
                </div>


                <div class="col-md-3 mt-4">
                    <div class="btn btn-primary btn-sm print_all_details"><i class="tf-icons bx bx-printer"></i></div>
                </div>
            </div>

            <div class="table-responsive" style="min-height: 200px">


                <table id="patient-list" style="width: 100% !important"
                    class="table table-responsive table-striped data_mf_table ">

                    <thead>
                        <tr>
                            <th style="display: none">Patient</th>
                            <th style="width: 15%">Patient</th>
                            <th>Invoice#</th>
                            <th style="display: none;">Invoice#</th>
                            <th>Investigation</th>

                            <th>Amount</th>
                            <th>Discount</th>
                            <th>Net Amount</th>
                            <th>Date</th>
                            <th>Created By</th>
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
            <input type="hidden" class="id_class" id="id" name="id" value="0">

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
                    Close </button>
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
    // Global function to open print popup window
    function openPrintPopup(url) {
        var width = 1000;
        var height = 700;
        var left = (screen.width - width) / 2;
        var top = (screen.height - height) / 2;
        var popup = window.open(url, 'PrintWindow',
            'width=' + width + ',height=' + height + ',top=' + top + ',left=' + left +
            ',toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes');

        if (popup) {
            popup.focus();

            // Check if popup loaded and then close after print
            var checkPopupTimer = setInterval(function() {
                if (popup.closed) {
                    clearInterval(checkPopupTimer);
                } else {
                    try {
                        // Check if the popup has finished loading
                        if (popup.document.readyState === 'complete') {
                            clearInterval(checkPopupTimer);

                            // Add event listeners for print completion
                            popup.onafterprint = function() {
                                popup.close();
                            };

                            // For browsers that don't support onafterprint
                            if (popup.matchMedia) {
                                var mediaQueryList = popup.matchMedia('print');
                                mediaQueryList.addListener(function(mql) {
                                    if (!mql.matches) {
                                        popup.close();
                                    }
                                });
                            }
                        }
                    } catch (e) {
                        // Cross-origin or popup closed
                    }
                }
            }, 100);
        } else {
            alert('Please allow popups for this site to print the report.');
        }
    }

    registered_patients = [];
    list_investigation = [];
    time = "";
    setTimeout(function() {
        $(".select2").select2();
        // $("#district_id").select2();
        $("#investigation_id").select2();
        $("#location_id").select2();
        $("#consultant_id").select2();
        $("#appointment_id").select2();

        $("#edit_consultant_id").select2({
            dropdownParent: $('.my_modal')
        });
        $("#edit_opd_type_id").select2({
            dropdownParent: $('.my_modal')
        });

        time = Date.now();
        $("#invoice_no").val(time);

        // Load today's appointments
        loadTodayAppointments();

    }, 2000);


    $("body").on("change", "#investigation_id", function(e) {
        setTimeout(function() {
            $("#frequency").focus();
        }, 100);
    });

    $("body").on("click", ".remove_item", function(e) {
        var id = $(this).attr("item_id");
        removeItem(id);
    });

    $("body").on("change", "#discount_percentage", function(e) {
        load_investigation();
    });

    $('form').on('keydown', function(e) {
        if (e.key === 'Enter') {
            var investigation_id = $("#investigation_id").val();
            var frequency = $("#frequency").val();
            if (frequency == '' || frequency == 0) {
                frequency = 1;
            }
            if (investigation_id == "") {
                alert("Please select investigation");
                return false;
            }
            let rate = $("#investigation_id").find("option:selected").attr("sale_price");
            let investigation_name = $("#investigation_id").find("option:selected").attr("investigation_name");

            const exists = list_investigation.some(item => item.investigation_id == investigation_id);
            if (!exists) {
                list_investigation.push({
                    investigation_id: investigation_id,
                    investigation_name: investigation_name,
                    frequency: frequency,
                    rate: rate
                });
                load_investigation();
            } else {
                alert("Investigation already exist");

            }



            // $("#investigation_id").val("").trigger("change");
            $("#investigation_id").select2('open');
            $("#investigation_id").focus();


            e.preventDefault(); // Prevent the default form submission
        }
    });

    function load_investigation() {
        $("#inv_body").html("");
        var discount = $("#discount_percentage").val();
        if (discount == '')
            discount = 0;

        var total_bill_amount = 0;
        var total_discount = 0;
        var net_bill_amount = 0;
        list_investigation.forEach(function(value, key) {
            discount_amount = (value.rate) * (discount / 100);
            discount_amount = (discount_amount.toFixed(2)) * value.frequency;
            list_investigation[key]['discount_percentage'] = discount;
            list_investigation[key]['discount_amount'] = discount_amount;

            var net_rate = (value.rate) - (discount_amount);
            net_rate = (net_rate) * value.frequency;


            total_bill_amount += parseFloat(value.rate);
            total_discount += parseFloat(discount_amount);
            net_bill_amount += parseFloat(net_rate);

            var html = `<tr>
                                <td>${value.investigation_name}</td>
                                <td>${value.frequency}</td>
                                <td>${value.rate * value.frequency}</td>
                                <td>${discount_amount}</td>
                                <td>${net_rate}</td>
                                <td ><a href="javascript:void(0)" class="remove_item" item_id='${value.investigation_id}' style="color:red">x</a></td>

                                `;
            $("#inv_body").append(html);
        });

        setTimeout(function() {
            $("#total_bill_amount").html(total_bill_amount);
            $("#net_bill_amount").html(net_bill_amount);
            $("#total_bill_discount").html(total_discount);
            $("#list_investigations").val(JSON.stringify(list_investigation));

        }, 100);
    }

    function removeItem(id) {
        list_investigation = list_investigation.filter(item => item.investigation_id != id);
        load_investigation();
    }




    $("body").on("click", ".print_all_details", function(e) {
        var from_date = $("#filter_from_date").val();
        var to_date = $("#filter_to_date").val();
        var investigation_sub_category_id = $("#investigation_sub_category_id").val();
        var created_by = $("#created_by").val();

        if (from_date == '') {
            from_date = 'nill';
        }
        if (to_date == '') {
            to_date = 'nill';
        }
        if (investigation_sub_category_id == '') {
            investigation_sub_category_id = 0;
        }
        if (created_by == '') {
            created_by = 0;
        }
        //print_all_investigations
        var url = "{{route('pos.print_all_investigations')}}/" + from_date + "/" + to_date + "/" + investigation_sub_category_id + "/" + created_by;
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
        dob.setMonth(dob.getMonth() - months); // Subtract months
        dob.setDate(dob.getDate() - days); // Subtract days


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
        var opd_type_id = $("#edit_opd_type_id").val();
        var consultant_id = $("#edit_consultant_id").val();
        if (id == '' || opd_type_id == '' || consultant_id == '') {
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
        if (year == '') {
            year = 0;
        }
        if (months == '') {
            months = 0;
        }
        if (days == '') {
            days = 0;
        }

        var dob = calculateDOB(year, months, days);
        $("#dob").val(dob);
    });



    $("body").on("change", "#dob", function() {
        calculateAgeDetails($(this).val());
    });

    // Load today's appointments
    function loadTodayAppointments() {
        // Calculate date 24 hours ago
        var now = new Date();
        var yesterday = new Date(now.getTime() - (24 * 60 * 60 * 1000));
        var fromDate = yesterday.toISOString().split('T')[0];
        var toDate = now.toISOString().split('T')[0];

        $.ajax({
            type: 'GET',
            url: "{{ route('pos.list_appointments') }}",
            data: {
                from_date: fromDate,
                to_date: toDate,
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                if (res.data && res.data.length > 0) {
                    $("#appointment_id").html('<option value="">Select Appointment (Last 24 Hours)</option>');
                    $.each(res.data, function(index, appointment) {
                        if (appointment.patient) {
                            var displayText = appointment.patient.name;
                            var mrNumber = appointment.patient.mr_no || appointment.patient.mr_number || '';
                            if (mrNumber) {
                                displayText += ' - MR: ' + mrNumber;
                            }
                            displayText += ' - ' + appointment.patient.contact_no;
                            if (appointment.consultant) {
                                displayText += ' - Dr. ' + appointment.consultant.name;
                            }
                            // Store appointment_id, mr_number, contact_no and consultant_id in the option value as JSON
                            var optionData = JSON.stringify({
                                appointment_id: appointment.id,
                                mr_number: mrNumber,
                                contact_no: appointment.patient.contact_no,
                                consultant_id: appointment.consultant_id || 0
                            });
                            $("#appointment_id").append('<option value=\'' + optionData + '\'>' + displayText + '</option>');
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading appointments:', error);
            }
        });
    }

    // Handle appointment selection - trigger mr_number blur event
    $("body").on("change", "#appointment_id", function() {
        var selectedValue = $(this).val();
        if (selectedValue) {
            try {
                var appointmentData = JSON.parse(selectedValue);
                var appointmentId = appointmentData.appointment_id;
                var mrNumber = appointmentData.mr_number;
                var contactNo = appointmentData.contact_no;
                var consultantId = appointmentData.consultant_id;

                // Store the appointment_id in the hidden field
                if (appointmentId) {
                    $("#selected_appointment_id").val(appointmentId);
                }

                // Use MR number if available, otherwise fall back to contact number
                if (mrNumber) {
                    $("#mr_number").val(mrNumber);

                    // Set consultant_id if available
                    if (consultantId && consultantId > 0) {
                        $("#consultant_id").val(consultantId).trigger('change');
                    }

                    // Trigger blur event to fetch patient information
                    $("#mr_number").trigger("blur");
                } else if (contactNo) {
                    $("#contact_no").val(contactNo);

                    // Set consultant_id if available
                    if (consultantId && consultantId > 0) {
                        $("#consultant_id").val(consultantId).trigger('change');
                    }

                    // Trigger blur event to fetch patient information
                    $("#contact_no").trigger("blur");
                }
            } catch (e) {
                console.error('Error parsing appointment data:', e);
            }
        }
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
                        if (registered_patients.length > 0) {
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
                    } else {
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

        if (mr_number.length > 3) {
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
                        if (registered_patients.length > 0) {
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
                    } else {
                        registered_patients = [];
                        reset_fields();
                    }

                }
            });

        }
    });

    $("body").on("change", "#filter_from_date,#filter_to_date,#investigation_sub_category_id,#created_by", function() {
        user_table.ajax.reload();
    });
    $("body").on("click", ".select_patient", function() {
        var index = $(this).attr("data-id");
        var details = registered_patients[index];

        $("#id").val(details.id);
        $("#cnic").val(details.cnic);
        $("#name").val(details.name);
        $("#contact_no").val(details.contact_no);
        $("#district_id").val(details.district_id).trigger('change');
        $("#location_id").val(details.location_id).trigger('change');
        $("#dob").val(details.dob);
        $("#father_husband_name").val(details.father_husband_name);
        $("#g4no").val(details.g4no);
        $("#age").val(details.age);
        $("#gender").val(details.gender);
        //$("#regdate").val(details.formatted_date);
        registered_patients = [];
        $("#add_new_record_model").modal("hide");

    });


    user_table = $('#patient-list').DataTable({
        processing: true,
        serverSide: true,

        pageLength: 20,
        ajax: {
            url: "{{ route('pos.get_list_investigations') }}",
            data: function(d) {
                d.from_date = $('#filter_from_date').val();
                d.to_date = $('#filter_to_date').val();
                d.opd_type_id = $('#filter_opd_type_id').val();
                d.consultant_id = $('#filter_consultant_id').val();
                d.investigation_sub_category_id = $('#investigation_sub_category_id').val();
                d.created_by = $('#created_by').val();

            }
        },

        columns: [{
                data: 'patient.name',
                visible: false
            },
            {
                data: null, // Use null because you're creating a custom render
                name: 'patient',
                searchable: false, // If you want it searchable, additional backend support is required
                render: function(data, type, row) {
                    // Combine first_name and last_name to display full_name
                    return '<b style="font-size:12px;color:green;">' + row.patient.name + '</b> <br><b style="font-size:12px;color:red;">' + row.patient.mr_no + '</b>';
                }
            },


            {
                data: 'invoice_no',
                name: 'invoice_no',
                visible: false
            },
            {
                data: 'print_invoice_number',
                name: 'print_invoice_number',
                searchable: true
            }, {
                data: 'investigation.name',
                name: 'investigation.name',
                searchable: true
            },

            {
                data: 'sale_price',
                name: 'sale_price',
                searchable: true
            },
            {
                data: 'discount_amount',
                name: 'discount_amount',
                searchable: true
            },
            {
                data: null, // Use null because you're creating a custom render
                name: 'Net Amount',
                searchable: false, // If you want it searchable, additional backend support is required
                render: function(data, type, row) {
                    // Combine first_name and last_name to display full_name
                    return '<b style="color:green;">' + (row.sale_price - row.discount_amount) + '</b> <br>';
                }
            },
            {
                data: 'inv_date',
                name: 'inv_date',
                searchable: true
            },
            {
                data: 'created_by_user',
                name: 'users.name',
                searchable: true
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

        var list_investigations = $("#list_investigations").val();
        if (list_investigations == '[]' || list_investigations == '') {
            alert("Please Add investigations");
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
                url: "{{ route('pos.save_general_patient_investigation') }}",
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}'

                },
                success: function(response) {
                    var invoice_no = $("#invoice_no").val();
                    if (response.status == 'empty') {
                        alert(response.message);
                        return false;
                    }

                    url = "{{route('pos.print_hospital_lab_invoice')}}/" + invoice_no;
                    window.open(url, '_blank');
                    setTimeout(function() {
                        reset_fields();
                    }, 1000);

                    $("#mr_number").val("");
                    //window.location.reload();
                    user_table.ajax.reload();

                    //var url = "{{route('pos.print_hospital_lab_invoice')}}/"+time;
                    //window.open(url, '_blank');
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
                url: "{{ route('pos.delete_patient_investigation') }}",
                data: {
                    id: id,
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
        $("#id").val("0");
        $("#id").val("0");

        $("#contact_no").val('');

        $("#district_id").val('22').trigger("change");
        $("#dob").val('');
        $("#father_husband_name").val('-');
        $("#g4no").val(0);
        $("#age").val('');
        $("#months").val('0');
        $("#days").val('0');
        $("#gender").val('male');
        $("#id").val('');
        $("#location_id").val('53').trigger("change");
        $("#consultant_id").val(0).trigger("change");
        $("#cnic").val('0');
        $("#cnic").val('0');
        $("#name").val('');
        $("#regdate").val('{{date("Y-m-d")}}');
        //$("#relation_id").val('');
        $("#sc_ref_no").val('0');
        let time = Date.now();
        $("#discount_percentage").val('0');
        $("#invoice_no").val(time);
        $("#selected_appointment_id").val('0');
        $("#appointment_id").val('').trigger('change');
        list_investigation = [];
        load_investigation();
        $(".id_class").val("0");

    }

    // Intercept print button clicks and open in popup
    $("body").on("click", ".print_inv_record", function(e) {
        e.preventDefault();
        var url = $(this).attr('href');

        // Change row color to indicate it has been printed
        var row = $(this).closest('tr');
        row.css({
            'background-color': '#d1fae5',
            'transition': 'background-color 0.3s ease'
        });

        // Optional: Add a visual indicator
        if (!row.find('.printed-badge').length) {
            row.find('td:first').prepend('<span class="badge bg-success printed-badge me-1" style="font-size: 0.65rem;">Printed</span>');
        }

        openPrintPopup(url);
    });

    // Intercept invoice number link clicks and open in popup
    $("body").on("click", ".print_invoice_link", function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        openPrintPopup(url);
    });
</script>
@endpush