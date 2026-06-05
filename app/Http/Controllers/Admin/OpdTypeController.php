<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration\InvestigationSubCategory;
use App\Models\OpdType;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OpdTypeController extends Controller
{
    /**
     * Display a listing of OPD types
     */
    public function index()
    {
        $data["title"] = "OPD Type Configuration";
        return view("opd_type.list", $data);
    }

    /**
     * Get OPD types for DataTables
     */
    public function list_opd_types()
    {
        $opdTypes = OpdType::with(['products', 'investigations']);

        return DataTables::of($opdTypes)
            ->addColumn('products_count', function ($opdType) {
                return $opdType->products->count();
            })
            ->addColumn('investigations_count', function ($opdType) {
                return $opdType->investigations->count();
            })
            ->addColumn('action', function ($opdType) {
                $html = '<a href="' . route('pos.edit_opd_type', $opdType->id) . '" class="btn btn-warning btn-icon btn-sm"><i class="tf-icons bx bx-pencil"></i></a>';
                $html .= ' <button class="btn btn-danger btn-icon btn-sm delete_opd_type" data-id="' . $opdType->id . '" type="button"><i class="bx bx-trash tf-icons"></i></button>';
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new OPD type
     */
    public function create()
    {
        $data["title"] = "Create OPD Type";
        $data["products"] = Product::where('IsActive', 1)->orderBy('ProductName', 'asc')->get();
        $data["investigations"] = InvestigationSubCategory::where('is_active', 1)->orderBy('name', 'asc')->get();
        $data["opdType"] = null;
        return view("opd_type.form", $data);
    }

    /**
     * Store a newly created OPD type
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'fees' => 'required|numeric|min:0',
        ]);

        try {
            $opdType = OpdType::create([
                'name' => $request->name,
                'fees' => $request->fees,
                'including_medicine' => $request->has('including_medicine') ? 1 : 0,
                'including_labs' => $request->has('including_labs') ? 1 : 0,
                'is_sync' => 0,
            ]);

            // Attach products without quantities (default quantity = 1)
            if ($request->has('products') && is_array($request->products)) {
                $productData = [];
                foreach ($request->products as $productId) {
                    $productData[$productId] = ['quantity' => 1];
                }
                $opdType->products()->attach($productData);
            }

            // Attach investigations
            if ($request->has('investigations') && is_array($request->investigations)) {
                $opdType->investigations()->attach($request->investigations);
            }

            return response()->json([
                'status' => true,
                'message' => 'OPD Type created successfully',
                'opd_type_id' => $opdType->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error creating OPD type: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing an OPD type
     */
    public function edit($id)
    {
        $data["title"] = "Edit OPD Type";
        $data["opdType"] = OpdType::with(['products', 'investigations'])->findOrFail($id);
        
        // Get already assigned product IDs
        $assignedProductIds = $data["opdType"]->products->pluck('ProductID')->toArray();
        
        // Get only products that are not already assigned
        $data["products"] = Product::where('IsActive', 1)
            ->whereNotIn('ProductID', $assignedProductIds)
            ->orderBy('ProductName', 'asc')
            ->get();
        
        // Get already assigned investigation IDs
        $assignedInvestigationIds = $data["opdType"]->investigations->pluck('id')->toArray();
        
        // Get only investigations that are not already assigned
        $data["investigations"] = InvestigationSubCategory::where('is_active', 1)
            ->whereNotIn('id', $assignedInvestigationIds)
            ->orderBy('name', 'asc')
            ->get();
        
        return view("opd_type.form", $data);
    }

    /**
     * Update the specified OPD type
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'fees' => 'required|numeric|min:0',
        ]);

        try {
            $opdType = OpdType::findOrFail($id);

            $opdType->update([
                'name' => $request->name,
                'fees' => $request->fees,
                'including_medicine' => $request->has('including_medicine') ? 1 : 0,
                'including_labs' => $request->has('including_labs') ? 1 : 0,
            ]);

            // Sync products without quantities (default quantity = 1)
            if ($request->has('products') && is_array($request->products)) {
                $productData = [];
                foreach ($request->products as $productId) {
                    $productData[$productId] = ['quantity' => 1];
                }
                $opdType->products()->sync($productData);
            } else {
                $opdType->products()->detach();
            }

            // Sync investigations
            if ($request->has('investigations') && is_array($request->investigations)) {
                $opdType->investigations()->sync($request->investigations);
            } else {
                $opdType->investigations()->detach();
            }

            return response()->json([
                'status' => true,
                'message' => 'OPD Type updated successfully',
                'opd_type_id' => $opdType->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error updating OPD type: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified OPD type
     */
    public function destroy($id)
    {
        try {
            $opdType = OpdType::findOrFail($id);
            $opdType->delete();

            return response()->json([
                'status' => true,
                'message' => 'OPD Type deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error deleting OPD type: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get OPD type details
     */
    public function get_opd_type($id)
    {
        try {
            $opdType = OpdType::with(['products', 'investigations'])->findOrFail($id);

            return response()->json([
                'status' => true,
                'data' => $opdType
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'OPD Type not found'
            ], 404);
        }
    }
}
