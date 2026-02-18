@extends('layout.layout')

@section('title')
    {{ 'Report Customers' }}
@endsection
@section('content')
    <section class="pt-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <p class="fs-3">Customer > {{ $customer->name }}</p>

                <div class="">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('report.customer.index') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Customers</li>
                        </ol>
                    </nav>
                </div>

            </div>
        </div>
    </section>
    {{-- employee table --}}
    <section>
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-sm-12 col-md-5 col-lg-5">
                    <div class="d-flex flex-column mb-2 gap-2">
                        <div class="">
                            <div class="card">
                                <div class="card-body">
                                    <p class="card-title fs-3">{{ $customer->name }}</p>
                                    <p class="card-text text-muted mb-0"> <i class="uil-map-marker-shield"></i>
                                        {{ $customer->address }}</p>
                                    <p class="card-text text-muted mb-0"> <i class="uil-calling"></i> {{ $customer->phone }}
                                    </p>
                                    <p class="card-text text-muted"> <i class="uil-user-square"></i> {{ $customer->gender }}
                                    </p>

                                    <div class="">
                                        <p class="card-text mb-0">Popular Items</p>
                                        @foreach ($frequentItemPurchase as $item)
                                            <span class="text-primary">{{ $item->name }} <span
                                                    class="text-muted">({{ $item->quantity }})</span> </span>,
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-4 col-lg-4">
                    <div class="d-flex flex-column mb-2">
                        @foreach ($records as $record)
                            <div class="">
                                <div class="card">
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"
                                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex gap-2">
                                            <div class="">

                                                <p class="card-title fs-3 fw-semibold text-success mb-0">₵
                                                    {{ number_format($record->grandtotal, 2) }}
                                                </p>
                                                <p class="card-text">Total Sales</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <div class="card">
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 25%"
                                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-title fs-3 fw-semibold text-success mb-0">{{ $record->visits }}
                                        </p>
                                        <p class="card-text">Total Visits</p>
                                    </div>
                                </div>
                            </div>
                            @php
                                $visits = $record->visits;
                                $grandtotal = $record->grandtotal;

                                $average = $grandtotal / $visits;
                            @endphp
                            <div class="">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="card-title fs-3 fw-semibold text-primary mb-0">₵
                                            {{ number_format($average, 2) }}
                                        </p>
                                        <p class="card-text">Average Spend</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
