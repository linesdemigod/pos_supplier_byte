@extends('layout.layout')

@section('title')
    {{ 'Show Request' }}
@endsection
@section('page-id', 'request_item')

@section('content')
    <x-breadcrumb title="Request Item" subtitle='Show Request Item' name='Request Item' href='storerequest.index' />
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
                                                    class="fw-normal">{{ $requestItems->store->name }}</span></p>
                                            <p class="fw-bold mb-0">Store location:
                                                <span class="fw-normal">{{ $requestItems->store->location }}</span>
                                            </p>

                                            <p class="fw-bold mb-0">Warehouse name:
                                                <span class="fw-normal">{{ $requestItems->warehouse->name }}</span>
                                            </p>
                                            <p class="fw-bold mb-0">Warehouse location:
                                                <span class="fw-normal">{{ $requestItems->warehouse->address }}</span>
                                            </p>

                                        </div>
                                        <div>
                                            <p class="fw-bold mb-0">Requested by: <span
                                                    class="fw-normal">{{ $requestItems->requestedBy->name }}</span></p>
                                            <p class="fw-bold mb-0">Requested date: <span
                                                    class="fw-normal">{{ $requestItems->requested_date }}</span>
                                            </p>
                                            <p class="fw-bold mb-0">Approved by: <span
                                                    class="fw-normal">{{ $requestItems->approvedBy->name ?? 'N/A' }}</span>
                                            </p>
                                            <p class="fw-bold mb-0">Approval date: <span
                                                    class="fw-normal">{{ $requestItems->approval_date ?? 'N/A' }}</span></p>
                                        </div>

                                        <div>
                                            <p class="fw-bold mb-0">Status: <span
                                                    class="text-success fw-normal request-status">{{ $requestItems->status }}
                                                </span></p>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-4">
                                    <div id="button-container">

                                        {{-- let only warehouse people see it --}}
                                        @if (Str::lower($requestItems->status) === 'pending' && Auth::user()->warehouse_id)
                                            <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">
                                                <button class="btn btn-primary approve" id="approve"
                                                    data-requested-value="{{ $requestItems->id }}"><i
                                                        class="fas fa-thumbs-up"></i>
                                                    Approve</button>
                                                <button class="btn btn-danger cancel" id="cancel"
                                                    data-requested-value="{{ $requestItems->id }}"><i
                                                        class="fas fa-ban"></i>
                                                    Cancel</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>


                            @unless (count($requestItems->storeRequestDetails) == 0)
                                <div class="table-responsive">
                                    <table class="table-centered table">
                                        <thead class="table-secondary">
                                            <tr>

                                                <th>#</th>
                                                <th>Item Code</th>
                                                <th>Item</th>
                                                <th>Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cart-table">
                                            @foreach ($requestItems->storeRequestDetails as $key => $data)
                                                <tr role="button">
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $data->item->item_code }}</td>
                                                    <td>{{ $data->item->name }}</td>
                                                    <td>{{ $data->requested_quantity }}</td>

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
