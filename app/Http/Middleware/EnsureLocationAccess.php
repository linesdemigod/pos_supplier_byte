<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLocationAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $type): Response
    {
        $user = auth()->user();

        if ($type === 'store' && $user->store_id === null) {
            abort(404);
        }

        if ($type === 'warehouse' && $user->warehouse_id === null) {
            abort(404);
        }

        return $next($request);
    }
}
