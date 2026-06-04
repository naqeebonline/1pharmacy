<?php

namespace Modules\Settings\Http\Controllers;

use App\Exports\PoliceStationExport;
use App\Models\AssignMeetings;
use App\Models\Districts;
use App\Models\FacilityType;
use App\Models\Hospital;
use App\Models\Meeting;
use App\Models\PoliceLine;
use App\Models\PoliceMobile;
use App\Models\PolicePost;
use App\Models\PoliceStation;
use App\Models\PollingStation;
use App\Models\Reagin;
use App\Models\Sensitivity;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Jubaer\Zoom\Facades\Zoom;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Settings\Entities\MyApp;

class MyAppsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

        //$res = $this->findNearestHospitals("34.0151","71.5249",20);
        $current_user_role = auth()->user()->roles->pluck('name')[0];

        $data = [
            'title' => 'Dashboard',
            'new_route' => ['settings.my-apps.create', 'New App'],
            'my_apps' => MyApp::all()
        ];


        return view('dashboard.smsDashboard', $data);

    }

    private function findNearestHospitals($latitude, $longitude, $kilometer)
    {
        /*
         * using eloquent approach, make sure to replace the "Restaurant" with your actual model name
         * replace 6371000 with 6371 for kilometer and 3956 for miles
         */
        $restaurants = Hospital::selectRaw("id, name, contact_number, lat, lng,
                         ( 6371 * acos( cos( radians(?) ) *
                           cos( radians( lat ) )
                           * cos( radians( lng ) - radians(?)
                           ) + sin( radians(?) ) *
                           sin( radians( lat ) ) )
                         ) AS distance", [$latitude, $longitude, $latitude])

            ->having("distance", "<", $kilometer)
            ->orderBy("distance",'asc')
            ->offset(0)
            ->limit(20)
            ->get();

        return $restaurants;
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(MyApp $item)
    {
        $data = [
            'title' => 'Create a New App',
            'back_route' => ['settings.my-apps.list', 'Apps List'],
            'new_route' => ['settings.my-apps.create', 'New App'],
            'item' => $item
        ];

        return view('settings::my_apps.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'route' => 'required|unique:apps,route'
        ]);

        $inputs = $request->all();
        $my_app = MyApp::create($inputs);

        if($my_app){
            Session::flash('success', 'New app created successfully');
            return redirect(route('settings.my-apps.list'));
        }else{
            Session::flash('error', 'Something went wrong, please try later');
            return redirect()->back();
        }

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('settings::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $item = MyApp::find(Crypt::decrypt($id));

        $data = [
            'title' => 'Update App',
            'back_route' => ['settings.my-apps.list', 'Apps List'],
            'new_route' => ['settings.my-apps.create', 'New App'],
            'item' => $item
        ];

        return view('settings::my_apps.form', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $my_app = MyApp::find($id);

        $this->validate($request, [
            'title' => 'required',
            'route' => 'required|unique:apps,route,'.$id
        ]);

        $inputs = $request->all();

        if($my_app->fill($inputs)->save()){
            Session::flash('success', 'App updated successfully');
            return redirect(route('settings.my-apps.list'));
        }else{
            Session::flash('error', 'Something went wrong, please try later');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        $my_app = MyApp::find($id);

        if($my_app->delete()){
            Session::flash('success', 'App deleted successfully');
            return redirect()->back();
        }else{
            Session::flash('error', 'Something went wrong, please try later');
            return redirect()->back();
        }
    }

    public function getRegionalData()
    {
        $district_ids = Districts::whereReagin(auth()->user()->region_id)->pluck("id")->all();
        $current_user_role = auth()->user()->roles->pluck('name')[0];
        $data['region'] = Reagin::when((auth()->user()->roles->pluck('name')[0] != "Super Admin" && auth()->user()->roles->pluck('name')[0] != "Readonly"), function ($q) {
            return $q->where(["id" => auth()->user()->region_id]);
        })->get();

        $data['districts'] = Districts::whereProvinceId(1)->where("reagin",auth()->user()->region_id)->get();

        $data['most_sensitive'] = PollingStation::where("sensitivity",1)->whereIn("district_id",$district_ids)->count();
        $data['sensitive'] = PollingStation::where("sensitivity",2)
            ->whereIn("district_id",$district_ids)
            ->count();
        $data['normal'] = PollingStation::where("sensitivity",3)
            ->whereIn("district_id",$district_ids)
            ->count();
        $data['number_of_male_booth'] = PollingStation::whereIn("district_id",$district_ids)->sum('number_of_male_booth');
        $data['number_of_female_booth'] = PollingStation::whereIn("district_id",$district_ids)->sum('number_of_female_booth');
        $data['male_voters'] = PollingStation::whereIn("district_id",$district_ids)->sum('male_voters');
        $data['female_voters'] = PollingStation::whereIn("district_id",$district_ids)->sum('female_voters');

        $data["total_police_stations"] = PoliceStation::whereIn("district_id",$district_ids)->count();
        $data["total_polling_station"] = PollingStation::whereIn("district_id",$district_ids)->count();
        $data["total_hospitals"] = Hospital::whereIn("district_id",$district_ids)->count();

        $data["police_line"] = PoliceLine::whereIn("district_id",$district_ids)->count();

        $data["police_post"] = PolicePost::whereIn("district_id",$district_ids)->count();

        $data["police_mobile"] = PoliceMobile::whereIn("district_id",$district_ids)->count();


        $type = FacilityType::get();
        $facility_chart = [];
        foreach ($type as $key => $value){
            array_push($facility_chart,["name"=> $value->name,'y'=> Hospital::where("type",$value->id)->count() ?? 0]);
        }
        $data['facility_chart'] = $facility_chart;
        $data["sensitivity"] = Sensitivity::get();
        return $data;
    }
}
