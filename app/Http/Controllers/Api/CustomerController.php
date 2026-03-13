<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'List of customers'], 200);
    }

    public function show(Request $request)
    {
        $search = $request->query('q');

        $customers = Customer::search($search)
            ->latest()
            ->get();

        return response()->json(
            [
                'message' => "Search results for customer",
                'customers' => $customers
            ],
            200
        );
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|sometimes|string',
            'location' => 'required|string',
        ]);

        $validated['user_id'] = auth()->id();

        Customer::create($validated);

        return response()->json(['message' => 'Customer created successfully'], 200);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|sometimes|string',
            'location' => 'required|string',
        ]);

        $validated['user_id'] = auth()->id();

        $customer->update($validated);

        return response()->json(['message' => 'Customer updated successfully'], 200);
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully'], 200);
    }
}
