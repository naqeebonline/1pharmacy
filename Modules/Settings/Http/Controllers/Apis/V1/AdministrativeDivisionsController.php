<?php

namespace Modules\Settings\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use Modules\Settings\Entities\Country;
use Modules\Settings\Entities\District;
use Modules\Settings\Entities\Division;
use Modules\Settings\Entities\Province;
use Modules\Settings\Entities\Tehsil;
use Modules\Settings\Entities\UnionCouncil;

class AdministrativeDivisionsController extends Controller
{
    public function countries($lite = null)
    {
        $countries = Country::select('id', 'title')->orderBy('title', 'asc');
        $countries = ($lite === 'lite') ? $countries->pluck('title', 'id') : $countries->get();
        return response()->json([
            'error' => false,
            'message' => 'List of Countries',
            'data' => $countries
        ], 200);
    }

    public function country($id)
    {
        return response()->json([
            'error' => false,
            'message' => 'Show Country',
            'data' => Country::find($id)
        ], 200);
    }

    public function countryProvinces($id, $lite = null)
    {
        if($lite === 'lite') {
            $country = Province::where('country_id', '=', $id)->orderBy('title', 'asc')->pluck('title', 'id');
        } else {
            $country = Country::select('id', 'title')->with(['provinces' => function ($query) {
                $query->select('id', 'country_id', 'title')->orderBy('title', 'asc');
            }])->find($id);
        }
        return response()->json([
            'error' => false,
            'message' => 'List of Country Provinces',
            'data' => $country
        ], 200);
    }

    public function countryDivisions($id, $lite = null)
    {
        if($lite === 'lite') {
            $country = Division::whereHas('province', function ($query) use ($id) {
                $query->where('country_id', '=', $id);
            })
            ->orderBy('title', 'asc')
            ->pluck('title', 'id');
        } else {
            $country = Country::select('id', 'title')->with(['provinces' => function ($query1) {
                $query1->select('id', 'country_id', 'title')->with(['divisions' => function ($query2) {
                    $query2->select('id', 'province_id','title')->orderBy('title', 'asc');
                }])
                ->orderBy('title', 'asc');
            }])->find($id);
        }
        return response()->json([
            'error' => false,
            'message' => 'List of Country Province Divisions',
            'data' => $country
        ], 200);
    }

    public function countryDistricts($id, $lite = null)
    {
        if ($lite === 'lite') {
            $country = District::whereHas('division.province', function ($query) use ($id) {
                $query->where('country_id', '=', $id);
            })
            ->orderBy('title', 'asc')
            ->pluck('title', 'id');
        } else {
            $country = Country::select('id', 'title')->with(['provinces' => function ($query1) {
                $query1->select('id', 'country_id', 'title')->with(['divisions' => function ($query2) {
                    $query2->select('id', 'province_id', 'title')->with(['districts' => function ($query3) {
                        $query3->select('id', 'division_id','title')->orderBy('title', 'asc');
                    }])
                        ->orderBy('title', 'asc');
                }])->orderBy('title', 'asc');
            }])->find($id);
        }
        return response()->json([
            'error' => false,
            'message' => 'List of Country Province Divisions Districts',
            'data' => $country
        ], 200);
    }

    public function countryTehsils($id, $lite = null)
    {
        if($lite === 'lite') {
            $country = Tehsil::whereHas('district.division.province', function ($query) use ($id) {
                $query->where('country_id', '=', $id);
            })->orderBy('title', 'asc')->pluck('title', 'id');
        } else {
            $country = Country::select('id', 'title')->with(['provinces' => function ($query1) {
                $query1->select('id', 'country_id', 'title')->with(['divisions' => function ($query2) {
                    $query2->select('id', 'province_id', 'title')->with(['districts' => function ($query3) {
                        $query3->select('id', 'division_id', 'title')->with(['tehsils' => function ($query4) {
                            $query4->select('id', 'district_id', 'title')->orderBy('title');
                        }])->orderBy('title', 'asc');
                    }])->orderBy('title', 'asc');
                }])->orderBy('title', 'asc');
            }])->find($id);
        }
        return response()->json([
            'error' => false,
            'message' => 'List of Country Province Divisions Districts Tehsils',
            'data' => $country
        ], 200);
    }

