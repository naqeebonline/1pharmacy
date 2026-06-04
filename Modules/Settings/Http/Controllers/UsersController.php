<?php

namespace Modules\Settings\Http\Controllers;

use App\Models\Circle;
use App\Models\Districts;
use App\Models\Hospital;
use App\Models\PoliceMobile;
use App\Models\PoliceStation;
use App\Models\PollingStation;
use App\Models\Reagin;
use App\Models\Tehsils;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use jeremykenedy\LaravelRoles\Models\Role;
use Modules\Settings\Entities\Company;
use Modules\Settings\Entities\MyApp;
use Modules\Settings\Entities\MyRole;
use Modules\Settings\Entities\Section;
use Yajra\DataTables\DataTables;

class UsersController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {

        //$role_id = auth()->user()->roles->pluck('id')[0];
        //$role_name = auth()->user()->roles->pluck('name')[0];
        $data = [
            'title' => 'Users List',
            'new_route' => ['settings.users-mgt.create', 'New User'],
            'districts' => Districts::whereProvinceId(1)
                ->when((auth()->user()->roles->pluck('name')[0] != "Super Admin" && auth()->user()->roles->pluck('name')[0] != "Regional User"), function ($q) {
                    return $q->where(["id" => auth()->user()->district_id]);
                })
                ->get(),
            'role_data' => Role::when((auth()->user()->roles->pluck('name')[0] != "Super Admin"), function ($q) {
                return $q->whereIn("id",[56,57,58]);
            })->get(),
            'items' => User::with([
                'section',
                'company',
                'permissions',
                'roles',
                'parent',
                'children'
            ])
                ->when(auth()->user()->roles->pluck('name')[0] != "Super Admin", function ($q) {
                    return $q->where(["district_id" => auth()->user()->district_id])->where("name","!=","Super Admin");
                })
                ->when(request()->get('search_by'),function ($q){
                    return $q->where('username', 'like', '%'.request()->get('search_by').'%');
                })
                ->when(request()->get('email'),function ($q){
                    return $q->where('email', 'like', '%'.request()->get('email').'%');
                })
                ->when(request()->get('district_id'),function ($q){
                    return $q->where('district_id', request()->get("district_id"));
                })
                ->when(request()->role_id, function($query){
                    $query->whereHas('roles', function ($q) {
                        $q->where('roles.id', request()->role_id);
                    });
                })
                ->paginate(500)
        ];


