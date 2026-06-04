@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@section('content')

    <div class="row">
        <div class="col-12">

            <!-- Traffic sources -->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">{{ $title }}</h6>
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

                            <div class="table-responsive" style="min-height: 200px">

                                <table class="table table-striped data_mf_table">

                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Level</th>
                                        <th>Parent</th>
                                        <th>No. of Sub {{ str_plural(config('settings.company_title')) }}</th>
                                        <th>Division</th>
                                        <th>District</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $item->title }}</strong></td>
                                            <td>{{ $item->type->title ?? "" }}</td>
                                            <td>{{ $item->level->title ?? "" }}</td>
                                            <td>{{ $item->parent->title ?? "" }}</td>
                                            <td>{{ $item->children->count() ?? 0 }}</td>
                                            <td>{{ $item->division->title ?? "" }}</td>
                                            <td>{{ $item->district->title ?? "" }}</td>
                                            <td style="width: 180px">


                                                <a href="{{ route('settings.companies.edit', ['id' => \Illuminate\Support\Facades\Crypt::encrypt($item->id)]) }}" class="btn btn-warning btn-icon">
                                                    <i class="tf-icons bx bx-pencil"></i>
                                                </a>

                                                {!! Form::open(['method' => 'delete', 'route' => ['settings.companies.delete',\Illuminate\Support\Facades\Crypt::encrypt($item->id)], 'class' => 'dropdown-item delete', 'style' => 'display:inline; padding: 0px']) !!}
                                                {!! Form::button('<i class="bx bx-trash tf-icons"></i>', array('class'=>'btn btn-danger btn-icon ', 'type'=>'submit')) !!}
                                                {!! Form::close() !!}


                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>


            </div>
            <!-- /traffic sources -->

        </div>
    </div>

@endsection
