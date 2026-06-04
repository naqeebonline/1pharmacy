@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {

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
                                'route' => $item->exists ? ['settings.company-types.update', \Illuminate\Support\Facades\Crypt::encrypt($item->id)] : ['settings.company-types.store']
                                ])
                            !!}


                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        {!! Form::label('title', 'Title ', ['class' => 'form-label req']) !!}
                                        <span
                                            class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('title') !!}@endif</span>
                                        {!! Form::text('title', null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required']) !!}
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        {!! Form::label('description', 'Description ', ['class' => 'form-label']) !!}
                                        <span
                                            class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('description') !!}@endif</span>
                                        {!! Form::textarea('description', null, ['class' => 'form-control', 'id' => 'description', 'rows' => 3]) !!}
                                    </div>
                                </div>
                            </div>




                            <div class="row">
                                <div class="col-12">

                                    <a href="{{ route('settings.company-types.list') }}" class="btn btn-warning">
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
