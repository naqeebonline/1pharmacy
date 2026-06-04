@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
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

    .vital-signs-section {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        padding: 15px;
        border-radius: 0 8px 8px 0;
        margin-bottom: 15px;
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

    .vital-signs-section .section-header i {
        color: #f59e0b;
    }

    .form-label {
        font-size: 0.8rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.3rem;
    }

    .asterisk {
        color: red;
        font-weight: bold;
    }

    .btn-save {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        border-radius: 6px;
        padding: 10px 30px;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
    }

    .appointments-table {
        font-size: 0.85rem;
    }

    .appointments-table th {
        background-color: #dbeafe;
        color: #1e40af;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 10px 8px;
    }

    .appointments-table td {
        padding: 8px;
        font-size: 0.8rem;
    }

    .card-header {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: white;
        font-weight: 600;
    }

    .vital-input {
        border-left: 3px solid #f59e0b;
    }

    #hx_form {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="page-content">
    <div class="page-header d-flex justify-content-between align-items-center mb-3">
        <h4 class="page-title">
            <i class="fas fa-heartbeat"></i> {{ $title }}
        </h4>
    </div>

    <!-- Today's Appointments Section -->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fas fa-calendar-day"></i> Today's Appointments
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover appointments-table" id="appointments_table">
                    <thead>
                        <tr>
                            <th>Sr#</th>
                            <th>MR No</th>
                            <th>Patient Name</th>
                            <th>Phone</th>
                            <th>Age</th>
                            <th>Consultant</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- HX Complaint Recording Form -->
    <div class="card" id="hx_form">
        <div class="card-header">
            <i class="fas fa-edit"></i> <span id="form_title">Record HX Complaint</span>
            <button type="button" class="btn btn-sm btn-light float-right" id="close_form">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
        <div class="card-body">
            <form id="save_hx_form">
                <input type="hidden" id="hx_id" name="id" value="0">
                <input type="hidden" id="appointment_id" name="appointment_id">
                <input type="hidden" id="patient_id" name="patient_id">

                <!-- Patient Information Display -->
                <div class="patient-form-section">
                    <div class="section-header">
                        <i class="fas fa-user"></i> Patient Information
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <p><strong>MR No:</strong> <span id="display_mr_no">-</span></p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Patient Name:</strong> <span id="display_patient_name">-</span></p>
                        </div>
                        <div class="col-md-2">
                            <p><strong>Age:</strong> <span id="display_age">-</span></p>
                        </div>
                        <div class="col-md-2">
                            <p><strong>Gender:</strong> <span id="display_gender">-</span></p>
                        </div>
                        <div class="col-md-2">
                            <p><strong>Phone:</strong> <span id="display_phone">-</span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Consultant:</strong> <span id="display_consultant">-</span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Appointment Time:</strong> <span id="display_appointment_time">-</span></p>
                        </div>
                    </div>
                </div>

                <!-- Vital Signs Section -->
                <div class="vital-signs-section">
                    <div class="section-header">
                        <i class="fas fa-heartbeat"></i> Vital Signs
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="bp" class="form-label">Blood Pressure (BP)</label>
                            <input type="text" class="form-control form-control-sm vital-input" id="bp" name="bp"
                                placeholder="e.g., 120/80">
                            <small class="text-muted">Format: 120/80</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="temp" class="form-label">Temperature (Temp)</label>
                            <input type="text" class="form-control form-control-sm vital-input" id="temp" name="temp"
                                placeholder="e.g., 98.6°F">
                            <small class="text-muted">Format: 98.6°F or 37°C</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="pulse" class="form-label">Pulse Rate</label>
                            <input type="text" class="form-control form-control-sm vital-input" id="pulse" name="pulse"
                                placeholder="e.g., 72 bpm">
                            <small class="text-muted">Format: 72 bpm</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="rr" class="form-label">Respiratory Rate (R/R)</label>
                            <input type="text" class="form-control form-control-sm vital-input" id="rr" name="rr"
                                placeholder="e.g., 16/min">
                            <small class="text-muted">Format: 16/min</small>
                        </div>
                    </div>
                </div>

                <!-- Chief Complaint Section -->
                <div class="patient-form-section">
                    <div class="section-header">
                        <i class="fas fa-comment-medical"></i> Chief Complaint
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="complaint" class="form-label">Complaint Details</label>
                            <textarea class="form-control" id="complaint" name="complaint" rows="4"
                                placeholder="Enter patient's chief complaint..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Investigation Section -->
                <div class="patient-form-section">
                    <div class="section-header">
                        <i class="fas fa-flask"></i> Investigation / Notes
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="investigation" class="form-label">Investigation Details</label>
                            <textarea class="form-control" id="investigation" name="investigation" rows="4"
                                placeholder="Enter investigation details or clinical notes..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-secondary" id="cancel_form">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save"></i> Save HX Complaint
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- jQuery (if not already loaded) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        // Check if jQuery and DataTables are loaded
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded!');
            alert('jQuery is not loaded. Please check your layout file.');
            return;
        }

        if (typeof $.fn.DataTable === 'undefined') {
            console.error('DataTables is not loaded!');
            alert('DataTables is not loaded. Please check your layout file.');
            return;
        }

        console.log('Initializing HX Complaints module...');

        // Initialize DataTable for appointments
        var appointmentsTable = $('#appointments_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('hx.get_today_appointments') }}",
                error: function(xhr, error, thrown) {
                    console.error('DataTable Error:', error, thrown);
                    console.error('Response:', xhr.responseText);
                    alert('Error loading appointments: ' + error);
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'mr_no',
                    name: 'mr_no'
                },
                {
                    data: 'patient_name',
                    name: 'patient_name'
                },
                {
                    data: 'patient_phone',
                    name: 'patient_phone'
                },
                {
                    data: 'patient_age',
                    name: 'patient_age'
                },
                {
                    data: 'consultant_name',
                    name: 'consultant_name'
                },
                {
                    data: 'appointment_time',
                    name: 'appointment_time'
                },
                {
                    data: 'hx_status',
                    name: 'hx_status',
                    orderable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [6, 'asc']
            ],
            language: {
                emptyTable: "No appointments found for today",
                zeroRecords: "No matching appointments found"
            }
        });

        console.log('DataTable initialized');


        // Record HX Complaint Button Click
        $(document).on('click', '.record_hx', function() {
            var appointment_id = $(this).data('id');
            $('#hx_id').val(0);
            $('#form_title').text('Record HX Complaint');
            loadAppointmentDetails(appointment_id);
        });

        // View/Edit HX Complaint Button Click
        $(document).on('click', '.view_hx_record', function() {
            var appointment_id = $(this).data('id');
            var hx_id = $(this).data('hx-id');
            $('#hx_id').val(hx_id);
            $('#form_title').text('Edit HX Complaint');
            loadHxComplaintDetails(hx_id);
        });

        // Load Appointment Details
        function loadAppointmentDetails(appointment_id) {
            $.ajax({
                url: "{{ route('hx.get_appointment_details') }}",
                method: 'GET',
                data: {
                    appointment_id: appointment_id
                },
                success: function(response) {
                    if (response.status) {
                        var appointment = response.appointment;
                        var patient = appointment.patient;

                        $('#appointment_id').val(appointment.id);
                        $('#patient_id').val(patient.id);
                        $('#display_mr_no').text(patient.mr_no || '-');
                        $('#display_patient_name').text(patient.name || '-');
                        $('#display_age').text(patient.age || '-');
                        $('#display_gender').text(patient.gender || '-');
                        $('#display_phone').text(patient.phone || '-');
                        $('#display_consultant').text(appointment.consultant ? appointment.consultant.name : '-');
                        $('#display_appointment_time').text(appointment.appointment_time || '-');

                        // If HX complaint exists, load it
                        if (response.hx_complaint) {
                            var hx = response.hx_complaint;
                            $('#hx_id').val(hx.id);
                            $('#bp').val(hx.bp || '');
                            $('#temp').val(hx.temp || '');
                            $('#pulse').val(hx.pulse || '');
                            $('#rr').val(hx.rr || '');
                            $('#complaint').val(hx.complaint || '');
                            $('#investigation').val(hx.investigation || '');
                            $('#form_title').text('Edit HX Complaint');
                        } else {
                            // Clear form for new entry
                            clearVitalSigns();
                        }

                        $('#hx_form').slideDown();
                        $('html, body').animate({
                            scrollTop: $("#hx_form").offset().top - 100
                        }, 500);
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error loading appointment details');
                }
            });
        }

        // Load HX Complaint Details
        function loadHxComplaintDetails(hx_id) {
            $.ajax({
                url: "{{ route('hx.get_hx_complaint') }}",
                method: 'GET',
                data: {
                    hx_id: hx_id
                },
                success: function(response) {
                    if (response.status) {
                        var hx = response.hx_complaint;
                        var appointment = response.appointment;
                        var patient = appointment.patient;

                        $('#appointment_id').val(appointment.id);
                        $('#patient_id').val(patient.id);
                        $('#hx_id').val(hx.id);

                        $('#display_mr_no').text(patient.mr_no || '-');
                        $('#display_patient_name').text(patient.name || '-');
                        $('#display_age').text(patient.age || '-');
                        $('#display_gender').text(patient.gender || '-');
                        $('#display_phone').text(patient.phone || '-');
                        $('#display_consultant').text(appointment.consultant ? appointment.consultant.name : '-');
                        $('#display_appointment_time').text(appointment.appointment_time || '-');

                        $('#bp').val(hx.bp || '');
                        $('#temp').val(hx.temp || '');
                        $('#pulse').val(hx.pulse || '');
                        $('#rr').val(hx.rr || '');
                        $('#complaint').val(hx.complaint || '');
                        $('#investigation').val(hx.investigation || '');

                        $('#hx_form').slideDown();
                        $('html, body').animate({
                            scrollTop: $("#hx_form").offset().top - 100
                        }, 500);
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error loading HX complaint details');
                }
            });
        }

        // Clear Vital Signs Form
        function clearVitalSigns() {
            $('#bp').val('');
            $('#temp').val('');
            $('#pulse').val('');
            $('#rr').val('');
            $('#complaint').val('');
            $('#investigation').val('');
        }

        // Submit HX Complaint Form
        $('#save_hx_form').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('hx.save_hx_complaint') }}",
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        alert(response.message);
                        $('#hx_form').slideUp();
                        appointmentsTable.ajax.reload();
                        clearVitalSigns();
                        $('#hx_id').val(0);
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        var errorMsg = '';
                        $.each(errors, function(key, value) {
                            errorMsg += value[0] + '\n';
                        });
                        alert(errorMsg);
                    } else {
                        alert('Error saving HX complaint');
                    }
                }
            });
        });

        // Close/Cancel Form Buttons
        $('#close_form, #cancel_form').on('click', function() {
            $('#hx_form').slideUp();
            clearVitalSigns();
            $('#hx_id').val(0);
        });
    });
</script>
@endpush