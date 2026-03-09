@extends('layout.layout')

@section('title', 'Credit items Detail')

@section('content')
    <x-breadcrumb title="{{ $creditItems->customer->name }} Credit" subtitle='Credit item detail' name='Credit Item Details'
        href='credit.detail' id="{{ $creditItems->customer->id }}" />

    <div class="card">
        <div class="card-body">

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-5">


                    <div class="col-md-4">
                        <h5 class="bg-primary mb-3 p-2 text-white">{{ $creditItems->customer->name }}</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th class="">Phone:</th>
                                        <td class="text-muted text-end">{{ $creditItems->customer->phone ?? 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="">Address:</th>
                                        <td class="text-muted text-end">
                                            {{ $creditItems->customer->location ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="">Date:</th>
                                        <td class="text-muted text-end">
                                            {{ date_format($credit->created_at, 'd-m-Y') }}</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div class="">
                @unless (count($creditItems->creditItems) == 0)
                    <div class="table-responsive">
                        <table class="table-hover table" id="report-table">
                            <thead class="table-primary">
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($creditItems->creditItems as $record)
                                    <tr>
                                        <td>
                                            {{ $record->item->name }} <br>
                                            <small class="text-muted">{{ $record->item->description ?? '' }}</small>

                                        </td>
                                        <td>{{ $record->quantity }}</td>
                                        <td>{{ $record->price }}</td>
                                        <td class="text-end">{{ $record->total }}</td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <th>Grand Total:</th>
                                    <td colspan="3" class="fw-bold text-end">{{ $credit->total_amount }}</td>
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
