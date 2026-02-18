@extends('layout.layout')

@section('title')
    {{ 'Add Return Item' }}
@endsection
@section('page-id', 'inventory_create')

@section('content')
    <x-breadcrumb title="Return Item" subtitle='Add Return Item' name='Return Item' href='return.index' />
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
                            <div class="form-group mb-3">
                                <label for="name" class="text-muted">Name</label>
                                <input type="text" class="form-control" name="name" id="item-name" value=""
                                    disabled>

                            </div>
                            <form action="{{ route('return.store') }}" method="Post">
                                @csrf
                                <input type="hidden" name="item_id" class="form-control" id="item-value"
                                    value="{{ old('item_id') }}">
                                <div class="form-group mb-3">
                                    <label for="reference" class="text-muted">Reference No</label>
                                    <input type="text" class="form-control" name="reference" id="reference"
                                        value="{{ old('reference') }}">
                                    @error('reference')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>

                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="">
                                            <div class="form-group mb-3">
                                                <label for="price" class="form-label">Price</label>
                                                <input type="text" class="form-control" name="price" id="price"
                                                    value="{{ old('price') }}">
                                                @error('price')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="">
                                            <div class="form-group mb-3">
                                                <label for="quantity" class="form-label">Quantity</label>
                                                <input type="number" class="form-control" name="quantity" placeholder=""
                                                    id="quantity" value="{{ old('quantity') }}">
                                                @error('quantity')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="reason" class="form-label">Reason</label>
                                    <textarea name="reason" class="form-control" id="" cols="10" rows="1">{{ old('reason') }}</textarea>
                                    @error('reason')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="">
                                            <div class="form-group mb-3">
                                                <label for="purchase_date" class="form-label">Purchase Date</label>
                                                <input type="date" class="form-control" name="purchase_date"
                                                    placeholder="" id="purchase_date" value="{{ old('purchase_date') }}">
                                                @error('purchase_date')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="">
                                            <div class="form-group mb-3">
                                                <label for="return_date" class="form-label">Return Date</label>
                                                <input type="date" class="form-control" name="return_date" placeholder=""
                                                    id="return_date" value="{{ old('return_date') }}">
                                                @error('return_date')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
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
