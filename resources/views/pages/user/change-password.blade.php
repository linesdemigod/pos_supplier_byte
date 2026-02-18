@extends('layout.layout')

@section('title')
    {{ 'Change password' }}
@endsection

@section('content')
    <x-breadcrumb title="Profile" subtitle='Change password' name='Change password' href='user.profile' />
    <div class="py-3">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <x-flash-message />
                            <div class="card-title font-20">Change Password</div>
                            <form action="{{ route('user.update_password') }}" method="Post">
                                @csrf
                                @method('PUT')
                                <div class="form-group mb-3">
                                    <label for="current_password" class="text-muted">Current Password</label>
                                    <input type="password" class="form-control" name="current_password"
                                        placeholder="Password" id="current_password">
                                    @error('current_password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password" class="text-muted">Password</label>
                                    <input type="password" class="form-control" name="password" placeholder="Password"
                                        id="password">
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="text-muted">Confirm Password</label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        placeholder="Confirm Password" id="password_confirmation">
                                    @error('password_confirmation')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <button type="submit" name="submit" class="btn btn-primary">Change
                                        password
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
    </div>
@endsection
