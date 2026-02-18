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

    {{-- total summary --}}
    <div class="row">
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big icon-primary bubble-shadow-small text-center">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-sm-0 ms-3">
                            <div class="numbers">
                                <p class="card-category">Grandtotal</p>
                                <h4 class="card-title">{{ number_format($grandtotalSum, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big icon-info bubble-shadow-small text-center">
                                <i class="fas fa-donate"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-sm-0 ms-3">
                            <div class="numbers">
                                <p class="card-category">Subtotal</p>
                                <h4 class="card-title">{{ number_format($subtotalSum, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big icon-success bubble-shadow-small text-center">
                                <i class="fab fa-creative-commons-nc"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-sm-0 ms-3">
                            <div class="numbers">
                                <p class="card-category">Discount</p>
                                <h4 class="card-title">{{ number_format($discountSum, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big icon-secondary bubble-shadow-small text-center">
                                <i class="fab fa-cuttlefish"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-sm-0 ms-3">
                            <div class="numbers">
                                <p class="card-category">Returned Items Count</p>
                                <h4 class="card-title">{{ number_format($itemReturnedSum, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
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
                                <th scope="col">Staff</th>
                                <th scope="col">Reference</th>
                                <th scope="col">Discount</th>
                                <th scope="col">Subtotal</th>
                                <th scope="col">Grandtotal</th>
                                <th scope="col">Created At</th>
                            </tr>
                        </thead>
                        <tbody id="sales-summary-body">
                            @foreach ($records as $item)
                                <tr wire:key="record-{{ $item->id }}">
                                    <td>{{ $item->user->name }}</td>
                                    <td>{{ $item->reference }}</td>
                                    <td>{{ $item->discount }}</td>
                                    <td>{{ $item->subtotal }}</td>
                                    <td>{{ $item->grandtotal }}</td>
                                    <td>{{ $item->created_at->format('d M, Y') }}</td>
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
