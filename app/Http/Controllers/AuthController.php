<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TimeRestriction;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {

        $name = 'Xtra Sako';

        return view('pages.index', [
            'name' => $name
        ]);
    }

    public function authenticate(Request $request)
    {
        $formData = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $formData['username'])->first();

        if (!$user) {
            return back()->withErrors(['username' => 'Invalid login details'])->onlyInput('username');
        }
        // check if user account is suspended
        if ($user && $user->status == 'inactive') {
            return redirect('/')->with('error', 'Your account is suspended');
        }

        // Check time restrictions
        if (!$this->isAccessAllowed($user)) {
            return back()->with('error', 'Access is restricted during this time.')->onlyInput('username');
        }



        //check if the remember me is checked
        $remember = $request->filled('remember');
        if (Auth::attempt($formData, $remember)) {


            $request->session()->regenerate();

            return to_route('dashboard')->with('message', 'Welcome back!');
        }

        return back()->withErrors(['username' => 'Invalid login details'])->onlyInput('username');

    }

    private function isAccessAllowed($user)
    {
        $timeRestriction = TimeRestriction::first();


        // Handle if TimeRestriction is not set
        if (!$timeRestriction) {
            return true; // Default to allowing access
        }

        $startTime = $timeRestriction->start_time;
        $endTime = $timeRestriction->end_time;
        $currentTime = now()->format('H:i');

        //change if user is exempted. if not exempted then check if user is logging within the start and end time

        if ($timeRestriction && in_array($user->id, $timeRestriction->user_exemptions)) {

            return true;
        }

        // Check if the current time falls within restricted hours
        return $currentTime >= $startTime && $currentTime <= $endTime;
    }

    //logout
    public function logout(Request $request)
    {


        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
