@extends('layout.layout')

@section('title')
    {{ 'Edit Role' }}
@endsection

@section('content')
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mx-auto">
                    <div class="card border-1 border">
                        <div class='table-responsive'>
                            <div class="card-body">
                                <span class="text-success font-title permission-message"></span>
                                <!-- forms -->
                                <form method="post" action="{{ route('permission.update.role', $role->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label for="gus_name" class="form-label text-muted">Permission Name</label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ $role->name }}" id="name">
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" name="submit"
                                            class="form-control btn btn-dark btn-block">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
