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


            {{-- LISTIN PATIENTS --}}
            <div class="card my-5">
                <div class="card-body">
                    <h5 class="card-title">{{$title}}</h5>

                    <div class="table-responsive" style="min-height: 200px">

                        <table id="patient-list"
                            class="table table-responsive table-striped data_mf_table table-condensed">
                            <thead>
                                <tr>

                                    <th>Patient Name</th>
                                    <th>Sehat Card Refrence No</th>

                                    <th>Guardian Name</th>
                                    <th>Consultant</th>
                                    <th>Procedure Type</th>
                                    <th>Ward Name</th>
                                    <th>Cancelation reason</th>

                                    <th>Admission Date</th>
                                    <th>Cancelation Date</th>
                                    <th>Status</th>
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


        user_table = $('#patient-list').DataTable({
            processing: true,
            serverSide: true,

            pageLength: 10,
            ajax: {
                url: "{{ route('pos.canceled_patient_list') }}",


            },

            columns: [


                {
                    data: 'patient.name',
                    name: 'patient.name',
                    searchable: true
                },
                 {
                    data: 'sc_ref_no',
                    name: 'sc_ref_no',
                    searchable: true
                },

                {
                    data: 'guardian_name',
                    name: 'guardian_name',
                    searchable: true
                },
                {
                    data: 'consultant.name',
                    name: 'consultant.name',
                    searchable: true
                },
                {
                    data: 'procedure_type.name',
                    name: 'procedure_type.name',
                    searchable: true
                },
                {
                    data: 'ward.name',
                    name: 'ward.name',
                    searchable: true
                },
                {
                    data: 'canelation_reason_text',
                    name: 'canelation_reason_text',
                    searchable: true
                },



                {
                    data: 'admission_date',
                    name: 'admission_date',
                    searchable: true
                },
                {
                    data: 'discharge_date',
                    name: 'discharge_date',
                    searchable: true
                },
                {
                    data: 'admission_status',
                    name: 'admission_status',
                    searchable: true
                },

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

    </script>
@endpush
