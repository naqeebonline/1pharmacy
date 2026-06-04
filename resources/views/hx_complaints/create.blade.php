@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Record HX Complaint</h3>
                    <a href="{{ route('hx-complaints.index') }}" class="btn btn-secondary btn-sm float-right">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('hx-complaints.store') }}" method="POST">
                        @csrf

                        <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                        <input type="hidden" name="patient_id" value="{{ $appointment->patient_id }}">

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Patient Information</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <p><strong>MR No:</strong> {{ $appointment->patient->mr_no ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><strong>Patient Name:</strong> {{ $appointment->patient->name ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><strong>Phone:</strong> {{ $appointment->patient->phone ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <p><strong>Age:</strong> {{ $appointment->patient->age ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><strong>Gender:</strong> {{ $appointment->patient->gender ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><strong>Appointment Time:</strong> {{ $appointment->appointment_time ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mb-3">Vital Signs</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="bp">Blood Pressure (BP)</label>
                                    <input type="text" class="form-control" id="bp" name="bp"
                                        placeholder="e.g., 120/80" value="{{ old('bp') }}">
                                    <small class="form-text text-muted">Format: 120/80</small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="temp">Temperature (Temp)</label>
                                    <input type="text" class="form-control" id="temp" name="temp"
                                        placeholder="e.g., 98.6°F or 37°C" value="{{ old('temp') }}">
                                    <small class="form-text text-muted">Format: 98.6°F or 37°C</small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="pulse">Pulse Rate</label>
                                    <input type="text" class="form-control" id="pulse" name="pulse"
                                        placeholder="e.g., 72 bpm" value="{{ old('pulse') }}">
                                    <small class="form-text text-muted">Format: 72 bpm</small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="rr">Respiratory Rate (R/R)</label>
                                    <input type="text" class="form-control" id="rr" name="rr"
                                        placeholder="e.g., 16/min" value="{{ old('rr') }}">
                                    <small class="form-text text-muted">Format: 16/min</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="complaint">Chief Complaint</label>
                                    <textarea class="form-control" id="complaint" name="complaint"
                                        rows="4" placeholder="Enter patient's chief complaint">{{ old('complaint') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="investigation">Investigation</label>
                                    <textarea class="form-control" id="investigation" name="investigation"
                                        rows="4" placeholder="Enter investigation details">{{ old('investigation') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Save HX Complaint
                                </button>
                                <a href="{{ route('hx-complaints.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection