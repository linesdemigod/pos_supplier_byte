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

                            <th scope="col">Store</th>
                            <th scope="col">Name</th>
                            <th scope="col">Telephone</th>
                            <th scope="col">Location</th>
                            <th scope="col">Action</th>

                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr wire:key="{{ $customer->id }}">
                                <td>{{ $customer->store->name ?? 'N/A' }} </td>
                                <td>{{ $customer->name ?? 'N/A' }} </td>
                                <td>{{ $customer->phone ?? 'N/A' }} </td>
                                <td>{{ $customer->location ?? 'N/A' }} </td>
                                <td>
                                    <div class="d-flex">
                                        @can('customer.edit')
                                            <a href="{{ route('customer.edit', $customer->id) }}"
                                                class="action-icon btn btn-primary me-2 text-white" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Edit">
                                                <i class="fas fa-pen-square"></i>
                                            </a>
                                        @endcan
                                        @can('customer.delete')
                                            <button
                                                onclick="return confirm('Are you sure you want to delete?') ||
                            event.stopImmediatePropagation()"
                                                wire:click="delete({{ $customer->id }})"
                                                class="btn btn-danger action-icon text-white">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan


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
            {{ $customers->links() }}
        </div>

    </div>
</div>
