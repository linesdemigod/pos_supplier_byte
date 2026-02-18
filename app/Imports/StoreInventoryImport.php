<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\AuditLog;
use App\Models\StoreInventory;
use App\Models\QuantityHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class StoreInventoryImport implements ToCollection, WithChunkReading, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{

    use Importable, SkipsErrors, SkipsFailures;

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {

        foreach ($collection as $index => $row) {
            $this->insertIntoTable($row);
        }

    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function onFailure(Failure ...$failures)
    {
        // Handle the failures how you'd like.

        return $failures;
    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.

        return $e;
    }

    public function rules(): array
    {
        return [
            'item_code' => ['required', 'exists:items,item_code'],
            'quantity' => ['required', 'integer', 'min:0']
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

            $itemId = Item::where('item_code', $row['item_code'])->value('id');
            // //check if item already exist in the store
            $ItemExists = StoreInventory::where('store_id', $storeId)->where('item_id', $itemId)->exists();

            if ($itemId && !$ItemExists) {
                //insert item
                $data = StoreInventory::create([
                    'item_id' => $itemId,
                    'quantity' => $row['quantity'],
                    'store_id' => $storeId,
                    'created_at' => now(),
                    'updated_at' => now(),

                ]);

                QuantityHistory::create([
                    'user_id' => $userId,
                    'store_id' => $storeId,
                    'item_id' => $data->item_id,
                    'old_quantity' => 0,
                    'new_quantity' => $data->quantity,
                    'change_type' => 'Add',
                ]);

                $auditTrail = [
                    'user_id' => $userId,
                    'store_id' => $storeId,
                    'ip_address' => $request->ip(),
                    'description' => 'store item creation',
                    'data_before' => json_encode([]), // no previous data since it's a new record
                    'data_after' => json_encode($data->getAttributes()),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Insert it into the audit log
                AuditLog::create($auditTrail);
                DB::commit();

                return true;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("item insert error " . $e->getMessage());
            return false;
        }
    }

}
