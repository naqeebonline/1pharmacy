<?php

namespace Modules\Settings\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Modules\Settings\Entities\MyApp;
use Modules\Settings\Entities\MyRole;

class MyRolesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $data = [
            'title' => 'Roles List',
            'new_route' => ['settings.my-roles.create', 'New Role'],
            'items' => MyRole::all()
        ];

        return view('settings::my_roles.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(MyRole $item)
    {
        $data = [
            'title' => 'Create a New Role',
            'back_route' => ['settings.my-roles.list', 'Roles List'],
            'new_route' => ['settings.my-roles.create', 'New Role'],
            'item' => $item
        ];

        return view('settings::my_roles.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required|unique:roles,slug'
        ]);

        $inputs = $request->all();
        $my_role = MyRole::create($inputs);

        if($my_role){
            Session::flash('success', 'New role created successfully');
            return redirect(route('settings.my-roles.list'));
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
        $id = Crypt::decrypt($id);
        $item = MyRole::with([
            'permissions'
        ])->find($id);

        $data = [
            'title' => 'Role Permissions',
            'back_route' => ['settings.my-roles.list', 'Roles List'],
            'new_route' => ['settings.my-roles.create', 'New Role'],
            'item' => $item
        ];

        // get the app-wise menus and permissions tree
        $trees_data = MyApp::with([
            'menus' => function($q){
                $q->with('myPermissions');
            }
        ])->get();
        // dd($tree_data);
        $data['trees'] = $trees_data;

        return view('settings::my_roles.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $item = MyRole::find(Crypt::decrypt($id));

        $data = [
            'title' => 'Update Role',
            'back_route' => ['settings.my-roles.list', 'Roles List'],
            'new_route' => ['settings.my-roles.create', 'New Role'],
            'item' => $item
        ];

        return view('settings::my_roles.form', $data);
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
        $my_role = MyRole::find($id);

        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required|unique:roles,slug,'.$id
        ]);

        $inputs = $request->all();

        if($my_role->fill($inputs)->save()){
            Session::flash('success', 'Role updated successfully');
            return redirect(route('settings.my-roles.list'));
        }else{
            Session::flash('error', 'Something went wrong, please try later');
            return redirect()->back();
        }
    }


    public function rolePermissionsSave(Request $request, $id){
        $id = Crypt::decrypt($id);
        $role = MyRole::find($id);

        if(is_null($request->get('checked_permissions'))){
            $permissions = [];
        }else{
            $permissions = explode(",", $request->get('checked_permissions'));
        }

        if($role->permissions()->sync($permissions)){
            Session::flash('success', 'Role updated successfully');
            return redirect()->back();
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
        $my_role = MyRole::find($id);

        if($my_role->delete()){
            Session::flash('success', 'Role deleted successfully');
            return redirect()->back();
        }else{
            Session::flash('error', 'Something went wrong, please try later');
            return redirect()->back();
        }
    }
}
