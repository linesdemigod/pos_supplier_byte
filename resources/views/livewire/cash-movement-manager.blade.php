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

        @unless (count($records) == 0)
            <div class="table-responsive">
                <table class="table-striped table">
                    <thead>

                        <tr>

                            <th scope="col">Date</th>
                            <th scope="col">Requested By</th>
                            <th scope="col">Type</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Status</th>
                            <th scope="col">Reason</th>
                            <th scope="col">Approved By</th>
                            <th scope="col">Action</th>

                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($records as $record)
                            <tr wire:key="{{ $record->id }}">
                                <td>{{ $record->created_at->format('d/m/Y') }} </td>
                                <td>{{ $record->user?->name }} </td>
                                <td>cash {{ $record->type }} </td>
                                <td>{{ $record->amount }} </td>
                                <td>
                                    <span
                                        class="badge @if ($record->status == 'approved') bg-success 
                                    @elseif($record->status == 'rejected')
                                    bg-danger
                                    @else
                                    bg-warning @endif">{{ $record->status }}</span>
                                </td>
                                <td>{{ $record->reason ?? 'N/A' }} </td>
                                <td>{{ $record->approvedBy?->name }} </td>


                                <td>
                                    <div class="d-flex">
                                        @can('cash_movement.edit')
                                            <a href="{{ route('cash_movement.edit', $record->id) }}"
                                                class="action-icon btn btn-primary me-2 text-white" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Edit">
                                                <i class="fas fa-pen-square"></i>
                                            </a>
                                        @endcan

                                        @can('cash_movement.approve')
                                            <button
                                                onclick="return confirm('Are you sure you want to approve?') ||
                              event.stopImmediatePropagation()"
                                                wire:click="approval({{ $record->id }})"
                                                class="btn btn-success action-icon me-2 text-white">
                                                <i class="fas fa-thumbs-up"></i>
                                            </button>
                                        @endcan

                                        @can('cash_movement.delete')
                                            <button
                                                onclick="return confirm('Are you sure you want to delete?') ||
                              event.stopImmediatePropagation()"
                                                wire:click="delete({{ $record->id }})"
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
            {{ $records->links() }}
        </div>

    </div>
</div>
