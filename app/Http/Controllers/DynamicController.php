<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class DynamicController extends Controller
{
    public function dropDown(Request $request)
    {

        $token = Crypt::decrypt($request->token);
        $model = DB::connection($token['connection'] ?? 'mysql')->table($token['model'] ?? '')->select(($token['value'] ?? 'value') . ' as value', ($token['label']  ?? 'label') . ' as label')->orderBy($token['label'] ?? '', 'asc');
        if ($request->has('search')) {
            $search = $request->search;
            $model->where($token['label'] ?? '', 'like', '%' . $search . '%');
        }

        $perPage = 10;
        $page = $request->input('page') ?: 1;

        $model = $model->paginate($perPage, ['*'], 'page', $page);
        return response()->json($model);
    }
}
