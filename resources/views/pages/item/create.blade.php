@extends('layout.layout')

@section('title')
    {{ 'Add Item' }}
@endsection

@section('content')
    <x-breadcrumb title="Item" subtitle='Add Item' name='Item' href='item.index' />
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card border-1 border">
                        <div class="card-body">
                            <h3 class="py-2 text-center">Add Item</h3>
                            <form action="{{ route('item.store') }}" method="Post">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="category">Category</label>
                                    <select name="category_id" id="" class="form-select element_select">
                                        <option value="0">-- select category --</option>
                                        @unless (count($categories) == 0)
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ Str::title($category->name) }}</option>
                                            @endforeach
                                        @else
                                            <option value="0">No Category</option>
                                        @endunless

                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name" class="text-muted">Name</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ old('name') }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>
                                <div class="form-group mb-3">
                                    <label for="price" class="text-muted">Price</label>
                                    <input type="text" class="form-control" name="price" id="price"
                                        value="{{ old('price') }}">
                                    @error('price')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="item_code" class="text-muted">Item Code</label>
                                    <input type="text" class="form-control" name="item_code" placeholder=""
                                        id="item_code" value="{{ old('item_code') }}">
                                    @error('item_code')
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
