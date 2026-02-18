@extends('layout.layout')

@section('title')
    {{ 'Sale Point Permissions' }}
@endsection

@section('content')
    <x-breadcrumb title="Apps" subtitle='Sale Point Permissions' name='Sale Point Permissions' />
    <div class="py-3">
        <div class="card">

            <div class="card-body">

                <div class="table-responsive">

                    @if ($sales->count() > 0)
                        <table class="table-centered w-100 table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Permission</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                    <tr>

                                        <td>{{ Str::replace('_', ' ', Str::title($sale->permission_name)) ?? 'N/A' }} </td>
                                        <td> {{ $sale->status === 1 ? 'allowed' : 'disallowed' }} </td>
                                        <td>
                                            <a href="{{ route('permission.sale.point.edit', $sale->id) }}"
                                                class="action-icon btn btn-primary me-2 text-white" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Edit">
                                                <i class="fas fa-pen-square"></i></a>
                                        </td>



                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    @else
                        <h3 class="text-center">No record available</h3>
                    @endif


                </div>

            </div>
        </div>
    </div>
@endsection