        return view('settings::users_mgt.index', $data);
    }

    public function allUsers()
    {
        $users = User::with([
            'section',
            'company',
            'permissions',
            'roles',
            'parent',
            'children'
        ])
            ->when(auth()->user()->roles->pluck('name')[0] != "Super Admin", function ($q) {
                return $q->where(["district_id" => auth()->user()->district_id])->where("name","!=","Super Admin");
            });
        return DataTables::of($users)
            ->addColumn('action', function($cert) {
                $actionsBtn = '<a class="btn btn-warning btn-icon btn-sm" href="'.route('settings.users-mgt.edit', ['id' => encrypt($cert->id)]).'"><i class="tf-icons bx bx-pencil"></i></a>';
                $actionsBtn .= '<a class="btn btn-primary btn-icon  btn-sm" href="'.route('settings.users-mgt.edit', ['id' => encrypt($cert->id)]).'"><i class="tf-icons bx bx-wrench"></i></a>';

                $actionsBtn .= '<a class="dropdown-item p-50 delete_table_data" data-id="'.$cert->id.'" href="javascript:void(0)"><i class="bx bx-window-close"></i> Delete</a>';


                return $actionsBtn;
            })

            ->rawColumns(["action"])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(User $item)
    {
        $data = [
            'title' => 'Create a New User',
            'back_route' => ['settings.users-mgt.list', 'Users List'],
            'new_route' => ['settings.users-mgt.create', 'New User'],
            'item' => $item,
            'roles' => MyRole::when(auth()->user()->roles->pluck('name')[0] != "Super Admin", function ($q) {
                return $q->whereIn("id",[56,57,58]);
            })->pluck('name', 'id'),
            'districts' => Districts::when(auth()->user()->roles->pluck('name')[0] != "Super Admin", function ($q) {
                return $q->where(["id" => auth()->user()->district_id]);
            })->pluck('title', 'id'),
            'departments'=>DB::table("departments")->pluck("name","id"),
            'tehsil' => Tehsils::pluck('title', 'id'),
            'sections' => collect([]),
            'parent_users' => collect([])
        ];

        $companies_dd_data = Company::with([
            'children' => function ($q) {
                $q->with('children');
            }
        ])->whereNull('parent_id')->get();

        // $companies_dd = [];
        // foreach ($companies_dd_data as $cdd) {
        //     $companies_dd[$cdd->id] = $cdd->title;
        //     if ($cdd->children->count() > 0) {
        //         foreach ($cdd->children as $cdd_cl1) {
        //             $companies_dd[$cdd_cl1->id] = $cdd->title . " -> " . $cdd_cl1->title;

        //             if ($cdd_cl1->children->count() > 0) {
        //                 foreach ($cdd_cl1->children as $cdd_cl2) {
        //                     $companies_dd[$cdd_cl2->id] = $cdd->title . " -> " . $cdd_cl1->title . " -> " . $cdd_cl2->title;
        //                 }
        //             }
        //         }
        //     }
        // }

            //return "create";
        $companies_dd = [];
        $this->buildCompanyTree($companies_dd_data, $companies_dd);
        $data['companies_dd'] = $companies_dd;

        return view('settings::users_mgt.form', $data);
    }


    private function buildCompanyTree($companies, &$result, $prefix = "")
    {
        foreach ($companies as $company) {
            $result[$company->id] = $prefix . $company->title;

            if ($company->children->count() > 0) {
                $this->buildCompanyTree($company->children, $result, $prefix . $company->title . " -> ");
            }
        }
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'username' => 'required|unique:users,username',
            'password' => 'required',
            'district_id' => 'required'
        ]);

        $inputs = $request->except('password');

        $inputs['password'] = Hash::make($request->get('password'));

        $user = User::create($inputs);

        if ($user) {

            $user->roles()->sync($request->get('role_id'));
            $user->addAllMediaFromTokens();
            Session::flash('success', 'New user created successfully');
            return redirect(route('settings.users-mgt.list'));
        } else {
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

        $id = Crypt::decrypt($id);
        $item = User::with([
            'permissions',
            'company',
            'section',
            'roles',
            'parent'
        ])->find($id);

        $data = [
            'title' => 'User Permissions',
            'back_route' => ['settings.users-mgt.list', 'Users List'],
            'new_route' => ['settings.users-mgt.create', 'New User'],
            'item' => $item
        ];

        // get the app-wise menus and permissions tree
        $trees_data = MyApp::with([
            'menus' => function ($q) {
                $q->with('myPermissions');
            }
        ])->get();
        // dd($tree_data);
        $data['trees'] = $trees_data;

        return view('settings::users_mgt.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $item = User::with('roles')->find($id);
        $district_id = $item->district_id;

        $data = [
            'title' => 'Create a New User',
            'back_route' => ['settings.users-mgt.list', 'Users List'],
            'new_route' => ['settings.users-mgt.create', 'New User'],
            'item' => $item,
            'roles' => MyRole::when(auth()->user()->roles->pluck('name')[0] != "Super Admin", function ($q) {
                return $q->whereIn("id",[56,57,58]);
            })->pluck('name', 'id'),
            'districts' => Districts::when(auth()->user()->roles->pluck('name')[0] != "Super Admin", function ($q) {
                return $q->where(["id" => auth()->user()->district_id]);
            })->pluck('title', 'id'),
           // 'tehsil' => Tehsils::pluck('title', 'id'),
            'departments'=>DB::table("departments")->pluck("name","id"),
            'sections' => Section::where('company_id', '=', $item->company_id)->pluck('title', 'id'),
            'parent_users' => User::where([
                ['company_id', '=', $item->company_id],
                ['id', '!=', $id]
            ])->pluck('name', 'id'),

        ];
        // dd($data);

        $companies_dd_data = Company::with([
            'children' => function ($q) {
                $q->with('children');
            }
        ])->whereNull('parent_id')->get();

        $companies_dd = [];
        foreach ($companies_dd_data as $cdd) {
            $companies_dd[$cdd->id] = $cdd->title;
            if ($cdd->children->count() > 0) {
                foreach ($cdd->children as $cdd_cl1) {
                    $companies_dd[$cdd_cl1->id] = $cdd->title . " -> " . $cdd_cl1->title;

                    if ($cdd_cl1->children->count() > 0) {
                        foreach ($cdd_cl1->children as $cdd_cl2) {
                            $companies_dd[$cdd_cl2->id] = $cdd->title . " -> " . $cdd_cl1->title . " -> " . $cdd_cl2->title;
                        }
                    }
                }
            }
        }
        $data['companies_dd'] = $companies_dd;

        return view('settings::users_mgt.form', $data);
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
        $user = User::find($id);

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $id,
            'username' => 'required|unique:users,username,' . $id,

        ]);

        $inputs = $request->except('password');

        if ($request->has('password') & strlen($request->get('password')) > 0) {

            $inputs['password'] = Hash::make($request->get('password'));
        }

        if ($user->fill($inputs)->save()) {

            $user->roles()->sync($request->get('role_id'));
            $user->addAllMediaFromTokens();
            Session::flash('success', 'User updated successfully');
            return redirect(route('settings.users-mgt.list'));
        } else {
            Session::flash('error', 'Something went wrong, please try later');
            return redirect()->back();
        }
    }

    public function userPermissionsSave(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $user = User::find($id);

        if (is_null($request->get('checked_permissions'))) {
            $permissions = [];
        } else {
            $permissions = explode(",", $request->get('checked_permissions'));
        }

        if ($user->permissions()->sync($permissions)) {
            Session::flash('success', 'User updated successfully');
            return redirect()->back();
        } else {
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
        $user = User::find($id);

        if ($user->delete()) {
            Session::flash('success', 'User deleted successfully');
            return redirect()->back();
        } else {
            Session::flash('error', 'Something went wrong, please try later');
            return redirect()->back();
        }
    }


    /**
     * change email address
     */
    public function myProfile()
    {

        $user = Auth::user();

        $data = [
            'title' => 'My Profile',
            'user' => $user
        ];

        return view('settings::users_mgt.my_profile', $data);
    }


    /**
     * change email
     */
    public function myProfileAct(Request $request)
    {

        $user = Auth::user();

        $this->validate($request, [
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|unique:users,username,' . $user->id,
        ]);

        $name = $request->get('name');
        $email = $request->get('email');
        $username = $request->get('username');
        $password = $request->get('password');

        // dd($new_email.$old_email.$password);

        $user = User::where([
            ['username', '=', $user->username]
        ])->get();

        if (Hash::check($password, $user[0]->password)) {

            $inputs = [
                'username' => $username,
                'email' => $email
            ];

            User::findOrFail($user[0]->id)->fill($inputs)->save();
            session()->flash('success', 'Profile updated successfully');
            return redirect()->back();
        } else {
            session()->flash('error', 'Incorrect password');
            return redirect()->back();
        }
    }




    /**
     * change password
     */
    public function changePassword()
    {

        $data = [
            'title' => 'Update Password'
        ];

        return view('settings::users_mgt.change_password', $data);
    }


    /**
     * change the pass
     */
    public function changePasswordAct(Request $request)
    {

        $user = Auth::user();
        // dd($request->all());

        $this->validate($request, [
            'new_password' => 'required|same:conf_new_password',
            'conf_new_password' => 'required',
            'current_password' => 'required'
        ]);


        $user = User::where([
            ['id', '=', $user->id]
        ])->get();

        if (Hash::check($request->get('current_password'), $user[0]->password)) {

            User::findOrFail($user[0]->id)->fill([
                'password' => Hash::make($request->get('new_password'))
            ])->save();
            session()->flash('success', 'Password changed successfully');
            return redirect()->back();
        } else {
            session()->flash('error', 'Incorrect password');
            return redirect()->back();
        }
    }

    public function getTehsils($district_id)
    {
        return["message"=>"success","data"=>Tehsils::whereDistrictId($district_id)->get()];
    }

    public function getPoliceStations()
    {
        return["message"=>"success","data"=>PoliceStation::whereDistrictId(request()->district_id)->get()];
    }

    public function getCirclePoliceStations($circle_id)
    {
        return["message"=>"success","data"=>PoliceStation::whereCircleId($circle_id)->get()];
    }

    public function getPoliceStationUser($police_station_id)
    {
        return["message"=>"success","data"=>User::wherePoliceStationId($police_station_id)->get()];
    }

    public function getDistrictUser($district_id)
    {
        $district_id = explode(",",$district_id);

        $res["hospital_users"] = User::with("roles")->with('district')->whereHas("roles", function($q) {
            $q->whereIn("roles.id", [55]);
        })->whereIn("district_id",$district_id)->get();

        $res["polling_station_user"] = User::with("roles")->with('district')->whereHas("roles", function($q) {
            $q->whereIn("roles.id", [56]);
        })->whereIn("district_id",$district_id)->get();

        $res["police_mobile_user"] = User::with("roles")->with('district')->whereHas("roles", function($q) {
            $q->whereIn("roles.id", [58]);
        })->whereIn("district_id",$district_id)->get();


        return["message"=>"success","data"=>User::whereDistrictId($district_id)->get(),"users"=>$res];
    }

    public function getCircles($district_id)
    {
        $district_id = $district_id ?? "";
        $data =Circle::when($district_id, function ($q) use ($district_id) {
            return $q->where(["district_id"=>$district_id]);
        })->get();
        return["message"=>"success","data"=>$data];
    }

    public function getPoliceMobile($district_id)
    {
        $district_id = $district_id ?? "";
        $data =PoliceMobile::when($district_id, function ($q) use ($district_id) {
            return $q->where(["district_id"=>$district_id]);
        })->get();
        return["message"=>"success","data"=>$data];
    }

    public function getPollingStations($district_id)
    {
        $district_id = $district_id ?? "";
        $data =PollingStation::when($district_id, function ($q) use ($district_id) {
            return $q->where(["district_id"=>$district_id]);
        })->get();
        return["message"=>"success","data"=>$data];
    }

    public function getHospitals($district_id)
    {
        $district_id = $district_id ?? "";
        $data =Hospital::when($district_id, function ($q) use ($district_id) {
            return $q->where(["district_id"=>$district_id]);
        })->get();
        return["message"=>"success","data"=>$data];
    }

    public function getMultiCircles()
    {

        $district_id = request()->districts;
        $data =Circle::select(["id","name"])->whereIn("district_id",$district_id)->get();
        return["message"=>"success","data"=>$data];
    }

    public function getMultiDistricts()
    {

        $region_id = request()->regions;
        $data =Districts::whereIn("reagin",$region_id)->get();
        return["message"=>"success","data"=>$data];
    }

    public function loadMultiPoliceStations()
    {

        $circles = request()->circles;
        $data =PoliceStation::select(["id","title"])->whereIn("circle_id",$circles)->get();
        return["message"=>"success","data"=>$data];
    }

    public function loadMultiPollingStations()
    {

        $district_id = request()->district_id;
        $circle_id = request()->circle_id ?? "";
        $police_station_id = "";
        if($circle_id){
            $police_station_id = PoliceStation::whereIn("circle_id",$circle_id)->pluck("id")->all();
        }

        $data =PollingStation::select(["id","polling_station_name"])
            ->when($district_id, function ($q) use ($district_id) {
                return $q->whereIn("district_id",$district_id);
            })
            ->when($police_station_id, function ($q) use ($police_station_id) {
                return $q->whereIn("police_station_id",$police_station_id);
            })
            ->get();
        return["message"=>"success","data"=>$data];
    }


    public function getMultiDistrictUser()
    {
        $police_station_id = "";
        $district_id = request()->districts;
        $circle_id = request()->circle_id ?? "";
        $polling_stations_id = "";
        $circle_police_mobiles = "";
        if($circle_id){
            $police_station_id = PoliceStation::whereIn("circle_id",$circle_id)->pluck("id")->all();
            $polling_stations_id = PollingStation::whereIn("police_station_id",$police_station_id)->pluck("id")->all();
            $circle_police_mobiles = PoliceMobile::whereIn("police_station_id",$police_station_id)->pluck("id")->all();
        }
        if(request()->polling_stations_id){
            $polling_stations_id = request()->polling_stations_id;
        }

        $res["polling_station_user"] = User::select(["id","name","username"])->with("roles:id,name")->with('district')->whereHas("roles", function($q) {
            $q->whereIn("roles.id", [56]);
        })
            ->whereIn("district_id",$district_id)
            ->when($polling_stations_id, function ($q) use ($polling_stations_id) {
                return $q->whereIn("polling_station_id",$polling_stations_id);
            })
            ->get()
            ->makeHidden(['roles',"roles_array","permissions_array"]) ;

       //============== get police mobile on bases of police station . police station will get from circle id ..=========//

        $res["police_mobile_user"] = User::select(["id","name","username","police_mobile_id"])->with("roles:id,name")->with('district')->whereHas("roles", function($q) {
            $q->whereIn("roles.id", [58]);
        })
            ->whereIn("district_id",$district_id)
            ->when($circle_police_mobiles, function ($q) use ($circle_police_mobiles) {
                return $q->whereIn("police_mobile_id",$circle_police_mobiles);
            })
            ->get()->makeHidden(['roles',"roles_array","permissions_array"]);

        foreach ($res["police_mobile_user"] as $key => $value){
            $value->registration_number = $value->policeMobile?->registration_number ?? "";
        }

        //========== end of getting police mobiles  ================//
        $res["police_station_users"] = [];

        /*$res["police_station_users"] = User::select(["id","name","username"])->with("roles:id,name")->with('district')->whereHas("roles", function($q) {
            $q->whereIn("roles.id", [4]);
        })
            ->whereIn("district_id",$district_id)
            ->get()->makeHidden(['roles',"roles_array","permissions_array"]);*/

        return["message"=>"success","data"=>User::select(["id","name","username"])->whereDistrictId($district_id)->get()->makeHidden(['roles',"roles_array","permissions_array"]),"users"=>$res];


    }


    public function getAllPollingStations()
    {
        $district_id = request()->districts ?? "";

        $police_station_id = request()->police_station_id ?? "";
        $ps_sensitivity = request()->ps_sensitivity ?? "";
        $data =PollingStation::when($district_id, function ($q) use ($district_id) {
            return $q->whereIn("district_id",$district_id);
        })
        ->when($police_station_id, function ($q) use ($police_station_id) {
            return $q->whereIn("police_station_id",$police_station_id);
        })
        ->when($ps_sensitivity, function ($q) use ($ps_sensitivity) {
            return $q->whereIn("sensitivity",$ps_sensitivity);
        })
            ->whereNotNull('lat')
            ->whereNotNull('lng')
        ->get();
        return["message"=>"success","data"=>$data];
    }

    public function getMultiPoliceStationUser()
    {
        $district_id = request()->districts;
        $police_station_id = request()->police_station_id;



        $res["polling_station_user"] = User::with("roles")->with('district')
            ->whereHas("roles", function($q) {
            $q->whereIn("roles.id", [56]);
            })
            ->whereIn("district_id",$district_id)
           /* ->whereIn("police_station_id",$police_station_id)*/
            ->get();

        $res["police_mobile_user"] = User::with("roles")->with('district')->whereHas("roles", function($q) {
            $q->whereIn("roles.id", [58]);
        })
            ->whereIn("district_id",$district_id)
          //  ->whereIn("police_station_id",$police_station_id)
            ->get();

        $res["police_station_users"] = User::with("roles")->with('district')->whereHas("roles", function($q) {
            $q->whereIn("roles.id", [4]);
        })
            //->whereIn("district_id",$district_id)
            ->whereIn("police_station_id",$police_station_id)
            ->get();
        return["message"=>"success","data"=>User::whereDistrictId($district_id)->get(),"users"=>$res];

        //return["message"=>"success","data"=>User::wherePoliceStationId($police_station_id)->get()];
    }

}
