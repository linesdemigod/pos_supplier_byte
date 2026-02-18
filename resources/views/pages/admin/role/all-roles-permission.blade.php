@extends('layout.layout')

@section('title')
    {{ 'All Permissions' }}
@endsection

@section('content')
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-10 mx-auto">
                    <div class="card border-1 border">
                        <div class='table-responsive'>
                            <div class="card-body">
                                {{-- <a href="{{ route('add.role') }}" class="btn btn-primary mb-3"> <i
                                        class="me-2 icon-xxs button-item-icon text-light" data-feather="edit"></i>Add
                                    Role</a> --}}
                                <x-flash-message />
                                <table class="table-centered w-100 table">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">SL</th>
                                            <th scope="col">Role Name</th>
                                            <th scope="col">Permission Name</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($roles as $key => $role)
                                            <tr>
                                                <th>{{ $key + 1 }}</th>
                                                <td>{{ $role->name }}</td>
                                                <td>
                                                    @foreach ($role->permissions as $permission)
                                                        <span
                                                            class="badge rounded-pill bg-danger">{{ mb_convert_case($permission->name, MB_CASE_TITLE) }}</span>
                                                    @endforeach
                                                </td>
                                                <td class="table-action">

                                                    <div class="d-flex">

                                                        {{-- edit --}}
                                                        <a href="{{ route('permission.admin.edit.role', $role->id) }}"
                                                            class="action-icon btn btn-primary me-2 text-white"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                            <i class="fas fa-pen-square"></i></a>
                                                        {{-- delete -- Hide button --}}
                                                        @can('hidden')
                                                            <form
                                                                action="{{ route('permission.admin.destroy.role', $role->id) }}"
                                                                method="post">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-danger action-icon text-white" value="Delete"
                                                                    id="table-delete" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" title="Delete"><i
                                                                        class="fas fa-trash"></i></button>
                                                            </form>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
