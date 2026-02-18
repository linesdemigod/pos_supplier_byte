@extends('layout.layout')

@section('title')
    {{ 'Edit Customer' }}
@endsection

@section('content')
    <x-breadcrumb title="Customer" subtitle='Edit Customer' name='Customer' href='customer.index' />
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card border-1 border">
                        <div class="card-body">
                            <h3 class="py-2 text-center">Edit Customer</h3>
                            <form action="{{ route('customer.update', $customer->id) }}" method="Post">
                                @csrf
                                @method('PUT')
                                <div class="form-group mb-3">
                                    <label for="name" class="text-muted">Name</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ $customer->name }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>
                                <div class="form-group mb-3">
                                    <label for="phone" class="text-muted">Phone</label>
                                    <input type="text" class="form-control" name="phone" id="phone"
                                        value="{{ $customer->phone }}">
                                    @error('phone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="location" class="text-muted">Location</label>
                                    <input type="text" class="form-control" name="location" placeholder="" id="location"
                                        value="{{ $customer->location }}">
                                    @error('location')
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
