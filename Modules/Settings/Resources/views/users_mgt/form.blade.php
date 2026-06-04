@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp


@push('vendor-style')

<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endpush

@push('scripts')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/js/app-custom.js') }}"></script>
<script type="text/javascript">

     var is_hospital = "<?php ($item->exists && $item->hospital_id) ? true : ''; ?>";
     var is_polling_station = "<?php ($item->exists && $item->polling_station_id) ? true : ''; ?>";
     if(is_polling_station){
         $(".polling_station_id_div").show();
     }

    function check_hod()
    {
        var company_id = $('#company_id').val();

       // alert(company_id);
       $.ajax({
                type: 'post',
                url: "{{ route('noauth.users-hod-check') }}",
                data: {
                    company_id: company_id,
                    _token: '{{ csrf_token() }}'
                    @if($item->exists),
                    user_id: '{{ $item->id }}'
                    @endif
                },
                success: function(res) {

                    if(res ==1)
                    {
                        alert('Head of Department user already Exist.');
                        $('#is_hod_no').prop('checked',true);

                    }
                }
            })

    }

    function reset_fields(){
        $(".hospital_id_div").hide();
        $("#hospital_id").val("");
        $("#hospital_id").removeAttr("required");

        $(".polling_station_id_div").hide();
        $("#polling_station_id").val('');
        $("#polling_station_id").removeAttr("required");

        $(".police_mobile_id_div").hide();
        $("#police_mobile_id").val('');
        $("#police_mobile_id").removeAttr("required");

        $(".region_id_div").hide();
        $("#region_id").val('');
        $("#region_id").removeAttr("required");

        $(".police_station_id_div").hide();
        $("#police_station_id").val('');
        $("#police_station_id").removeAttr("required");
    }
    $(document).ready(function() {

        $('#role_id').on('change', function () {
                //var selectedRole = $(this).val();
            var district_id = $("#district_id").val();
            if(district_id == ''){
                alert("Please select district");
                $("#role_id").val('');
                return false;
            }
                var selectedRole = $( "#role_id option:selected" ).text();

            reset_fields();

                if(selectedRole == "Hospital User"){
                    $(".hospital_id_div").show();
                    $("#hospital_id").attr("required","required");
                }
                if(selectedRole == "Polling Station"){
                    $(".polling_station_id_div").show();
                    $("#polling_station_id").attr("required","required");
                }
                if(selectedRole == "Police Mobile Vehicle"){
                    $(".police_mobile_id_div").show();
                    $("#police_mobile_id").attr("required","required");
                }

                if(selectedRole == "Police Station"){
                    $(".police_station_id_div").show();
                    $("#police_station_id").attr("required","required");
                }

                if(selectedRole == "Regional User"){
                    $(".region_id_div").show();
                    $("#region_id").attr("required","required");
                }

        });

        $('#district_id').on('change', function () {
                var selectedDistrict = $(this).val();
                var ProvinceID = $('#province_id').val();




        });
        $("#company_id").change(function() {
            var company_id = $(this).val()
            $('#is_hod_no').prop('checked',true);

            // get the sections/units of this company
            $.ajax({
                type: 'post',
                url: "{{ route('noauth.sections-by-company-id') }}",
                data: {
                    company_id: company_id,
                    _token: '{{ csrf_token() }}'
                    @if($item->exists),
                    section_id: '{{ $item->id }}'
                    @endif
                },
                success: function(res) {
                    // console.log(res)
                    var sections = "<option value>Select {{ config('settings.section_title') }}</option>"
                    $.each(res, function(i, v) {
                        sections += "<option value='" + i + "'>" + v + "</option>"
                    })
                    $("#section_id").html(sections)
                }
            })

            // get the users of this company for parent user
            $.ajax({
                type: 'post',
                url: "{{ route('noauth.users-list-by-company-id') }}",
                data: {
                    company_id: company_id,
                    _token: '{{ csrf_token() }}'
                    @if($item->exists),
                    user_id: '{{ $item->id }}'
                    @endif
                },
                success: function(res) {
                    // console.log(res)
                    var sections = "<option value>This is parent user</option>"
                    $.each(res, function(i, v) {
                        sections += "<option value='" + i + "'>" + v + "</option>"
                    })
                    $("#parent_id").html(sections)
                }
            })
             // get the HOD Users of this company if exist then disabled the 'Yes'
            $.ajax({
                type: 'post',
                url: "{{ route('noauth.users-hod-id-check') }}",
                data: {
                    company_id: company_id,
                    _token: '{{ csrf_token() }}'
                    @if($item->exists),
                    user_id: '{{ $item->id }}'
                    @endif
                },
                success: function(res) {
                    // console.log(res)
                    // var sections = "<option value>This is parent user</option>"
                    // $.each(res, function(i, v) {
                    //     sections += "<option value='" + i + "'>" + v + "</option>"
                    // })
                    // $("#parent_id").html(sections)
                    //console.log(res);
                    // if(res ==1)
                    // {
                    //     $('#hod_div').css('display','none');
                    // }else{
                    //     $('#hod_div').css('display','block');

                    // }
                }
            })
        })




        {{-- @if($item->exists)
                setTimeout(function() {
                    $("#company_id").val('')
                }, 400);
                setTimeout(function() {
                    $("#company_id").val('{{ $item->company_id }}').trigger('change')
                }, 500);
                setTimeout(function() {
                    $("#section_id").val('{{ $item->section_id }}').trigger('change')
                }, 600);
                setTimeout(function() {
                    $("#parent_id").val('{{ $item->parent_id }}')
                }, 700);
                @endif --}}
    })
