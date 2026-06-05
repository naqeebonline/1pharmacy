@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />

<style>
    /* Red background for negative balance rows */
    .table-danger {
        background-color: #f8d7da !important;
        border-color: #f5c6cb !important;
    }
    .table-danger > td {
        background-color: #f8d7da !important;
        border-color: #f5c6cb !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Package Appointments - Today ({{ date('Y-m-d') }})</h4>
                    <p class="text-muted mb-0">Appointments with Medicine or Lab packages</p>
                </div>
                <div class="card-body">
                    @if(getUserRole() == 'Super Admin' || getUserRole() == 'Finance')
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="from_date" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="from_date" name="from_date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="to_date" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="to_date" name="to_date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-secondary" id="reset-dates">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                        </div>
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="package-appointments-table">
                            <thead>
                                <tr>
                                    <th>Actions</th>
                                    <th>Appointment No</th>
                                    <th>Patient Name</th>
                                    <th>MR No</th>
                                    <th>Consultant</th>
                                    <th>OPD Type</th>
                                    <th>Appointment Date</th>
                                    <th>Fee</th>
                                    <th>Investigation Cost</th>
                                    <th>Medicine Cost</th>
                                    <th>Total Cost</th>
                                    <th>Balance</th>
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

<!-- Appointment Details Modal -->
<div class="modal fade" id="appointmentDetailsModal" tabindex="-1" aria-labelledby="appointmentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="appointmentDetailsModalLabel">
                    <i class="fa fa-file-medical"></i> Appointment Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fa fa-user"></i> Patient Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Appointment No:</th>
                                <td id="detail_appointment_number"></td>
                            </tr>
                            <tr>
                                <th>Patient Name:</th>
                                <td id="detail_patient_name"></td>
                            </tr>
                            <tr>
                                <th>MR No:</th>
                                <td id="detail_mr_no"></td>
                            </tr>
                            <tr>
                                <th>Consultant:</th>
                                <td id="detail_consultant_name"></td>
                            </tr>
                            <tr>
                                <th>OPD Type:</th>
                                <td id="detail_opd_type"></td>
                            </tr>
                            <tr>
                                <th>Appointment Date:</th>
                                <td id="detail_appointment_date"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fa fa-calculator"></i> Financial Summary</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">OPD Fee:</th>
                                <td>Rs. <span id="detail_opd_fee"></span></td>
                            </tr>
                            <tr>
                                <th>Package Fee (Budget):</th>
                                <td class="fw-bold text-primary">Rs. <span id="detail_fee"></span></td>
                            </tr>
                            <tr class="border-top">
                                <th>Investigation Cost:</th>
                                <td>Rs. <span id="detail_investigation_cost"></span></td>
                            </tr>
                            <tr>
                                <th>Medicine Cost:</th>
                                <td>Rs. <span id="detail_medicine_cost"></span></td>
                            </tr>
                            <tr>
                                <th>Consultant Fee:</th>
                                <td>Rs. <span id="detail_consultant_fee"></span></td>
                            </tr>
                            <tr class="border-top fw-bold">
                                <th>Total Expenses:</th>
                                <td class="text-danger">Rs. <span id="detail_total_expenses"></span></td>
                            </tr>
                            <tr class="border-top fw-bold">
                                <th>Balance:</th>
                                <td id="detail_balance_container"></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fa fa-flask"></i> Investigations</h6>
                        <div id="investigations_details">
                            <p class="text-muted">No investigations added</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="fa fa-pills"></i> Medicines</h6>
                        <div id="medicines_details">
                            <p class="text-muted">No medicines added</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Investigation Modal (reuse from appointments.blade.php) -->
<div class="modal fade" id="investigationModal" tabindex="-1" aria-labelledby="investigationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="investigationModalLabel">Add Investigations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="already-added-investigations" class="mb-3">
                    <h6>Already Added:</h6>
                    <div id="added-investigations-list"></div>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="select_all_investigations">
                    <label class="form-check-label" for="select_all_investigations">
                        <strong>Select All</strong>
                    </label>
                </div>
                <div id="investigations-list">
                    <!-- Investigations will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-investigations">Save Investigations</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>

<script>
    var packageAppointmentsTable;
    var currentAppointmentId = 0;
    var currentPatientId = 0;

    $(document).ready(function() {
        // Initialize DataTable
        packageAppointmentsTable = $('#package-appointments-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pos.get_package_appointments') }}",
                type: 'GET',
                data: function(d) {
                    var fromDate = $('#from_date').val();
                    var toDate = $('#to_date').val();
                    console.log('DataTable sending - From:', fromDate, 'To:', toDate);
                    d.from_date = fromDate;
                    d.to_date = toDate;
                }
            },
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'appointment_number', name: 'appointment_number' },
                { data: 'patient_name', name: 'patient.name' },
                { data: 'mr_no', name: 'patient.mr_no' },
                { data: 'consultant_name', name: 'consultant.name' },
                { data: 'opd_type_name', name: 'opd_type.name' },
                { data: 'appointment_date', name: 'appointment_date' },
                { data: 'fee', name: 'fee' },
                { data: 'investigation_cost', name: 'investigation_cost', orderable: false },
                { data: 'medicine_cost', name: 'medicine_cost', orderable: false },
                { data: 'total_cost', name: 'total_cost', orderable: false },
                { data: 'balance', name: 'balance', orderable: false }
            ],
            order: [[7, 'desc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
        });

        // Add Investigation button click
        $(document).on('click', '.add-investigation', function() {
            currentAppointmentId = $(this).data('appointment_id');
            currentPatientId = $(this).data('patient_id');
            loadInvestigations(currentAppointmentId);
            $('#investigationModal').modal('show');
        });

        // View appointment details button click
        $(document).on('click', '.view-appointment', function() {
            var appointmentId = $(this).data('id');
            loadAppointmentDetails(appointmentId);
        });

        // Select all investigations
        $('#select_all_investigations').on('change', function() {
            $('.investigation-checkbox').prop('checked', $(this).prop('checked'));
        });

        // Save investigations
        $('#save-investigations').on('click', function() {
            saveInvestigations();
        });

        // Auto-filter on date change (only if elements exist)
        if ($('#from_date').length && $('#to_date').length) {
            $('#from_date, #to_date').on('change', function() {
                console.log('Date changed - From:', $('#from_date').val(), 'To:', $('#to_date').val());
                packageAppointmentsTable.ajax.reload();
            });
        }

        // Reset date filters
        $('#reset-dates').on('click', function() {
            $('#from_date').val('{{ date('Y-m-d') }}');
            $('#to_date').val('{{ date('Y-m-d') }}');
            packageAppointmentsTable.ajax.reload();
        });
    });

    function loadInvestigations(appointmentId) {
        $.ajax({
            url: "{{ route('pos.get_opd_investigations') }}",
            type: 'POST',
            data: {
                appointment_id: appointmentId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Display already added investigations
                    if (response.already_added && response.already_added.length > 0) {
                        var alreadyAddedHtml = '';
                        response.already_added.forEach(function(inv) {
                            alreadyAddedHtml += '<span class="badge bg-success me-2 mb-2">' + inv.investigation_name + '</span>';
                        });
                        $('#added-investigations-list').html(alreadyAddedHtml);
                        $('#already-added-investigations').show();
                    } else {
                        $('#already-added-investigations').hide();
                    }

                    // Display available investigations
                    var html = '';
                    if (response.investigations && response.investigations.length > 0) {
                        response.investigations.forEach(function(inv) {
                            html += '<div class="form-check mb-2">';
                            html += '<input class="form-check-input investigation-checkbox" type="checkbox" value="' + inv.id + '" id="inv_' + inv.id + '">';
                            html += '<label class="form-check-label" for="inv_' + inv.id + '">';
                            html += inv.name + ' - Rs. ' + inv.price;
                            html += '</label>';
                            html += '</div>';
                        });
                    } else {
                        html = '<p class="text-muted">No investigations available for this OPD type.</p>';
                    }
                    $('#investigations-list').html(html);
                }
            },
            error: function(xhr) {
                alert('Error loading investigations');
            }
        });
    }

    function saveInvestigations() {
        var selectedInvestigations = [];
        $('.investigation-checkbox:checked').each(function() {
            selectedInvestigations.push($(this).val());
        });

        if (selectedInvestigations.length === 0) {
            alert('Please select at least one investigation');
            return;
        }

        $.ajax({
            url: "{{ route('pos.save_patient_investigations') }}",
            type: 'POST',
            data: {
                appointment_id: currentAppointmentId,
                patient_id: currentPatientId,
                investigations: selectedInvestigations,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#investigationModal').modal('hide');
                    packageAppointmentsTable.ajax.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Error saving investigations');
            }
        });
    }

    function loadAppointmentDetails(appointmentId) {
        $.ajax({
            url: "{{ route('pos.get_appointment_details') }}",
            type: 'GET',
            data: {
                appointment_id: appointmentId
            },
            success: function(response) {
                if (response.success) {
                    // Fill appointment information
                    $('#detail_appointment_number').text(response.appointment.appointment_number);
                    $('#detail_patient_name').text(response.appointment.patient_name);
                    $('#detail_mr_no').text(response.appointment.mr_no);
                    $('#detail_consultant_name').text(response.appointment.consultant_name);
                    $('#detail_opd_type').text(response.appointment.opd_type);
                    $('#detail_appointment_date').text(response.appointment.appointment_date);
                    $('#detail_opd_fee').text(parseFloat(response.appointment.opd_fee).toFixed(2));
                    $('#detail_fee').text(parseFloat(response.appointment.fee).toFixed(2));
                    $('#detail_investigation_cost').text(parseFloat(response.investigation_cost).toFixed(2));
                    $('#detail_medicine_cost').text(parseFloat(response.medicine_cost).toFixed(2));
                    $('#detail_consultant_fee').text(parseFloat(response.consultant_fee).toFixed(2));
                    $('#detail_total_expenses').text(parseFloat(response.total_expenses).toFixed(2));
                    
                    // Display balance with color
                    var balance = parseFloat(response.balance);
                    var balanceClass = balance >= 0 ? 'text-success' : 'text-danger';
                    var balanceIcon = balance >= 0 ? 'fa-plus-circle' : 'fa-minus-circle';
                    $('#detail_balance_container').html(
                        '<span class="' + balanceClass + ' fw-bold">' +
                        '<i class="fa ' + balanceIcon + '"></i> Rs. ' + balance.toFixed(2) +
                        '</span>'
                    );
                    
                    // Display investigations
                    if (response.investigations && response.investigations.length > 0) {
                        var investigationsHtml = '<table class="table table-sm table-bordered">';
                        investigationsHtml += '<thead><tr><th>Investigation Name</th><th>Cost</th></tr></thead>';
                        investigationsHtml += '<tbody>';
                        response.investigations.forEach(function(inv) {
                            investigationsHtml += '<tr>';
                            investigationsHtml += '<td>' + inv.name + '</td>';
                            investigationsHtml += '<td>Rs. ' + parseFloat(inv.inv_amount).toFixed(2) + '</td>';
                            investigationsHtml += '</tr>';
                        });
                        investigationsHtml += '</tbody></table>';
                        $('#investigations_details').html(investigationsHtml);
                    } else {
                        $('#investigations_details').html('<p class="text-muted">No investigations added</p>');
                    }
                    
                    // Display medicines
                    if (response.medicines && response.medicines.length > 0) {
                        var medicinesHtml = '<table class="table table-sm table-bordered">';
                        medicinesHtml += '<thead><tr><th>Medicine Name</th><th>Qty</th><th>Unit Cost</th><th>Total</th></tr></thead>';
                        medicinesHtml += '<tbody>';
                        response.medicines.forEach(function(med) {
                            var qty = med.Quantity - (med.ReturnQuantity || 0);
                            medicinesHtml += '<tr>';
                            medicinesHtml += '<td>' + med.ProductName + '</td>';
                            medicinesHtml += '<td>' + qty + '</td>';
                            medicinesHtml += '<td>Rs. ' + parseFloat(med.PurchasePrice).toFixed(2) + '</td>';
                            medicinesHtml += '<td>Rs. ' + parseFloat(med.total_cost).toFixed(2) + '</td>';
                            medicinesHtml += '</tr>';
                        });
                        medicinesHtml += '</tbody></table>';
                        $('#medicines_details').html(medicinesHtml);
                    } else {
                        $('#medicines_details').html('<p class="text-muted">No medicines added</p>');
                    }
                    
                    $('#appointmentDetailsModal').modal('show');
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Error loading appointment details');
            }
        });
    }
</script>
@endpush
