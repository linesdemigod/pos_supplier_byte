@extends('layout.layout')

@section('title')
    {{ 'Show Transfer Order' }}
@endsection
@section('page-id', 'request_item')

@section('content')
    <x-breadcrumb title="Transfer Order Item" subtitle='Show Transfer Order Item' name='Transfer Order Item'
        href='transfer.index' />
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
                                                    class="fw-normal">{{ $transfer->store->name }}</span></p>
                                            <p class="fw-bold mb-0">Store location:
                                                <span class="fw-normal">{{ $transfer->store->location }}</span>
                                            </p>

                                            <p class="fw-bold mb-0">Warehouse name:
                                                <span class="fw-normal">{{ $transfer->warehouse->name }}</span>
                                            </p>
                                            <p class="fw-bold mb-0">Warehouse location:
                                                <span class="fw-normal">{{ $transfer->warehouse->address }}</span>
                                            </p>

                                        </div>
                                        <div>

                                            <p class="fw-bold mb-0">Approved by: <span
                                                    class="fw-normal">{{ $transfer->user->name ?? 'N/A' }}</span>
                                            </p>
                                            <p class="fw-bold mb-0">Approval date: <span
                                                    class="fw-normal">{{ $transfer->created_at ?? 'N/A' }}</span></p>
                                            <p class="fw-bold mb-0">Updated Date: <span
                                                    class="fw-normal">{{ $transfer->updated_at ?? 'N/A' }}</span></p>
                                        </div>

                                        <div>
                                            <p class="fw-bold mb-0">Status: <span
                                                    class="text-success fw-normal request-status">{{ $transfer->status }}
                                                </span></p>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-4">
                                    <div id="button-container">

                                        {{-- let only store people see it --}}
                                        @if (Str::lower($transfer->status) === 'dispatched' && Auth::user()->store_id)
                                            <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">
                                                <button class="btn btn-primary delivered" id="delivered"
                                                    data-requested-value="{{ $transfer->id }}"><i
                                                        class="fas fa-thumbs-up"></i>
                                                    Delivered</button>

                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>


                            @unless (count($transfer->transferOrderDetails) == 0)
                                <div class="table-responsive">
                                    <table class="table-centered table">
                                        <thead class="table-secondary">
                                            <tr>

                                                <th>#</th>
                                                <th>Item Code</th>
                                                <th>Item</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                            </tr>

                                        </thead>
                                        <tbody id="cart-table">

                                            @foreach ($transfer->transferOrderDetails as $key => $data)
                                                <tr role="button">
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $data->item->item_code }}</td>
                                                    <td>{{ $data->item->name }}</td>
                                                    <td>{{ $data->quantity }}</td>
                                                    <td>PCS</td>
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
