@extends('layout.layout')

@section('title')
    {{ 'Warehouse' }}
@endsection

@section('content')
    <x-breadcrumb title="Create Warehouse" subtitle='Warehouse' name='Warehouse' href='warehouse.create' />
    <section class="py-2">
        <div class="contain-fluid">
            <div class="row">
                <div class="col">
                    <div class="card border-1 border">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-5">
                                @can('warehouse.create')
                                    <a href="{{ route('warehouse.create') }}" class="btn btn-primary">
                                        <i class="uil-plus"></i>
                                        Create Warehouse</a>
                                @endcan



                            </div>


                            @unless (count($warehouses) == 0)
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
                                            @foreach ($warehouses as $key => $warehouse)
                                                <tr>
                                                    <td>{{ $key + 1 }} </td>
                                                    <td> {{ $warehouse->name }} </td>
                                                    <td> {{ $warehouse->address }} </td>
                                                    <td> {{ $warehouse->phone ?? 'N/A' }} </td>
                                                    <td> {{ date('d M, Y', strtotime($warehouse->created_at)) }} </td>
                                                    <td class="table-action">

                                                        <div class="d-flex gap-2">

                                                            {{-- edit --}}
                                                            @can('warehouse.edit')
                                                                <a href="{{ route('warehouse.edit', $warehouse->id) }}"
                                                                    class="action-icon btn btn-primary me-2 text-white"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                                    <i class="fas fa-pen"></i></a>
                                                            @endcan
                                                            @can('warehouse.delete')
                                                                {{-- delete -- Hide button --}}
                                                                <form action="{{ route('warehouse.destroy', $warehouse->id) }}"
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
                                {{ $warehouses->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
