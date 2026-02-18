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

        @unless (count($items) == 0)
            <div class="table-responsive">
                <table class="table-striped table">
                    <thead>

                        <tr>
                            <th scope="col">Store</th>
                            <th scope="col">Warehouse</th>
                            <th scope="col">Total</th>
                            <th scope="col">Reference</th>
                            <th scope="col">Date</th>
                            <th scope="col">Request Status</th>
                            <th scope="col">Action</th>
                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr wire:key="{{ $item->id }}">
                                <td>{{ $item->store->name ?? 'N/A' }} </td>
                                <td>{{ $item->warehouse->name ?? 'N/A' }} </td>
                                <td>{{ $item->total ?? 'N/A' }} </td>
                                <td> {{ $item->reference ?? 'N/A' }} </td>
                                <td> {{ $item->created_at->format('d M, Y H:i:s') }} </td>
                                <td> <span
                                        class="badge {{ $item->status === 'successful' ? 'bg-success' : 'bg-primary' }}">{{ $item->status }}</span>
                                </td>


                                <td>
                                    <div class="d-flex">

                                        <a href="{{ route('itemrequest.show', $item->id) }}"
                                            class="action-icon btn btn-warning me-2 text-white" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if ($item->status !== 'completed')
                                            <a href="{{ route('itemrequest.edit', $item->id) }}"
                                                class="action-icon btn btn-primary me-2 text-white" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Edit">
                                                <i class="fas fa-pen-square"></i>
                                            </a>
                                            <button
                                                onclick="return confirm('Are you sure you want to delete?') ||
                                                    event.stopImmediatePropagation()"
                                                wire:click="delete({{ $item->id }})"
                                                class="btn btn-danger action-icon text-white">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif


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
            {{ $items->links() }}
        </div>

    </div>
</div>
