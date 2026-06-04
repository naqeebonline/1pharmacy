@extends('layouts.'.config('settings.active_layout'))
@php
$app_id = config('settings.app_id');
$brandName = config('settings.company_title', config('app.name', 'HMIS'));
$brandSubtitle = 'Appointment Summary';
$brandPhone = config('settings.company_contact_number', config('settings.company_phone', ''));
$brandEmail = config('settings.company_email', config('mail.from.address'));
$brandAddress = config('settings.company_address', '');
$filtersForPrint = [
'Date Range' => $date_range_label,
'Consultant' => $selected_consultant ? ($consultants->firstWhere('id', $selected_consultant)->name ?? 'Unknown Consultant') : 'All Consultants',
'OPD Type' => $selected_opd_type ? ($opd_types->firstWhere('id', $selected_opd_type)->name ?? 'Unknown OPD Type') : 'All OPD Types'
];
@endphp

@push('stylesheets')
<style>
    .appointment-dashboard {
        padding-bottom: 1.5rem;
    }

    .appointment-dashboard .dashboard-title {
        font-weight: 600;
    }

    .appointment-dashboard .dashboard-subtitle {
        max-width: 640px;
    }

    .appointment-dashboard .filter-card {
        border-radius: 1rem;
    }

    .appointment-dashboard .filters .form-label {
        font-weight: 600;
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: var(--bs-secondary-color, #6c757d);
    }

    .appointment-dashboard .filters .form-control,
    .appointment-dashboard .filters .form-select {
        border-radius: .75rem;
    }

    .appointment-dashboard .stat-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 .75rem 2rem rgba(15, 23, 42, 0.08);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .appointment-dashboard .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 .5rem 2.5rem rgba(15, 23, 42, 0.12);
    }

    .appointment-dashboard .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: .85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        background: linear-gradient(135deg, #7367f0 0%, #63a2ff 100%);
        color: #fff;
        box-shadow: 0 12px 24px rgba(115, 103, 240, 0.28);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .appointment-dashboard .stat-card:hover .stat-icon {
        transform: translateY(-2px);
        box-shadow: 0 16px 28px rgba(115, 103, 240, 0.32);
    }

    .appointment-dashboard .stat-card[data-variant="primary"] .stat-icon {
        background: linear-gradient(135deg, #5a8dee 0%, #8a7dff 100%);
        box-shadow: 0 12px 24px rgba(90, 141, 238, 0.28);
    }

    .appointment-dashboard .stat-card[data-variant="success"] .stat-icon {
        background: linear-gradient(135deg, #28c76f 0%, #81fbb8 100%);
        box-shadow: 0 12px 24px rgba(40, 199, 111, 0.28);
        color: #fff;
    }

    .appointment-dashboard .stat-card[data-variant="info"] .stat-icon {
        background: linear-gradient(135deg, #00cfe8 0%, #4f9efc 100%);
        box-shadow: 0 12px 24px rgba(0, 207, 232, 0.28);
        color: #fff;
    }

    .appointment-dashboard .stat-card[data-variant="warning"] .stat-icon {
        background: linear-gradient(135deg, #ff9f43 0%, #ffd76f 100%);
        box-shadow: 0 12px 24px rgba(255, 159, 67, 0.28);
        color: #4a1a00;
    }

    .appointment-dashboard .stat-card[data-variant="danger"] .stat-icon {
        background: linear-gradient(135deg, #ff5b5c 0%, #ff8f8f 100%);
        box-shadow: 0 12px 24px rgba(255, 91, 92, 0.28);
        color: #fff;
    }

    .appointment-dashboard .stat-value {
        font-size: 0.9rem;
        font-weight: 700;
    }

    .appointment-dashboard .stat-label {
        font-size: .875rem;
        color: var(--bs-secondary-color, #6c757d);
    }

    .appointment-dashboard .summary-label {
        text-transform: uppercase;
        font-size: .75rem;
        font-weight: 600;
        color: var(--bs-secondary-color, #6c757d);
        letter-spacing: .05em;
    }

    .appointment-dashboard .chart-area {
        min-height: 340px;
    }

    @media (max-width: 575.98px) {
        .appointment-dashboard {
            padding-inline: 1rem;
        }

        .appointment-dashboard .chart-area {
            min-height: 260px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 appointment-dashboard px-3 px-md-4 py-4">
    <div class="row">
        <div>
            <h4 class="dashboard-title mb-1 " style="text-align: center;">Appointment Analytics Dashboard</h4>
        </div>
    </div>

    <div class="card filter-card border-0 shadow-sm mb-4">
        <div class="card-body filters">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-sm-6 col-lg-3 col-xl-2">
                    <label for="from_date" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $from_date }}">
                </div>
                <div class="col-12 col-sm-6 col-lg-3 col-xl-2">
                    <label for="to_date" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $to_date }}">
                </div>
                <div class="col-12 col-sm-6 col-lg-3 col-xl-3">
                    <label for="consultant_id" class="form-label">Consultant</label>
                    <select class="form-select" id="consultant_id" name="consultant_id">
                        <option value="">All Consultants</option>
                        @foreach($consultants as $consultant)
                        <option value="{{ $consultant->id }}" {{ $selected_consultant == $consultant->id ? 'selected' : '' }}>{{ $consultant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 col-xl-3">
                    <label for="opd_type_id" class="form-label">OPD Type</label>
                    <select class="form-select" id="opd_type_id" name="opd_type_id">
                        <option value="">All OPD Types</option>
                        @foreach($opd_types as $opd)
                        <option value="{{ $opd->id }}" {{ $selected_opd_type == $opd->id ? 'selected' : '' }}>{{ $opd->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-6 col-xl-2 d-grid">
                    <button type="button" class="btn btn-primary" id="search_dashboard">
                        <i class="bx bx-refresh me-1"></i>
                        Update View
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100" data-variant="success">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon">
                        <i class="bx bx-calendar"></i>
                    </span>
                    <div>
                        <div class="stat-value">{{ $total_appointments }}</div>
                        <div class="stat-label">Total Appointments</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100" data-variant="primary">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon">
                        <i class="bx bx-calculator"></i>
                    </span>
                    <div>
                        <div class="stat-value">{{ number_format($total_amount, 2) }}</div>
                        <div class="stat-label">Total Amount</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100" data-variant="info">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon">
                        <i class="bx bx-building"></i>
                    </span>
                    <div>
                        <div class="stat-value">{{ number_format($total_hospital_share, 2) }}</div>
                        <div class="stat-label">Hospital Share</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100" data-variant="warning">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon">
                        <i class="bx bx-user"></i>
                    </span>
                    <div>
                        <div class="stat-value">{{ number_format($total_consultant_share, 2) }}</div>
                        <div class="stat-label">Consultant Share</div>
                    </div>
                </div>
            </div>
        </div>

        @foreach($opd_type_counts as $row)
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100" data-variant="info">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon">
                        <i class="bx bx-clinic"></i>
                    </span>
                    <div>
                        <div class="stat-value">{{ $row->total }}</div>
                        <div class="stat-label">{{ $opd_types->firstWhere('id', $row->opd_type_id)->name ?? $row->opd_type_id }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>



    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-0 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 flex-wrap">

            <div class="d-flex align-items-center gap-2 flex-wrap">

                <button type="button" class="btn btn-outline-primary btn-sm" id="view-doctor-summary">
                    <i class="bx bx-user-voice me-1"></i> View Doctor Summary
                </button>
                <span class="text-muted small">{{ $date_range_label }}</span>
            </div>
        </div>
        <div class="card-body">
            <div id="daily-appointments-chart" class="chart-area"></div>
        </div>
    </div>

</div>
<div class="modal fade" id="dayAppointmentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <div>
                    <h5 class="modal-title mb-0">Appointments on <span class="day-appointments-date"></span></h5>
                    <small class="text-muted day-appointments-count"></small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="day-appointments-print">
                        <i class="bx bx-printer me-1"></i> Print List
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="day-appointments-loading text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-3 mb-0">Loading appointments...</p>
                </div>
                <div class="day-appointments-empty text-center py-5 d-none">
                    <i class="bx bx-calendar-x display-5 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">No appointments found for the selected day.</p>
                </div>
                <div class="day-appointments-table-wrapper d-none">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th>Patient</th>
                                    <th>Consultant</th>
                                    <th>OPD Type</th>
                                    <th>Time</th>
                                    <th>Created By</th>
                                </tr>
                            </thead>
                            <tbody id="day-appointments-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="doctorSummaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <div>
                    <h5 class="modal-title mb-0">Doctors Appointment Summary</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="doctor-summary-print">
                        <i class="bx bx-printer me-1"></i> Print Summary
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="doctor-summary-empty text-center py-5 text-muted">No appointments found for the selected filters.</div>
                <div class="doctor-summary-table-wrapper d-none">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 10%;">Rank</th>
                                    <th>Consultant</th>
                                    <th style="width: 25%;" class="text-end">Appointments</th>
                                </tr>
                            </thead>
                            <tbody id="doctor-summary-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script id="apexcharts-local" src="{{ asset('assets/vendors/js/charts/apexcharts.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var searchButton = document.getElementById('search_dashboard');
        if (searchButton) {
            searchButton.addEventListener('click', function() {
                var fromDate = document.getElementById('from_date').value;
                var toDate = document.getElementById('to_date').value;
                var consultantId = document.getElementById('consultant_id').value;
                var opdTypeId = document.getElementById('opd_type_id').value;

                var url = `{{ route('appointment.dashboard') }}?from_date=${fromDate}&to_date=${toDate}`;
                if (consultantId) {
                    url += `&consultant_id=${consultantId}`;
                }
                if (opdTypeId) {
                    url += `&opd_type_id=${opdTypeId}`;
                }

                window.location.href = url;
            });
        }

        var chartEl = document.querySelector('#daily-appointments-chart');
        if (!chartEl) {
            return;
        }

        var dailyLabels = @json($daily_appointment_labels);
        var dailyCounts = @json($daily_appointment_totals);
        var dailyDates = @json($daily_appointment_dates);
        var dayDetailsEndpoint = "{{ route('appointment.dashboard.day') }}";
        var selectedConsultant = @json($selected_consultant);
        var selectedOpdType = @json($selected_opd_type);
        var doctorSummaryData = @json($doctor_summary);
        var brandInfo = {
            name: @json($brandName),
            subtitle: @json($brandSubtitle),
            phone: @json($brandPhone),
            email: @json($brandEmail),
            address: @json($brandAddress)
        };
        var filtersForPrint = @json($filtersForPrint);

        var modalEl = document.getElementById('dayAppointmentsModal');
        var modalInstance = null;
        if (modalEl) {
            if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
                modalInstance = new bootstrap.Modal(modalEl);
            } else if (window.jQuery && typeof window.jQuery(modalEl).modal === 'function') {
                modalInstance = {
                    show: function() {
                        window.jQuery(modalEl).modal('show');
                    },
                    hide: function() {
                        window.jQuery(modalEl).modal('hide');
                    }
                };
            }
        }

        var modalPrintBtn = modalEl ? modalEl.querySelector('#day-appointments-print') : null;
        var modalDateEl = modalEl ? modalEl.querySelector('.day-appointments-date') : null;
        var modalCountEl = modalEl ? modalEl.querySelector('.day-appointments-count') : null;
        var modalTableBody = modalEl ? modalEl.querySelector('#day-appointments-body') : null;
        var modalTableWrapper = modalEl ? modalEl.querySelector('.day-appointments-table-wrapper') : null;
        var modalLoadingState = modalEl ? modalEl.querySelector('.day-appointments-loading') : null;
        var modalEmptyState = modalEl ? modalEl.querySelector('.day-appointments-empty') : null;
        var defaultEmptyMessage = modalEmptyState ? modalEmptyState.innerHTML : '';
        if (modalPrintBtn) {
            modalPrintBtn.disabled = true;
        }

        function setModalState(state) {
            if (!modalEl) {
                return;
            }
            if (modalLoadingState) {
                modalLoadingState.classList.toggle('d-none', state !== 'loading');
            }
            if (modalTableWrapper) {
                modalTableWrapper.classList.toggle('d-none', state !== 'data');
            }
            if (modalEmptyState) {
                if (state === 'empty') {
                    modalEmptyState.classList.remove('d-none');
                } else {
                    modalEmptyState.classList.add('d-none');
                    modalEmptyState.innerHTML = defaultEmptyMessage;
                }
            }
            if (modalPrintBtn) {
                modalPrintBtn.disabled = state !== 'data';
            }
        }

        var doctorSummaryBtn = document.getElementById('view-doctor-summary');
        var doctorSummaryModalEl = document.getElementById('doctorSummaryModal');
        var doctorSummaryModal = null;
        if (doctorSummaryModalEl) {
            if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
                doctorSummaryModal = new bootstrap.Modal(doctorSummaryModalEl);
            } else if (window.jQuery && typeof window.jQuery(doctorSummaryModalEl).modal === 'function') {
                doctorSummaryModal = {
                    show: function() {
                        window.jQuery(doctorSummaryModalEl).modal('show');
                    },
                    hide: function() {
                        window.jQuery(doctorSummaryModalEl).modal('hide');
                    }
                };
            }
        }

        var doctorSummaryPrintBtn = doctorSummaryModalEl ? doctorSummaryModalEl.querySelector('#doctor-summary-print') : null;
        var doctorSummaryBody = doctorSummaryModalEl ? doctorSummaryModalEl.querySelector('#doctor-summary-body') : null;
        var doctorSummaryWrapper = doctorSummaryModalEl ? doctorSummaryModalEl.querySelector('.doctor-summary-table-wrapper') : null;
        var doctorSummaryEmpty = doctorSummaryModalEl ? doctorSummaryModalEl.querySelector('.doctor-summary-empty') : null;
        var doctorSummaryRendered = false;

        function renderDoctorSummaryTable() {
            if (!doctorSummaryBody) {
                return;
            }
            doctorSummaryBody.innerHTML = '';

            var fragment = document.createDocumentFragment();
            doctorSummaryData.forEach(function(item, index) {
                var row = document.createElement('tr');
                row.innerHTML = '<td>' + (index + 1) + '</td>' +
                    '<td>' + (item.consultant_name || 'Unknown Consultant') + '</td>' +
                    '<td class="text-end fw-semibold">' + item.total + '</td>';
                fragment.appendChild(row);
            });

            doctorSummaryBody.appendChild(fragment);
            doctorSummaryRendered = true;
        }

        if (doctorSummaryBtn) {
            doctorSummaryBtn.addEventListener('click', function() {
                if (!doctorSummaryModal) {
                    return;
                }

                if (!doctorSummaryData || !doctorSummaryData.length) {
                    if (doctorSummaryEmpty) {
                        doctorSummaryEmpty.classList.remove('d-none');
                    }
                    if (doctorSummaryWrapper) {
                        doctorSummaryWrapper.classList.add('d-none');
                    }
                    if (doctorSummaryPrintBtn) {
                        doctorSummaryPrintBtn.disabled = true;
                    }
                } else {
                    if (doctorSummaryEmpty) {
                        doctorSummaryEmpty.classList.add('d-none');
                    }
                    if (doctorSummaryWrapper) {
                        doctorSummaryWrapper.classList.remove('d-none');
                    }

                    if (!doctorSummaryRendered) {
                        renderDoctorSummaryTable();
                    }
                    if (doctorSummaryPrintBtn) {
                        doctorSummaryPrintBtn.disabled = false;
                    }
                }

                doctorSummaryModal.show();
            });
        }

        if (doctorSummaryPrintBtn) {
            doctorSummaryPrintBtn.disabled = !(doctorSummaryData && doctorSummaryData.length);
            doctorSummaryPrintBtn.addEventListener('click', function() {
                if (!doctorSummaryData || !doctorSummaryData.length) {
                    return;
                }

                if (!doctorSummaryRendered) {
                    renderDoctorSummaryTable();
                }

                printDoctorSummary();
            });
        }

        function printDoctorSummary() {
            if (!doctorSummaryData || !doctorSummaryData.length) {
                return;
            }

            var printWindow = window.open('', '_blank', 'width=1000,height=720');
            if (!printWindow) {
                return;
            }

            var title = 'Doctors Appointment Summary';

            var contactLines = [];
            if (brandInfo.phone) {
                contactLines.push('Phone: ' + brandInfo.phone);
            }
            if (brandInfo.email) {
                contactLines.push('Email: ' + brandInfo.email);
            }
            if (brandInfo.address) {
                contactLines.push(brandInfo.address);
            }

            var filterPieces = [];
            if (filtersForPrint) {
                Object.keys(filtersForPrint).forEach(function(key) {
                    var value = filtersForPrint[key];
                    var displayValue = '';
                    if (Array.isArray(value)) {
                        var filtered = value.filter(Boolean);
                        displayValue = filtered.length ? filtered.join(' to ') : '';
                    } else {
                        displayValue = value || '';
                    }
                    if (!displayValue) {
                        displayValue = 'All';
                    }
                    filterPieces.push('<div class="filter-item"><span class="label">' + key + ':</span><span class="value">' + displayValue + '</span></div>');
                });
            }

            var rowsHtml = '';
            doctorSummaryData.forEach(function(item, index) {
                rowsHtml += '<tr><td>' + (index + 1) + '</td><td>' + (item.consultant_name || 'Unknown Consultant') + '</td><td class="text-end fw-semibold">' + item.total + '</td></tr>';
            });

            var doc = printWindow.document;
            doc.open();
            doc.write('<!DOCTYPE html>');
            doc.write('<html><head><title>' + title + '</title>');
            doc.write('<style>' +
                'body{font-family:Arial,Helvetica,sans-serif;margin:28px;color:#1f2d3d;}' +
                'h1{font-size:24px;margin:0;}' +
                'h2{font-size:16px;margin:6px 0 0 0;color:#50607d;text-transform:uppercase;letter-spacing:0.08em;}' +
                'p{margin:6px 0 0 0;color:#6c757d;}' +
                'table{width:100%;border-collapse:collapse;margin-top:18px;}' +
                'th,td{border:1px solid #d9dde5;padding:8px 10px;font-size:13px;text-align:left;}' +
                'th{background:#f5f7fa;font-weight:600;text-transform:uppercase;font-size:12px;letter-spacing:0.04em;}' +
                'tbody tr:nth-child(even){background:#fafbff;}' +
                '.brand-header{border-bottom:2px solid #1f2d3d;padding-bottom:12px;margin-bottom:18px;text-align:center;}' +
                '.brand-header .contact{margin-top:10px;font-size:12px;color:#4c5d75;}' +
                '.brand-header .contact span{display:inline-block;margin:0 6px;}' +
                '.filters{display:flex;flex-wrap:wrap;justify-content:center;gap:8px;border-top:1px solid #d9dde5;border-bottom:1px solid #d9dde5;padding:10px 0;margin-top:14px;}' +
                '.filters .filter-item{font-size:12px;color:#4c5d75;padding:0 10px;border-left:1px solid #d9dde5;}' +
                '.filters .filter-item:first-child{border-left:none;}' +
                '.filters .filter-item .label{font-weight:600;margin-right:6px;}' +
                '.print-hint{margin-top:12px;font-size:12px;color:#6c757d;text-align:right;}' +
                '@media print{.table-responsive{overflow:visible;}}' +
                '</style>');
            doc.write('</head><body>');
            doc.write('<div class="brand-header">');
            doc.write('<h6>' + (brandInfo.name || 'Hospital Management System') + '</h6>');
            doc.write('<h2>Doctors Appointment Summary</h2>');
            if (contactLines.length) {
                doc.write('<div class="contact">' + contactLines.map(function(item) {
                    return '<span>' + item + '</span>';
                }).join('') + '</div>');
            }
            doc.write('</div>');
            if (filterPieces.length) {
                doc.write('<div class="filters">' + filterPieces.join('') + '</div>');
            }
            doc.write('<div class="print-hint">Use Ctrl+P to print this page.</div>');
            doc.write('<table><thead><tr><th style="width:10%;">Rank</th><th>Consultant</th><th style="width:25%;" class="text-end">Appointments</th></tr></thead><tbody>' + rowsHtml + '</tbody></table>');
            doc.write('</body></html>');
            doc.close();

            try {
                printWindow.focus();
            } catch (error) {
                console.error('Unable to focus print window', error);
            }
        }

        function printDayAppointments() {
            if (modalPrintBtn && modalPrintBtn.disabled) {
                return;
            }
            if (!modalTableWrapper || modalTableWrapper.classList.contains('d-none')) {
                return;
            }
            if (modalTableBody && modalTableBody.children.length === 0) {
                return;
            }

            var printWindow = window.open('', '_blank', 'width=1000,height=720');
            if (!printWindow) {
                return;
            }

            var title = modalDateEl ? modalDateEl.textContent : 'Appointments';
            var countText = modalCountEl ? modalCountEl.textContent : '';
            var tableHtml = modalTableWrapper.innerHTML;

            var contactLines = [];
            if (brandInfo.phone) {
                contactLines.push('Phone: ' + brandInfo.phone);
            }
            if (brandInfo.email) {
                contactLines.push('Email: ' + brandInfo.email);
            }
            if (brandInfo.address) {
                contactLines.push(brandInfo.address);
            }

            var filterPieces = [];
            if (filtersForPrint) {
                Object.keys(filtersForPrint).forEach(function(key) {
                    var value = filtersForPrint[key];
                    var displayValue = '';
                    if (Array.isArray(value)) {
                        var filtered = value.filter(Boolean);
                        displayValue = filtered.length ? filtered.join(' to ') : '';
                    } else {
                        displayValue = value || '';
                    }
                    if (!displayValue) {
                        displayValue = 'All';
                    }
                    filterPieces.push('<div class="filter-item"><span class="label">' + key + ':</span><span class="value">' + displayValue + '</span></div>');
                });
            }

            var doc = printWindow.document;
            doc.open();
            doc.write('<!DOCTYPE html>');
            doc.write('<html><head><title>' + title + '</title>');
            doc.write('<style>' +
                'body{font-family:Arial,Helvetica,sans-serif;margin:28px;color:#1f2d3d;}' +
                'h1{font-size:24px;margin:0;}' +
                'h2{font-size:16px;margin:6px 0 0 0;color:#50607d;text-transform:uppercase;letter-spacing:0.08em;}' +
                'p{margin:6px 0 0 0;color:#6c757d;}' +
                'table{width:100%;border-collapse:collapse;margin-top:18px;}' +
                'th,td{border:1px solid #d9dde5;padding:8px 10px;font-size:13px;text-align:left;}' +
                'th{background:#f5f7fa;font-weight:600;text-transform:uppercase;font-size:12px;letter-spacing:0.04em;}' +
                'tbody tr:nth-child(even){background:#fafbff;}' +
                '.brand-header{border-bottom:2px solid #1f2d3d;padding-bottom:12px;margin-bottom:18px;text-align:center;}' +
                '.brand-header .contact{margin-top:10px;font-size:12px;color:#4c5d75;}' +
                '.brand-header .contact span{display:inline-block;margin:0 6px;}' +
                '.filters{display:flex;flex-wrap:wrap;justify-content:center;gap:8px;border-top:1px solid #d9dde5;border-bottom:1px solid #d9dde5;padding:10px 0;margin-top:14px;}' +
                '.filters .filter-item{font-size:12px;color:#4c5d75;padding:0 10px;border-left:1px solid #d9dde5;}' +
                '.filters .filter-item:first-child{border-left:none;}' +
                '.filters .filter-item .label{font-weight:600;margin-right:6px;}' +
                '.print-hint{margin-top:12px;font-size:12px;color:#6c757d;text-align:right;}' +
                '@media print{.table-responsive{overflow:visible;}}' +
                '</style>');
            doc.write('</head><body>');
            doc.write('<div class="brand-header">');
            doc.write('<h1>' + (brandInfo.name || 'Hospital Management System') + '</h1>');
            doc.write('<h2>' + title + '</h2>');
            if (contactLines.length) {
                doc.write('<div class="contact">' + contactLines.map(function(item) {
                    return '<span>' + item + '</span>';
                }).join('') + '</div>');
            }
            doc.write('</div>');
            if (filterPieces.length) {
                doc.write('<div class="filters">' + filterPieces.join('') + '</div>');
            }
            doc.write('<div class="print-hint">Use Ctrl+P to print this page.</div>');
            if (countText) {
                doc.write('<p style="margin-top:18px; font-weight:600;">' + countText + '</p>');
            }
            doc.write(tableHtml);
            doc.write('</body></html>');
            doc.close();

            var focusWindow = function() {
                try {
                    printWindow.focus();
                } catch (e) {
                    console.error('Unable to focus print window', e);
                }
            };

            if (printWindow.document.readyState === 'complete') {
                focusWindow();
            } else {
                printWindow.onload = focusWindow;
            }

        }

        function openDayAppointmentsModal(dateString, label) {
            if (!modalInstance) {
                return;
            }

            if (modalDateEl) {
                modalDateEl.textContent = label;
            }
            if (modalCountEl) {
                modalCountEl.textContent = '';
            }
            if (modalTableBody) {
                modalTableBody.innerHTML = '';
            }
            if (modalEmptyState) {
                modalEmptyState.innerHTML = defaultEmptyMessage;
            }

            setModalState('loading');
            modalInstance.show();
            if (modalPrintBtn) {
                modalPrintBtn.disabled = true;
            }

            var params = new URLSearchParams({
                date: dateString
            });
            if (selectedConsultant) {
                params.append('consultant_id', selectedConsultant);
            }
            if (selectedOpdType) {
                params.append('opd_type_id', selectedOpdType);
            }

            fetch(dayDetailsEndpoint + '?' + params.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(function(data) {
                    if (modalDateEl && data.date_label) {
                        modalDateEl.textContent = data.date_label;
                    }
                    if (modalCountEl) {
                        if (data.total) {
                            modalCountEl.textContent = data.total + (data.total === 1 ? ' appointment' : ' appointments');
                        } else {
                            modalCountEl.textContent = 'No appointments';
                        }
                    }

                    if (!data.appointments || !data.appointments.length) {
                        setModalState('empty');
                        return;
                    }

                    setModalState('data');

                    if (modalTableBody) {
                        var fragment = document.createDocumentFragment();

                        data.appointments.forEach(function(item, index) {
                            var row = document.createElement('tr');
                            var patientCell = item.patient_name || 'N/A';
                            if (item.mr_no) {
                                patientCell += '<div class="text-muted small">MR# ' + item.mr_no + '</div>';
                            }


                            row.innerHTML = '<td>' + (index + 1) + '</td>' +
                                '<td>' + patientCell + '</td>' +
                                '<td>' + (item.consultant || '-') + '</td>' +
                                '<td>' + (item.opd_type || '-') + '</td>' +
                                '<td>' + (item.appointment_time || '-') + '</td>' +
                                '<td>' + (item.created_by || '-') + '</td>';

                            fragment.appendChild(row);
                        });

                        modalTableBody.appendChild(fragment);
                        if (modalPrintBtn) {
                            modalPrintBtn.disabled = false;
                        }
                    }
                })
                .catch(function() {
                    if (modalEmptyState) {
                        modalEmptyState.innerHTML = '<i class="bx bx-error-circle display-6 text-danger"></i><p class="text-danger mt-2 mb-0">Unable to fetch appointments for the selected day.</p>';
                    }
                    setModalState('empty');
                });
        }

        if (modalPrintBtn) {
            modalPrintBtn.addEventListener('click', function() {
                printDayAppointments();
            });
        }

        function renderChart() {
            if (typeof window.ApexCharts === 'undefined') {
                chartEl.innerHTML = '<div class="text-center text-muted py-5">Unable to load chart library.</div>';
                return;
            }

            var options = {
                chart: {
                    type: 'bar',
                    height: 360,
                    toolbar: {
                        show: false
                    },
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            if (!modalInstance || !dailyDates || !dailyDates.length) {
                                return;
                            }
                            var index = config.dataPointIndex;
                            var selectedDate = dailyDates[index];
                            if (!selectedDate) {
                                return;
                            }
                            var label = dailyLabels && dailyLabels[index] ? dailyLabels[index] : selectedDate;
                            openDayAppointmentsModal(selectedDate, label);
                        }
                    }
                },
                series: [{
                    name: 'Appointments',
                    data: dailyCounts
                }],
                plotOptions: {
                    bar: {
                        columnWidth: '52%',
                        borderRadius: 8
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val;
                    },
                    offsetY: -16,
                    style: {
                        fontSize: '12px',
                        colors: ['#304758']
                    }
                },
                xaxis: {
                    categories: dailyLabels,
                    labels: {
                        rotate: -30,
                        trim: false,
                        style: {
                            fontSize: '12px'
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    min: 0,
                    forceNiceScale: true,
                    labels: {
                        formatter: function(val) {
                            return Math.floor(val);
                        }
                    }
                },
                colors: ['#5A8DEE'],
                grid: {
                    strokeDashArray: 4,
                    borderColor: '#e4e6ef'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + ' appointments';
                        }
                    }
                }
            };

            var chart = new ApexCharts(chartEl, options);
            chart.render();
        }

        if (typeof window.ApexCharts !== 'undefined') {
            renderChart();
            return;
        }

        var fallback = document.createElement('script');
        fallback.src = 'https://cdn.jsdelivr.net/npm/apexcharts';
        fallback.onload = renderChart;
        fallback.onerror = function() {
            chartEl.innerHTML = '<div class="text-center text-muted py-5">Unable to load chart library.</div>';
        };
        document.head.appendChild(fallback);
    });
</script>

@endpush