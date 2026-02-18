@extends('layout.front-layout')

@section('title')
    {{ 'Store' }}
@endsection

@section('content')

    <section class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12">

                    <h1 class="text-center">Store Selection</h1>
                </div>
            </div>
            <div class="row">
                @unless (count($stores) == 0)
                    @foreach ($stores as $store)
                        <div class="col-sm-12 col-md-3 col-lg-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h2 class="card-title fw-bold fs-4">{{ $store->name }}</h2>
                                    <p class="card-text">{{ $store->location }}</p>
                                    <form action="{{ route('dashboard.store.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" class="form-control" value="{{ $store->id }}">
                                        <input type="Submit" name="submit" class="btn btn-primary" value="Go">
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="fw-bold fs-2 text-center">No store available</p>
                @endunless
            </div>
        </div>

        <div class="container mt-5">
            <div class="row mb-2">
                <div class="col-12">
                    <h1 class="text-center">Warehouse Selection</h1>
                </div>
            </div>
            <div class="row">
                @unless (count($warehouses) == 0)
                    @foreach ($warehouses as $warehouse)
                        <div class="col-sm-12 col-md-3 col-lg-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h2 class="card-title fw-bold fs-4">{{ $warehouse->name }}</h2>
                                    <p class="card-text">{{ $warehouse->address ?? 'N/A' }}</p>
                                    <form action="{{ route('dashboard.warehouse.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" class="form-control" value="{{ $warehouse->id }}">
                                        <input type="Submit" name="submit" class="btn btn-primary" value="Go">
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="fw-bold fs-2 text-center">No warehouse available</p>
                @endunless
            </div>
        </div>
    </section>
@endsection
