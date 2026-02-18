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

        @unless (count($customers) == 0)
            <div class="table-responsive">
                <table class="table-striped table">
                    <thead>

                        <tr>

                            <th scope="col">Name</th>
                            <th scope="col">All Time Spend (₵)</th>
                            <th scope="col">All Time Average Spend (₵)</th>
                            <th scope="col">All Time Visits</th>


                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            @php
                                $visits = $customer->visits;
                                $grandtotal = $customer->grandtotal;

                                $average = $grandtotal / $visits;
                            @endphp
                            <tr wire:key="{{ $customer->id }}">
                                <td><a href="{{ route('report.customer.purchase', $customer->id) }}"
                                        class="text-decoration-none">{{ $customer->name }}</a></td>
                                <td>{{ number_format($customer->grandtotal, 2) }}</td>
                                <td>{{ number_format($average, 2) }}</td>
                                <td>{{ $customer->visits }}</td>
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
