@extends('layout.layout')

@section('title')
    {{ 'Administrator' }}
@endsection

@section('content')
    <x-breadcrumb title="Apps" subtitle='Administrator' name='Administrator' />
    <div class="py-3">
        <div class="card">

            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-5">
                    <a href="{{ route('admin.create') }}" class="btn btn-primary">
                        <i class="uil-plus"></i>
                        Add Admin</a>
                </div>
                <div class="table-responsive">
                    <div id="msg">
                    </div>
                    <x-flash-message />
                    @unless (count($admins) == 0)
                        <table class="table-centered w-100 table">
                            <thead class="table-light">
                                <tr>

                                    <th scope="col">SN</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Contact</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Avatar</th>
                                    <th scope="col">Account Status</th>
                                    <th scope="col">Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($admins as $key => $admin)
                                    <tr>
                                        <td>{{ $key + 1 }} </td>
                                        <td>{{ ucwords($admin->name) }}
                                        </td>
                                        <td> {{ $admin->email }} </td>
                                        <td> {{ $admin->contact ?? 'N/A' }} </td>
                                        <td>{{ $admin->role ?? 'N/A' }} </td>

                                        <td><img src="{{ $admin->avatar ? asset('storage/' . $admin->avatar) : asset('/images/avatar.png') }}"
                                                alt="{{ $admin->name }}" class="img-fluid rounded-2" height="40"
                                                width="40"></td>
                                        <td><span
                                                class="badge {{ $admin->status == '1' ? 'bg-primary' : 'bg-danger' }}">{{ $admin->status == '1' ? 'active' : 'suspended' }}</span>
                                        </td>

                                        <td>
                                            <div class="d-flex">



                                                <a href="{{ route('admin.edit', $admin->id) }}"
                                                    class="action-icon btn btn-primary me-2 text-white" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Edit">
                                                    <i class="fas fa-pen-square"></i></a>



                                                <form action="{{ route('admin.destroy', $admin->id) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger action-icon text-white"
                                                        value="Delete" id="table-delete" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete?');"
                                                        data-id={{ $admin->id }}><i class="fas fa-trash"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                        </td>


                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <h3 class="text-center">No record available</h3>
                    @endunless

                </div>
                <div class="mt-3">
                    {{ $admins->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script></script>
@endsection
