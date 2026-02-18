@extends('layout.layout')

@section('title')
    {{ 'Update Store Inventory' }}
@endsection
@section('page-id', 'store_inventory')

@section('content')
    <x-breadcrumb title="Store Inventory" subtitle='Update Store Inventory' name='Store Inventory'
        href='storeinventory.index' />
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card border-1 border">
                        <div class="card-body">
                            <h3 class="py-2 text-center">Update Item</h3>

                            <form action="{{ route('storeinventory.update', $inventory->id) }}" method="Post">
                                @csrf
                                @method('PUT')
                                <div class="form-group mb-3">
                                    <label for="name" class="text-muted">Name</label>
                                    <input type="text" class="form-control" name="" id="name"
                                        value="{{ $inventory->item->name }}" readonly>

                                </div>
                                <div class="form-group mb-3">
                                    <label for="code" class="text-muted">Item Code</label>
                                    <input type="text" class="form-control" name="" id="code"
                                        value="{{ $inventory->item->item_code }}" readonly>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="quantity" class="text-muted">Current Quantity</label>
                                    <input type="text" class="form-control" name="" id="quantity"
                                        value="{{ $inventory->quantity }}" readonly>

                                </div>

                                <div class="form-group mb-3">
                                    <label for="price" class="text-muted">Qauntity</label>
                                    <input type="number" class="form-control" name="quantity" id="quantity"
                                        value="{{ old('quantity') }}" min="0">
                                    @error('quantity')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="stock" value="Add"
                                            checked>
                                        <label class="form-check-label" for="status1">
                                            Add
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="stock" value="Minus">
                                        <label class="form-check-label" for="status2">
                                            Minus
                                        </label>
                                    </div>

                                    @error('stock')
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
