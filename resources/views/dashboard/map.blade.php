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

        .text_height_map{
            line-height: 18px;
        }



    </style>


    <div class="row">


        <div class="col-lg-2 col-md-2 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">

                            <div class="card-info">
                                <small class="text-muted">Police Stations</small>
                                <h6 class="card-title mb-0 me-2 text-center">{{$total_police_stations}}</h6>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">

                            <div class="card-info">
                                <small class="text-muted">Polling Stations</small>
                                <h6 class="card-title mb-0 me-2 text-center">{{$total_polling_station}}</h6>
                            </div>
                        </div>
                        <div id="incomeChart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">

                            <div class="card-info">
                                <small class="text-muted">Health Services</small>
                                <h6 class="card-title mb-0 me-2 text-center">{{$total_hospitals}}</h6>
                            </div>
                        </div>
                        <div id="profitChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-2 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">

                            <div class="card-info">
                                <small class="text-muted">Police Lines</small>
                                <h6 class="card-title mb-0 me-2 text-center">{{$police_line}}</h6>
                            </div>
                        </div>
                        <div id="profitChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-2 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">

                            <div class="card-info">
                                <small class="text-muted">Police Post</small>
                                <h6 class="card-title mb-0 me-2 text-center">{{$police_post}}</h6>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-2 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">

                            <div class="card-info">
                                <small class="text-muted">Mobile Vehicle</small>
                                <h6 class="card-title mb-0 me-2 text-center">{{$police_mobile}}</h6>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>




        <div class="col-lg-4 col-md-4 col-sm-4" >
            <div id="chart-container" style="height: 322px"></div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4" >
            <div id="facility-chart" style="height: 322px"></div>
        </div>

        <div class="col-md-4 col-lg-4 col-xl-4 col-xl-4">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between mb-3">
                    <h6 class="card-title mb-0">Polling Booths & Registered Voters</h6>

                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">

                        <li class="d-flex mb-4">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded-circle bg-label-info"><i class="bx bx-male"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <p class="mb-0 lh-1">MALE BOOTHS</p>

                                </div>
                                <div class="item-progress">{{$number_of_male_booth}}</div>
                            </div>
                        </li>
                        <li class="d-flex mb-4">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded-circle bg-label-danger"><i class="bx bx-female"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <p class="mb-0 lh-1">FEMALE BOOTHS</p>

                                </div>
                                <div class="item-progress">{{$number_of_female_booth}}</div>
                            </div>
                        </li>
                        <li class="d-flex mb-4">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-male"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <p class="mb-0 lh-1">MALE VOTERS</p>

                                </div>
                                <div class="item-progress">{{$male_voters}}</div>
                            </div>
                        </li>

                        <li class="d-flex mb-4">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-female"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <p class="mb-0 lh-1">FEMALE VOTERS</p>

                                </div>
                                <div class="item-progress">{{$female_voters}}</div>
                            </div>
                        </li>
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
                    <label>Region</label>
                    <select class="form-select select2" id="search_region" multiple="multiple">
                        <option value="">Select Region....</option>
                        {{--<option value="{{strtoupper($value->title)}}" lat="{{$value->latitude}}" lng="{{$value->longitude}}">{{strtoupper($value->title)}}</option>--}}
                        @foreach($region as $key => $value)
                            <option value="{{$value->id}}">{{strtoupper($value->name)}}</option>
                        @endforeach
                    </select>
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
                    <label>Circle</label>
                    <select class="form-select select2" multiple="multiple" id="circle_id">


                    </select>
                </div>
                <div class="col-md-3 ">
                    <label>Police Station</label>
                    <select class="form-select select2" multiple="multiple" id="police_station_id">


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

                <div class="col-md-4 ">
                    <label>Polling Station</label>
                    <select class="form-select select2" multiple="multiple" id="polling_stations_id">


                    </select>
                </div>

                <div class="col-md-2 ">
                    <label>Search Police Mobile</label>
                    <input class="form-control" name="search_mobile" id="search_mobile">
                </div>

                <div class="col-md-3 ">
                    <div class="btn btn-success checkbox_change mt-4">Search</div>
                </div>





            </div>
        </div>
    </div>

    <div class="card my-3">
        <div class="card-body">
            <div class="row">


                <div class="col-md-3  ">

                    <input type="checkbox" class="checkbox_change" name="police_station" id="police_station_checkbox">
                    <label>Police Stations</label>
                    <span style="color: green" id="police_station_checkbox_count">0</span>
                </div>

                <div class="col-md-3">

                    <input type="checkbox" class="checkbox_change" name="polling_station_che" id="polling_station_checkbox">
                    <label>Polling Stations</label>
                    <span style="color: green" id="polling_station_checkbox_count">0</span>
                </div>

                <div class="col-md-2">

                    <input type="checkbox" class="checkbox_change" name="hospital" id="hospital_checkbox">
                    <label>Hospitals</label>
                    <span style="color: green" id="hospital_checkbox_count">0</span>
                </div>

                <div class="col-md-2">

                    <input type="checkbox" class="checkbox_change" name="police_mobile" id="police_mobile_checkbox">
                    <label>Police Mobiles</label>
                    <span style="color: green" id="police_mobile_checkbox_count">0</span>
                </div>

                <div class="col-md-2">

                    <input type="checkbox" class="checkbox_change" name="police_line" id="police_line_checkbox">
                    <label>Police Lines</label>
                    <span style="color: green" id="police_line_checkbox_count">0</span>
                </div>





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
    <script src="https://code.highcharts.com/highcharts.js"></script>

    <script>
        $("body").on("blur","#search_mobile",function () {
            reload_map();
        });
        // Sample data for the pie chart
        var chartData = [{
            name: 'Most Sensitive',
            y: <?php echo $most_sensitive; ?>
        }, {
            name: 'Sensitive',
            y: <?php echo $sensitive; ?>
        }, {
            name: 'Normal',
            y: <?php echo $normal; ?>
        }];

        // Initialize the pie chart
        Highcharts.chart('chart-container', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Polling Stations'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: false,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y}'
                    }
                }
            },
            series: [{
                name: 'Polling Station',
                colorByPoint: true,
                data: chartData
            }]
        });


        var chartData = <?php echo json_encode($facility_chart) ?>;

        // Initialize the pie chart
        Highcharts.chart('facility-chart', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Health Facility'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.y}</b> '
                    }
                }
            },
            series: [{
                name: 'Health Faciltiy',
                colorByPoint: true,
                data: chartData
            }]
        });


    </script>
    <script type="text/javascript">
        count_checkbox = 0;
        function playSound(url) {
            const audio = new Audio(url);
            audio.play();
        }
        function reload_map(){
            var police_station = $("#police_station_checkbox").is(':checked');
            var hospital_checkbox = $("#hospital_checkbox").is(':checked');
            var police_mobile = $("#police_mobile_checkbox").is(':checked');
            var polling_station_checkbox = $("#polling_station_checkbox").is(':checked');
            var police_line_checkbox = $("#police_line_checkbox").is(':checked');

            $("#hospital_checkbox_count").text(0);
            $("#police_mobile_checkbox_count").text(0);
            $("#police_station_checkbox_count").text(0);
            $("#polling_station_checkbox_count").text(0);
            $("#police_line_checkbox_count").text(0);
            reinitMap();

            if(police_station){
                count_checkbox = 1;
                getAllPoliceStations();

            }
            if(hospital_checkbox){
                count_checkbox = 1;
                loadHospitals();
            }

            if(police_mobile){
                count_checkbox = 1;
                getAllPoliceMobiles();
            }
            if(polling_station_checkbox){
                count_checkbox = 1;
                getAllPollingStations();
            }

            if(police_line_checkbox){
                count_checkbox = 1;
                getAllPoliceLine();
            }
            if(count_checkbox == 0){
                alert("Select minimum one checkbox to load data");
                //loadBoundaryData();
            }
        }
        $("body").on("click",".checkbox_change",function () {
            reload_map();
        });
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
                selected_region = "";
                police_station_ids = "";
                sensitivity_type = "";

                $("body").on("change","#search_region",function (e) {
                    selected_region = $(this).val();
                  if(selected_region != null && selected_region.length > 0){
                      loadDistricts();
                      //loadBoundaryData();
                  }else{
                      current_lat = 34.9526;
                      current_lng = 72.3311;
                     // reinitMap();
                  }
                });

                $("body").on("change","#search_district",function (e) {
                    var option = $('option:selected', this);
                    if(option){
                        current_lat = option.attr("lat");
                        current_lng = option.attr("lng");
                    }
                    selected_districts = $(this).val();
                  if(selected_districts != null && selected_districts.length > 0){
                      loadCircle();
                      allPollingStations();
                      //loadBoundaryData();
                  }else{
                      current_lat = 34.9526;
                      current_lng = 72.3311;
                     // reinitMap();
                  }
                });



                $("body").on("change","#circle_id",function (e) {
                    loadMultiPoliceStations();
                });

                $("body").on("change","#police_station_id",function (e) {

                    police_station_ids = $(this).val();
                    allPollingStations();

                //    var element = $(this).find('option:selected');

                   // loadBoundaryData();
                });

                $("body").on("change","#ps_sensitivity",function (e) {
                    sensitivity_type = $(this).val();
                    allPollingStations();
                //    var element = $(this).find('option:selected');

                    //loadBoundaryData();
                });

                window.onload = function() {
                    initMap();
                   // loadBoundaryData();

                    /*loadHospitals();
                    getAllPoliceStations();
                    getAllPoliceMobiles();
                    getAllPollingStations();*/
                   // loadIcon();
                }

                // Initialize map
                function reinitMap() {
                    myMap.off();
                    myMap.remove();
                    myMap = L.map('map', {
                        center: [current_lat,current_lng],
                        zoom: 8,
                        zoomControl: true
                    });

                    //add basemap layer         http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}
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

                    var fg = L.featureGroup();
                    new L.geoJson.ajax("{{ URL::to('api/v1/getAllHospitals') }}?district_id="+selected_districts+"&police_station_id="+police_station_ids, {
                        middleware: function(data) {
                            $("#hospital_checkbox_count").text(data.features.length);

                            return L.geoJson(data, {

                                onEachFeature: function(feature, layer) {
                                    console.log("getAllHospitals");
                                    if (feature.properties.type == 1) {
                                       // var mymarker = L.marker([feature.properties.lat,feature.properties.lng], {icon: redIcon}).addTo(myMap);


                                        var myIcon = L.icon({
                                            iconUrl: base_url+"markers/hospital.png",
                                            className:"hospital_marker",
                                            iconSize: [30, 30],

                                        });
                                        var mymarker = L.marker([feature.properties.lat,feature.properties.lng], {icon: myIcon}).addTo(myMap);

                                        mymarker.bindPopup(`
                                            <div style="line-height:0.2rem">
                                            <h6>Hospital</h6>
                                            <p class="text_height_map"><b>Name:</b> ${feature.properties.name}</p>
                                            <p class="text_height_map"><b>Contact:</b> ${feature.properties.contact_number}</p>
                                            <a target="_blank" href="${base_url}edit-hospital/${feature.properties.id}">View Details<a>
                                            </div>
                                        `);

                                    }
                                    else if (feature.properties.type == 2) {

                                        //var mymarker = L.marker([feature.properties.lat,feature.properties.lng], {icon: orangeIcon}).addTo(myMap);
                                        var myIcon = L.icon({
                                            iconUrl: base_url+"markers/1122.png",
                                            className:"hospital_marker",
                                            iconSize: [40, 40],

                                        });
                                        var mymarker = L.marker([feature.properties.lat,feature.properties.lng], {icon: myIcon}).addTo(myMap);
                                        mymarker.bindPopup(`
                                            <div style="line-height:0.2rem">
                                            <h6>Edhi Center</h6>
                                            <p class="text_height_map"><b>Name:</b> ${feature.properties.name}</p>
                                            <p class="text_height_map"><b>Contact:</b> ${feature.properties.contact_number}</p>
                                            <a target="_blank" href="${base_url}edit-hospital/${feature.properties.id}">View Details<a>
                                            </div>
                                        `);

                                    }
                                    else if (feature.properties.type == 3) {
                                        //var mymarker = L.marker([feature.properties.lat,feature.properties.lng], {icon: yellowIcon}).addTo(myMap);
                                        var myIcon = L.icon({
                                            iconUrl: base_url+"markers/chippa.png",
                                            className:"hospital_marker",
                                            iconSize: [40, 40],

                                        });
                                        var mymarker = L.marker([feature.properties.lat,feature.properties.lng], {icon: myIcon}).addTo(myMap);
                                        mymarker.bindPopup(`
                                            <div style="line-height:0.2rem">
                                            <h6>Chhipa Centers</h6>
                                            <p class="text_height_map"><b>Name:</b> ${feature.properties.name}</p>
                                            <p class="text_height_map"><b>Contact:</b> ${feature.properties.contact_number}</p>
<a target="_blank" href="${base_url}edit-hospital/${feature.properties.id}">View Details<a>
                                            </div>
                                        `);


                                    }else{
                                        //var mymarker = L.marker([feature.properties.lat,feature.properties.lng], {icon: blueIcon}).addTo(myMap);
                                        var myIcon = L.icon({
                                            iconUrl: base_url+"markers/rescue.png",
                                            className:"hospital_marker",
                                            iconSize: [40, 40],

                                        });
                                        var mymarker = L.marker([feature.properties.lat,feature.properties.lng], {icon: myIcon}).addTo(myMap);

                                        mymarker.bindPopup(`
                                            <div style="line-height:0.2rem">
                                            <h6>Rescue</h6>
                                            <p class="text_height_map"><b>Name:</b> ${feature.properties.name}</p>
                                            <p class="text_height_map"><b>Contact:</b> ${feature.properties.contact_number}</p>
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

                function loadDistricts() {

                   var regions = $("#search_region").val();
                    $.ajax({
                        url: '{{ route("getMultiDistricts") }}' , // Replace with your actual URL
                        method: 'post',
                        data: {
                            regions: regions,
                            _token: '{{ csrf_token() }}'

                        },
                        success: function (response) {

                            var psOptions = '<option value="">Select District</option>';
                            $.each(response.data, function (index, item) {
                                psOptions += '<option value="' + item.id + '" lat="' + item.latitude + '" lng="' + item.longitude + '">' + item.title + '</option>';
                            });
                            $("#search_district").html(psOptions);
                        }
                    });
                }

                function allPollingStations() {

                   var districts = $("#search_district").val();
                   var police_station_id = $("#police_station_id").val();
                   var ps_sensitivity = $("#ps_sensitivity").val();
                    $.ajax({
                        url: '{{ route("getAllPollingStations") }}' , // Replace with your actual URL
                        method: 'post',
                        data: {
                            districts: districts,
                            police_station_id: police_station_id,
                            ps_sensitivity: ps_sensitivity,
                            _token: '{{ csrf_token() }}'

                        },
                        success: function (response) {

                            var psOptions = '<option value="">Select District</option>';
                            $.each(response.data, function (index, item) {
                                psOptions += '<option value="' + item.id + '">' + item.polling_station_name + '</option>';
                            });
                            $("#polling_stations_id").html(psOptions);
                        }
                    });
                }

                function loadCircle() {

                   var districts = $("#search_district").val();
                    $.ajax({
                        url: '{{ route("getMultiCircles") }}' , // Replace with your actual URL
                        method: 'post',
                        data: {
                            districts: districts,
                            _token: '{{ csrf_token() }}'

                        },
                        success: function (response) {

                            var psOptions = '<option value="">Select Circle</option>';
                            $.each(response.data, function (index, item) {
                                psOptions += '<option value="' + item.id + '">' + item.name + '</option>';
                            });
                            $("#circle_id").html(psOptions);
                        }
                    });
                }

                function loadMultiPoliceStations() {

                   var circles = $("#circle_id").val();
                    $.ajax({
                        url: '{{ route("loadMultiPoliceStations") }}' , // Replace with your actual URL
                        method: 'post',
                        data: {
                            circles: circles,
                            _token: '{{ csrf_token() }}'

                        },
                        success: function (response) {

                            var psOptions = '<option value="">Select Police Stations</option>';
                            $.each(response.data, function (index, item) {
                                psOptions += '<option value="' + item.id + '">' + item.title + '</option>';
                            });
                            $("#police_station_id").html(psOptions);
                        }
                    });
                }
                function loadBoundaryData() {
                    reinitMap();
                    getAllPoliceStations();
                    getAllPollingStations();
                    loadHospitals();
                    /*if (myMap.hasLayer(kp_boundary)) {
                        myMap.removeLayer(kp_boundary);

                    }
                    kp_boundary = L.geoJson.ajax('theme/plugins/leaflet/boundary_data/kp_district_boundary.geojson',{filter: districts}).bindPopup(function(kp_boundary) {
                        return kp_boundary.feature.properties.DISTRICT;
                    }).addTo(myMap);*/
                }

                function districts(feature){

                    if (feature.properties.DISTRICT === search_district_name) {
                        return true;
                    }
                }

                function getAllPoliceStations() {
                    var police_marker = [];
                    var greenIcon = L.icon.pulse({ iconSize:[10,10],color: "green", fillColor: "green", animate: false});

                    new L.geoJson.ajax("{{ URL::to('api/v1/getAllPoliceStation') }}?district_id="+selected_districts+"&police_station_id="+police_station_ids, {
                        middleware: function(data) {
                            $("#police_station_checkbox_count").text(data.features.length);
                            return L.geoJson(data, {

                                onEachFeature: function(feature, layer) {
                                    console.log("PoliceStation");
                                        if(feature.properties.latitude !='' && feature.properties.longitude !=''){
                                            //var mymarker = L.marker([parseFloat(feature.properties.latitude).toFixed(4),parseFloat(feature.properties.longitude).toFixed(4)], {icon: greenIcon}).addTo(myMap);
                                            var myIcon = L.icon({
                                                iconUrl: base_url+"markers/ps.png",
                                                className:"police_stations_marker",
                                                iconSize: [40, 40],

                                            });
                                            var mymarker = L.marker([feature.properties.latitude,feature.properties.longitude], {icon: myIcon}).addTo(myMap);
                                            mymarker.bindPopup(`
                                           <div style="line-height:0.2rem">
                                            <h6>Police Stations</h6>
                                            <p class="text_height_map"><b>Name:</b> ${feature.properties.title}</p>
                                            <p class="text_height_map"><b>Contact:</b> ${feature.properties.ps_contact_number}</p>
                                            <p class="text_height_map"><b>SHO Contact:</b> ${feature.properties.sho_contact}</p>
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
                    var polling_stations_id = $("#polling_stations_id").val();
                    var ps_sensitivity = $("#ps_sensitivity").val();
                    var greenIcon = L.icon.pulse({ iconSize:[10,10],color: "#faa5a5", fillColor: "#faa5a5", animate: true});


                    new L.geoJson.ajax("{{ URL::to('api/v1/getAllPollingStations') }}?district_id="+selected_districts+"&police_station_id="+police_station_ids+"&sensitivity_type="+sensitivity_type+"&polling_station_id="+polling_stations_id+"&ps_sensitivity="+ps_sensitivity, {
                        middleware: function(data) {
                            $("#polling_station_checkbox_count").text(data.features.length);
                            return L.geoJson(data, {

                                onEachFeature: function(feature, layer) {


                                       // var mymarker = L.marker([parseFloat(feature.properties.lat).toFixed(4),parseFloat(feature.properties.lng).toFixed(4)], {icon: greenIcon}).addTo(myMap);
                                    if(feature.properties.sensitivity == 1){
                                        var myIcon = L.icon({
                                            iconUrl: base_url+"markers/polling_ms.png",
                                            className:"polling_station_marker most_sensitive",
                                            iconSize: [30,30],

                                        });
                                        var mymarker = L.marker([parseFloat(feature.properties.lat).toFixed(6),parseFloat(feature.properties.lng).toFixed(6)], {icon: myIcon}).addTo(myMap);
                                        mymarker.bindPopup(`
                                           <div style="line-height:0.2rem">
                                            <h6>Polling Stations</h6>
                                            <p><b class="text_height_map">PS Name:</b> ${feature.properties.polling_station_name}</p>
                                            <p><b class="text_height_map">Name:</b> ${feature.properties.incharge_name}</p>
                                            <p><b class="text_height_map">Contact:</b> ${feature.properties.incharge_contact}</p>
                                            <p><b >type:</b> Most Sensitive</p>

                                            <a target="_blank" href="${base_url}edit-polling-station/${feature.properties.id}">View Details<a>

                                            </div>
                                        `);

                                    }else if(feature.properties.sensitivity == 2){
                                        var myIcon = L.icon({
                                            iconUrl: base_url+"markers/sensiteve_ps.png",
                                            className:"polling_station_marker sensitive",
                                            iconSize: [40, 40],

                                        });
                                        var mymarker = L.marker([parseFloat(feature.properties.lat).toFixed(6),parseFloat(feature.properties.lng).toFixed(6)], {icon: myIcon}).addTo(myMap);
                                        mymarker.bindPopup(`
                                           <div style="line-height:0.2rem">
                                            <h6>Polling Stations</h6>
                                           <p><b class="text_height_map">PS Name:</b> ${feature.properties.polling_station_name}</p>
                                            <p><b class="text_height_map">Name:</b> ${feature.properties.incharge_name}</p>
                                            <p><b class="text_height_map">Contact:</b> ${feature.properties.incharge_contact}</p>

                                            <p><b>type:</b> Sensitive</p>

                                            <a target="_blank" href="${base_url}edit-polling-station/${feature.properties.id}">View Details<a>

                                            </div>
                                        `);

                                    }else{
                                        var myIcon = L.icon({
                                            iconUrl: base_url+"markers/normal_ps.png",
                                            className:"polling_station_marker normal_polling_station",
                                            iconSize: [40, 40],

                                        });
                                        var mymarker = L.marker([parseFloat(feature.properties.lat).toFixed(6),parseFloat(feature.properties.lng).toFixed(6)], {icon: myIcon}).addTo(myMap);
                                        mymarker.bindPopup(`
                                           <div style="line-height:0.2rem">
                                            <h6>Polling Stations</h6>
                                            <p><b class="text_height_map">PS Name:</b> ${feature.properties.polling_station_name}</p>
                                            <p><b class="text_height_map">Name:</b> ${feature.properties.incharge_name}</p>
                                            <p><b class="text_height_map">Contact:</b> ${feature.properties.incharge_contact}</p>

                                            <p><b>type:</b> Normal</p>

                                            <a target="_blank" href="${base_url}edit-polling-station/${feature.properties.id}">View Details<a>

                                            </div>
                                        `);

                                    }
                                }
                            }).addTo(myMap);
                        }
                    });


                }


                function getAllPoliceMobiles() {
                       var search_police_mobile = $("#search_mobile").val();
                    $.ajax({
                        url: '{{ route("getAllPoliceMobiles") }}', // Replace with your actual URL
                        type: 'post',
                        data: {
                            police_station_id:$("#police_station_id").val(),
                            districts:$("#search_district").val(),
                            search_police_mobile:search_police_mobile,
                            _token: '{{ csrf_token() }}'

                        },
                        success: function (response) {
                            var blackIcon = L.icon.pulse({ color: "black", fillColor: "black", animate: true});
                            $("#police_mobile_checkbox_count").text(response.data.length);
                            $.each(response.data, function (index, item) {
                                if(item.lat != null && item.lng != null){

                                    if(item.minuts_ago <= 480){
                                        var myIcon = L.icon({
                                            iconUrl: base_url+"markers/police_mobile.png",
                                            iconSize: [30, 30],
                                            className:"active_cars_on_map"

                                        });
                                    }else{
                                        var myIcon = L.icon({
                                            iconUrl: 'https://w7.pngwing.com/pngs/5/851/png-transparent-marker-map-icon-car-location-automobile-vehicle-target-design.png',
                                            iconSize: [30, 30],
                                            className:"deactive_cars_on_map"

                                        });
                                    }

                                    var mymarker = L.marker([item.lat,item.lng], {icon: myIcon}).addTo(myMap);
                                    mymarker.bindPopup(`
                                            <div style="line-height:0.2rem">
                                            <h6>Police Mobile</h6>
                                            <p class="text_height_map"><b>Registration#:</b> ${item.registration_number}</p>
                                            <p class="text_height_map"><b>Incharge Name:</b> ${item.incharge_name}</p>
                                            <p class="text_height_map"><b>Contact:</b> ${item.contact_number}</p>
                                            <a target="_blank" href="${base_url}view-on-map/${item.id}">View Details<a>

                                            </div>
                                        `);
                                }



                            });

                        }
                    });
                }

                function getAllPoliceLine() {
                alert();
                    $.ajax({
                        url: '{{ route("getAllPoliceLines") }}', // Replace with your actual URL
                        type: 'POST',
                        data: {
                            districts:$("#search_district").val(),
                            _token: '{{ csrf_token() }}'

                        },
                        success: function (response) {
                            var blackIcon = L.icon.pulse({ color: "red", fillColor: "red", animate: false});
                            $("#police_line_checkbox_count").text(response.data.length);
                            $.each(response.data, function (index, item) {
                                if(item.lat != null && item.lng != null){

                                    var myIcon = L.icon({
                                        iconUrl: 'https://w7.pngwing.com/pngs/5/851/png-transparent-marker-map-icon-car-location-automobile-vehicle-target-design.png',
                                        iconSize: [20, 30],
                                        className:"police_mobile_marker"

                                    });
                                    var mymarker = L.marker([item.lat,item.lng], {icon: blackIcon}).addTo(myMap);
                                    mymarker.bindPopup(`
                                            <div style="line-height:0.2rem">
                                            <h6>Police Line</h6>
                                            <p class="text_height_map"><b>Name:</b> ${item.name}</p>
                                            <p class="text_height_map"><b>Incharge Name:</b> ${item.incharge_name}</p>
                                            <p class="text_height_map"><b>Contact:</b> ${item.contact_number}</p>

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
