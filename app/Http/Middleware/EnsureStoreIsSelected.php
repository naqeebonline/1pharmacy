<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureStoreIsSelected
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('store_id') || session('store_id') == '') {
            return redirect()->route('pos.select_store');
        }

        return $next($request);
    }
}

