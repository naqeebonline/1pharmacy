@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <style>
        .table > :not(caption) > * > * {padding: 5px;}
    </style>

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')

    <div class="row">
        <div class="col-12">

            <!-- Traffic sources -->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <div class="btn btn-primary add_new_record">Investigation Parameter</div>
                   {{-- <div class="btn btn-primary add_is_parent">Is Parent</div>--}}

                </div>

                <div class="card-body">

                    <div class="row">

                            <h5>{{$inv_sub_category->name}}</h5>
                        <div class="col-12">

                            <div class="table-responsive" style="min-height: 200px">

                                <table id="users-list" class="table table-responsive table-striped data_mf_table table-condensed" >

                                    <thead>
                                    <tr>
                                        <th >Index Number</th>
                                        <th >Paramet Header</th>
                                        <th >Parameter Name</th>
                                        <th >Parameter Unit</th>
                                        <th >Male Min</th>
                                        <th >Male Max</th>
                                        <th >Female Min</th>
                                        <th >Female Max</th>
                                        <th >Child Min</th>
                                        <th >Child Max</th>
                                        <th  style="width: 10%">Action</th>
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
            <!-- /traffic sources -->
        </div>
    </div>

    <div class="modal fade" id="add_new_record_model" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="modal-content form-submit-event" id="from_submit">
                <input type="hidden" id="id" name="id" value="0">
                <input type="hidden" id="investigation_sub_category_id" name="investigation_sub_category_id" value="{{$id}}">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add Investigation Main Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Heading Name</label>
                            <select class="form-control" name="parameter_heading_id" id="parameter_heading_id">
                                @foreach($parameter_heading as $key => $value)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label">Parameter Name<span class="asterisk">*</span></label>
                            <input type="text" required id="name" name="name" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label">Unit<span class="asterisk">*</span></label>
                            <input type="text" required id="unit" name="unit" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label">Male Min Range<span class="asterisk">*</span></label>
                            <input type="number" required id="male_min" name="male_min" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label">Male Max Range<span class="asterisk">*</span></label>
                            <input type="number" required id="male_max" name="male_max" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label">Female Min Range<span class="asterisk">*</span></label>
                            <input type="number" required id="female_min" name="female_min" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label">Female Max Range<span class="asterisk">*</span></label>
                            <input type="number" required id="female_max" name="female_max" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label">Child Min Range<span class="asterisk">*</span></label>
                            <input type="number" required id="child_min" name="child_min" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label">Child Max Range<span class="asterisk">*</span></label>
                            <input type="number" required id="child_max" name="child_max" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label">Index Number<span class="asterisk">*</span></label>
                            <input type="number" required id="index_number" name="index_number" class="form-control" placeholder="" autocomplete="off">
                        </div>
                    </div>




                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close                </button>
                    <button type="submit" id="submit_btn" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>


    <div class="modal my_modal fade" id="add_is_parent_model" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="modal-content form-submit-event" id="from_submit">
                <input type="hidden" name="investigation_sub_category_id" value="{{$id}}">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Parameter Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label">Display Order (e.g 1,2,3)<span class="asterisk">*</span></label>
                            <input type="text" required id="parent_order" name="parent_order" class="form-control" placeholder="" autocomplete="off">

                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label">Parent Name<span class="asterisk">*</span></label>
                            <input type="text" required id="parent_name" name="parent_name" class="form-control" placeholder="" autocomplete="off">
                        </div>


                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Close                </button>
                        <button type="submit" id="submit_btn" class="btn btn-primary">Save</button>
                    </div>

                    <div class="row">
                        <table class="table table-bordered">
                            <tr>
                                <th>order</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>

                            <tr>
                                <td>1</td>
                                <td>Test</td>
                                <td><a class="btn btn-sm btn-danger">x</a></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Test</td>
                                <td><a class="btn btn-sm btn-danger">x</a></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Test</td>
                                <td><a class="btn btn-sm btn-danger">x</a></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Test</td>
                                <td><a class="btn btn-sm btn-danger">x</a></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Test</td>
                                <td><a class="btn btn-sm btn-danger">x</a></td>
                            </tr>
                        </table>
                    </div>




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

        $("body").on("click",".add_is_parent",function (e) {
            $("#add_is_parent_model").modal("show");
        });

        $("body").on("click",".add_new_record",function (e) {
            $("#id").val(0);
            $("#investigation_sub_category_id").val("{{$id}}");
            $("#name").val('');
            $("#index_number").val('');
            $("#unit").val('');
            $("#male_min").val('');
            $("#male_max").val('');
            $("#female_min").val('');
            $("#female_max").val('');
            $("#child_min").val('');
            $("#child_max").val('');
            $("#add_new_record_model").modal("show");

        });

        $("body").on("click",".edit_record",function (e) {
            record_id = $(this).attr("data-id");
            var details = JSON.parse($(this).attr("data-details"));

            $("#id").val(record_id);
            $("#parameter_heading_id").val(details.parameter_heading_id);
            $("#name").val(details.name);
            $("#investigation_sub_category_id").val("{{$id}}");
            $("#index_number").val(details.index_number);
            $("#unit").val(details.unit);
            $("#male_min").val(details.male_min);
            $("#male_max").val(details.male_max);
            $("#female_min").val(details.female_min);
            $("#female_max").val(details.female_max);
            $("#child_min").val(details.child_min);
            $("#child_max").val(details.child_max);
            $("#add_new_record_model").modal("show");
        });

        $("body").on("click","#submit_btn",function (e) {
            e.preventDefault();

            $("#from_submit").ajaxSubmit({
                url: '{{route('pos.save_investigation_parameter')}}',
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}'

                },
                success: function(response){
                    $("#add_new_record_model").modal("hide");
                    user_table.ajax.reload();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    //console.log();
                    //alert("Status: " + textStatus); alert("Error: " + errorThrown);
                    alert(JSON.parse(XMLHttpRequest.responseText).message);
                }
            });
        });

        $(document).ready(function (){

            district_id = "";
            police_station_id = [];
            leave_request_id = '';

            user_table = $('#users-list').DataTable({
                processing: true,
                serverSide: true,

                lengthMenu: [
                    [ 100, 250, 500, 1000 ],
                    [ '100', '250', '500', '1000']
                ],
                pageLength: 50,
                ajax: {
                    url: '{{route("pos.list_investigation_parameter",[$id])}}',
                    data: function (d) {
                        d.user_id = $('#attendance_user_filter').val();
                        /*d.attendance_date_from = $('#attendance_date_from').val();
                        d.attendance_date_to = $('#attendance_date_to').val();*/


                    }

                },

                columns: [

                    {data: 'index_number', name: 'index_number',searchable: true},
                    {data: 'parameter_heading.name', name: 'parameter_heading.name',searchable: true},
                    {data: 'name', name: 'name',searchable: true},
                    {data: 'unit', name: 'unit',searchable: true},
                    {data: 'male_min', name: 'male_min',searchable: true},
                    {data: 'male_max', name: 'male_max',searchable: true},
                    {data: 'female_min', name: 'female_min',searchable: true},
                    {data: 'female_max', name: 'female_max',searchable: true},
                    {data: 'child_min', name: 'child_min',searchable: true},
                    {data: 'child_max', name: 'child_max',searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],

                responsive: true,
                processing: true,
                serverSide: true,
                searching:  true,
                sorting:    true,
                paging:     true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            $('#attendance_user_filter, #attendance_date_from, #attendance_date_to').on('change', function(e) {
                e.preventDefault();
                user_table.ajax.reload();
            });

            $("body").on("click",".delete_record",function (e) {
                var id  = $(this).attr("data-id");
                if (confirm('Are you sure to delete this record ?')) {
                    $.ajax({
                        type: 'post',
                        url: "{{ route('pos.deactivate_record') }}",
                        data: {
                            id: id,
                            table:"investigation_sub_category_parameters",
                            _token: '{{ csrf_token() }}'

                        },
                        success: function(res) {
                            user_table.ajax.reload();
                           // window.location.reload();
                        }
                    })
                } else {
                    alert('Why did you press cancel? You should have confirmed');
                }
            });
        });






    </script>
@endpush