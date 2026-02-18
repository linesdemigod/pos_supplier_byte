<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesPointPermission;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function index()
    {
        $permissions = SalesPointPermission::whereIn('permission_name', ['allow_negative', 'price_edit'])
            ->pluck('status', 'permission_name');

        return response()->json(['message' => 'Permission fetched successfully!', 'data' => $permissions], 200);

    }
}