    public function countryUnionCouncils($id, $lite = null)
    {
        if($lite === 'lite') {
            $country = UnionCouncil::whereHas('tehsil.district.division.province', function ($query) use ($id) {
                $query->where('country_id', '=', $id);
            })->orderBy('name', 'asc')->pluck('name', 'id');
        } else {
            $country = Country::select('id', 'title')->with(['provinces' => function ($query1) {
                $query1->select('id', 'country_id', 'title')->with(['divisions' => function ($query2) {
                    $query2->select('id', 'province_id', 'title')->with(['districts' => function ($query3) {
                        $query3->select('id', 'division_id', 'title')->with(['tehsils' => function ($query4) {
                            $query4->select('id', 'district_id', 'title')->with(['unionCouncils' => function ($query5) {
                                $query5->select('id', 'tehsil_id', 'name as title')->orderBy('name', 'asc');
                            }])->orderBy('title', 'asc');
                        }])->orderBy('title', 'asc');
                    }])->orderBy('title', 'asc');
                }])->orderBy('title', 'asc');
            }])->find($id);
        }
        return response()->json([
            'error' => false,
            'message' => 'List of Country Province Divisions Districts Tehsils Union Councils',
            'data' => $country
        ], 200);
    }

    public function provinces($lite = null)
    {
        $provinces = Province::select('id', 'title')->orderBy('title', 'asc');
        $provinces = ($lite === 'lite') ? $provinces->pluck('title', 'id') : $provinces->get();
        return response()->json([
            'error' => false,
            'message' => 'List of Provinces',
            'data' => $provinces
        ], 200);
    }

    public function province($id)
    {
        return response()->json([
            'error' => false,
            'message' => 'Show Province',
            'data' => Province::find($id)
        ], 200);
    }

    public function provinceDivisions($id, $lite = null)
    {
        if($lite === 'lite') {
            $province = Division::where('province_id', '=', $id)->orderBy('title', 'asc')->pluck('title', 'id');
        } else {
            $province = Province::select('id', 'title')->with(['divisions' => function ($query) {
                $query->select('id', 'province_id', 'title')->orderBy('title', 'asc');
            }])->find($id);
        }
        return response()->json([
            'error' => false,
            'message' => 'List of Province Divisions',
            'data' => $province
        ], 200);
    }

    public function provinceDistricts($id, $lite = null)
    {
        if ($lite === 'lite') {
            $province = District::whereHas('division', function ($query) use ($id) {
                $query->where('province_id', '=', $id);
            })->orderBy('title', 'asc')->pluck('title', 'id');
        } else {
            $province = Province::select('id', 'title')->with(['divisions' => function ($query1) {
                $query1->select('id', 'province_id', 'title')->with(['districts' => function ($query2) {
                    $query2->select('id', 'division_id', 'title')->orderBy('title', 'asc');
                }])->orderBy('title', 'asc');
            }])->find($id);
        }
        return response()->json([
            'error' => false,
            'message' => 'List of Province Divisions Districts',
            'data' => $province
        ], 200);
    }

    public function provinceTehsils($id, $lite = null)
    {
        if($lite === 'lite') {
            $province = Tehsil::whereHas('district.division', function ($query) use ($id) {
                $query->where('province_id', '=', $id);
            })->orderBy('title', 'asc')->pluck('title', 'id');
        } else {
            $province = Province::select('id', 'title')->with(['divisions' => function ($query1) {
                $query1->select('id', 'province_id', 'title')->with(['districts' => function ($query2) {
                    $query2->select('id', 'division_id', 'title')->with(['tehsils' => function ($query3) {
                        $query3->select('id', 'district_id', 'title')->orderBy('title');
                    }])->orderBy('title', 'asc');
                }])->orderBy('title', 'asc');
            }])->find($id);
        }
        return response()->json([
            'error' => false,
            'message' => 'List of Province Divisions Districts Tehsils',
            'data' => $province
        ], 200);
    }

