<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisableResponseCaching
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request)->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }
}
