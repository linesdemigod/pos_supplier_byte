@extends('layout.layout')

@section('title')
    {{ 'Store' }}
@endsection

@section('content')
    <x-breadcrumb title="Create Store" subtitle='Store' name='Store' href='store.create' />
    <section class="py-2">
        <div class="contain-fluid">
            <div class="row">
                <div class="col">
                    <div class="card border-1 border">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-5">
                                @can('store.create')
                                    <a href="{{ route('store.create') }}" class="btn btn-primary">
                                        <i class="uil-plus"></i>
                                        Create Store</a>
                                @endcan



                            </div>


                            @unless (count($stores) == 0)
                                <div class='table-responsive'>
                                    <table class="table-centered table-striped table">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col">SN</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Location</th>
                                                <th scope="col">Telephone</th>
                                                <th scope="col">Created At</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($stores as $key => $store)
                                                <tr>
                                                    <td>{{ $key + 1 }} </td>
                                                    <td> {{ $store->name }} </td>
                                                    <td> {{ $store->location }} </td>
                                                    <td> {{ $store->phone ?? 'N/A' }} </td>
                                                    <td> {{ date('d M, Y', strtotime($store->created_at)) }} </td>
                                                    <td class="table-action">

                                                        <div class="d-flex gap-2">

                                                            {{-- edit --}}
                                                            @can('store.edit')
                                                                <a href="{{ route('store.edit', $store->id) }}"
                                                                    class="action-icon btn btn-primary me-2 text-white"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                                    <i class="fas fa-pen"></i></a>
                                                            @endcan
                                                            {{-- delete -- Hide button --}}
                                                            @can('store.delete')
                                                                <form action="{{ route('store.destroy', $store->id) }}"
                                                                    method="post">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-danger action-icon text-white" value="Delete"
                                                                        id="table-delete" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" title="Delete"
                                                                        onclick="return confirm('Are you sure you want to delete?');"><i
                                                                            class="fas fa-trash"></i></button>
                                                                </form>
                                                            @endcan

                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <h3 class="text-center">No data available</h3>
                            @endunless

                            <div class="">
                                {{ $stores->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
