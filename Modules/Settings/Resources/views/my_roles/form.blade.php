@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp


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
                                'route' => $item->exists ? ['settings.my-roles.update', \Illuminate\Support\Facades\Crypt::encrypt($item->id)] : ['settings.my-roles.store']
                                ])
                            !!}


                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        {!! Form::label('name', 'Role Title ', ['class' => 'form-label req']) !!}
                                        <span
                                            class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('name') !!}@endif</span>
                                        {!! Form::text('name', null, ['class' => 'form-control', 'id' => 'name', 'required' => 'required']) !!}
                                    </div>
                                </div>
                            </div>


                            <div class="row">

                                <div class="col-6">
                                    <div class="form-group">
                                        {!! Form::label('slug', 'Role Slug ', ['class' => 'form-label req']) !!}
                                        <span
                                            class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('slug') !!}@endif</span>
                                        {!! Form::text('slug', null, ['class' => 'form-control', 'id' => 'slug', 'required' => 'required']) !!}
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        {!! Form::label('level', 'Role Level ', ['class' => 'form-label req']) !!}
                                        <span
                                            class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('level') !!}@endif</span>
                                        {!! Form::number('level', null, ['class' => 'form-control', 'id' => 'level', 'required' => 'required', 'min' => 0]) !!}
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        {!! Form::label('description', 'Details ', ['class' => 'form-label']) !!}
                                        <span
                                            class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('description') !!}@endif</span>
                                        {!! Form::textarea('description', null, ['class' => 'form-control', 'id' => 'description']) !!}
                                    </div>
                                </div>
                            </div>








                            <div class="row">
                                <div class="col-12">

                                    <a href="{{ route('settings.my-roles.list') }}" class="btn btn-warning">
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
