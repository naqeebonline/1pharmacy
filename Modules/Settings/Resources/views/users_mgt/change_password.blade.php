{{--@extends('layouts.'.config('settings.active_layout'))--}}
@extends('layouts.'.config('eidentity.active_layout'))
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


                            <form action="{{ route('settings.users-mgt.change-password-save') }}" method="post">


                                <input type="hidden" name="_token" value="{{ csrf_token() }}">


                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('new_password', 'New Password ', ['class' => 'form-label req']) !!}
                                            <span class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('new_password') !!} @endif</span>
                                            <input type="password" name="new_password" id="new_password" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('conf_new_password', 'Confirm New Password ', ['class' => 'form-label req']) !!}
                                            <span class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('conf_new_password') !!} @endif</span>
                                            <input type="password" name="conf_new_password" id="conf_new_password" class="form-control" required>
                                        </div>
                                    </div>

                                </div>




                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('current_password', 'Current Password ', ['class' => 'form-label req']) !!}
                                            <span class="help">@if(session()->has('errors')) {!! session()->get('errors')->first('current_password') !!} @endif</span>
                                            <input type="password" name="current_password" id="current_password" class="form-control" required>
                                            <span class="-help text-primary">Please provide your current password to save changes</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">

                                        <button class="btn btn-danger">
                                            <i class="icon-undo mr-1"></i> Reset
                                        </button>

                                        <button type="submit" class="btn btn-success">
                                            <i class="icon-database-check mr-1"></i> Update Password
                                        </button>

                                    </div>
                                </div>

                            </form>


                        </div>

                    </div>

                </div>


            </div>
            <!-- /traffic sources -->

        </div>
    </div>




@endsection
