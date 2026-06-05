@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('scripts')
    <script src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".select2").select2()

            $("#company_id").change(function(){
                var company_id = $(this).val()
                $.ajax({
                    type: 'post',
                    url: '{{ route('noauth.sections-by-company-id') }}',
                    data: {
                        company_id: company_id,
                        _token: '{{ csrf_token() }}'
                        @if($item->exists)
                        ,section_id: '{{ $item->id }}'
                        @endif
                    },
                    success: function(res){
                        console.log(res)
                        var sections = "<option value>This is parent {{ config('settings.section_title') }}</option>"
                        $.each(res, function(i,v){
                            sections += "<option value='"+i+"'>"+v+"</option>"
                        })
                        $("#parent_id").html(sections)
                    }
                })
            })

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


            @if($item->exists)
                setTimeout(function(){
                    $("#company_id").val('')
                }, 400);
                setTimeout(function(){
                    $("#company_id").val('{{ $item->company_id }}').trigger('change')
                }, 500);
                setTimeout(function(){
                    $("#parent_id").val('{{ $item->parent_id }}')
                }, 800);
            @endif


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
                                'route' => $item->exists ? ['settings.sections.update', \Illuminate\Support\Facades\Crypt::encrypt($item->id)] : ['settings.sections.store']
                                ])
                            !!}



                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        {!! Form::label('title', config('settings.section_title').' Name ', ['class' => 'form-label req']) !!}
                                        <span
                                            class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('title') !!}@endif</span>
                                        {!! Form::text('title', null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required']) !!}
                                    </div>
                                </div>
                            </div>



                            <div class="row">

                                <div class="col-6">
                                    <div class="form-group">
                                        {!! Form::label('company_id', 'Select '.config('settings.company_title'), ['class' => 'control-label']) !!}

                                        <span
                                            class="help">@if(Session::has('errors')) {!! Session::get('errors')->first('company_id') !!} @endif</span>
                                        {!! Form::select('company_id', [null=>'Select a '.config('settings.company_title')]+$companies_dd, NULL, ['class' => 'form-select select2', 'id' => 'company_id', 'required' => 'required']) !!}
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        {!! Form::label('parent_id', 'Select a Prent '.config('settings.section_title'), ['class' => 'control-label']) !!}

                                        <span
                                            class="help">@if(Session::has('errors')) {!! Session::get('errors')->first('parent_id') !!} @endif</span>
                                        {!! Form::select('parent_id', [null=>'This is Parent '.config('settings.section_title')], NULL, ['class' => 'form-select select2', 'id' => 'parent_id']) !!}
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

                                    <a href="{{ route('settings.sections.list') }}" class="btn btn-warning">
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
