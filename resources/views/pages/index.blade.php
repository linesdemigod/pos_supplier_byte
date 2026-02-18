@extends('layout.front-layout')

@section('title')
    {{ 'Login' }}
@endsection


@section('content')
    <x-flash-message class="alert alert-success" />
    <section>

        <div class="min-vh-100 d-flex justify-content-center align-items-center container">
            <div class="card rounded-2 shadow" style="width: 30rem;">
                <div class="card-body">
                    <p class="card-title fw-bold fs-2"> {{ $name }}</p>
                    <p class="fs-5 py-2">Login</p>
                    <form action="{{ route('login') }}" method="Post">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="username" class="text-muted">Username</label>
                            <input type="text" class="form-control" name="username" id="username"
                                value="{{ old('username') }}">
                            @error('username')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="text-muted">Password</label>
                            <input type="password" class="form-control" name="password" id="password">
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">

                            <div class="form-check mb-3">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            {{-- <a href="{{ route('forgotpassword') }}" class="ps-3 text-decoration-none">Forgot
                                password</a> --}}
                        </div>

                        <div class="form-group mb-3">
                            <button type="submit" name="submit" class="form-control btn btn-primary">Login</button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </section>
@endsection
