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
                            <th scope="col">Reference</th>
                            <th scope="col">Item</th>
                            <th scope="col">Price</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Total</th>
                            <th scope="col">Purpose</th>
                            <th scope="col">Purchase Date</th>
                            <th scope="col">Return Date</th>
                            <th scope="col">Action</th>
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
                                <td>{{ $value->reference ?? 'N/A' }}</td>
                                <td>{{ $value->item->name ?? 'N/A' }}</td>
                                <td>{{ $value->price ?? 0 }}</td>
                                <td>{{ $value->quantity ?? 0 }}</td>
                                <td>{{ $value->total ?? 0 }}</td>
                                <td>{{ $value->reason ?? 'N/A' }}</td>
                                <td>{{ $value->purchase_date ?? 'N/A' }}</td>
                                <td>{{ $value->return_date ?? 'N/A' }}</td>
                                <td>
                                    @can('returnItem.delete')
                                        <button
                                            onclick="return confirm('Are you sure you want to delete?') ||
                                                      event.stopImmediatePropagation()"
                                            wire:click="delete({{ $value->id }})"
                                            class="btn btn-danger action-icon text-white">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </td>
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
