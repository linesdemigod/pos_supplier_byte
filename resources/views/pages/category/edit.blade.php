@extends('layout.layout')

@section('title')
    {{ 'Edit Category' }}
@endsection

@section('content')
    <x-breadcrumb title="Category" subtitle='Edit Category' name='Category' href='category.index' />
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card border-1 border">
                        <div class="card-body">
                            <h3 class="py-2 text-center">Edit Category</h3>
                            <form action="{{ route('category.update', $category->id) }}" method="Post">
                                @csrf
                                @method('PUT')
                                <div class="form-group mb-3">
                                    <label for="name" class="text-muted">Name</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ $category->name }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror

                                </div>
                                <div class="form-group mb-3">
                                    <label for="description" class="text-muted">Description</label>
                                    <input type="text" class="form-control" name="description" id="description"
                                        value="{{ $category->description }}">
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="category_code" class="text-muted">Category Code</label>
                                    <input type="text" class="form-control" name="category_code" placeholder=""
                                        id="category_code" value="{{ $category->category_code }}">
                                    @error('category_code')
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
