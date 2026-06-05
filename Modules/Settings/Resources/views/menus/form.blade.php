@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('scripts')

    <script type="text/javascript">
        $(document).ready(function () {
            $("#app_id").change(function () {
                var app_id = $(this).val()
                $.ajax({
                    type: 'post',
                    url: '{{ route('settings.menus.menus-by-app-id') }}',
                    data: {
                        app_id: app_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        var options = "<option value>Select a Menu</option>"
                        $.each(res, function (i, k) {
                            options += "<option value='" + i + "'>" + k + "</option>"
                        })
                        $("#menu_id").html(options)
                    }
                })
            })

            $(".new_route").click(function (e) {
                e.preventDefault()
                var new_route_html = $(".rgr_cont").children('.row').html()

                @if($item->exists)
                var new_route_html_rem = "<div class='row new_route_form'>" +
                    "" + new_route_html + "" +
                    "</div>";
                @else
                var new_route_html_rem = "<div class='row new_route_form'>" +
                    "" + new_route_html + "" +
                    "<div class='col-1'><a class='btn btn-sm btn-danger remove_route'><i class='bx bx-trash text-white'></i></a></div>" +
                    "</div>";
                @endif

                $(".rgr_cont").after(new_route_html_rem)
            })

            $(".rgr_configs").on('click', '.remove_route', function () {
                $(this).closest('.new_route_form').remove()
            })

            $(document).on("change", ".is_default_dd", function () {
                $('.is_default_dd').not(this).children('option:first-child').prop('selected', true);
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
                                'route' => $item->exists ? ['settings.menus.update', \Illuminate\Support\Facades\Crypt::encrypt($item->id)] : ['settings.menus.store']
                                ])
                            !!}


                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        {!! Form::label('title', 'Menu Title ', ['class' => 'form-label req']) !!}
                                        <span
                                            class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('title') !!}@endif</span>
                                        {!! Form::text('title', null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required']) !!}
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="row">

                                        <div class="col-6">
                                            <div class="form-group">
                                                {!! Form::label('app_id', 'Select App ', ['class' => 'control-label']) !!}

                                                <span
                                                    class="help">@if(Session::has('errors')) {!! Session::get('errors')->first('app_id') !!} @endif</span>
                                                {!! Form::select('app_id', [null=>'Select a App']+$apps->toArray(), NULL, ['class' => 'form-select select2', 'id' => 'app_id']) !!}
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                {!! Form::label('parent_id', 'Select parent ', ['class' => 'control-label']) !!}

                                                <span
                                                    class="help">@if(Session::has('errors')) {!! Session::get('errors')->first('parent_id') !!} @endif</span>
                                                {!! Form::select('parent_id', [null=>'This is parent']+$menus_parents->toArray(), NULL, ['class' => 'form-select select2', 'id' => 'parent_id']) !!}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        {!! Form::label('description', 'Description ', ['class' => 'form-label req']) !!}
                                        <span
                                            class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('description') !!}@endif</span>
                                        {!! Form::textarea('description', null, ['class' => 'form-control', 'id' => 'description']) !!}
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        {!! Form::label('icon', 'Menu Icon ', ['class' => 'form-label req']) !!}
                                        <span
                                            class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('icon') !!}@endif</span>
                                        {!! Form::text('icon', null, ['class' => 'form-control', 'id' => 'icon', 'required' => 'required']) !!}
                                    </div>
                                </div>
                                
                                <div class="col-6">
                                    <div class="form-group">
                                        {!! Form::label('order', 'Menu Order ', ['class' => 'form-label req']) !!}
                                        <span
                                            class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('order') !!}@endif</span>
                                        {!! Form::number('order', null, ['class' => 'form-control', 'id' => 'order', 'required' => 'required']) !!}
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
    
                                    <label class="switch switch-square switch-lg switch-success">
                                        <input type="checkbox" class="switch-input" name="is_collapsible" @if($item->exists and $item->is_collapsible == "yes") checked @endif />
                                        <span class="switch-toggle-slider">
    
                                            <span class="switch-on">
                                              <i class="bx bx-check"></i>
                                            </span>
                                            <span class="switch-off">
                                              <i class="bx bx-x"></i>
                                            </span>
                                        </span>
    
                                        <span class="switch-label">Is Collapsible?</span>
                                    </label>
    
                                </div>
                            </div>

                            {{-- <div class="row"> --}}
                            {{-- </div> --}}



                            <div class="row">
                                <div class="col-12">

                                    <a href="{{ route('settings.menus.list') }}" class="btn btn-warning">
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
