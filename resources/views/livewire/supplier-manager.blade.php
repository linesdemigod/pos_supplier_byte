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

        @unless (count($suppliers) == 0)
            <div class="table-responsive">
                <table class="table-hover table" id="report-table">
                    <thead class="table-primary">

                        <tr>

                            <th scope="col">Supplier</th>
                            <th>Contact Info</th>
                            <th>Address</th>
                            <th scope="col">Action</th>

                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr wire:key="{{ $supplier->id }}">
                                <td>
                                    {{ $supplier->name ?? 'N/A' }} <br>
                                    <span class="text-success">{{ $supplier->email ?? '' }}</span>

                                </td>
                                <td>{{ $supplier->contact_info ?? '' }}</td>
                                <td>{{ $supplier->address ?? '' }}</td>
                                <td>

                                    <a href="{{ route('supplier.show', $supplier->id) }}"
                                        class="btn btn-sm btn-success me-1" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Payment Summary">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('supplier.detail', $supplier->id) }}"
                                        class="btn btn-sm btn-primary me-1" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Purchase Summary">
                                        <i class="fas fa-shopping-bag"></i>
                                    </a>



                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <h3 class="text-center">No record found</h3>
        @endunless


        <div class="">
            {{ $suppliers->links() }}
        </div>

    </div>
</div>
