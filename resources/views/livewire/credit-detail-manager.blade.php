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

        @unless (count($credits) == 0)
            <div class="table-responsive">
                <table class="table-hover table" id="report-table">
                    <thead class="table-primary">
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>reference</th>
                            <th>Subtotal</th>
                            <th scope="col">Discount</th>
                            <th scope="col">Total</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($credits as $credit)
                            <tr wire:key="{{ $credit->id }}">
                                <td> {{ date_format($credit->created_at, 'd-m-Y h:i:s') }} </td>
                                <td> {{ $credit->user?->name }} </td>
                                <td> {{ $credit->reference }} </td>
                                <td>{{ number_format($credit->subtotal, 2) }}</td>
                                <td>{{ number_format($credit->discount, 2) }}</td>
                                <td> {{ number_format($credit->total_amount, 2) ?? 'N/A' }} </td>
                                <td><span
                                        class="badge {{ ($credit->status === 'paid' ? 'bg-success' : $credit->status === 'voided') ? 'bg-danger' : 'bg-primary' }}">{{ $credit->status }}</span>
                                </td>

                                <td>
                                    <a href="{{ route('credit.item.detail', $credit->id) }}"
                                        class="btn btn-sm btn-primary me-1" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Display items">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('credit.void')
                                        @if ($credit->status !== 'voided')
                                            <button onClick="confirmVoid({{ $credit->id }})" class="btn btn-sm btn-danger"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Void credit">
                                                <i class="feather icon-trash"></i>
                                            </button>
                                        @endif
                                    @endcan
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
            {{ $credits->links() }}
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
                    Livewire.dispatch('voidCredit', {
                        credit: id
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