</script>
@endpush

@section('content')


<div class="row">
    <div class="col-12">

        <!-- Traffic sources -->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">{{ $title }}</h5>
                <div class="header-elements">
                    <div class="form-check form-check-right form-check-switchery form-check-switchery-sm">

                        {{--<label class="form-check-label">
                                Live update:
                                <input type="checkbox" class="form-input-switchery" checked data-fouc>
                            </label>--}}
                    </div>
                </div>
            </div>

            <div class="card-body">

                <div class="row">


                    <div class="col-12">


                        {!! Form::model($item, [
                        'enctype' => 'multipart/form-data',
                        'method' => $item->exists ? 'put' : 'post',
                        'route' => $item->exists ? ['settings.users-mgt.update', \Illuminate\Support\Facades\Crypt::encrypt($item->id)] : ['settings.users-mgt.store']
                        ])
                        !!}

                        <div class="row">

                            <div class="col-4">
                                <div class="form-group">
                                    {!! Form::label('department_id', 'Department Name  ', ['class' => 'form-label']) !!}
                                    {!! Form::select('department_id', [null=>'Select Department']+$departments->toArray(), null, ['class' => 'form-control select2',"required"=>"required"]) !!}
                                    <span class="help" style="color: red">
                                                            @if ($errors->has('department_id'))
                                            <span>{{ $errors->first('department_id') }}</span>
                                        @endif

                                                        </span>
                                </div>


                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    {!! Form::label('name', 'Service Name ', ['class' => 'form-label req']) !!}
                                    <span class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('service_name') !!}@endif</span>
                                    {!! Form::text('name', null, ['class' => 'form-control', 'id' => 'name', 'required' => 'required']) !!}
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    {!! Form::label('email', 'Email Address ', ['class' => 'form-label req']) !!}
                                    <span class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('email') !!}@endif</span>
                                    {!! Form::email('email', null, ['class' => 'form-control', 'id' => 'email', 'required' => 'required']) !!}
                                </div>
                            </div>

                        </div>


                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    {!! Form::label('contact_number', 'Contact No', ['class' => 'form-label']) !!}
                                    <span class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('contact_number') !!}@endif</span>
                                    {!! Form::text('contact_number', null, ['class' => 'form-control', 'id' => 'contact_number',]) !!}
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    {!! Form::label('username', 'Username ', ['class' => 'form-label req']) !!}
                                    <span class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('username') !!}@endif</span>
                                    {!! Form::text('username', null, ['class' => 'form-control', 'id' => 'username', 'required' => 'required']) !!}
                                </div>
                            </div>

                            <div class="col-4">

                                @if($item->exists)
                                <div class="form-group">
                                    {!! Form::label('password', 'Password ', ['class' => 'form-label req']) !!}
                                    <span class="help">Leave empty if you don't want to change password. @if(session()->has('errors')) {!! session()->get('errors')->first('password') !!}@endif</span>
                                    {!! Form::text('password', "", ['class' => 'form-control', 'id' => 'password']) !!}
                                </div>
                                @else
                                <div class="form-group">
                                    {!! Form::label('password', 'Password ', ['class' => 'form-label req']) !!}
                                    <span class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('password') !!}@endif</span>
                                    {!! Form::text('password', null, ['class' => 'form-control', 'id' => 'password', 'required' => 'required']) !!}
                                </div>
                                @endif

                            </div>



                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('district_id', 'District  ', ['class' => 'form-label']) !!}
                                    {!! Form::select('district_id', [null=>'Select District']+$districts->toArray(), null, ['class' => 'form-control select2',"required"=>"required"]) !!}
                                    <span class="help" style="color: red">
                                                            @if ($errors->has('district_id'))
                                            <span>{{ $errors->first('district_id') }}</span>
                                        @endif

                                                        </span>
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    {!! Form::label('role_id[]', 'Select Roles ', ['class' => 'control-label']) !!}

                                    <span class="help">@if(Session::has('errors')) {!! Session::get('errors')->first('role_id[]') !!} @endif</span>
                                    {!! Form::select('role_id[]', [null=>'Select Role...']+$roles->toArray(), ($item->exists & $item->roles->count() > 0) ? $item->roles : null, ['class' => 'form-select select2', 'id' => 'role_id',"required"=>"required"]) !!}
                                </div>
                            </div>
                        </div>










                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    {!! Form::label('description', 'Details ', ['class' => 'form-label req']) !!}
                                    <span class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('description') !!}@endif</span>
                                    {!! Form::textarea('description', null, ['class' => 'form-control', 'id' => 'description']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row" id="vueapp">
                            <div class="col-sm-4">
                                <file-uploader :unlimited="false" collection="PROFILE_PICTURE" label="PROFILE PICTURE" accept="image/jpeg,image/png,image/jpg,image/gif">
                                </file-uploader>
                            </div>
                        </div>
                        <div id='hod_div' class="row col-12">
                            <div class="row">

                                <div class="col-2">
                                    <div class="form-group primary">
                                        <strong style="color: rgb(18, 111, 224)">Is Head of Department</strong>
                                    </div>
                                </div>

                                <div class="col-2">
                                    <div class="form-group">
                                        <div class="form-check form-check-primary radio-custom active">
                                            <input name="is_hod" class="form-check-input"
                                                type="radio" value="0"
                                                id="is_hod_no"

                                                <?php
                                                    if($item->exists & $item->is_hod == 0)
                                                    {
                                                        echo 'checked';
                                                    }else{
                                                        echo 'checked';

                                                    }
                                                ?> >
                                            <label class="form-check-label"
                                                for="is_hod_no">
                                                No </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <div class="form-check form-check-primary radio-custom ">
                                            <input name="is_hod" class="form-check-input"
                                                onclick="check_hod();"
                                                type="radio" value="1"
                                                id="is_hod_yes"
                                                <?php
                                                    if($item->exists & $item->is_hod != 0)
                                                    { echo 'checked'; }
                                                ?>  >
                                            <label class="form-check-label"
                                                for="is_hod_yes">
                                                Yes </label>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>



                        <div class="row">
                            <div class="col-12">

                                <a href="{{ route('settings.my-apps.list') }}" class="btn btn-warning">
                                    <i class="bx bx-arrow-back"></i> Back
                                </a>

                                <button type="submit" class="btn btn-success">
                                    <i class="bx bx-save"></i> Save
                                </button>

                            </div>
                        </div>

                        {!! Form::close() !!}


                    </div>

                </div>

            </div>


        </div>
        <!-- /traffic sources -->

    </div>
</div>




@endsection