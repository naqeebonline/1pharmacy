<?php

namespace Modules\Settings\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Modules\Settings\Entities\Menu;
use Modules\Settings\Entities\MyApp;

class MenusController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $menus = Menu::with(['parent', 'myApp'])->orderBy("order","asc")->get();
        // dd($menus);

        $data = [
            'title' => 'Menus List',
            'new_route' => ['settings.menus.create', 'New Menu'],
            'menus' => $menus
        ];

        return view('settings::menus.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Menu $item)
    {
        $data = [
            'title' => 'Create a New Menu',
            'back_route' => ['settings.menus.list', 'Menus List'],
            'new_route' => ['settings.menus.create', 'New Menu'],
            'item' => $item,
            'apps' => MyApp::pluck('title', 'id'),
            'menus_parents' => Menu::whereNull('parent_id')->pluck('title', 'id')
        ];

        return view('settings::menus.form', $data);
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
            'app_id' => 'required',
            'order' => 'required'
        ]);

        $inputs = $request->all();
        $inputs['is_collapsible'] = $request->has('is_collapsible') ? 'yes' : 'no';
        $menu = Menu::create($inputs);

        if($menu){
            Session::flash('success', 'New menu created successfully');
            return redirect(route('settings.menus.list'));
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
        $id = Crypt::decrypt($id);
        $item = Menu::find($id);
        // dd($item);

        $data = [
            'title' => 'Update Menu',
            'back_route' => ['settings.menus.list', 'Menus List'],
            'new_route' => ['settings.menus.create', 'New Menu'],
            'item' => $item,
            'apps' => MyApp::pluck('title', 'id'),
            'menus_parents' => Menu::whereNull('parent_id')->where('id', '!=', $id)->pluck('title', 'id')
        ];

        return view('settings::menus.form', $data);
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
        $menu = Menu::find($id);
        // dd($menu);

        $this->validate($request, [
            'title' => 'required',
            'app_id' => 'required',
            'order' => 'required'
        ]);

        $inputs = $request->all();
        $inputs['is_collapsible'] = $request->has('is_collapsible') ? 'yes' : 'no';

        if($menu->fill($inputs)->save()){
            Session::flash('success', 'Menu updated successfully');
            return redirect(route('settings.menus.list'));
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
        $menu = Menu::find($id);

        if($menu->delete()){
            Session::flash('success', 'Menu deleted successfully');
            return redirect()->back();
        }else{
            Session::flash('error', 'Something went wrong, please try later');
            return redirect()->back();
        }
    }


    public function menuByAppId(Request $request){
        $app_id = $request->get('app_id');
        $menus = Menu::where('app_id', $app_id)->pluck('title', 'id');
        return $menus;
    }
}
