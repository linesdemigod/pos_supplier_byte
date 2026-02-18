<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnItemController extends Controller
{
    public function index()
    {

        return view('pages.return.index', );
    }

    public function create()
    {


        return view('pages.return.create');
    }

    public function store(Request $request)
    {
        $formData = $request->validate([
            'reference' => 'required|exists:sales,reference',
            'item_id' => 'required|exists:items,id',
            'price' => ['required', 'regex:/^\d+(\.\d+)?$/'],
            'quantity' => 'required|numeric',
            'reason' => 'required',
            'purchase_date' => 'required|date',
            'return_date' => 'required|date',

        ]);

        $user = auth()->user();
        $userId = $user->id;
        $warehouseId = $user->warehouse_id;
        $storeId = $user->store_id;

        $formData['store_id'] = $storeId;
        $formData['warehouse_id'] = $warehouseId;

        $formData['total'] = $formData['quantity'] * $formData['price'];

        DB::beginTransaction();
        try {
            $data = ReturnItem::create($formData);

            $auditTrail = [
                'user_id' => $userId,
                'ip_address' => $request->ip(),
                'store_id' => $storeId,
                'warehouse_id' => $warehouseId,
                'description' => 'Return Item creation',
                'data_before' => json_encode([]),
                'data_after' => json_encode($data->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);

            DB::commit();
            return back()->with('message', 'Return item created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating return item');
        }

    }


}
