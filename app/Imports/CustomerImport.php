<?php

namespace App\Imports;

use App\Models\AuditLog;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class CustomerImport implements ToCollection, WithChunkReading, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $this->insertIntoTable($row);
        }
    }

    public function chunkSize(): int
    {
        return 200;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'phone' => 'required',
            'location' => 'required'
        ];
    }

    private function insertIntoTable($row)
    {
        DB::beginTransaction();
        try {
            $request = request();
            $user = auth()->user();
            $storeId = $user->store_id;
            $userId = $user->id;
            //insert item
            $data = Customer::create([
                'name' => $row['name'],
                'phone' => $row['phone'],
                'location' => $row['location'],
                'store_id' => $storeId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),

            ]);

            $auditTrail = [
                'user_id' => auth()->id(),
                'store_id' => auth()->user()->store_id,
                'ip_address' => $request->ip(),
                'description' => 'customer creation',
                'data_before' => json_encode([]), // no previous data since it's a new record
                'data_after' => json_encode($data->getAttributes()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Insert it into the audit log
            AuditLog::create($auditTrail);
            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("item insert error " . $e->getMessage());
            return false;
        }
    }
}
