<?php

namespace Modules\Settings\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Modules\Settings\Entities\Company;
use Modules\Settings\Entities\District;
use Modules\Settings\Entities\MyApp;
use Modules\Settings\Entities\Province;
use Modules\Settings\Entities\Section;

class SectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $data = [
            'title' => str_plural(config('settings.section_title')).' List',
            'new_route' => ['settings.sections.create', 'New '.config('settings.section_title')],
            'items' => Section::with([
                'parent',
                'children',
                'company'
            ])->get()
        ];

        return view('settings::sections.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Section $item)
    {
        $data = [
            'title' => 'Create a New '.config('settings.section_title'),
            'back_route' => ['settings.sections.list', str_plural(config('settings.section_title')).' List'],
            'new_route' => ['settings.sections.create', 'New '.config('settings.section_title')],
            'item' => $item,
            'provinces' => Province::pluck('title', 'id'),
            'districts' => District::where("province_id", "1")->pluck('title', 'id')
        ];

        $companies_dd_data = Company::with([
            'children' => function($q){
                $q->with('children');
            }
        ])->whereNull('parent_id')->get();

        $companies_dd = [];
        foreach($companies_dd_data as $cdd){
            $companies_dd[$cdd->id] = $cdd->title;
            if($cdd->children->count() > 0){
                foreach($cdd->children as $cdd_cl1){
                    $companies_dd[$cdd_cl1->id] = $cdd->title." -> ".$cdd_cl1->title;

                    if($cdd_cl1->children->count() > 0){
                        foreach($cdd_cl1->children as $cdd_cl2){
                            $companies_dd[$cdd_cl2->id] = $cdd->title." -> ".$cdd_cl1->title." -> ".$cdd_cl2->title;
                        }
                    }

                }
            }
        }
        $data['companies_dd'] = $companies_dd;

        return view('settings::sections.form', $data);
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
            'company_id' => 'required'
        ]);

        $inputs = $request->all();
        $inputs['user_id'] = Auth::user()->id;

        $section = Section::create($inputs);

        if($section){
            Session::flash('success', 'New '.config('settings.section_title').' created successfully');
            return redirect(route('settings.sections.list'));
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
        $item = Section::find(Crypt::decrypt($id));

        $data = [
            'title' => 'Update '.config('settings.section_title'),
            'back_route' => ['settings.sections.list', str_plural(config('settings.section_title')).' List'],
            'new_route' => ['settings.sections.create', 'New '.config('settings.section_title')],
            'item' => $item,
            'provinces' => Province::pluck('title', 'id'),
            'districts' => District::where("province_id", "1")->pluck('title', 'id')
        ];

        $companies_dd_data = Company::with([
            'children' => function($q){
                $q->with('children');
            }
        ])->whereNull('parent_id')->get();

        $companies_dd = [];
        foreach($companies_dd_data as $cdd){
            $companies_dd[$cdd->id] = $cdd->title;
            if($cdd->children->count() > 0){
                foreach($cdd->children as $cdd_cl1){
                    $companies_dd[$cdd_cl1->id] = $cdd->title." -> ".$cdd_cl1->title;

                    if($cdd_cl1->children->count() > 0){
                        foreach($cdd_cl1->children as $cdd_cl2){
                            $companies_dd[$cdd_cl2->id] = $cdd->title." -> ".$cdd_cl1->title." -> ".$cdd_cl2->title;
                        }
                    }

                }
            }
        }
        $data['companies_dd'] = $companies_dd;

        return view('settings::sections.form', $data);
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
        $section = Section::find($id);

        $this->validate($request, [
            'title' => 'required',
            'company_id' => 'required'
        ]);

        $inputs = $request->all();

        if($section->fill($inputs)->save()){
            Session::flash('success', config('settings.section_title').' updated successfully');
            return redirect(route('settings.sections.list'));
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
        $section = Section::find($id);

        if($section->delete()){
            Session::flash('success', config('settings.section_title').' deleted successfully');
            return redirect()->back();
        }else{
            Session::flash('error', 'Something went wrong, please try later');
            return redirect()->back();
        }
    }
}
