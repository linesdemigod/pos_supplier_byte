<?php

namespace App\Http\Controllers;

use App\Models\TimeRestriction;
use App\Models\User;
use Illuminate\Http\Request;

class TimeRestrictionController extends Controller
{
    public function index()
    {

        $restriction = TimeRestriction::first();
        $usersAllowed = User::whereIn('id', $restriction->user_exemptions)->get();

        //make the array a comma separated string
        $usersAllowedString = implode(',', $usersAllowed->pluck('name')->toArray());
        $users = User::all();

        return view('pages.restrictions.index', [
            'users' => $users,
            'usersAllowed' => $usersAllowedString,
            'restriction' => $restriction,
        ]);
    }

    public function update(Request $request, TimeRestriction $restriction)
    {

        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s',
            'user_exemptions' => 'required|string',
        ]);

        $userAllowed = json_decode($validated['user_exemptions'], true);

        // Check if the JSON is valid
        if (json_last_error() !== JSON_ERROR_NONE) {

            return back()->with('error', 'Invalid JSON format in user_exemptions field.');

        }


        try {

            $restriction->update([
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'user_exemptions' => $userAllowed,
            ]);

            return back()->with('message', 'Time restriction updated successfully.');

        } catch (\Exception $e) {
            return back()->with('An error occured while updating');
        }


    }
}
