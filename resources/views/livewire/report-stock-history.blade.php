<div>
    <div class="py-3">
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center gap-5">
                <!-- Show Per Page -->
                <div class="d-flex align-items-center gap-2">
                    <label for="per_page">Show:</label>
                    <select wire:model.live="perPage" class="form-select form-select-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <!-- Search Input -->
                <div class="d-flex align-items-center gap-2">
                    <label for="search">Search:</label>
                    <input wire:model.live.debounce.300ms="search" type="search" id="search" name="search"
                        class="form-control" />
                </div>
            </div>
        </div>

        @unless (count($items) == 0)
            <div class="table-responsive">
                <table class="table-striped table">
                    <thead>
                        <tr>
                            @if ($showStoreColumn)
                                <th scope="col">Store</th>
                            @endif
                            @if ($showWarehouseColumn)
                                <th scope="col">Warehouse</th>
                            @endif
                            <th scope="col">Created By</th>
                            <th scope="col">Item</th>
                            <th scope="col">Item Code</th>
                            <th scope="col">Change Type</th>
                            <th scope="col">Current Quantity</th>
                            <th scope="col">Added Quantity</th>
                            <th scope="col">Created at</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $value)
                            <tr wire:key="{{ $value->id }}">
                                @if ($showStoreColumn)
                                    <td>{{ $value->store->name ?? 'N/A' }}</td>
                                @endif
                                @if ($showWarehouseColumn)
                                    <td>{{ $value->warehouse->name ?? 'N/A' }}</td>
                                @endif
                                <td>{{ $value->user->name ?? 'N/A' }}</td>
                                <td>{{ $value->item->name ?? 'N/A' }}</td>
                                <td>{{ $value->item->item_code ?? 0 }}</td>
                                <td><span
                                        class="badge {{ Str::lower($value->change_type) === 'add' ? 'bg-primary' : 'bg-danger' }}">{{ $value->change_type ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $value->old_quantity ?? 0 }}</td>
                                <td>{{ $value->new_quantity ?? 0 }}</td>
                                <td>{{ $value->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <h3 class="text-center">No record available</h3>
        @endunless

        <div class="">
            {{ $items->links() }}
        </div>
    </div>
</div>
