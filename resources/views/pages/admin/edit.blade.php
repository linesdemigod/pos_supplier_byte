@extends('layout.layout')

@section('title')
    {{ 'Admin' }}
@endsection

@section('content')
    <x-breadcrumb title="Admin" subtitle='Edit' name='Admin' href='admin.home' />
    <div class="py-3">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title mb-3">Edit Admin</h3>
                <x-flash-message />
                <form action="{{ route('admin.update', $admin->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="Name"
                                    value="{{ $admin->name }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="mb-3">
                                <label for="contact" class="form-label">Contact</label>
                                <input type="text" name="contact" class="form-control" id="contact"
                                    placeholder="Contact" value="{{ $admin->contact }}">
                                @error('contact')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                            </div>
                        </div>
                    </div>
                    {{-- second --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" id="email" placeholder="Email"
                                    value="{{ $admin->email }}">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" id="" class="form-select">
                                    <option value="0">Select Role</option>
                                    @foreach ($role as $item)
                                        <option value="{{ $item->name }}"
                                            {{ $item->name == $admin->role ? 'selected' : '' }}>{{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Photo <sup class="text-danger">*</sup></label>
                                <input type="file" name="image" class="form-control" id="image"
                                    value="{{ $admin->image }}">
                                @error('image')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="" class="form-select">
                                    <option value="1" {{ $admin->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $admin->status == 0 ? 'selected' : '' }}>Suspend</option>
                                </select>

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
@section('script')
    <script></script>
@endsection
