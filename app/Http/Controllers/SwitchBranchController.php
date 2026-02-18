<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BranchSwitch;
use Illuminate\Http\Request;

class SwitchBranchController extends Controller
{
    public function index()
    {
        $branchSwitch = BranchSwitch::first();
        $users = User::latest()->get();
        $usersAllowed = User::whereIn('id', $branchSwitch->user_allowed)->get();

        //make the array a comma separated string
        $usersAllowedString = implode(',', $usersAllowed->pluck('name')->toArray());



        return view('pages.home.branch', [
            'branchSwitch' => $branchSwitch,
            'users' => $users,
            'usersAllowed' => $usersAllowedString,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'user_allowed' => 'required|string',
        ]);

        try {

            $userAllowed = json_decode($validated['user_allowed'], true);

            // Check if the JSON is valid
            if (json_last_error() !== JSON_ERROR_NONE) {

                return back()->with('error', 'Invalid JSON format in user_allowed field.');

            }

            // Find the record by ID
            $branchSwitch = BranchSwitch::findOrFail($id);

            // Update the user_allowed field
            $branchSwitch->update([
                'user_allowed' => $userAllowed,
            ]);

            return back()->with('message', 'Branch switch entry updated successfully.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'Branch switch entry not found.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update branch switch entry.');
        }
    }

}
