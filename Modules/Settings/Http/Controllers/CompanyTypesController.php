<?php

namespace Modules\Settings\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Modules\Settings\Entities\Company;
use Modules\Settings\Entities\CompanyType;
use Modules\Settings\Entities\MyApp;

class CompanyTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $data = [
            'title' => config('settings.company_title').' Types List',
            'new_route' => ['settings.company-types.create', 'New '.config('settings.company_title')." Type"],
            'items' => CompanyType::all()
        ];

        return view('settings::company_types.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(CompanyType $item)
    {
        $data = [
            'title' => 'Create a New '.config('settings.company_title')." Type",
            'back_route' => ['settings.company-types.list', config('settings.company_title').' Types List'],
            'new_route' => ['settings.company-types.create', 'New '.config('settings.company_title')." Type"],
            'item' => $item
        ];

        return view('settings::company_types.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required'
        ]);

        $inputs = $request->all();
        $item = CompanyType::create($inputs);

        if($item){
            Session::flash('success', 'New '.config('settings.company_title').' type created successfully');
            return redirect(route('settings.company-types.list'));
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
        $item = CompanyType::find(Crypt::decrypt($id));

        $data = [
            'title' => 'Update '.config('settings.company_title')." Type",
            'back_route' => ['settings.company-types.list', config('settings.company_title').' Types List'],
            'new_route' => ['settings.company-types.create', 'New '.config('settings.company_title')." Type"],
            'item' => $item
        ];

        return view('settings::company_types.form', $data);
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
        $item = CompanyType::find($id);

        $this->validate($request, [
            'title' => 'required'
        ]);

        $inputs = $request->all();

        if($item->fill($inputs)->save()){
            Session::flash('success', config('settings.company_title').' type updated successfully');
            return redirect(route('settings.company-types.list'));
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
        $item = CompanyType::find($id);

        if($item->delete()){
            Session::flash('success', config('settings.company_title').' type deleted successfully');
            return redirect()->back();
        }else{
            Session::flash('error', 'Something went wrong, please try later');
            return redirect()->back();
        }
    }
}
