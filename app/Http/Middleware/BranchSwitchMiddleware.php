<?php

namespace App\Http\Middleware;

use App\Models\BranchSwitch;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BranchSwitchMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = Auth::id();
        $branchSwitch = BranchSwitch::first();

        if ($branchSwitch && in_array($userId, $branchSwitch->user_allowed)) {

            return $next($request);
        } else {
            return redirect()->route('dashboard')->with('message', 'Access denied');
        }

    }
}
