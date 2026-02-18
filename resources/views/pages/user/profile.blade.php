@extends('layout.layout')

@section('title')
    {{ 'Profile' }}
@endsection

@section('content')
    <x-breadcrumb title="Apps" subtitle='Profile' name='Profile' />
    <div class="py-3">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title font-20">Profile</div>
                            <div class="table-responsive">
                                @php
                                    $user = Auth::user();
                                @endphp
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>Name: </th>
                                            <td>{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Username: </th>
                                            <td>{{ $user->username }}</td>
                                        </tr>
                                        <tr>
                                            <th>Role: </th>
                                            <td><span class="badge bg-primary">{{ $user->role }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Password: </th>
                                            <td>
                                                <a href="{{ route('user.change_password') }}" class="btn btn-primary">Change
                                                    Password</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
