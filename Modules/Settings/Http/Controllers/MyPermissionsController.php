<?php

namespace Modules\Settings\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Modules\Settings\Entities\Menu;
use Modules\Settings\Entities\MyApp;
use Modules\Settings\Entities\MyPermission;
use Modules\Settings\Entities\PermissionRoute;

class MyPermissionsController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $items = MyPermission::with(['myApp', 'menu'])->get();
        // dd($menus);

        $data = [
            'title' => 'Permissions List',
            'new_route' => ['settings.my-permissions.create', 'New Permission'],
            'items' => $items
        ];

        return view('settings::my_permissions.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(MyPermission $item)
    {
        $data = [
            'title' => 'Create a New Permission',
            'back_route' => ['settings.my-permissions.list', 'Permissions List'],
            'new_route' => ['settings.my-permissions.create', 'New Permission'],
            'item' => $item,
            'apps' => MyApp::pluck('title', 'id'),
            'menus' => Menu::pluck('title', 'id')
        ];

        return view('settings::my_permissions.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        // dd($request->all());

        $this->validate($request, [
            'name' => 'required',
            'app_id' => 'required',
            'menu_id' => 'required',
            'slug' => 'required|unique:permissions,slug'
        ]);

        $inputs = $request->all();
        $inputs['show_in_menu'] = 'no';
        if ($request->has('show_in_menu')) {
            $inputs['show_in_menu'] = 'yes';
        }

        $item = MyPermission::create($inputs);

        if ($item) {

            // save the routes
            if ($request->has('title')) {
                $bulk_inputs = [];
                $sn = 0;
                foreach ($request->get('title') as $rtitle) {
                    $thisInputs = [
                        'title' => $rtitle,
                        'route' => $request->get('route')[$sn],
                        'app_id' => $request->get('app_id'),
                        'menu_id' => $request->get('menu_id'),
                        'permission_id' => $item->id,
                        'is_default' => $request->get('is_default')[$sn],
                        'menu_order' => $request->get('menu_order')[$sn] ?? 0
                    ];
                    array_push($bulk_inputs, $thisInputs);

                    $sn++;
                }
                PermissionRoute::insert($bulk_inputs);
            }

            Session::flash('success', 'New permission created successfully');
            return redirect(route('settings.my-permissions.list'));
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
        return view('settings::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $item = MyPermission::find($id);
        // dd($item);

        $data = [
            'title' => 'Update Permission',
            'back_route' => ['settings.my-permissions.list', 'Permissions List'],
            'new_route' => ['settings.my-permissions.create', 'New Permission'],
            'item' => $item,
            'apps' => MyApp::pluck('title', 'id'),
            'menus' => Menu::pluck('title', 'id')
        ];

        return view('settings::my_permissions.form', $data);
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
        $item = MyPermission::find($id);
        // dd($menu);

        $this->validate($request, [
            'title' => 'required',
            'app_id' => 'required',
            'menu_id' => 'required',
            'slug' => 'required|unique:permissions,slug,' . $id
        ]);

        $inputs = $request->all();
        $inputs['show_in_menu'] = 'no';
        if ($request->has('show_in_menu')) {
            $inputs['show_in_menu'] = 'yes';
        }

        if ($item->fill($inputs)->save()) {

            // save the routes
            if ($request->has('title')) {

                $bulk_inputs = [];
                $sn = 0;
                foreach ($request->get('title') as $rtitle) {
                    $thisInputs = [
                        'title' => $rtitle,
                        'route' => $request->get('route')[$sn],
                        'app_id' => $request->get('app_id'),
                        'menu_id' => $request->get('menu_id'),
                        'permission_id' => $item->id,
                        'is_default' => $request->get('is_default')[$sn],
                        'menu_order' => $request->get('menu_order')[$sn] ?? 0
                    ];
                    array_push($bulk_inputs, $thisInputs);

                    $sn++;
                }

                // delete all route before re-inserting
                $del_sql = "delete from permission_routes where permission_id = ?";
                DB::delete($del_sql, [$id]);

                // now insert the new
                PermissionRoute::insert($bulk_inputs);
            }


            Session::flash('success', 'Permission updated successfully');
            return redirect(route('settings.my-permissions.list'));
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
        $item = MyPermission::find($id);

        if ($item->delete()) {
            Session::flash('success', 'Item deleted successfully');
            return redirect()->back();
        } else {
            Session::flash('error', 'Something went wrong, please try later');
            return redirect()->back();
        }
    }
}
