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
        {{-- @if ($sales->count() < 1) --}}
        <button
            onclick="return confirm('Are you sure you want to open the month sale session?') ||
                                                    event.preventDefault()"
            wire:click="openMonthSale()" class="btn btn-primary action-icon text-white">
            Open Month
        </button>
        {{-- @endif --}}
        <x-flash-message />
        @unless (count($sales) == 0)
            <div class="table-responsive">
                <table class="table-striped table">
                    <thead>

                        <tr>
                            <th scope="col">Store</th>
                            <th scope="col">Name</th>
                            <th scope="col">Month</th>
                            <th scope="col">Year</th>
                            <th scope="col">Total Sales</th>
                            <th scope="col">Open Date</th>
                            <th scope="col">Close Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($sales as $key => $sale)
                            <tr>
                                <td>{{ $sale->store->name ?? 'N/A' }} </td>
                                <td> {{ $sale->user->name }} </td>
                                <td> {{ $sale->month }} </td>
                                <td> {{ $sale->year }} </td>
                                <td> {{ $sale->total_sales }} </td>
                                <td> {{ $sale->open_date }} </td>
                                <td> {{ $sale->close_date }} </td>
                                <td> <span
                                        class="badge {{ $sale->status === 'open' ? 'bg-warning' : 'bg-primary' }}">{{ $sale->status }}</span>
                                </td>
                                <td>
                                    @if ($sale->status === 'open')
                                        <button
                                            onclick="return confirm('Are you sure you want to end?') ||
                                                    event.stopImmediatePropagation()"
                                            wire:click="endMonthSale({{ $sale->id }})"
                                            class="btn btn-danger action-icon text-white">
                                            End Month
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