    public function provinceUnionCouncils($id, $lite = null)
    {
        if($lite === 'lite') {
            $province = UnionCouncil::whereHas('tehsil.district.division', function ($query) use ($id) {
                $query->where('province_id', '=', $id);
            })->orderBy('name', 'asc')->pluck('name', 'id');
        } else {
            $province = Province::select('id', 'title')->with(['divisions' => function ($query1) {
                $query1->select('id', 'province_id', 'title')->with(['districts' => function ($query2) {
                    $query2->select('id', 'division_id', 'title')->with(['tehsils' => function ($query3) {
                        $query3->select('id', 'district_id', 'title')->with(['unionCouncils' => function ($query4) {
                            $query4->select('id', 'tehsil_id', 'name as title')->orderBy('name', 'asc');
                        }])->orderBy('title');
                    }])->orderBy('title', 'asc');
                }])->orderBy('title', 'asc');
            }])->find($id);
        }
        return response()->json([
            'error' => false,
            'message' => 'List of Province Divisions Districts Tehsils Union Councils',
            'data' => $province
        ], 200);
    }

    public function divisions($lite = null)
    {
        $divisions = Division::select('id', 'title')->orderBy('title', 'asc');
        $divisions = ($lite === 'lite') ? $divisions->pluck('title', 'id') : $divisions->get();
        return response()->json([
            'error' => false,
            'message' => 'List of Divisions',
            'data' => $divisions
        ], 200);
    }

    public function division($id)
    {
        return response()->json([
            'error' => false,
            'message' => 'Show Division',
            'data' => Division::find($id)
        ], 200);
    }

    public function divisionDistricts($id, $lite = null)
    {
        if($lite === 'lite') {
            $division = District::where('division_id', '=', $id)->orderBy('title', 'asc')->pluck('title', 'id');
        } else {
            $division = Division::select('id', 'title')->with(['districts' => function ($query) {
                $query->select('id', 'division_id', 'title')->orderBy('title', 'asc');
            }])->find($id);
        }
        return response()->json([
            'error' => false,
            'message' => 'List of Division Districts',
            'data' => $division
        ], 200);
    }

    public function divisionTehsils($id, $lite = null)
    {
        if($lite === 'lite') {
            $division = Tehsil::whereHas('district', function ($query) use ($id) {
                $query->where('division_id', '=', $id);
            })->orderBy('title', 'asc')->pluck('title', 'id');
        } else {
            $division = Division::select('id', 'title')->with(['districts' => function ($query1) {
                $query1->select('id', 'division_id', 'title')->with(['tehsils' => function ($query2) {
                    $query2->select('id', 'district_id', 'title')->orderBy('title', 'asc');
                }])->orderBy('title', 'asc');
            }])->find($id);
        }
        return response()->json([
            'error' => false,
            'message' => 'List of Division Districts Tehsils',
            'data' => $division
        ], 200);

    }

    public function divisionUnionCouncils($id, $lite = null)
    {
        if($lite === 'lite') {
            $division = UnionCouncil::whereHas('tehsil.district', function ($query) use ($id) {
                $query->where('division_id', '=', $id);
            })->orderBy('name', 'asc')->pluck('name', 'id');
        } else {
            $division = Division::select('id', 'title')->with(['districts' => function ($query1) {
                $query1->select('id', 'division_id', 'title')->with(['tehsils' => function ($query2) {
                    $query2->select('id', 'district_id', 'title')->with(['unionCouncils' => function ($query3) {
                        $query3->select('id', 'tehsil_id', 'name as title')->orderBy('name', 'asc');
                    }])
                    ->orderBy('title', 'asc');
                }])->orderBy('title', 'asc');
            }])->find($id);
        }
        return response()->json([
            'error' => false,
            'message' => 'List of Division Districts Tehsils Union Councils',
            'data' => $division
        ], 200);

    }

