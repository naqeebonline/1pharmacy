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
            <div class="card">
                <div class="card-header header-elements-inline">
                    <div class="btn btn-primary add_new_record">Add New Level 2 Head</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive" style="min-height: 200px">
                                <table id="level2-heads-list" class="table table-responsive table-striped data_mf_table table-condensed" >
                                    <thead>
                                    <tr>
                                        <th>Head Name</th>
                                        <th>Head Code</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Is Contra</th>
                                        <th style="width: 20%">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="level3-section" style="display:none;">
                        <h5>Level 3 Heads under <span id="parent-head-name"></span></h5>
                        <div class="table-responsive">
                            <table id="level3-heads-list" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Head Name</th>
                                        <th>Head Code</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Is Contra</th>
                                        <th style="width: 10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for Level 2 -->
    <div class="modal fade" id="add_new_record_model" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="modal-content form-submit-event" id="from_submit">
                <input type="hidden" id="id" name="id" value="0">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Level 2 Head</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Head Name <span class="asterisk">*</span></label>
                            <input type="text" required id="name" name="name" class="form-control" placeholder="Enter head name" autocomplete="off">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Head Code</label>
                            <input type="text" id="head_code" name="head_code" class="form-control" placeholder="Enter head code" autocomplete="off">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type <span class="asterisk">*</span></label>
                            <select name="type" required id="type" class="form-select">
                                <option value="asset">Asset</option>
                                <option value="income">Income</option>
                                <option value="expense">Expense</option>
                                <option value="liability">Liability</option>
                                <option value="capital">Capital</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control" placeholder="Description"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Is Contra</label>
                            <select name="is_contra" id="is_contra" class="form-select">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="submit_btn" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal for Level 3 -->
    <div class="modal fade" id="add_level3_model" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="modal-content form-submit-event" id="from_submit_level3">
                <input type="hidden" id="parent_id_level3" name="parent_id">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Level 3 Head</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Head Name <span class="asterisk">*</span></label>
                            <input type="text" required id="name_level3" name="name" class="form-control" placeholder="Enter head name" autocomplete="off">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Head Code</label>
                            <input type="text" id="head_code_level3" name="head_code" class="form-control" placeholder="Enter head code" autocomplete="off">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type <span class="asterisk">*</span></label>
                            <select name="type" required id="type_level3" class="form-select">
                                <option value="asset">Asset</option>
                                <option value="income">Income</option>
                                <option value="expense">Expense</option>
                                <option value="liability">Liability</option>
                                <option value="capital">Capital</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Description</label>
                            <textarea id="description_level3" name="description" class="form-control" placeholder="Description"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Is Contra</label>
                            <select name="is_contra" id="is_contra_level3" class="form-select">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="submit_btn_level3" class="btn btn-primary">Save</button>
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
    <script src="{{ asset('assets/js/jquery.form.min.js') }}"></script>
    <script>
        var table2, table3;
        $(document).ready(function (){
            // Level 2 table
            table2 = $('#level2-heads-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("finance-heads.index") }}',
                    data: { level: 2 }
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'head_code', name: 'head_code'},
                    {data: 'type', name: 'type'},
                    {data: 'description', name: 'description'},
                    {data: 'is_contra', name: 'is_contra'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                responsive: true,
                searching:  true,
                sorting:    true,
                paging:     true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
            // Add New Level 2
            $(".add_new_record").on("click", function(){
                $("#id").val(0);
                $("#name").val("");
                $("#head_code").val("");
                $("#type").val("asset");
                $("#description").val("");
                $("#is_contra").val("0");
                $("#add_new_record_model").modal("show");
            });
            // Submit Level 2
            $("#from_submit").ajaxForm({
                url: '{{ route('finance-heads.store') }}',
                type: 'post',
                dataType: 'json',
                beforeSubmit: function(arr, $form, options) {
                    arr.push({name: '_token', value: '{{ csrf_token() }}'});
                    arr.push({name: 'level', value: 2});
                    arr.push({name: 'parent_id', value: ''});
                },
                success: function(response){
                    $("#add_new_record_model").modal("hide");
                    table2.ajax.reload();
                },
                error: function(xhr){
                    alert(xhr.responseJSON.message || 'Error occurred');
                }
            });
            // Add Level 3 button
            $(document).on('click', '.add_level3_btn', function(){
                var parentId = $(this).data('id');
                var parentName = $(this).data('name');
                $("#parent_id_level3").val(parentId);
                $("#parent-head-name").text(parentName);
                $("#name_level3").val("");
                $("#head_code_level3").val("");
                $("#type_level3").val("asset");
                $("#description_level3").val("");
                $("#is_contra_level3").val("0");
                $("#add_level3_model").modal("show");
            });
            // Submit Level 3
            $("#from_submit_level3").ajaxForm({
                url: '{{ route('finance-heads.store') }}',
                type: 'post',
                dataType: 'json',
                beforeSubmit: function(arr, $form, options) {
                    arr.push({name: '_token', value: '{{ csrf_token() }}'});
                    arr.push({name: 'level', value: 3});
                },
                success: function(response){
                    $("#add_level3_model").modal("hide");
                    if(table3) table3.ajax.reload();
                },
                error: function(xhr){
                    alert(xhr.responseJSON.message || 'Error occurred');
                }
            });
            // View Level 3 button
            $(document).on('click', '.view_level3_btn', function(){
                var parentId = $(this).data('id');
                var parentName = $(this).data('name');
                $("#parent-head-name").text(parentName);
                $("#level3-section").show();
                if(table3) table3.destroy();
                table3 = $('#level3-heads-list').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route("finance-heads.index") }}',
                        data: { level: 3, parent_id: parentId }
                    },
                    columns: [
                        {data: 'name', name: 'name'},
                        {data: 'head_code', name: 'head_code'},
                        {data: 'type', name: 'type'},
                        {data: 'description', name: 'description'},
                        {data: 'is_contra', name: 'is_contra'},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    responsive: true,
                    searching:  true,
                    sorting:    true,
                    paging:     true,
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ]
                });
            });
        });

        // Edit Level 2
