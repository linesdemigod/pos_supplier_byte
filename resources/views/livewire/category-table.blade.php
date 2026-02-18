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

        @unless (count($categories) == 0)
            <div class="table-responsive">
                <table class="table-striped table">
                    <thead>

                        <tr>

                            <th scope="col">Category Code</th>
                            <th scope="col">Name</th>
                            <th scope="col">Description</th>
                            <th scope="col">Action</th>

                        </tr>

                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr wire:key="{{ $category->id }}">
                                <td>{{ $category->category_code ?? 'N/A' }} </td>
                                <td>{{ $category->name ?? 'N/A' }} </td>
                                <td>{{ $category->description ?? 'N/A' }} </td>


                                <td>
                                    <div class="d-flex">
                                        @can('category.edit')
                                            <a href="{{ route('category.edit', $category->id) }}"
                                                class="action-icon btn btn-primary me-2 text-white" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Edit">
                                                <i class="fas fa-pen-square"></i>
                                            </a>
                                        @endcan
                                        @can('category.delete')
                                            <button
                                                onclick="return confirm('Are you sure you want to delete?') ||
                              event.stopImmediatePropagation()"
                                                wire:click="delete({{ $category->id }})"
                                                class="btn btn-danger action-icon text-white">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan


                                        {{-- <form action="{{ route('item.destroy', $item->id) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger action-icon text-white"
                                                        value="Delete" id="table-delete" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete?');"
                                                        data-id={{ $item->id }}><i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form> --}}
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
            {{ $categories->links() }}
        </div>

    </div>
</div>
