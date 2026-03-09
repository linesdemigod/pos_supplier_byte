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
            return response()->view('errors.403', ['reason' => 'forbidden'], 403);
        }

        if ($type === 'warehouse' && $user->warehouse_id === null) {
            return response()->view('errors.403', ['reason' => 'forbidden'], 403);
        }

        return $next($request);
    }
}
