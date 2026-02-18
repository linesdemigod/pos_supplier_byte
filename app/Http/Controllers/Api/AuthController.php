<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $formData = $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::where('username', $formData['username'])->first();

        if (!$user) {
            return response()->json([
                "message" => "Invalid Credentials",
            ], 401);
        }

        if ($user && $user->status == "inactive") {
            return response()->json([
                "message" => "Your account is inactive. Please contact support.",
            ], 401);
        }

        try {
            // Attempt login
            if (!Auth::attempt($formData) || $user == null) {

                return response()->json([
                    "message" => "Invalid Credentials",
                ], 401);
            }

            // Successful login
            $user = Auth::user();

            // Return user and token
            return response([
                'user' => $user,
                'token' => $user->createToken('secret')->plainTextToken,
            ], 200);
        } catch (\Exception $e) {

            return response()->json(['error' => 'An error occurred during login', 'details' => $e->getMessage()], 500);
        }
    }


    public function change_password(Request $request)
    {

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        //if the current password is not the same as the one in db
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json(['message' => 'Your current password is incorrect!'], 401);
        }

        User::find(auth()->user()->id)->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'message' => 'password updated successfully'
        ], 200);
    }


    //get user details
    public function user()
    {

        return response([
            "user" => auth()->user(),
        ], 200);
    }

    //logout
    public function logout()
    {
        $user = auth()->user();


        try {
            // Delete all tokens of the authenticated user
            $user->tokens()->delete();

            return response([
                "message" => "Logout successful",
            ], 200);
        } catch (\Exception $e) {

            return response()->json(['error' => 'An error occurred during logout', 'details' => $e->getMessage()], 500);
        }
    }

    //check if user is auth and also blocked
    public function check_status(Request $request)
    {
        $status = User::where('id', $request->id)->value('status');

        if (!$status) {
            return response()->json([
                'status' => 0,
            ], 200);
        }


        return response()->json([
            'status' => $status,
        ], 200);
    }

    public function auth_status()
    {

        //check if user is auth
        if (Auth::check()) {

            $company = Company::first();

            return response()->json([
                'message' => true,
                'company' => $company
            ], 200);
        } else {
            return response()->json([
                'message' => false,
            ], 200);
        }


    }
}
