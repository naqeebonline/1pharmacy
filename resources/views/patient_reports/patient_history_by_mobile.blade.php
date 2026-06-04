@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
<style>
    .patient-card {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background: #f9fafb;
    }

    .patient-header {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .info-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.85rem;
        margin-right: 10px;
        background: rgba(255, 255, 255, 0.2);
    }

    .appointment-section {
        background: white;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .investigation-item {
        padding: 8px;
        border-left: 3px solid #3b82f6;
        margin-bottom: 8px;
        background: #f8fafc;
        border-radius: 0 4px 4px 0;
    }

    .section-title {
        font-weight: 600;
        color: #1e40af;
        margin-bottom: 10px;
        font-size: 1rem;
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: #6b7280;
        font-style: italic;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bx bx-search-alt"></i> Patient History Search
                </h5>
            </div>
            <div class="card-body">
                <!-- Search Form -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="search_type" class="form-label">Search By</label>
                        <select id="search_type" class="form-select">
                            <option value="mobile">Mobile Number</option>
                            <option value="mr_number">MR Number</option>
                            <option value="appointment_number">Appointment Number</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="search_value" class="form-label">Search Value</label>
                        <div class="input-group">
                            <span class="input-group-text" id="search_icon"><i class="bx bx-phone"></i></span>
                            <input type="text" id="search_value" class="form-control" placeholder="Enter mobile number (e.g., 03001234567)">
                            <button type="button" id="search_btn" class="btn btn-primary">
                                <i class="bx bx-search"></i> Search
                            </button>
                        </div>
                        <small class="text-muted" id="search_hint">Enter 11 digit mobile number</small>
                    </div>
                </div>

                <!-- Loading Spinner -->
                <div id="loading_spinner" style="display: none;" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Searching patient records...</p>
                </div>

                <!-- Results Container -->
                <div id="results_container" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Search Results</h6>
                        <button type="button" id="print_all_btn" class="btn btn-success btn-sm">
                            <i class="bx bx-printer"></i> Print All Records
                        </button>
                    </div>
                    <div id="patient_results"></div>
                </div>

                <!-- No Results Message -->
                <div id="no_results" style="display: none;" class="no-data">
                    <i class="bx bx-info-circle" style="font-size: 3rem;"></i>
                    <p>No patient records found for this search criteria.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
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
        } else {
            alert('Please allow popups for this site to print the report.');
        }
    }

    $(document).ready(function() {
        let currentSearchValue = '';
        let currentSearchType = 'mobile';

        // Update placeholder and icon based on search type
        $('#search_type').on('change', function() {
            var searchType = $(this).val();
            var icon = $('#search_icon i');
            var input = $('#search_value');
            var hint = $('#search_hint');

            if (searchType === 'mobile') {
                icon.attr('class', 'bx bx-phone');
                input.attr('placeholder', 'Enter mobile number (e.g., 03001234567)');
                hint.text('Enter 11 digit mobile number');
            } else if (searchType === 'mr_number') {
                icon.attr('class', 'bx bx-id-card');
                input.attr('placeholder', 'Enter MR Number (e.g., MR-12345)');
                hint.text('Enter patient MR number');
            } else if (searchType === 'appointment_number') {
                icon.attr('class', 'bx bx-calendar');
                input.attr('placeholder', 'Enter Appointment Number');
                hint.text('Enter appointment number');
            }
        });

        // Search button click
        $('#search_btn').on('click', function() {
            var searchValue = $('#search_value').val().trim();
            var searchType = $('#search_type').val();

            if (searchValue.length < 3) {
                alert('Please enter a valid search value (at least 3 characters)');
                return;
            }

            currentSearchValue = searchValue;
            currentSearchType = searchType;
            searchPatients(searchValue, searchType);
        });

        // Enter key press
        $('#search_value').on('keypress', function(e) {
            if (e.which === 13) {
                $('#search_btn').click();
            }
        });

        // Search patients function
        function searchPatients(searchValue, searchType) {
            $('#loading_spinner').show();
            $('#results_container').hide();
            $('#no_results').hide();

            $.ajax({
                url: '{{ route("patient.search_by_mobile") }}',
                type: 'POST',
                data: {
                    search_value: searchValue,
                    search_type: searchType,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#loading_spinner').hide();

                    if (response.status && response.data.length > 0) {
                        displayResults(response.data);
                        $('#results_container').show();
                    } else {
                        $('#no_results').show();
                    }
                },
                error: function(xhr) {
                    $('#loading_spinner').hide();
                    alert('Error searching patients. Please try again.');
                    console.error(xhr);
                }
            });
        }

        // Display results function
        function displayResults(patients) {
            var html = '';

            $.each(patients, function(index, patient) {
                html += '<div class="patient-card">';
                html += '  <div class="patient-header">';
                html += '    <h6 class="mb-1">';
                html += '      <i class="bx bx-user"></i> ' + patient.name;
                html += '    </h6>';
                html += '    <div>';
                html += '      <span class="info-badge"><i class="bx bx-id-card"></i> MR: ' + (patient.mr_no || 'N/A') + '</span>';
                html += '      <span class="info-badge"><i class="bx bx-phone"></i> ' + patient.contact_no + '</span>';
                html += '      <span class="info-badge"><i class="bx bx-calendar"></i> Age: ' + (patient.age || 'N/A') + '</span>';
                html += '      <span class="info-badge"><i class="bx bx-venus-mars"></i> ' + (patient.gender || 'N/A') + '</span>';
                html += '    </div>';
                html += '  </div>';

                // Appointments Section
                if (patient.appointments && patient.appointments.length > 0) {
                    html += '  <div class="section-title"><i class="bx bx-calendar-check"></i> Appointments (' + patient.appointments.length + ')</div>';

                    $.each(patient.appointments, function(i, appointment) {
                        html += '  <div class="appointment-section">';
                        html += '    <div class="row">';
                        html += '      <div class="col-md-6">';
                        html += '        <strong>Date:</strong> ' + appointment.appointment_date + '<br>';
                        html += '        <strong>Consultant:</strong> ' + (appointment.consultant ? appointment.consultant.name : 'N/A') + '<br>';
                        html += '        <strong>OPD Type:</strong> ' + (appointment.opd_type ? appointment.opd_type.name : 'N/A');
                        html += '      </div>';
                        html += '      <div class="col-md-6 text-end">';
                        html += '        <strong>Fee:</strong> Rs. ' + (appointment.fee || '0') + '<br>';
                        html += '        <a href="javascript:void(0);" onclick="openPrintPopup(\'{{ url("print_appointment") }}/' + appointment.id + '\')" class="btn btn-sm btn-info mt-2">';
                        html += '          <i class="bx bx-printer"></i> Print Slip';
                        html += '        </a>';
                        html += '        <a href="javascript:void(0);" onclick="openPrintPopup(\'{{ url("print_e_prescription") }}/' + appointment.id + '\')" class="btn btn-sm btn-primary mt-2">';
                        html += '          <i class="bx bx-file"></i> E-Prescription';
                        html += '        </a>';
                        html += '      </div>';
                        html += '    </div>';

                        // Investigations for this appointment
                        if (appointment.investigations && appointment.investigations.length > 0) {
                            html += '    <div class="mt-3">';
                            html += '      <div class="section-title" style="font-size: 0.9rem;"><i class="bx bx-test-tube"></i> Investigations (' + appointment.investigations.length + ')</div>';

                            $.each(appointment.investigations, function(j, inv) {
                                html += '      <div class="investigation-item">';
                                html += '        <div class="row align-items-center">';
                                html += '          <div class="col-md-8">';
                                html += '            <strong>' + (inv.investigation ? inv.investigation.name : 'N/A') + '</strong><br>';
                                html += '            <small class="text-muted">Date: ' + (inv.created_at || 'N/A') + '</small>';
                                html += '          </div>';
                                html += '          <div class="col-md-4 text-end">';
                                html += '            <a href="javascript:void(0);" onclick="openPrintPopup(\'{{ url("print_inv_result") }}/' + inv.id + '\')" class="btn btn-sm btn-success">';
                                html += '              <i class="bx bx-printer"></i> Print Result';
                                html += '            </a>';
                                html += '          </div>';
                                html += '        </div>';
                                html += '      </div>';
                            });

                            html += '    </div>';
                        }

                        html += '  </div>';
                    });
                } else {
                    html += '  <div class="alert alert-info">No appointments found for this patient.</div>';
                }

                html += '</div>';
            });

            $('#patient_results').html(html);
        }

        // Print all records
        $('#print_all_btn').on('click', function() {
            if (currentSearchValue && currentSearchType) {
                var url = '{{ route("patient.print_history_by_mobile") }}?search_value=' + currentSearchValue + '&search_type=' + currentSearchType;
                openPrintPopup(url);
            }
        });
    });
</script>
@endpush