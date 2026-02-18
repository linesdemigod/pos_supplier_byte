@extends('layout.layout')

@section('title')
    {{ 'Role' }}
@endsection

@section('content')
    <div class="container pt-3">
        <x-breadcrumb title="Apps" subtitle='Role' name='Role' />
    </div>
    {{-- table --}}
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <div class="card border-1 border">
                        <div class='table-responsive'>
                            <div class="card-body">
                                <div class="mb-3">

                                    <a href="{{ route('permission.add.role') }}" class="btn btn-primary"> <i
                                            class="icon-xxs button-item-icon text-light me-2" data-feather="edit"></i>Add
                                        Role</a>

                                </div>

                                <x-flash-message class="alert alert-success" />
                                <table class="table-centered w-100 table">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">SL</th>
                                            <th scope="col">Role Name</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($roles as $key => $role)
                                            <tr>
                                                <th>{{ $key + 1 }}</th>
                                                <td>{{ $role->name }}</td>
                                                <td class="table-action">
                                                    @can('hidden')
                                                        <div class="d-flex">

                                                            {{-- edit --}}
                                                            <a href="{{ route('edit.role', $role->id) }}"
                                                                class="action-icon btn btn-primary me-2 text-white"
                                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                                <i class="fas fa-pen-square"></i></a>
                                                            {{-- delete -- Hide button --}}
                                                            <form action="{{ route('destroy.role', $role->id) }}"
                                                                method="post">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-danger action-icon text-white" value="Delete"
                                                                    id="table-delete" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" title="Delete"><i
                                                                        class="fas fa-trash"></i></button>
                                                            </form>
                                                        </div>
                                                    @endcan
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
