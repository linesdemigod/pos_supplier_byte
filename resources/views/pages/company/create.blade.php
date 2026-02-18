@extends('layout.layout')

@section('title')
    {{ 'Company' }}
@endsection

@section('content')
    <x-breadcrumb title="Company" subtitle='Add' name='Company' href='company.index' />
    <div class="py-3">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title mb-3">Add Company</h3>
                <x-flash-message />
                <form action="{{ route('company.store') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="Name"
                                    value="{{ old('name') }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="mb-3">
                                <label for="contact" class="form-label">Contact</label>
                                <input type="text" name="phone" class="form-control" id="phone" placeholder="Phone"
                                    value="{{ old('phone') }}">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                            </div>
                        </div>
                    </div>


                    {{-- address --}}
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" id="address"
                                    placeholder="Address" value="{{ old('address') }}">
                                @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                    </div>

                    <div class="">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
