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

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr style="background-color: gray; font-weight: bold; color:white">
                            <td><b>Invoice No:</b> {{$investigation->invoice_no}}</td>
                            <td><b>Patient Name:</b> {{$patient->name}}</td>
                            <td><b>MR.No:</b> {{$patient->mr_no}}</td>
                            <td><b>Gender:</b> {{$patient->gender}}</td>
                            <td><b>Age:</b> {{$patient->age}} Years - {{$patient->months}} Months - {{$patient->days}} Days</td>


                        </tr>
                    </table>
                    <br>
                    <br>
                    <form class="form-submit-event" id="add_inv_result">
                        <div class="row">
                            <input type="hidden" value="{{ $investigation->id }}" required id="inv_id" name="inv_id" value="0" class="form-control" />
                            <input type="hidden" value="{{ $is_textual }}" required id="is_textual" name="is_textual"  class="form-control" />
                        @if($is_textual != true)
                            @foreach($all_data as $key => $value2)
                                @if(strtolower($value2->name) != "no heading")
                                    <h5 style="text-decoration: underline">{{$value2->name ?? ''}}</h5>
                                @endif
                                    @foreach ($value2->parameters as $key => $parameter)
                                    @php
                                        $range_max_value = 0;
                                      $range_min_value = 0;

                                      if ($patient->gender == 'male' && $patient->age > 2) {
                                          $range_max_value = $parameter->male_max;
                                          $range_min_value = $parameter->male_min;
                                      } elseif ($patient->gender == 'female' && $patient->age > 2) {
                                          $range_max_value = $parameter->female_max;
                                          $range_min_value = $parameter->female_min;
                                      } else {
                                          $range_max_value = $parameter->child_max;
                                          $range_min_value = $parameter->child_min;
                                      }
                                    @endphp


                                    <div class="row col-12">
                                        <div class="col-3">
                                            <label for="nameBasic" class="form-label">{{ $parameter->name }}<span
                                                    class="asterisk">*</span></label>

                                        </div>
                                        <div class="col-3 mb-3">
                                            <input type="hidden" name="parameter_id[]" value="{{ $parameter->id }}">
                                             <input type="text" name="result_text_value[]" value="{{ $result[$key]['result_text_value'] ?? '' }}" placeholder="text" class="form-control " style="display: {{ $is_ict ? "block" : "none" }}" />
                                            <input type="text" value="{{ $result[$key]['result_value'] ?? '0' }}" {{ $is_ict ? "" : "required" }}
                                                id="result-{{ $parameter->id }}" name="result[]" class="form-control next_input" style="display: {{ !$is_ict ? "block" : "none" }}"
                                                placeholder="Add Result" autocomplete="off">
                                        </div>

                                        <div class="col-3 mb-3">
                                            @if ($parameter->result_text_value != '' && $inv_sub_category->is_ict)
                                                <p class="text-center">-</p>
                                            @else
                                                @if ($patient->gender == 'male' && $patient->age > 2)
                                                    <p class="text-center">{{ number_format($parameter->male_min, 1) }} -
                                                        {{ number_format($parameter->male_max,1) }}</p>
                                                @elseif($patient->gender == 'female' && $patient->age > 2)
                                                    <p class="text-center">{{ number_format($parameter->female_min,1) }} -
                                                        {{ number_format($parameter->female_max,1) }}</p>
                                                @else
                                                    <p class="text-center">{{ number_format($parameter->child_min) }} -
                                                        {{ number_format($parameter->child_max,1) }}</p>
                                                @endif
                                            @endif
                                        </div>



                                        {{-- <div class="col-md-4">
                                            <input type="text" name="result_text_value[]" value="{{ $result[$key]['result_text_value'] ?? '' }}" placeholder="text" class="form-control" />
                                        </div> --}}

                                    </div>
                                @endforeach
                            @endforeach

                        @else

                                <div class="col-12 mb-3">

                                    <label><b style="color: red">{{$sub_category->name ?? "" }}</b> Result</label>

                                    <textarea id="result_text_value" class="form-control" name="result_text_value" cols="30" rows="4">{{ (count($result)) ? $result[0]['result_text_value'] : $sub_category->result_text  }}</textarea>

                                </div>
                        @endif


                            <div class="col-12">
                                <label for="" class="form-label">Comment</label>
                                <textarea name="inv_comment" id="inv_comment"  class="form-control" cols="30" rows="4">{{$investigation->inv_comment ?? ''}}</textarea>
                            </div>

                            <div class="text-left mt-3">
                                <button class="btn btn-success" id="submit_btn">Add</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>


            {{-- LISTIN PATIENTS --}}
            {{-- <div class="card my-5">
                <div class="card-body">
                    <h5 class="card-title">Investigation List</h5>

                    <div class="table-responsive" style="min-height: 200px">

                        <table id="investigation-list"
                            class="table table-responsive table-striped data_mf_table table-condensed">
                            <thead>
                                <tr>
                                    <th>Patient MR no.</th>
                                    <th>Patient Name</th>
                                    <th>Gender</th>
                                    <th>Investigation Category</th>
                                    <th>Status</th>

                                    <th style="width: 10%">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div> --}}
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
        integrity="sha512-YUkaLm+KJ5lQXDBdqBqk7EVhJAdxRnVdT2vtCzwPHSweCzyMgYV/tgGF4/dCyqtCC2eCphz0lRQgatGVdfR0ww=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).on("keydown", "form", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();

                 return false;
            }
        });
        $(document).on("keydown", ".next_input", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();

                // find all .next_input in DOM order
                let $inputs = $(".next_input:visible:enabled");

                // current index
                let idx = $inputs.index(this);

                // move to next .next_input if exists
                if (idx + 1 < $inputs.length) {
                    $inputs.eq(idx + 1).focus();
                }
            }
        });
        $("#add_inv_result").on("submit", function(e) {
            e.preventDefault();

            let isValid = true;

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

                $("#add_inv_result").ajaxSubmit({
                    url: '{{ route('pos.store_inv_result') }}',
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(response) {
                        if (response.status) {
                            window.location = "{{ route('post.investigation_result') }}";
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        //console.log();
                        //alert("Status: " + textStatus); alert("Error: " + errorThrown);
                        alert(JSON.parse(XMLHttpRequest.responseText).message);
                    }
                });
            }
        });
    </script>

    {{-- <script>
        // setTimeout(function() {
        //     $("#patient_id").select2();
        //     $("#procedure_type_id").select2();
        //     $("#consultant_id").select2();
        // }, 1000);
        investigation_result = $('#investigation-list').DataTable({
            processing: true,
            serverSide: true,

            pageLength: 10,
            ajax: {
                url: "{{ route('pos.investigation_result_list') }}",


            },

            columns: [

                {
                    data: 'patient.mr_no',
                    name: 'patient.mr_no',
                    searchable: true
                },
                {
                    data: 'patient.name',
                    name: 'patient.name',
                    searchable: true
                },
                {
                    data: 'patient.gender',
                    name: 'patient.gender',
                    searchable: true
                },

                {
                    data: 'investigation.name',
                    name: 'investigation.name',
                    searchable: true
                },

                {
                    data: 'status',
                    name: 'status',
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






        // $("body").on("change", "#ward_id", function() {
        //     getBeds();
        // });

        // function getBeds() {
        //     let bedId = $("#ward_id").val();
        //     $.ajax({
        //         type: "post",
        //         url: "{{ route('pos.ward_bed') }}",
        //         data: {
        //             id: bedId,
        //             "_token": "{{ csrf_token() }}"
        //         },
        //         success: function(res) {
        //             if (res.status)

        //                 $("#bed_id").find("option").not(":first").remove();

        //             if (res.data.length > 0) {
        //                 $.each(res.data, function(key, bed) {
        //                     $("#bed_id").append(
        //                         `<option value="${bed.id}">${bed.name}</option>`
        //                     )
        //                 });
        //             }
        //         }
        //     });
        // }

        // function getEditBeds(ward_id, bed_id) {

        //     $.ajax({
        //         type: "post",
        //         url: "{{ route('pos.ward_bed') }}",
        //         data: {
        //             id: ward_id,
        //             "_token": "{{ csrf_token() }}"
        //         },
        //         success: function(res) {
        //             if (res.status)

        //                 $("#bed_id").find("option").not(":first").remove();

        //             if (res.data.length > 0) {
        //                 $.each(res.data, function(key, bed) {
        //                     $("#bed_id").append(
        //                         `<option value="${bed.id}">${bed.name}</option>`
        //                     )
        //                 });
        //             }

        //             setTimeout(function() {
        //                 $("#bed_id").val(bed_id);
        //             }, 1000);
        //         }
        //     });
        // }


        // $("#patient_admission").on("submit", function(e) {

        //     e.preventDefault();

        //     let isValid = true;

        //     // Clear previous error messages
        //     $(".error-message").remove();
        //     $(".is-invalid").removeClass("is-invalid");

        //     // Validate required fields
        //     $(this).find("[required]").each(function() {
        //         if (!$(this).val().trim()) {
        //             isValid = false;
        //             $(this).addClass("is-invalid"); // Highlight invalid field
        //             $(this).after(
        //                 `<span class="error-message text-danger">This field is required.</span>`
        //             ); // Show error message
        //         }
        //     });

        //     if (isValid) {

        //         $("#patient_admission").ajaxSubmit({
        //             url: '{{ route('pos.store_patient_admission') }}',
        //             type: 'post',
        //             data: {
        //                 _token: '{{ csrf_token() }}'

        //             },
        //             success: function(response) {
        //                 console.log(response);
        //                 reset_fields();
        //                 investigation_result.ajax.reload();
        //             },
        //             error: function(XMLHttpRequest, textStatus, errorThrown) {
        //                 //console.log();
        //                 //alert("Status: " + textStatus); alert("Error: " + errorThrown);
        //                 alert(JSON.parse(XMLHttpRequest.responseText).message);
        //             }
        //         });
        //     }
        // });

        // function reset_fields() {
        //     $("#id").val(0);
        //     $("#patient_id").val('');
        //     $("#bed_id").val('');
        //     $("#ward_id").val('');
        //     $("#procedure_type_id").val('').trigger("change");
        //     $("#consultant_id").val('').trigger("change");
        //     $("#admission_data").val('');
        //     $("#emergency_contact_no").val('');
        //     $("#guardian_name").val('');
        //     $("#admission_date").val('');
        // }

        // $("body").on("click", ".edit_record", function(e) {
        //     record_id = $(this).attr("data-id");
        //     var details = JSON.parse($(this).attr("data-details"));
        //     var fullDateTime = details.admission_date;
        //     let dateOnly = fullDateTime.split(' ')[0];

        //     $("#id").val(details.id);

        //     $("#admission_date").val(details.admission_date_edit);
        //     $("#emergency_contact_no").val(details.emergency_contact_no);
        //     $("#guardian_name").val(details.guardian_name);
        //     $("#procedure_type_id").val(details.procedure_type_id).trigger("change");
        //     $("#consultant_id").val(details.consultant_id).trigger("change");
        //     $("#bed_id").val(details.bed_id);
        //     $("#patient_id").val(details.patient_id).trigger('change');
        //     $("#ward_id").val(details.ward_id);
        //     $("#relation_id").val(details.relation_id);
        //     $("#admission_date").val(dateOnly);
        //     getEditBeds(details.ward_id, details.bed_id);

        // });


        // $("body").on("click", ".delete_record", function(e) {
        //     var id = $(this).attr("data-id");
        //     if (confirm('Are you sure to delete this record ?')) {
        //         $.ajax({
        //             type: 'post',
        //             url: "{{ route('pos.deactivate_record') }}",
        //             data: {
        //                 id: id,
        //                 table: "patient_admissions",
        //                 _token: '{{ csrf_token() }}'

        //             },
        //             success: function(res) {
        //                 investigation_result.ajax.reload();
        //                 //window.location.reload();
        //             }
        //         })
        //     } else {
        //         alert('Why did you press cancel? You should have confirmed');
        //     }
        // });
    </script> --}}
@endpush
