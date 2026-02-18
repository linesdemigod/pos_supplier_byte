@extends('layout.layout')

@section('title')
    {{ 'Edit Request For Item' }}
@endsection
@section('page-id', 'request_item')

@section('content')
    <x-breadcrumb title="Request Item" subtitle='Add Request Item' name='Request Item' href='itemrequest.index' />
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <div class="card border-1 mb-3 border">
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-4">
                                <p>
                                    <strong>Store Name:</strong> {{ $requestItems->store->name }} <br>
                                    <strong>Store Location:</strong> {{ $requestItems->store->location }}
                                </p>
                                <p>
                                    <strong>Warehouse Name:</strong> {{ $requestItems->warehouse->name }} <br>
                                    <strong>Warehouse Location:</strong> {{ $requestItems->warehouse->address }}
                                </p>
                                <p>
                                    <strong>Total:</strong> {{ $requestItems->total }} <br>
                                    <strong>Reference</strong> {{ $requestItems->reference }}
                                </p>
                                <p>
                                    <strong>Status:</strong> <span class="text-success">{{ $requestItems->status }} </span>

                                </p>
                            </div>

                            @unless (count($requestItems->itemRequestDetails) == 0)
                                <div class="table-responsive">
                                    <table class="table-centered table">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th class="d-none">ID</th>
                                                <th>Item</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>Total</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cart-table">
                                            @foreach ($requestItems->itemRequestDetails as $data)
                                                <tr role="button">
                                                    <td class="d-none">{{ $data->item_id }}</td>
                                                    <td>{{ $data->item->name }}</td>
                                                    <td>{{ $data->price }}</td>
                                                    <td>{{ $data->quantity }}</td>
                                                    <td>{{ $data->total }}</td>
                                                    <td>{{ $data->created_at->format('d M, Y H:s:i') }}</td>


                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <h3 class="text-center">No record available</h3>
                            @endunless
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