    public function districts($lite = null)
    {
        $districts = District::select('id', 'title')->orderBy('title', 'asc');
        $districts = ($lite === 'lite') ? $districts->pluck('title', 'id') : $districts->get();
        return response()->json([
            'error' => false,
            'message' => 'List of Districts',
            'data' => $districts
        ], 200);
    }

    public function district($id)
    {
        return response()->json([
            'error' => false,
            'message' => 'Show District',
            'data' => District::find($id)
        ], 200);
    }

    public function districtTehsils($id, $lite = null)
    {
        if($lite === 'lite') {
            $district = Tehsil::where('district_id', '=', $id)->orderBy('title', 'asc')->pluck('title', 'id');
        } else {
            $district = District::select('id', 'title')->with(['tehsils' => function ($query) {
                $query->select('id', 'district_id', 'title')->orderBy('title', 'asc');
            }])->find($id);
        }
        return response()->json([
            'error' => false,
            'message' => 'List of District Tehsils',
            'data' => $district
        ], 200);
    }

    public function districtUnionCouncils($id, $lite = null)
    {
        if($lite === 'lite') {
            $district = UnionCouncil::whereHas('tehsil', function ($query) use ($id) {
                $query->where('district_id', '=', $id);
            })->orderBy('name', 'asc')->pluck('name', 'id');
        } else {
            $district = District::select('id', 'title')->with(['tehsils' => function ($query1) {
                $query1->select('id', 'district_id', 'title')->with(['unionCouncils' => function ($query2) {
                    $query2->select('id', 'tehsil_id', 'name as title')->orderBy('name', 'asc');
                }])->orderBy('title', 'asc');
            }])->find($id);
        }
        return response()->json([
            'error' => false,
            'message' => 'List of District Tehsils Union Councils',
            'data' => $district
        ], 200);
    }

    public function tehsils($lite = null)
    {
        $tehsils = Tehsil::select('id', 'title')->orderBy('title', 'asc');
        $tehsils = ($lite === 'lite') ? $tehsils->pluck('title', 'id') : $tehsils->get();
        return response()->json([
            'error' => false,
            'message' => 'List of Tehsils',
            'data' => $tehsils
        ], 200);
    }

    public function tehsil($id)
    {
        return response()->json([
            'error' => false,
            'message' => 'Show Tehsil',
            'data' => Tehsil::select('id', 'title')->find($id)
        ], 200);
    }

    public function tehsilUnionCouncils($id, $lite = null)
    {
        if($lite === 'lite') {
            $tehsil = UnionCouncil::where('tehsil_id', '=', $id)->orderBy('name', 'asc')->pluck('name', 'id');
        } else {
            $tehsil = Tehsil::select('id', 'title')->with(['unionCouncils' => function ($query) {
                $query->select('id', 'tehsil_id', 'name as title')->orderBy('name', 'asc');
            }])->find($id);
        }
        return response()->json([
            'error' => false,
            'message' => 'List of Tehsils Union Councils',
            'data' => $tehsil
        ], 200);
    }

    public function unionCouncils($lite = null)
    {
        $unionCouncils = UnionCouncil::select('id', 'name as title')->orderBy('name', 'asc');
        $unionCouncils = ($lite === 'lite') ? $unionCouncils->pluck('title', 'id') : $unionCouncils->get();
        return response()->json([
            'error' => false,
            'message' => 'List of Union Councils',
            'data' => $unionCouncils
        ], 200);
    }

    public function unionCouncil($id)
    {
        return response()->json([
            'error' => false,
            'message' => 'Show Union Council',
            'data' => UnionCouncil::select('id', 'name as title')->find($id)
        ], 200);
    }
}
