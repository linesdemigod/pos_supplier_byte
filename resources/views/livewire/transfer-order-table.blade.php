<div>
    <div class="py-3">


        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center gap-5">
                {{-- create show perpage --}}
                <div class="d-flex align-items center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <label for="per_page">Show:</label>
                        <select wire:model.live="perPage" class="form-select form-select-sm">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label for="status">Status:</label>
                        <select wire:model.live="status" class="form-select form-select-sm">
                            <option value="all">All</option>
                            <option value="pending">Pending</option>
                            <option value="dispatched">Dispatched</option>
                            <option value="delivered">Delivered</option>
                        </select>
                    </div>

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
                            <th scope="col">Approved By</th>
                            <th scope="col">Requested Date</th>
                            <th scope="col">Approved Dated</th>
                            <th scope="col">Reference</th>
                            <th scope="col">Order Number</th>
                            <th scope="col">Transfer Status</th>
                            <th scope="col">Action</th>
                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr wire:key="{{ $item->id }}">
                                <td>{{ $item->store->name ?? 'N/A' }} </td>
                                <td>{{ $item->warehouse->name ?? 'N/A' }} </td>
                                <td>{{ $item->user->name ?? 'N/A' }} </td>
                                <td>{{ $item->storeRequest->requested_date ?? 'N/A' }} </td>
                                <td> {{ $item->storeRequest->approval_date ?? 'N/A' }} </td>
                                <td> {{ $item->storeRequest->reference }} </td>
                                <td> {{ $item->order_number }} </td>
                                <td> <span
                                        class="badge {{ $item->status === 'approved' ? 'bg-success' : 'bg-primary' }}">{{ $item->status }}</span>
                                </td>


                                <td>
                                    <div class="d-flex">
                                        {{-- view --}}
                                        @can('transferOrder.menu')
                                            <a href="{{ route('transfer.show', $item->id) }}"
                                                class="action-icon btn btn-warning me-2 text-white" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan
                                        @if ($item->status !== 'approved' && Auth::user()->warehouse_id)
                                            {{-- edit --}}
                                            <a href="{{ route('transfer.edit', $item->id) }}"
                                                class="action-icon btn btn-primary me-2 text-white" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Acknowledge">
                                                <i class="fas fa-pen-square"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('transfer.print', $item->id) }}"
                                            class="action-icon btn btn-black me-2 text-white" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Print">
                                            <i class="fas fa-print"></i>
                                        </a>

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
