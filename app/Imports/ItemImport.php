<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\AuditLog;
use App\Models\Category;
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

class ItemImport implements ToCollection, WithChunkReading, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{

    use Importable, SkipsErrors, SkipsFailures;

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            // $this->insertItem($row);

            // $categoryId = Category::where('category_code', $row['category_code'])->value('id');

            // if ($categoryId) {
            //     //insert item
            //     Item::create([
            //         'item_code' => $row['item_code'],
            //         'category_id' => $categoryId,
            //         'name' => $row['name'],
            //         'price' => $row['price'],
            //         'created_at' => now(),
            //         'updated_at' => now(),

            //     ]);
            // }

            $this->insertItem($row);
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
            'category_code' => 'required',
            'name' => ['required', 'max:255'],
            'price' => ['required', 'regex:/^\d+(\.\d+)?$/'],
            'item_code' => ['required', 'unique:items,item_code']
        ];
    }

    private function insertItem($row)
    {
        DB::beginTransaction();
        try {
            $request = request();
            //get categories id by using category code
            $categoryId = Category::where('category_code', $row['category_code'])->value('id');

            if ($categoryId) {
                //insert item
                $data = Item::create([
                    'item_code' => $row['item_code'],
                    'category_id' => $categoryId,
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'created_at' => now(),
                    'updated_at' => now(),

                ]);

                $auditTrail = [
                    'user_id' => auth()->id(),
                    'store_id' => auth()->user()->store_id,
                    'ip_address' => $request->ip(),
                    'description' => 'item creation',
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
