<?php
namespace App\Http\Controllers;

use App\Models\Finance\FinanceHead as FinanceFinanceHead;
use Illuminate\Http\Request;
use App\Models\FinanceHead;

class FinanceHeadController extends Controller
{
    public function index(Request $request)
    {
         
        if ($request->ajax()) {
            $level = $request->get('level');
            $parent_id = $request->get('parent_id');
            $query = FinanceHead::with('parent');
            if ($level == 2) {
                $query->where('level', 2);
            } elseif ($level == 3 && $parent_id) {
                $query->where('level', 3)->where('parent_id', $parent_id);
            }
            return \DataTables::of($query)
                ->addColumn('parent_name', function($row) {
                    return $row->parent ? $row->parent->name : '';
                })
                ->addColumn('action', function($row) {
                    if ($row->level == 2) {
                        $details = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                        return '<button class="btn btn-sm btn-success add_level3_btn" data-id="'.$row->id.'" data-name="'.$row->name.'">Add Level 3</button> '
                            .'<button class="btn btn-sm btn-primary view_level3_btn" data-id="'.$row->id.'" data-name="'.$row->name.'">View Level 3</button> '
                            .'<button class="btn btn-sm btn-info edit_record" data-details="'.$details.'">Edit</button> '
                            .'<button class="btn btn-sm btn-danger delete_record" data-id="'.$row->id.'">Delete</button>';
                    } else {
                        $details = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                        return '<button class="btn btn-sm btn-info edit_record_level3" data-details="'.$details.'">Edit</button> '
                            .'<button class="btn btn-sm btn-danger delete_record" data-id="'.$row->id.'">Delete</button>';
                    }
                })
                ->editColumn('is_contra', function($row) {
                    return $row->is_contra ? 'Yes' : 'No';
                })
                ->make(true);
        }
        $level2 = FinanceHead::where('level', 2)->get();
        
        return view('finance_head.index', compact('level2'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:finance_heads,id',
            'level' => 'required|in:2,3',
            'head_code' => 'nullable|string',
            'name' => 'required|string|unique:finance_heads,name',
            'type' => 'required|in:income,expense,asset,liability,capital',
            'description' => 'nullable|string',
            'is_contra' => 'nullable|boolean',
        ]);
        $data['is_contra'] = $request->input('is_contra', 0);
        $head = FinanceHead::create($data);
        return response()->json(['status' => true, 'data' => $head]);
    }

    public function update(Request $request, $id)
    {
        $head = FinanceHead::findOrFail($id);
        $data = $request->validate([
            'parent_id' => 'nullable|exists:finance_heads,id',
            'level' => 'required|in:2,3',
            'head_code' => 'nullable|string',
            'name' => 'required|string|unique:finance_heads,name,' . $id,
            'type' => 'required|in:income,expense,asset,liability,capital',
            'description' => 'nullable|string',
            'is_contra' => 'nullable|boolean',
        ]);
        $data['is_contra'] = $request->input('is_contra', 0);
        $head->update($data);
        return response()->json(['status' => true, 'data' => $head]);
    }

    public function destroy($id)
    {
        $head = FinanceHead::findOrFail($id);
        if ($head->level == 2) {
            $head->children()->delete();
        }
        $head->delete();
        return response()->json(['status' => true]);
    }
}
