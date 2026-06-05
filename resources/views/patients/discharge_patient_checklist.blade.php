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

            <div class="row ">

                <!-- Right Block: Form Inputs -->
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <table class="table table-responsive table-bordered">
                                    <tr>
                                        <td style="font-weight: bold">MRNO</td>
                                        <td>{{ $data->patient->mr_no ?? '' }}</td>
                                        <td style="font-weight: bold">Name</td>
                                        <td>{{ $data->patient->name ?? '' }}</td>
                                        <td style="font-weight: bold">Father Name</td>
                                        <td>{{ $data->patient->father_husband_name ?? '' }}</td>
                                        <td style="font-weight: bold">Contact Number</td>
                                        <td>{{ $data->patient->contact_no ?? "" }}</td>
                                    </tr>

                                    <tr>
                                        <td style="font-weight: bold">Ward No</td>
                                        <td>{{ $data->ward->name ?? '' }}</td>
                                        <td style="font-weight: bold">Bed No</td>
                                        <td>{{ $data->bed->name ?? '' }}</td>
                                        <td style="font-weight: bold">Admission Date</td>
                                        <td>{{ $data->admission_date ?? '' }}</td>
                                        <td style="font-weight: bold"></td>
                                        <td></td>
                                    </tr>

                                </table>

                            </div>
                        </div>
                    </div>




                </div>
            </div>


            <div class="card">
                <div class="card-body">
                    <h5 style="text-align: center">Checklist</h5>
                    <form class=" form-submit-event" method="post" enctype="multipart/form-data" action="{{route('pos.save_discharge_checklist')}}" id="save_discharge_checklist">
                        @csrf
                        <div class="row">
                            <input type="hidden" required id="id" name="id" value="{{$checklist ? $checklist->id : 0}}" class="form-control" />
                            <input type="hidden" required id="admission_id" name="admission_id" value="{{$admission_id}}" class="form-control" />
                            <input type="hidden" required id="patient_id" name="patient_id" value="{{$data->patient_id}}" class="form-control" />

                            <div class="col-md-3 mb-3">
                                <label for="nameBasic"  class="form-label">Sehat Card Reference Number<span class="asterisk" style="color: red"> (must be 8 digits)</span></label>
                                <input type="text" class="form-control" pattern="\d{8,}"  style="font-weight: bold; color:red" required name="sc_ref_no" value="{{ $data->sc_ref_no ?? '' }}">



                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="nameBasic" class="form-label">Discharge Type<span class="asterisk">*</span></label>
                                <select class="form-select" required id="admission_status" name="admission_status">
                                    <option value="">Select Discharge Type...</option>
                                    <option value="Discharged" {{ ($data->admission_status == 'Discharged') ? "selected" : '' }} >Discharged</option>
                                    <option value="Reffered" {{ ($data->admission_status == 'Reffered') ? "selected" : '' }}>Reffered</option>
                                    <option value="Canceled" {{ ($data->admission_status == 'Canceled') ? "selected" : '' }}>Canceled</option>
                                </select>

                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nameBasic"  class="form-label">Comment<span class="asterisk">*</span></label>
                                <input type="text" class="form-control" required name="discharge_summary" value="{{$data->discharge_summary ?? ''}}">



                            </div>

                            <div class="col-md-3 mb-3">
                                <input type="checkbox" required id="cnic" {{ ($checklist && $checklist->cnic == 1) ? "checked" : "" }} name="cnic" class="form-check-input checklist_checkbox" value="{{ ($checklist && $checklist->cnic == 1) ? 1 : 0 }}">
                                <label for="nameBasic" class="form-label">CNIC<span class="asterisk">*</span></label>

                            </div>

                            <div class="col-md-3 mb-3">
                                <input type="checkbox" required {{ ($checklist && $checklist->shp_form == 1) ? "checked" : "" }}  id="shp_form" name="shp_form" class="form-check-input checklist_checkbox" value="{{ ($checklist && $checklist->shp_form == 1) ? 1 : 0 }}">
                                <label for="nameBasic" class="form-label">Shp Form<span class="asterisk">*</span></label>

                            </div>

                            <div class="col-md-3 mb-3">
                                <input type="checkbox" required {{ ($checklist && $checklist->ultrasound == 1) ? "checked" : "" }}  id="ultrasound" name="ultrasound" class="form-check-input checklist_checkbox" value="{{ ($checklist && $checklist->ultrasound == 1) ? 1 : 0 }}">
                                <label for="nameBasic" class="form-label">Ultrasound<span class="asterisk">*</span></label>

                            </div>

                            <div class="col-md-3 mb-3">
                                <input type="checkbox" required {{ ($checklist && $checklist->doctor_prescription == 1) ? "checked" : "" }}  id="doctor_prescription" name="doctor_prescription" class="form-check-input checklist_checkbox" value="{{ ($checklist && $checklist->doctor_prescription == 1) ? 1 : 0 }}">
                                <label for="nameBasic" class="form-label">Doctor Prescription<span class="asterisk">*</span></label>

                            </div>

                            <div class="col-md-3 mb-3">
                                <input type="checkbox" required {{ ($checklist && $checklist->labs == 1) ? "checked" : "" }}  id="labs" name="labs" class="form-check-input checklist_checkbox" value="{{ ($checklist && $checklist->labs == 1) ? 1 : 0 }}">
                                <label for="nameBasic" class="form-label">Labs<span class="asterisk">*</span></label>

                            </div>

                            <div class="col-md-3 mb-3">
                                <input type="checkbox" required {{ ($checklist && $checklist->admission_form == 1) ? "checked" : "" }}  id="admission_form" name="admission_form" class="form-check-input checklist_checkbox" value="{{ ($checklist && $checklist->admission_form == 1) ? 1 : 0 }}">
                                <label for="nameBasic" class="form-label">Admission Form<span class="asterisk">*</span></label>

                            </div>

                            <div class="col-md-3 mb-3">
                                <input type="checkbox" required {{ ($checklist && $checklist->nursing_notes == 1) ? "checked" : "" }}  id="nursing_notes" name="nursing_notes" class="form-check-input checklist_checkbox" value="{{ ($checklist && $checklist->nursing_notes == 1) ? 1 : 0 }}">
                                <label for="nameBasic" class="form-label">Nursing notes<span class="asterisk">*</span></label>
                            </div>

                            <div class="col-md-3 mb-3">
                                <input type="checkbox" required {{ ($checklist && $checklist->medication_chart == 1) ? "checked" : "" }}  id="medication_chart" name="medication_chart" class="form-check-input checklist_checkbox" value="{{ ($checklist && $checklist->medication_chart == 1) ? 1 : 0 }}">
                                <label for="nameBasic" class="form-label">Medication Chart<span class="asterisk">*</span></label>
                            </div>

                            <div class="col-md-3 mb-3">
                                <input type="checkbox" required {{ ($checklist && $checklist->ot_notes == 1) ? "checked" : "" }}  id="ot_notes" name="ot_notes" class="form-check-input checklist_checkbox" value="{{ ($checklist && $checklist->ot_notes == 1) ? 1 : 0 }}">
                                <label for="nameBasic" class="form-label">OT Notes<span class="asterisk">*</span></label>
                            </div>

                            <div class="col-md-3 mb-3">
                                <input type="checkbox" required {{ ($checklist && $checklist->consern_notes == 1) ? "checked" : "" }}  id="consern_notes" name="consern_notes" class="form-check-input checklist_checkbox" value="{{ ($checklist && $checklist->consern_notes == 1) ? 1 : 0 }}">
                                <label for="nameBasic" class="form-label">Consern Notes<span class="asterisk">*</span></label>

                            </div>

                            <div class="col-md-3 mb-3">
                                <input type="checkbox" required {{ ($checklist && $checklist->discharge_slip == 1) ? "checked" : "" }}  id="discharge_slip" name="discharge_slip" class="form-check-input checklist_checkbox" value="{{ ($checklist && $checklist->discharge_slip == 1) ? 1 : 0 }}">
                                <label for="nameBasic" class="form-label">Discharge Slip<span class="asterisk">*</span></label>

                            </div>










                            <div class="text-left mt-3">
                                <button class="btn btn-success" type="submit" id="submit_btn">Discharge Patient</button>
                            </div>

                        </div>
                    </form>
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
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.form.min.js') }}"></script>
    <script>
        procedure_type_id = "{{$procedure_type_id}}";
        baby_count = "{{$baby_count}}";
        $('.checklist_checkbox').change(function () {
            // Check if the current checkbox is checked
            if ($(this).is(':checked')) {
                $(this).val(1); // Set value to 1 if checked

            } else {
                $(this).val(0); // Set value to 0 if unchecked

            }
            console.log($(this).attr('name') + ' value is now: ' + $(this).val());
        });

        $("body").on("change","#admission_status",function () {
            var value = $(this).val();

            if(value == "Discharged" && procedure_type_id == 6 && baby_count == 0){
                alert("Please add Baby Information before discharge");
                $(this).val('');
                return false;
            }
        });

    </script>
@endpush
