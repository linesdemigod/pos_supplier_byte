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

        @unless (count($sales) == 0)
            <div class="table-responsive">
                <table class="table-striped table">
                    <thead>

                        <tr>

                            <th scope="col">Staff</th>
                            <th scope="col">Customer</th>
                            <th scope="col">Store</th>
                            <th scope="col">Reference #</th>
                            <th scope="col">Disc.</th>
                            <th scope="col">Subtotal</th>
                            <th scope="col">Grandtotal</th>
                            <th scope="col">Status</th>
                            <th scope="col">Created at</th>
                            <th scope="col">Action</th>

                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            <tr wire:key="{{ $sale->id }}">
                                <td>{{ $sale->user->name ?? 'N/A' }} </td>
                                <td>{{ $sale->customer->name ?? 'Walk-in' }} </td>
                                <td>{{ $sale->store->name ?? 'N/A' }} </td>
                                <td>{{ $sale->reference ?? 'N/A' }} </td>
                                <td> {{ $sale->discount ?? 'N/A' }} </td>
                                <td> {{ $sale->subtotal ?? 'N/A' }} </td>
                                <td> {{ $sale->grandtotal ?? 'N/A' }} </td>
                                <td><span
                                        class="badge @if ($sale->payment_status == 'paid') bg-success 
                                    @else
                                    bg-warning @endif">{{ $sale->payment_status }}</span>
                                </td>
                                <td> {{ $sale->created_at->format('d M, Y') }} </td>
                                <td>
                                    <div class="d-flex">
                                        @can('sale.show')
                                            <a href="{{ route('sale.show', $sale->id) }}"
                                                class="action-icon btn btn-primary me-2 text-white" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="show">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('sale.print')
                                            <a href="{{ route('sale.print', $sale->id) }}"
                                                class="action-icon btn btn-black me-2 text-white" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="print">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        @endcan
                                        <button
                                            onclick="return confirm('Are you sure you want to void?') ||
                              event.stopImmediatePropagation()"
                                            wire:click="void({{ $sale->id }})" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="void"
                                            class="btn btn-danger action-icon text-white">
                                            <i class="fas fa-ban"></i>
                                        </button>
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
            {{ $sales->links() }}
        </div>

    </div>
</div>
