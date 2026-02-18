@extends('layout.layout')

@section('title')
    {{ 'Company' }}
@endsection

@section('content')
    <x-breadcrumb title="Company" subtitle='Edit' name='Company' href='company.index' />
    <div class="py-3">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title mb-3">Update Company</h3>
                <x-flash-message />
                <form action="{{ route('company.update', $company->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="Name"
                                    value="{{ $company->name }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="mb-3">
                                <label for="contact" class="form-label">Contact</label>
                                <input type="text" name="phone" class="form-control" id="phone" placeholder="Phone"
                                    value="{{ $company->phone }}">
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
                                    placeholder="Address" value="{{ $company->address }}">
                                @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                    </div>

                    <div class="">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
@section('script')
    <script></script>
@endsection
