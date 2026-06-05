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





            <div class="card my-5">
                <div class="card-body">
                    <h5 class="card-title">Admitted Patients List</h5>

                    <div class="table-responsive" style="min-height: 200px">

                        <table id="patient-list" class="table table-responsive table-striped data_mf_table table-condensed">
                            <thead>
                                <tr>
                                    <th >Patient MR no.</th>
                                    <th >Sehat Card Ref#</th>
                                    <th >Patient Name</th>
                                    <th >Guardian Name</th>
                                    <th >Consultant</th>
                                    <th >Procedure Type</th>
                                    <th >Ward Name</th>
                                    <th >Bed no.</th>

                                    <th >Admission Date</th>
                                    <th>Status</th>
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

        setTimeout(function() {

        }, 1000);
        user_table = $('#patient-list').DataTable({
            processing: true,
            serverSide: true,

            pageLength: 10,
            ajax: {
                url: "{{ route('pos.list_admitted_patients') }}",


            },

            columns: [
                
                {
                    data: 'patient.mr_no',
                    name: 'patient.mr_no',
                    searchable: true
                },
                {
                    data: 'sc_ref_no',
                    name: 'sc_ref_no',
                    searchable: true
                },
                {
                    data: 'patient.name',
                    name: 'patient.name',
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
                    data: 'bed.name',
                    name: 'bed.name',
                    searchable: true
                },



                {
                    data: 'admission_date',
                    name: 'admission_date',
                    searchable: true
                },
           
                {
                    data: 'admission_status',
                    name: 'admission_status',
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
        });



        
        
    </script>
@endpush
