@extends('layout.layout')

@section('title')
    {{ 'Sale' }}
@endsection

@section('content')
    <x-breadcrumb title="Sales" subtitle='Sale Item' name='Sales' href='sale.index' />
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <div class="card border-1 mb-3 border">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xxl-8">
                                    <div class="d-flex mb-3 flex-wrap gap-4">
                                        <div>

                                            <p class="fw-bold mb-0">Store name: <span
                                                    class="fw-normal">{{ $sale->store->name }}</span></p>
                                            <p class="fw-bold mb-0">Staff:
                                                <span class="fw-normal">{{ $sale->user->name }}</span>
                                            </p>

                                            <p class="fw-bold mb-0">Customer:
                                                <span class="fw-normal">{{ $sale->customer->name ?? 'N/A' }}</span>
                                            </p>

                                        </div>
                                        <div>
                                            <p class="fw-bold mb-0">Discount:
                                                <span class="fw-normal">{{ $sale->discount ?? 0 }}</span>
                                            </p>

                                            <p class="fw-bold mb-0">Subtotal: <span
                                                    class="fw-normal">{{ $sale->subtotal ?? 0 }}</span>
                                            </p>
                                            <p class="fw-bold mb-0">Grandtotal: <span
                                                    class="fw-normal">{{ $sale->grandtotal ?? 0 }}</span></p>
                                            <p class="fw-bold mb-0">Date: <span
                                                    class="fw-normal">{{ $sale->created_at->format('d M, Y') ?? 'N/A' }}</span>
                                            </p>
                                        </div>

                                        <div>
                                            <p class="fw-bold mb-0">Reference: <span
                                                    class="text-success fw-normal">{{ $sale->reference }}
                                                </span></p>

                                        </div>
                                    </div>
                                </div>
                            </div>


                            @unless (count($sale->saleItems) == 0)
                                <div class="table-responsive">
                                    <table class="table-centered table">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th class="d-none">ID</th>
                                                <th>Item</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Total</th>
                                            </tr>

                                        </thead>
                                        <tbody id="cart-table">

                                            @foreach ($sale->saleItems as $data)
                                                <tr role="button">
                                                    <td class="d-none">{{ $data->item_id }}</td>
                                                    <td>{{ $data->item->name }}</td>
                                                    <td>{{ $data->quantity }}</td>
                                                    <td>{{ $data->price }}</td>
                                                    <td>{{ $data->total }}</td>
                                                </tr>
                                            @endforeach
                                            {{-- <tr>
                                                <td colspan="3">Total</td>
                                                <td>{{ $total }}</td>
                                            </tr> --}}
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
