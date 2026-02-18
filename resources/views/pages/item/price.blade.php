@extends('layout.layout')

@section('title')
    {{ 'Price Adjustment' }}
@endsection
@section('page-id', 'inventory_create')

@section('content')
    <x-breadcrumb title="Price Adjustment" subtitle='Price Adjustment' name='Price Adjustment' />
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card border-1 border">
                        <div class="card-body">
                            <h3 class="py-2 text-center">Adjust Price</h3>
                            <div class="item-search-container form-group mb-3">
                                <label for="form-label">Search Item</label>
                                <input type="text" name="item" class="form-control" id="item-search"
                                    autocomplete="off" placeholder="Start typing a item...">
                                <ul id="suggestions" class="autocomplete-list">
                                </ul>
                            </div>
                            <form action="{{ route('item.price.adjustment.store') }}" method="Post">
                                @csrf
                                <input type="hidden" name="id" class="form-control" id="item-value"
                                    value="{{ old('id') }}">
                                <div class="form-group mb-3">
                                    <label for="name" class="text-muted">Name</label>
                                    <input type="text" class="form-control" name="name" id="item-name"
                                        value="{{ old('name') }}" readonly>
                                    @error('item_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>
                                <div class="form-group mb-3">
                                    <label for="current-price" class="text-muted">Current Price</label>
                                    <input type="number" class="form-control" name="current-price" id="current-price"
                                        readonly>

                                </div>
                                <div class="form-group mb-3">
                                    <label for="price" class="text-muted">New Price</label>
                                    <input type="text" class="form-control" name="price" id="price"
                                        value="{{ old('price') }}" min="0">
                                    @error('price')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <button type="submit" name="submit"
                                        class="form-control btn btn-primary btn-block">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
