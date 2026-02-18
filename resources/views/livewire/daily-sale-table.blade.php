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
                            <th scope="col">Store</th>
                            <th scope="col">Name</th>
                            <th scope="col">Date</th>
                            <th scope="col">Total Sales</th>
                            <th scope="col">Open Time</th>
                            <th scope="col">Close Time</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($sales as $key => $sale)
                            <tr>
                                <td>{{ $sale->store->name ?? 'N/A' }} </td>
                                <td> {{ $sale->user->name }} </td>
                                <td> {{ $sale->date }} </td>
                                <td> {{ $sale->total_sales }} </td>
                                <td> {{ $sale->open_time }} </td>
                                <td> {{ $sale->close_time }} </td>
                                <td> <span
                                        class="badge {{ $sale->status === 'open' ? 'bg-warning' : 'bg-primary' }}">{{ $sale->status }}</span>
                                </td>
                                <td>
                                    @if ($sale->status === 'open')
                                        <button
                                            onclick="return confirm('Are you sure you want to end?') ||
                                                    event.stopImmediatePropagation()"
                                            wire:click="endDaySale({{ $sale->id }})"
                                            class="btn btn-danger action-icon text-white">
                                            End Day
                                        </button>
                                    @endif
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
