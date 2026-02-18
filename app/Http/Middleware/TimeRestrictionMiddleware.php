<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\TimeRestriction;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TimeRestrictionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = Auth::id();
        $timeRestriction = TimeRestriction::first();


        if (!$timeRestriction) {
            return $next($request); // Allow access if no restrictions are defined
        }

        $startTime = $timeRestriction->start_time;
        $endTime = $timeRestriction->end_time;
        $currentTime = now()->format('H:i');

        //change if user is exempted. if not exempted then check if user is logging within the start and end time

        if (collect($timeRestriction->user_exemptions)->contains($userId)) {
            return $next($request);
        }

        // Check if the current time is within restricted hours
        if ($currentTime < $startTime || $currentTime > $endTime) {
            return redirect()->route('home')->with('message', 'Access is restricted during this time.');
        }

        return redirect()->route('dashboard');

    }
}
