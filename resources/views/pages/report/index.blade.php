@extends('layout.layout')

@section('title')
    {{ 'Report' }}
@endsection
@section('content')
    <section class="py-5">
        <div class="container">


            <div class="">
                <div class="row">
                    <div class="col-xxl-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-shopping-cart"></i>
                                    Store Sales Report
                                </h5>

                                <div class="">
                                    <ul>
                                        <li><a href="{{ route('report.sale.analytics') }}" class="">Sales
                                                Summary</a></li>
                                        <li><a href="{{ route('report.summary') }}" class="">Sales Details</a></li>
                                        <li><a href="{{ route('report.sale.trends') }}" class="">Sales Trends</a></li>
                                        <li><a href="{{ route('report.item.summary') }}" class="">Item
                                                Summary</a>
                                        <li><a href="{{ route('report.sale.items') }}" class="">Sales by Item</a>
                                        </li>
                                    </ul>


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-plug"></i>
                                    Inventory Report
                                </h5>

                                <div class="">
                                    <ul>

                                        <li><a href="{{ route('report.item.stock') }}" class="">Stock History</a></li>
                                        <li><a href="{{ route('report.item.price') }}" class="">Price History</a></li>
                                    </ul>


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-users"></i>
                                    User Report
                                </h5>
                                <div class="">
                                    <ul>
                                        <li><a href="{{ route('report.staff.sales') }}" class="">User Sales</a>
                                        </li>
                                        <li><a href="{{ route('report.shift') }}" class="">User Shift</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xxl-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-warehouse"></i>
                                    Warehouse Report
                                </h5>

                                <div class="">
                                    <ul>
                                        <li><a href="{{ route('report.warehouse') }}" class="">Inventory
                                                Report</a></li>
                                    </ul>


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-user-tie"></i>
                                    Customer Report
                                </h5>

                                <div class="">
                                    <ul>
                                        <li><a href="{{ route('report.customer.index') }}" class="">Purchase
                                                History</a>
                                        </li>
                                    </ul>


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4">

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
