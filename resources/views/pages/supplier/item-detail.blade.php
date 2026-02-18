@extends('layout.layout')

@section('title', 'Supplier Purchase items Detail')

@section('content')
    <x-breadcrumb title="{{ $purchaseItems->supplier->name }} Supplier" subtitle='Supplier item detail'
        name='Supplier Item Details' href='supplier.detail' id="{{ $purchaseItems->supplier->id }}" />

    <div class="card">
        <div class="card-body">

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-5">


                    <div class="col-md-4">
                        <h5 class="bg-primary mb-3 p-2 text-white">{{ $purchaseItems->supplier->name }}</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th class="">Phone:</th>
                                        <td class="text-muted text-end">{{ $purchaseItems->supplier->contact_info }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="">Email:</th>
                                        <td class="text-muted text-end">
                                            {{ $purchaseItems->supplier->email ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="">Address:</th>
                                        <td class="text-muted text-end">
                                            {{ $purchaseItems->supplier->address ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="">Date:</th>
                                        <td class="text-muted text-end">
                                            {{ date_format($purchase->created_at, 'd-m-Y') }}</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div class="">
                @unless (count($purchaseItems->supplierPurchaseItems) == 0)
                    <div class="table-responsive">
                        <table class="table-hover table" id="report-table">
                            <thead class="table-primary">
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Unit type</th>
                                    <th>Conversion rate</th>
                                    <th>Total units added</th>
                                    <th>Cost price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseItems->supplierPurchaseItems as $record)
                                    <tr>
                                        <td>
                                            {{ $record->item->name ?? '' }} <br>
                                            <small class="text-muted">{{ $record->item->description ?? '' }}</small>

                                        </td>
                                        <td>{{ $record->quantity }}</td>
                                        <td>{{ $record->purchase_unit_type }}</td>
                                        <td>{{ $record->conversion_rate }}</td>
                                        <td>{{ $record->total_units_added }}</td>
                                        <td>{{ $record->cost_price }}</td>
                                        <td class="text-end">{{ $record->subtotal }}</td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <th>Grand Total:</th>
                                    <td colspan="6" class="fw-bold text-end">{{ $purchase->total_amount }}</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                @else
                    <h3 class="text-center">No record found</h3>
                @endunless

            </div>
        </div>
    </div>
@endsection
