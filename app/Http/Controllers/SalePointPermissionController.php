<?php

namespace App\Http\Controllers;

use App\Models\SalesPointPermission;
use Illuminate\Http\Request;

class SalePointPermissionController extends Controller
{
    public function index()
    {

        $sale = SalesPointPermission::all();

        return view('pages.salepointpermission.index', [
            'sales' => $sale
        ]);
    }
    public function edit(SalesPointPermission $sale)
    {

        return view('pages.salepointpermission.edit', [
            'sale' => $sale
        ]);
    }

    public function update(Request $request, SalesPointPermission $sale)
    {
        $validated = $request->validate([
            'permission_name' => 'required|in:allow_negative,price_edit',
            'status' => 'required|boolean',
        ]);

        $sale->update($validated);


        return to_route('permission.sale.point')->with('message', 'updated successfully');
    }


}