$(document).on('click', '.edit_record', function(){
    var details = $(this).data('details');
    $('#id').val(details.id);
    $('#name').val(details.name);
    $('#head_code').val(details.head_code);
    $('#type').val(details.type);
    $('#description').val(details.description);
    $('#is_contra').val(details.is_contra);
    $('#add_new_record_model').modal('show');
    $('#submit_btn').data('edit-id', details.id);
});

// Submit Level 2 (edit or create)
$('#from_submit').on('submit', function(e){
    e.preventDefault();
    var id = $('#submit_btn').data('edit-id');
    var url = id ? "{{ url('finance-heads') }}/" + id : "{{ route('finance-heads.store') }}";
    var type = id ? "PUT" : "POST";
    $.ajax({
        url: url,
        type: type,
        data: $(this).serialize() + "&level=2&parent_id=",
        success: function(response){
            $('#add_new_record_model').modal('hide');
            table2.ajax.reload();
            $('#submit_btn').removeData('edit-id');
        }
    });
});

// Edit Level 3
$(document).on('click', '.edit_record_level3', function(){
    var details = $(this).data('details');
    $('#parent_id_level3').val(details.parent_id);
    $('#name_level3').val(details.name);
    $('#head_code_level3').val(details.head_code);
    $('#type_level3').val(details.type);
    $('#description_level3').val(details.description);
    $('#is_contra_level3').val(details.is_contra);
    $('#add_level3_model').modal('show');
    $('#submit_btn_level3').data('edit-id', details.id);
});

// Submit Level 3 (edit or create)
$('#from_submit_level3').on('submit', function(e){
    e.preventDefault();
    var id = $('#submit_btn_level3').data('edit-id');
    var url = id ? "{{ url('finance-heads') }}/" + id : "{{ route('finance-heads.store') }}";
    var type = id ? "PUT" : "POST";
    $.ajax({
        url: url,
        type: type,
        data: $(this).serialize() + "&level=3",
        success: function(response){
            $('#add_level3_model').modal('hide');
            if(table3) table3.ajax.reload();
            $('#submit_btn_level3').removeData('edit-id');
        }
    });
});

$(document).on('click', '.delete_record', function(){
    var id = $(this).data('id');
    if (confirm('Are you sure to delete this record?')) {
        $.ajax({
            url: "{{ url('finance-heads') }}/" + id,
            type: "POST",
            data: {
                _method: 'DELETE',
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                if(table2) table2.ajax.reload();
                if(table3) table3.ajax.reload();
            },
            error: function(xhr){
                alert(xhr.responseJSON.message || 'Error occurred');
            }
        });
    }
});
    </script>
@endpush
