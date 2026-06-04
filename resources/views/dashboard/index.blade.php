@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@section('content')

    <style>
        .tags {
            list-style: none;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 0;
            width: 95%;
            margin: 0 auto;

        }

        .tags li {
            padding: 0 20px;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .tags li.warning:after {
            background-color: orange;
        }

        .tags li.success:after {
            background-color: green;
        }

        .tags li.danger:after {
            background-color: red;
        }

        .tags li:after {
            content: '';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 0px;
            width: 10px;
            border-radius: 10px;
            height: 10px;
        }



    </style>

    <div class="row">



        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-label-warning"
                            ><i class="bx bx-buildings fs-4"></i
                                ></span>
                            </div>
                            <div class="card-info">
                                <small class="text-muted">Polling Stations</small>
                                <h5 class="card-title mb-0 me-2">{{$total_polling_station}}</h5>
                            </div>
                        </div>
                        <div id="incomeChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-label-warning"
                            ><i class="bx bx-buildings fs-4"></i
                                ></span>
                            </div>
                            <div class="card-info">
                                <small class="text-muted">Most Sensitive</small>
                                <h5 class="card-title mb-0 me-2">{{$most_sensitive}}</h5>
                            </div>
                        </div>
                        <div id="incomeChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-label-warning"
                            ><i class="bx bx-buildings fs-4"></i
                                ></span>
                            </div>
                            <div class="card-info">
                                <small class="text-muted">Sensitive</small>
                                <h5 class="card-title mb-0 me-2">{{$sensitive}}</h5>
                            </div>
                        </div>
                        <div id="incomeChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-label-warning"
                            ><i class="bx bx-buildings fs-4"></i
                                ></span>
                            </div>
                            <div class="card-info">
                                <small class="text-muted">Normal</small>
                                <h5 class="card-title mb-0 me-2">{{$normal}}</h5>
                            </div>
                        </div>
                        <div id="incomeChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-label-primary"
                            ><i  class="bx bx-user fs-4"></i
                                ></span>
                            </div>
                            <div class="card-info">
                                <small class="text-muted">Total Police Stations</small>
                                <h5 class="card-title mb-0 me-2">{{$total_police_stations}}</h5>
                            </div>
                        </div>
                        <div id="conversationChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-label-danger"
                            ><i class="bx bx-location-plus fs-4"></i
                                ></span>
                            </div>
                            <div class="card-info">
                                <small class="text-muted">Health Services</small>
                                <h5 class="card-title mb-0 me-2">{{$total_hospitals}}</h5>
                            </div>
                        </div>
                        <div id="profitChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-label-danger"
                            ><i class="bx bx-location-plus fs-4"></i
                                ></span>
                            </div>
                            <div class="card-info">
                                <form action="{{ route('send.sendNotificationDashboard') }}" method="POST">
                                    @csrf

                                    <input type="hidden" value="{{rand(10,50)}} New University Announcemnet" class="form-control" name="title">
                                    <input type="hidden" value="{{rand(220,890)}} university announcment are here" class="form-control" name="body">



                                    <button type="submit" class="btn btn-success btn-block">Send Alert</button>
                                </form>

                            </div>
                        </div>
                        <div id="profitChart"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="nav-align-top mb-4">
                <ul class="nav nav-tabs nav-fill" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-home" aria-controls="navs-justified-home" aria-selected="true">
                            <i class="tf-icons bx bx-home me-1"></i> Active Meetings
                            <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-danger ms-1">{{count($meetings)}}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-profile" aria-controls="navs-justified-profile" aria-selected="false" tabindex="-1">
                            <i class="tf-icons bx bx-user me-1"></i> Archive Meetings
                            <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-info ms-1">{{count($archive_meetings)}}</span>
                        </button>
                    </li>

                </ul>
                <div class="tab-content" style="height: 430px; overflow-y: scroll">
                    <div class="tab-pane fade active show" id="navs-justified-home" role="tabpanel">
                        {{--<button
                                type="button"
                                class="btn btn-primary m-1"
                                data-bs-toggle="modal"
                                data-bs-target="#animationModal"
                                style="width: 200px; color: white"
                        >
                            Create New Meeting
                        </button>--}}

                                <ul class="timeline timeline-dashed mt-4">
                                    @foreach($meetings as $key => $value)
                                        <?php


                                        $color = "info";
                                        if($value["agenda"] == "Emergency High Alert")
                                            $color = "danger";
                                        else if($value["agenda"] == "Hospital/BHUs")
                                            $color = "warning";
                                        else
                                            $color = "info";
                                        ?>
                                        <li class="timeline-item timeline-item-{{$color}} mb-4">
                          <span class="timeline-indicator timeline-indicator-{{$color}}">
                            <i class="bx bx-user-circle"></i>
                          </span>
                                            <div class="timeline-event">
                                                <div class="timeline-header">
                                                    <h6 class="mb-0">{{$value["agenda"]}}</h6>
                                                    <small class="text-muted">{{date("d M Y",strtotime($value['created_at']))}}</small>
                                                </div>
                                                <p>
                                                    {{$value["topic"]}}
                                                    <br>
                                                    Meeting ID: {{$value["zoom_meeting_id"]}}
                                                    <br>
                                                    Start Time: {{date("d M Y h:i A",strtotime($value->json_response->data->start_time))}}
                                                </p>

                                                <div class="d-flex justify-content-end flex-wrap gap-2">

                                                    <div class="d-flex flex-wrap align-items-centers cursor-pointer" >

                                                        {{--<a   onClick="MyWindow=window.open('{{$value["join_url"]}}','MyWindow','width=600,height=300'); return false;" class="btn btn-{{$color}}" style="color:white;float: right !important;" target="popup"><i class="bx bx-video"></i> Join</a>
                                                        <a onClick="MyWindow=window.open(base_url+'zoom/index.html?id={{$value->zoom_meeting_id}}&pass={{$value->encrypted_password}}&title={{$value->topic}}&email=abcd@gmail.com&meeting_role=1','MyWindow','width=600,height=600'); return false;"   class="bx bx-video" style="color: green; float: left !important;"></a>--}}
                                                        <button onClick="MyWindow=window.open('{{$value["join_url"]}}','MyWindow','width=600,height=300'); return false;" type="button" class="btn btn-xs btn-success"><i class="bx bx-video"></i> Join Zoom</button> &nbsp;&nbsp;&nbsp;
                                                        <button  type="button" onClick="MyWindow=window.open(base_url+'zoom/index.html?id={{$value->zoom_meeting_id}}&pass={{$value->encrypted_password}}&title={{$value->topic}}&email={{auth()->user()->email}}&meeting_role=1&user={{auth()->user()->name}}','MyWindow','width=600,height=600'); return false;" class="btn btn-xs btn-primary"><i class="bx bx-video"></i> Join</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>


                    </div>
                    <div class="tab-pane fade" id="navs-justified-profile" role="tabpanel">
                        <ul class="timeline timeline-dashed mt-4">

                            @foreach($archive_meetings as $key => $value)
                                <?php


                                $color = "info";
                                if($value["agenda"] == "Emergency High Alert")
                                    $color = "danger";
                                else if($value["agenda"] == "Hospital/BHUs")
                                    $color = "warning";
                                else
                                    $color = "info";
                                ?>
                                <li class="timeline-item timeline-item-{{$color}} mb-4">
                          <span class="timeline-indicator timeline-indicator-{{$color}}">
                            <i class="bx bx-user-circle"></i>
                          </span>
                                    <div class="timeline-event">
                                        <div class="timeline-header">
                                            <h6 class="mb-0">{{$value["agenda"]}}</h6>
                                            <small class="text-muted">{{date("d M Y",strtotime($value['created_at']))}}</small>
                                        </div>
                                        <p>
                                            {{$value["topic"]}}
                                            <br>
                                            Meeting ID: {{$value["zoom_meeting_id"]}}
                                            <br>
                                            Start Time: {{date("d M Y h:i A",strtotime($value->start_time))}}
                                        </p>

                                        <div class="d-flex justify-content-end flex-wrap gap-2">
                                            <div class="d-flex flex-wrap align-items-centers cursor-pointer" >
                                                <button onClick="MyWindow=window.open('{{$value["join_url"]}}','MyWindow','width=600,height=300'); return false;" type="button" class="btn btn-xs btn-success"> Join Zoom</button>&nbsp;&nbsp;&nbsp;
                                                <button  type="button" onClick="MyWindow=window.open(base_url+'zoom/index.html?id={{$value->zoom_meeting_id}}&pass={{$value->encrypted_password}}&title={{$value->topic}}&email={{auth()->user()->email}}&meeting_role=1&user={{auth()->user()->name}}','MyWindow','width=600,height=600'); return false;" class="btn btn-xs btn-primary"><i class="bx bx-video"></i> Join</button>


                                            </div>
                                            @if(date("Ymdhis",strtotime($value->end_time)) - date("Ymdhis")  > 0 )

                                                <button type="button" class="btn btn-xs btn-success">Active</button>
                                            @else

                                                <button type="button" class="btn btn-xs btn-danger">Expired</button>
                                            @endif


                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>

        </div>

        <div class="col-xl-6">
            <div class="card">
                <h5 class="card-header">Meeting Invitation</h5>
                <div class="card-body" style="height: 400px; overflow-y: scroll">
                    <ul class="timeline timeline-dashed mt-4">

                        @foreach($meeting_invitation as $key => $value)
                            <?php
                            $color = "danger";
                            $value = $value->meetings;

                            ?>
                            <li class="timeline-item timeline-item-{{$color}} mb-4">
                          <span class="timeline-indicator timeline-indicator-{{$color}}">
                            <i class="bx bx-user-circle"></i>
                          </span>
                                <div class="timeline-event">
                                    <div class="timeline-header">
                                        <h6 class="mb-0">{{$value["agenda"]}}</h6>
                                        <small class="text-muted">{{date("d M Y",strtotime($value['created_at']))}}</small>
                                    </div>
                                    <p>
                                        {{$value["topic"]}}
                                        <br>
                                        Meeting ID: {{$value->zoom_meeting_id}}
                                        <br>
                                        Start Time: {{date("d M Y h:i A",strtotime($value->start_time))}}<br>
                                        Created By: <button type="button" class="btn btn-xs btn-info">{{$value->createdByUser->name}}</button>
                                    </p>

                                    <div class="d-flex justify-content-end flex-wrap gap-2">


                                        <div class="d-flex flex-wrap align-items-centers cursor-pointer" >
                                            <a onClick="MyWindow=window.open(base_url+'zoom/index.html?id={{$value->zoom_meeting_id}}&pass={{$value->encrypted_password}}&title={{$value->topic}}&email=kkkk@gmail.com&meeting_role=0','MyWindow','width=600,height=600'); return false;"   class="btn btn-xs btn-primary" style="color: green"><i class="bx bx-video" style="color:white"></i></a>
                                            {{-- <a   onClick="MyWindow=window.open('{{$value["join_url"]}}','MyWindow','width=600,height=300'); return false;" class="btn btn-{{$color}}" style="color:white;float: right !important;" target="popup"><i class="bx bx-video"></i> Join</a>--}}
                                        </div>
                                        @if(date("Ymdhis",strtotime($value->end_time)) - date("Ymdhis")  > 0 )

                                            <button type="button" class="btn btn-xs btn-success">Active</button>
                                        @else
                                            <button type="button" class="btn btn-xs btn-danger">Expired</button>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach


                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card my-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h4>Search By</h4>
                </div>

                <div class="col-md-3  ">
                    <label>District</label>
                    <select class="form-select select2" id="search_district" multiple="multiple">
                        <option value="">Select District....</option>
                        {{--<option value="{{strtoupper($value->title)}}" lat="{{$value->latitude}}" lng="{{$value->longitude}}">{{strtoupper($value->title)}}</option>--}}
                        @foreach($districts as $key => $value)
                            <option value="{{$value->id}}" lat="{{$value->latitude}}" lng="{{$value->longitude}}">{{strtoupper($value->title)}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 ">
                    <label>Police Station</label>
                    <select class="form-select select2" multiple="multiple" id="police_station_id">
                        <option value="">Select Police Station......</option>
                        @foreach($police_stations as $key => $value)
                            <option value="{{strtoupper($value->id)}}" lat="{{$value->latitude}}" lng="{{$value->longitude}}">{{strtoupper($value->title)}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 ">
                    <label>Polling Station Sensitivity</label>
                    <select class="form-select select2" multiple="multiple" id="ps_sensitivity">
                        @foreach($sensitivity as $key => $value)
                            <option   value="{{$value->id}}">{{$value->title}}</option>
                        @endforeach

                    </select>
                </div>

                {{--<div class="col-md-3 ">
                    <select class="form-select select2">
                        <option value="">Select Polling Station......</option>
                        @foreach($polling_stations as $key => $value)
                            <option value="{{strtoupper($value->id)}}" lat="{{$value->lat}}" lng="{{$value->lng}}">{{strtoupper($value->polling_station_name)}}</option>
                        @endforeach
                    </select>
                </div>--}}

                {{--<div class="col-md-3 ">
                    <select class="form-select select2">
                        <option value="">Select Hospital......</option>
                        @foreach($hospitals as $key => $value)
                            <option value="{{strtoupper($value->id)}}" lat="{{$value->lat}}" lng="{{$value->lng}}">{{strtoupper($value->name)}}</option>
                        @endforeach
                    </select>
                </div>--}}

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-lg-12">
           <div class="card">
               <div class="card-body">
                   <ul class="tags">
                       <li class="success"> Police Stations</li>
                       <li class="warning"> Polling Stations</li>
                       <li class="danger"> Hospitals </li>
                   </ul>
                   {{--<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3308.331328072957!2d71.42666777454747!3d33.98402242133152!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x38d9105f342ff529%3A0x5b4ea1317180c1a7!2sKPITB%20Rd%2C%20Hayatabad%2C%20Peshawar%2C%20Khyber%20Pakhtunkhwa%2C%20Pakistan!5e0!3m2!1sen!2s!4v1702461233830!5m2!1sen!2s"  style="border:0;min-height: 400px;width: 100%;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>--}}
                   <div id="map" class="" style="height: 500px">
               </div>
           </div>
        </div>
    </div>


    <div
            class="modal fade animate__animated fadeIn"
            id="animationModal"
            tabindex="-1"
            aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel5">Create Zoom Meeting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameAnimation" class="form-label">Meeting Agenda</label>
                            <select class="form-control" id="meeting_type">
                                <option value="">Select Meeting Type...</option>
                                <option value="Emergency High Alert">Emergency</option>
                                <option value="Hospital/BHUs">Hospital/BHUs</option>
                                <option value="Edhi">Edhi</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameAnimation" class="form-label">Meeting Topic</label>
                            <input type="text" id="meeting_title" class="form-control">
                        </div>
                    </div>



                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary create_new_meeting">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script type="text/javascript">
        function playSound(url) {
            const audio = new Audio(url);
            audio.play();
        }

        $("body").on("click",".create_new_meeting",function () {
            $(this).hide();
            var meeting_type = $("#meeting_type").val();
            var meeting_title = $("#meeting_title").val();
            if(meeting_type == ""){
                alert("Please Select Meeting Type");
                $(".create_new_meeting").show();
                return false;
            }
            if(meeting_title == ""){
                alert("Please Enter Meeting Title");
                $(".create_new_meeting").show();
                return false;
            }
            $.ajax({
                type: 'post',
                url: "{{ route('create-meeting') }}",
                data: {
                    meeting_type: meeting_type,
                    meeting_title: meeting_title,
                    _token: '{{ csrf_token() }}'

                },
                success: function(res) {
                    alert("Meeting Has Been Created Successfully");
                    $(".create_new_meeting").show();
                    window.location.reload();


                }
            });
        });

        $("body").on("click",".send_alert",function () {


            $.ajax({
                type: 'post',
                url: "{{ route('emergency_alert') }}",
                data: {
                    title: "High Alert Notification",
                    message: "Emegency at Polling Station 1 Please all police stations",
                    _token: '{{ csrf_token() }}'

                },
                success: function(res) {
                    alert("Alert Has Been sent");



                }
            });
        });


    </script>


            <script>
                // var kp_bhus;
                var kp_boundary;
                var myMap;
                var lyrGoogleMap;
                var lyrGoogleHybrid;
                var lyrOSM;
                var crimeLocations;
                current_lat = 34.9526;
                current_lng = 72.3311;
                selected_districts = "";
                police_station_ids = "";
                sensitivity_type = "";

                $("body").on("change","#search_district",function (e) {

                    selected_districts = $(this).val();
                //    var element = $(this).find('option:selected');

                    loadBoundaryData();
                });

                $("body").on("change","#police_station_id",function (e) {
                    police_station_ids = $(this).val();
                //    var element = $(this).find('option:selected');

                    loadBoundaryData();
                });

                $("body").on("change","#ps_sensitivity",function (e) {
                    sensitivity_type = $(this).val();
                //    var element = $(this).find('option:selected');

                    loadBoundaryData();
                });

                window.onload = function() {
                    initMap();
                   // loadBoundaryData();

                    loadHospitals();
                    getAllPoliceStations();
                    getAllPoliceMobiles();
                    getAllPollingStations();
                   // loadIcon();
                }

                // Initialize map
                function reinitMap(lat,lng) {
                    myMap.off();
                    myMap.remove();
                    myMap = L.map('map', {
                        center: [lat,lng],
                        zoom: 8,
                        zoomControl: true
                    });

                    //add basemap layer
                    lyrGoogleMap = L.tileLayer('http://mts3.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                        maxZoom: 22,
                        maxNativeZoom: 12
                    }); //lyrGoogleMa

                    lyrGoogleHybrid = L.tileLayer('http://mts2.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                        maxZoom: 22,
                        maxNativeZoom: 12
                    }); //lyrGoogleHybrid

                    lyrOSM = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 22,
                        maxNativeZoom: 12
                    }); //OSM

                    myMap.addLayer(lyrGoogleMap);
                    objBaseMap = {
                        "Google Map": lyrGoogleMap,
                        "Google Hybrid": lyrGoogleHybrid,
                        "OSM map": lyrOSM
                    }
                    L.control.layers(objBaseMap).addTo(myMap);
                    ctlPan = L.control.pan().addTo(myMap);
                    ctlMousePosition = L.control.mousePosition().addTo(myMap);
                }

                function initMap() {
                    myMap = L.map('map', {
                        center: [34.9526,72.3311],
                        zoom: 8,
                        zoomControl: true
                    });

                    //add basemap layer
                    lyrGoogleMap = L.tileLayer('http://mts3.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                        maxZoom: 22,
                        maxNativeZoom: 12
                    }); //lyrGoogleMa

                    lyrGoogleHybrid = L.tileLayer('http://mts2.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                        maxZoom: 22,
                        maxNativeZoom: 12
                    }); //lyrGoogleHybrid

                    lyrOSM = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 22,
                        maxNativeZoom: 12
                    }); //OSM

                    myMap.addLayer(lyrGoogleMap);
                    objBaseMap = {
                        "Google Map": lyrGoogleMap,
                        "Google Hybrid": lyrGoogleHybrid,
                        "OSM map": lyrOSM
                    }
                    L.control.layers(objBaseMap).addTo(myMap);
                    ctlPan = L.control.pan().addTo(myMap);
                    ctlMousePosition = L.control.mousePosition().addTo(myMap);

                }

                function loadHospitals() {
                    //https://img.lovepik.com/free-png/20210926/lovepik-car-icon-png-image_401452256_wh1200.png
                    var blueIcon = L.icon.pulse({iconSize:[10,10], color: "blue", fillColor: "blue", animate: false});
                    var redIcon = L.icon.pulse({ iconSize:[10,10],color: "red", fillColor: "red", animate: false});
                    var yellowIcon = L.icon.pulse({ iconSize:[10,10],color: "yellow", fillColor: "yellow", animate: false});
                    var orangeIcon = L.icon.pulse({ iconSize:[10,10],color: "orange", fillColor: "orange", animate: true});


                    new L.geoJson.ajax("{{ URL::to('api/v1/getAllHospitals') }}?district_id="+selected_districts+"&police_station_id="+police_station_ids, {
                        middleware: function(data) {

                            return L.geoJson(data, {

                                onEachFeature: function(feature, layer) {
                                    if (feature.properties.type == 1) {
                                        var mymarker = L.marker([feature.properties.lat,feature.properties.lng], {icon: redIcon}).addTo(myMap);
                                        mymarker.bindPopup(`
                                            <div style="line-height:0.2rem">
                                            <h6>Hospital</h6>
                                            <p><b>Name:</b> ${feature.properties.name}</p>
                                            <p><b>Contact:</b> ${feature.properties.contact_number}</p>
                                            <a target="_blank" href="${base_url}edit-hospital/${feature.properties.id}">View Details<a>
                                            </div>
                                        `);

                                    }
                                    else if (feature.properties.type == 2) {

                                        var mymarker = L.marker([feature.properties.lat,feature.properties.lng], {icon: orangeIcon}).addTo(myMap);
                                        mymarker.bindPopup(`
                                            <div style="line-height:0.2rem">
                                            <h6>Edhi Center</h6>
                                            <p><b>Name:</b> ${feature.properties.name}</p>
                                            <p><b>Contact:</b> ${feature.properties.contact_number}</p>
                                            <a target="_blank" href="${base_url}edit-hospital/${feature.properties.id}">View Details<a>
                                            </div>
                                        `);

                                    }
                                    else if (feature.properties.type == 3) {
                                        var mymarker = L.marker([feature.properties.lat,feature.properties.lng], {icon: yellowIcon}).addTo(myMap);
                                        mymarker.bindPopup(`
                                            <div style="line-height:0.2rem">
                                            <h6>Chhipa Centers</h6>
                                            <p><b>Name:</b> ${feature.properties.name}</p>
                                            <p><b>Contact:</b> ${feature.properties.contact_number}</p>
<a target="_blank" href="${base_url}edit-hospital/${feature.properties.id}">View Details<a>
                                            </div>
                                        `);


                                    }else{
                                        var mymarker = L.marker([feature.properties.lat,feature.properties.lng], {icon: blueIcon}).addTo(myMap);
                                        mymarker.bindPopup(`
                                            <div style="line-height:0.2rem">
                                            <h6>Rescue 1122</h6>
                                            <p><b>Name:</b> ${feature.properties.name}</p>
                                            <p><b>Contact:</b> ${feature.properties.contact_number}</p>
                                            <a target="_blank" href="${base_url}edit-hospital/${feature.properties.id}">View Details<a>
                                            </div>
                                        `);

                                    }

                                }
                            }).addTo(myMap);
                        }
                    });



                }




                search_district_name = '';
                function loadBoundaryData() {


                    reinitMap(current_lat,current_lng);
                    /*if (myMap.hasLayer(kp_boundary)) {
                        myMap.removeLayer(kp_boundary);

                    }
                    kp_boundary = L.geoJson.ajax('theme/plugins/leaflet/boundary_data/kp_district_boundary.geojson',{filter: districts}).bindPopup(function(kp_boundary) {
                        return kp_boundary.feature.properties.DISTRICT;
                    }).addTo(myMap);*/


                    getAllPoliceStations();
                    getAllPollingStations();
                    loadHospitals();

                    /*loadHospitals();
                    getAllPoliceStations();
                    getAllPoliceMobiles();
                    getAllPollingStations();*/
                }

                function districts(feature){

                    if (feature.properties.DISTRICT === search_district_name) {
                        return true;
                    }
                }

                function getAllPoliceStations() {
                    var greenIcon = L.icon.pulse({ iconSize:[10,10],color: "green", fillColor: "green", animate: false});

                    new L.geoJson.ajax("{{ URL::to('api/v1/getAllPoliceStation') }}?district_id="+selected_districts+"&police_station_id="+police_station_ids, {
                        middleware: function(data) {

                            return L.geoJson(data, {

                                onEachFeature: function(feature, layer) {
                                        if(feature.properties.latitude !='' && feature.properties.longitude !=''){
                                            var mymarker = L.marker([parseFloat(feature.properties.latitude).toFixed(4),parseFloat(feature.properties.longitude).toFixed(4)], {icon: greenIcon}).addTo(myMap);
                                            mymarker.bindPopup(`
                                           <div style="line-height:0.2rem">
                                            <h6>Police Stations</h6>
                                            <p><b>Name:</b> ${feature.properties.title}</p>
                                            <p><b>Contact:</b> ${feature.properties.ps_contact_number}</p>
                                            <p><b>SHO Contact:</b> ${feature.properties.sho_contact}</p>
                                            <a target="_blank" href="${base_url}edit-police-station/${feature.properties.id}">View Details<a>

                                            </div>
                                        `);
                                        }





                                }
                            }).addTo(myMap);
                        }
                    });


                }

                function getAllPollingStations() {
                    var greenIcon = L.icon.pulse({ iconSize:[10,10],color: "#faa5a5", fillColor: "#faa5a5", animate: true});


                    new L.geoJson.ajax("{{ URL::to('api/v1/getAllPollingStations') }}?district_id="+selected_districts+"&police_station_id="+police_station_ids+"&sensitivity_type="+sensitivity_type, {
                        middleware: function(data) {

                            return L.geoJson(data, {

                                onEachFeature: function(feature, layer) {


                                        var mymarker = L.marker([parseFloat(feature.properties.lat).toFixed(4),parseFloat(feature.properties.lng).toFixed(4)], {icon: greenIcon}).addTo(myMap);
                                        mymarker.bindPopup(`
                                           <div style="line-height:0.2rem">
                                            <h6>Polling Stations</h6>
                                            <p><b>PS Name:</b> ${feature.properties.polling_station_name}</p>
                                            <p><b>Name:</b> ${feature.properties.incharge_name}</p>
                                            <p><b>Contact:</b> ${feature.properties.incharge_contact}</p>
                                            <p><b>type:</b> ${feature.properties.sensitivity}</p>

                                            <a target="_blank" href="${base_url}edit-polling-station/${feature.properties.id}">View Details<a>

                                            </div>
                                        `);






                                }
                            }).addTo(myMap);
                        }
                    });


                }


                function getAllPoliceMobiles() {

                    $.ajax({
                        url: '{{ route("getAllPoliceMobiles") }}/', // Replace with your actual URL
                        method: 'GET',
                        success: function (response) {
                            var blackIcon = L.icon.pulse({ color: "black", fillColor: "black", animate: true});
                            $.each(response.data, function (index, item) {

                                if(item.lat != null && item.lng != null){
                                    var myIcon = L.icon({
                                        iconUrl: 'https://w7.pngwing.com/pngs/5/851/png-transparent-marker-map-icon-car-location-automobile-vehicle-target-design.png',
                                        iconSize: [20, 30],
                                        iconAnchor: [22, 94],
                                    });
                                    var mymarker = L.marker([item.lat,item.lng], {icon: myIcon}).addTo(myMap);
                                    mymarker.bindPopup(`
                                            <div style="line-height:0.2rem">
                                            <h6>Police Mobile</h6>
                                            <p><b>Registration#:</b> ${item.registration_number}</p>
                                            <p><b>Incharge Name:</b> ${item.incharge_name}</p>
                                            <p><b>Contact:</b> ${item.contact_number}</p>

                                            </div>
                                        `);
                                }



                            });

                        }
                    });
                }
            </script>




@endpush

        @push('vendor-style')

            <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
        @endpush

        @push('scripts')
            <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
            <script src="{{ asset('assets/js/app-custom.js') }}"></script>
    @endpush
