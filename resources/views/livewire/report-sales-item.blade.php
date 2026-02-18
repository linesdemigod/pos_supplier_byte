<div>

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



    @if ($records && $records->count() > 0)
        <!-- Sales Table -->
        <div class='table-responsive mt-4'>
            <table class="table-centered w-100 dt-responsive nowrap table" id="report-tables">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">Item</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Subtotal</th>
                        <th scope="col">Returned Item</th>
                    </tr>
                </thead>
                <tbody id="sales-summary-body">
                    @foreach ($records as $data)
                        <tr wire:key="record-{{ $data->itemId }}">
                            <td>{{ $data->name }}</td>
                            <td>{{ $data->quantity }}</td>
                            <td>{{ $data->subtotal }}</td>
                            <td>{{ $data->item_returned }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $records->links() }}
        </div>
    @elseif($records && $records->count() === 0)
        <div class="mt-6 text-gray-500">
            No results found for the selected date range.
        </div>
    @endif
</div>
