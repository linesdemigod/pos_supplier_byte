<div>
    <div class="py-3">


        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center gap-5">
                {{-- create show perpage --}}
                <div class="d-flex align-items-center gap-2">
                    <label for="per_page">Show:</label>
                    <select wire:model.live="perPage" class="form-select form-select-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                {{-- create search input --}}
                <div class="d-flex align-items-center gap-2">
                    <label for="search">Search:</label>
                    <input wire:model.live.debounce.300ms="search" type="search" id="search" name="search"
                        class="form-control" />
                </div>



            </div>
        </div>

        @unless (count($inventories) == 0)
            <div class="table-responsive">
                <table class="table-striped table">
                    <thead>

                        <tr>

                            <th scope="col">Item Code</th>
                            <th scope="col">Category</th>
                            <th scope="col">Name</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Price</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>

                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($inventories as $inventory)
                            <tr wire:key="{{ $inventory->id }}">
                                <td>{{ $inventory->item->item_code ?? 'N/A' }} </td>
                                <td>{{ $inventory->item->category->name ?? 'N/A' }} </td>
                                <td>{{ $inventory->item->name ?? 'N/A' }} </td>
                                <td> {{ $inventory->quantity ?? 'N/A' }} </td>
                                <td> {{ $inventory->item->price ?? 'N/A' }} </td>
                                <td>
                                    <span
                                        class="{{ $inventory->quantity <= 0 ? 'text-danger' : ($inventory->quantity <= 20 ? 'text-primary' : '') }}">
                                        {{ $inventory->quantity <= 0 ? 'Out of Stock' : ($inventory->quantity <= 20 ? 'Running out of stock' : '') }}
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex">
                                        @can('warehouseInventory.update')
                                            <a href="{{ route('warehouseinventory.edit', $inventory->id) }}"
                                                class="action-icon btn btn-primary me-2 text-white" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Update Stock">
                                                <i class="fas fa-pen-square"></i>
                                            </a>
                                        @endcan

                                        {{-- <button
                                            onclick="return confirm('Are you sure you want to delete?') ||
                                                    event.stopImmediatePropagation()"
                                            wire:click="delete({{ $inventory->id }})"
                                            class="btn btn-danger action-icon text-white">
                                            <i class="fas fa-trash"></i>
                                        </button> --}}


                                    </div>
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
            {{ $inventories->links() }}
        </div>

    </div>
</div>
