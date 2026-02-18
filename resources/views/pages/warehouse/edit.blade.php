@extends('layout.layout')

@section('title')
    {{ 'Edit Warehouse' }}
@endsection

@section('content')
    <x-breadcrumb title="Warehouse" subtitle='Edit Warehouse' name='Warehouse' href='warehouse.index' />
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card border-1 border">
                        <div class="card-body">
                            <h3 class="py-2 text-center">Edit Warehouse</h3>
                            <form action="{{ route('warehouse.update', $warehouse->id) }}" method="Post">
                                @csrf
                                @method('PUT')
                                <div class="form-group mb-3">
                                    <label for="name" class="text-muted">Name</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ $warehouse->name }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>
                                <div class="form-group mb-3">
                                    <label for="phone" class="text-muted">Phone</label>
                                    <input type="text" class="form-control" name="phone" id="phone"
                                        value="{{ $warehouse->phone }}">
                                    @error('phone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="address" class="text-muted">Location</label>
                                    <input type="text" class="form-control" name="address" placeholder="" id="address"
                                        value="{{ $warehouse->address }}">
                                    @error('address')
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
