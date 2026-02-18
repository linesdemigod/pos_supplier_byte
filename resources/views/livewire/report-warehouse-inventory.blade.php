<div>

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
            <div class="d-flex justify-content-end align-items-end gap-3">

                <!-- Date Filters -->
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="selectedStoreId" class="form-label">Store</label>
                    </div>
                    <div class="col-auto">
                        <select name="selectedStoreId" id="selectedStoreId" wire:model="selectedStoreId"
                            class="form-select">
                            <option selected disabled>Select Store</option>
                            @foreach ($stores as $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach

                        </select>
                        @error('selectedStoreId')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="from" class="form-label">From</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" class="form-control form-control-light" id="startDate"
                            wire:model="startDate">
                        @error('startDate')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="to" class="form-label">To</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" class="form-control form-control-light" id="endDate"
                            wire:model="endDate">
                        @error('endDate')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <button type="button" wire:click="getSaleDetail"
                            class="form-control btn btn-primary btn-block">
                            Search
                        </button>
                    </div>
                </div>
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
                        <th scope="col">Price</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody id="sales-summary-body">
                    @foreach ($records as $data)
                        <tr wire:key="record-{{ $data->id }}">
                            <td>{{ $data->name }}</td>
                            <td>{{ $data->price }}</td>
                            <td>{{ $data->quantity }}</td>
                            <td>{{ $data->total }}</td>
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
