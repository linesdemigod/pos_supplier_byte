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
                        class="form-control" placeholder="Search by customer name, phone, email...." />
                </div>



            </div>
        </div>

        @unless (count($customers) == 0)
            <div class="table-responsive">
                <table class="table-hover table" id="report-table">
                    <thead class="table-primary">

                        <tr>

                            <th scope="col">Customer</th>
                            <th>Total Credit</th>
                            <th>Total Repaid</th>
                            <th scope="col">Outstanding</th>
                            <th scope="col">Action</th>

                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr wire:key="{{ $customer->id }}">
                                <td>
                                    {{ $customer->name ?? 'N/A' }} <br>
                                    <small class="text-success">{{ $customer->email ?? 'N/A' }}</small>,
                                    <small class="text-info">{{ $customer->phone ?? 'N/A' }}</small>

                                </td>
                                <td>{{ number_format($customer->total_credit_amount, 2) }}</td>
                                <td>{{ number_format($customer->total_repaid_amount, 2) }}</td>
                                <td> {{ number_format($customer->outstanding, 2) ?? 'N/A' }} </td>
                                <td>



                                    {{-- <a href="{{ route('credit.show', $customer->id) }}"
                                            class="action-icon btn btn-primary me-2 text-white" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="show">
                                            <i class="fas fa-eye"></i>
                                        </a> --}}

                                    <a href="{{ route('credit.summary', $customer->id) }}"
                                        class="btn btn-sm btn-primary me-1" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Credit summary">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('credit.detail', $customer->id) }}"
                                        class="btn btn-sm btn-success me-1" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="credit detail">
                                        <i class="fas fa-cog"></i>
                                    </a>



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
            {{ $customers->links() }}
        </div>

    </div>
</div>
