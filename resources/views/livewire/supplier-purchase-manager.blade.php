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
                        class="form-control" placeholder="Search by date" />
                </div>



            </div>
        </div>

        @unless (count($purchases) == 0)
            <div class="table-responsive">
                <table class="table-hover table" id="report-table">
                    <thead class="table-primary">
                        <tr>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($purchases as $purchase)
                            <tr wire:key="{{ $purchase->id }}">
                                <td> {{ date_format($purchase->created_at, 'd-m-Y h:i:s') }} </td>
                                <td> {{ $purchase->reference }} </td>
                                <td>{{ number_format($purchase->total_amount, 2) }}</td>
                                <td>
                                    <span
                                        class="badge @if ($purchase->status == 'paid') bg-success
                                         @elseif($purchase->status == 'partial' || $purchase->status == 'unpaid') 
                                            bg-primary
                                         @elseif($purchase->status == 'voided') 
                                            bg-danger @endif">
                                        {{ $purchase->status }}
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route('supplier.item.detail', $purchase->id) }}"
                                        class="btn btn-sm btn-primary me-1" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Display items">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if ($purchase->status !== 'voided')
                                        <button onClick="confirmVoid({{ $purchase->id }})" class="btn btn-sm btn-danger"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Void purchase">
                                            <i class="fas fa-shopping-bag"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <h3 class="text-center">No
                record found</h3>
        @endunless


        <div class="">
            {{ $purchases->links() }}
        </div>

    </div>
</div>
@push('scripts')
    <script>
        function confirmVoid(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, void it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('voidPurchase', {
                        purchase: id
                    });
                }
            });
        }


        document.addEventListener('livewire:initialized', function() {
            Livewire.on('formSubmitted', (data) => {
                notyf.success(data.message)
            });
        });
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endpush
