@extends('layout.layout')

@section('title')
    {{ 'Add Store Inventory' }}
@endsection
@section('page-id', 'inventory_create')

@section('content')
    <x-breadcrumb title="Store Inventory" subtitle='Add Store Inventory' name='Store Inventory' href='storeinventory.index' />
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card border-1 border">
                        <div class="card-body">
                            <h3 class="py-2 text-center">Add Item</h3>
                            <div class="item-search-container form-group mb-3">
                                <label for="form-label">Search Item</label>
                                <input type="text" name="item" class="form-control" id="item-search"
                                    autocomplete="off" placeholder="Start typing a item...">
                                <ul id="suggestions" class="autocomplete-list">
                                </ul>
                            </div>
                            <form action="{{ route('storeinventory.store') }}" method="Post">
                                @csrf
                                <input type="hidden" name="item_id" class="form-control" id="item-value"
                                    value="{{ old('item_id') }}">
                                <div class="form-group mb-3">
                                    <label for="name" class="text-muted">Name</label>
                                    <input type="text" class="form-control" name="name" id="item-name"
                                        value="{{ old('name') }}">
                                    @error('item_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>
                                <div class="form-group mb-3">
                                    <label for="price" class="text-muted">Qauntity</label>
                                    <input type="number" class="form-control" name="quantity" id="quantity"
                                        value="{{ old('quantity') }}" min="0">
                                    @error('quantity')
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
