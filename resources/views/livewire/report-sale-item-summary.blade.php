<div>

    <div class="d-flex justify-content-end align-items-end mb-4 gap-3">

        <!-- Date Filters -->
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="from" class="col-form-label">From</label>
            </div>
            <div class="col-auto">
                <input type="date" class="form-control form-control-light" id="startDate" wire:model="startDate">
                @error('startDate')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="to" class="col-form-label">To</label>
            </div>
            <div class="col-auto">
                <input type="date" class="form-control form-control-light" id="endDate" wire:model="endDate">
                @error('endDate')
                    <span class="text-sm text-red-500">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <button type="button" wire:click="getSaleDetail" class="form-control btn btn-primary btn-block">
                    Search
                </button>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-body">

            @if ($records && $records->count() > 0)
                <!-- Sales Table -->
                <div class='table-responsive mt-4'>
                    <table class="table-centered w-100 dt-responsive nowrap table" id="report-tables">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Item</th>
                                <th scope="col">Quantity Sold</th>
                                <th scope="col">Unit Price</th>
                                <th scope="col">Total</th>
                            </tr>
                        </thead>
                        <tbody id="sales-summary-body">
                            @foreach ($records as $item)
                                <tr wire:key="record-{{ $item->itemId }}">
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->price }}</td>
                                    <td>{{ $item->subtotal }}</td>
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
    </div>

</div>
