@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {


            $("#province_id").change(function(){
                var province_id = $(this).val()
                $.ajax({
                    type: 'post',
                    url: '{{ route('noauth.districts-by-province-id') }}',
                    data: {
                        province_id: province_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res){
                        // console.log(res)
                        var districts = "<option value>Select a district</option>"
                        $.each(res, function(i,v){
                            districts += "<option value='"+i+"'>"+v+"</option>"
                        })
                        $("#district_id").html(districts)
                    }
                })
            })


            $("#division_id").change(function(){
                var division_id = $(this).val()
                // alert('there')
                $.ajax({
                    type: 'post',
                    url: '{{ route('noauth.districts-by-division-id') }}',
                    data: {
                        division_id: division_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res){
                        console.log(res)
                        var districts = "<option value>Select a district</option>"
                        $.each(res, function(i,v){
                            districts += "<option value='"+i+"'>"+v+"</option>"
                        })
                        $("#district_id").html(districts)
                    }
                })
            })

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
                                'route' => $item->exists ? ['settings.companies.update', \Illuminate\Support\Facades\Crypt::encrypt($item->id)] : ['settings.companies.store']
                                ])
                            !!}



                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        {!! Form::label('title', config('settings.company_title').' Name ', ['class' => 'form-label req']) !!}
                                        <span
                                            class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('title') !!}@endif</span>
                                        {!! Form::text('title', null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required']) !!}
                                    </div>
                                </div>
                            </div>


                            <div class="row">

                                <div class="col-3">
                                    <div class="form-group">
                                        {!! Form::label('company_type_id', 'Select Type ', ['class' => 'control-label req']) !!}

                                        <span
                                            class="help">@if(Session::has('errors')) {!! Session::get('errors')->first('company_type_id') !!} @endif</span>
                                        {!! Form::select('company_type_id', [null=>'Select a Type']+$types->toArray(), NULL, ['class' => 'form-control select2', 'id' => 'company_type_id', 'required']) !!}
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="form-group">
                                        {!! Form::label('company_level_id', 'Select Level ', ['class' => 'control-label req']) !!}

                                        <span
                                            class="help">@if(Session::has('errors')) {!! Session::get('errors')->first('company_level_id') !!} @endif</span>
                                        {!! Form::select('company_level_id', [null=>'Select a Level']+$levels->toArray(), NULL, ['class' => 'form-control select2', 'id' => 'company_level_id', 'required' => 'required']) !!}
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        {!! Form::label('parent_id', 'Select Parent '.config('settings.company_title'), ['class' => 'control-label']) !!}

                                        <span
                                            class="help">@if(Session::has('errors')) {!! Session::get('errors')->first('parent_id') !!} @endif</span>
                                        {!! Form::select('parent_id', [null=>'This is a parent '.config('settings.company_title')]+$companies_dd, NULL, ['class' => 'form-control select2', 'id' => 'parent_id']) !!}
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        {!! Form::label('province_id', 'Select Province ', ['class' => 'control-label']) !!}

                                        <span
                                            class="help">@if(Session::has('errors')) {!! Session::get('errors')->first('province_id') !!} @endif</span>
                                        {!! Form::select('province_id', [null=>'Select a Province']+$provinces->toArray(), NULL, ['class' => 'form-control select2', 'id' => 'province_id']) !!}
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        {!! Form::label('prefix', 'Prefix', ['class' => 'control-label']) !!}

                                        <span
                                            class="help">@if(Session::has('errors')) {!! Session::get('errors')->first('prefix') !!} @endif</span>
                                        {!! Form::text('prefix',  NULL, ['class' => 'form-control', 'id' => 'prefix']) !!}
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="form-group">
                                        {!! Form::label('division_id', 'Select Division ', ['class' => 'control-label']) !!}

                                        <span
                                            class="help">@if(Session::has('errors')) {!! Session::get('errors')->first('division_id') !!} @endif</span>
                                        {!! Form::select('division_id', [null=>'Select a Division']+$divisions->toArray(), NULL, ['class' => 'form-control', 'id' => 'division_id']) !!}
                                    </div>
                                </div>


                                <div class="col-3">
                                    <div class="form-group">
                                        {!! Form::label('district_id', 'Select District ', ['class' => 'control-label']) !!}

                                        <span
                                            class="help">@if(Session::has('errors')) {!! Session::get('errors')->first('district_id') !!} @endif</span>
                                        {!! Form::select('district_id', [null=>'Select a District']+$districts->toArray(), NULL, ['class' => 'form-control select2', 'id' => 'district_id']) !!}
                                    </div>
                                </div>


                            </div>


                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        {!! Form::label('description', 'Details ', ['class' => 'form-label req']) !!}
                                        <span
                                            class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('description') !!}@endif</span>
                                        {!! Form::textarea('description', null, ['class' => 'form-control', 'id' => 'description']) !!}
                                    </div>
                                </div>
                            </div>



                            <div class="row">
                                <div class="col-12">

                                    <a href="{{ route('settings.companies.list') }}" class="btn btn-warning">
                                        <i class="bx bx-arrow-back tf-icons"></i> Back
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
