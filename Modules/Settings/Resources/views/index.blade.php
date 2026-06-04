@extends('layouts.app_screen_l3')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from module: {!! config('settings.name') !!}
    </p>
@endsection
