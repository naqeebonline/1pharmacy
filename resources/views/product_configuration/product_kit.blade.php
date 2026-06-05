@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <style>
        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 7px;
            text-align: center;
            margin-bottom: 20px;
            float: left;
            margin-left: 43px;
            margin-top: -26px;


        }

        label {
            font-weight: bold;
        }

        input[type="checkbox"] {
            /* 1.5 times bigger */


        }

        .table> :not(caption)>*>* {
            padding: 5px;
        }

        table tr td {
            text-transform: capitalize;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script> --}}
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Traffic sources -->
            <div class="row ">
                <!-- Right Block: Form Inputs -->
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post" id="product_kit_form">
                                <input type="hidden" value="{{ request()->id }}" name="product_main_id" id="product_main_id" >
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="" class="form-label">Select Product</label>
                                        <select required name="product_id" id="product_id" class="form-select">
                                            <option value="" disabled selected>Select Product</option>
                                            @foreach ($products as $item)
                                                <option value="{{ $item->ProductID }}">{{ $item->ProductName }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="" class="form-label">Quantity</label>
                                        <input required type="text" value="" id="qty"
                                            name="qty" class="form-control">

                                    </div>


                                </div>

                                <div class="d-flex align-items-center">
                                    <button class="btn btn-success" id="save_button" type="submit">Save</button>

                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>



             <div class="card">
                <div class="row">
                    <div class="col">

                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="table-responsive" style="min-height: 200px">

                                    <table id="product_kit_list" style="width: 100% !important"
                                        class="table table-responsive table-striped  ">

                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Qty</th>
                                                <th style="width: 100px">Actions</th>
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
        setTimeout(() => {
            $("#product_id").select2();
        }, 1000);

    
        $("#product_kit_form").on("submit", function(e) {
            e.preventDefault();
            let isValid = true;
            $(".error-message").remove();
            $(".is-invalid").removeClass("is-invalid");

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

                $("#product_kit_form").ajaxSubmit({
                    url: '{{ route('pos.save_product_kit') }}',
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        if(!response.status){
                            alert(response.message);
                        }else{

                            machine_patient.ajax.reload();
                        }
                    }
                });
            }
        });

        loadProductKit();

        function loadProductKit() {
            id = "{{ request()->id }}";
            
            machine_patient = $('#product_kit_list').DataTable({

                processing: true,
                serverSide: true,

                lengthMenu: [
                    [100, 250, 500, 1000],
                    ['100', '250', '500', '1000']
                ],
                pageLength: 50,
                ajax: {
                    url: `{{ route('pos.product_kit_list') }}/${id}`,
                   
                },

                columns: [

                    {
                        data: 'product.ProductName',
                        name: 'product.ProductName',
                        searchable: true
                    },

                    {
                        data: 'qty',
                        name: 'qty',
                        searchable: true
                    },
                  
                    {
                        data: 'actions',
                        name: 'actions',
                        searchable: true
                    },

                ],


                responsive: true,
                processing: true,
                serverSide: true,
                searching: true,
                sorting: true,
                paging: true,

            });
        }


        $("body").on("click", ".delete_record", function(e) {
            var id = $(this).attr("data-id");
            if (confirm('Are you sure to delete this record ?')) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.deactivate_record') }}",
                    data: {
                        id: id,
                        table: "product_kits",
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(res) {
                        machine_patient.ajax.reload();
                        //window.location.reload();
                    }
                })
            }
        });
    </script>
@endpush
