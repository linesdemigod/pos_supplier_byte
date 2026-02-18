@extends('layout.layout')

@section('title')
    {{ 'Permission' }}
@endsection

@section('content')
    <div class="container pt-3">
        <x-breadcrumb title="Apps" subtitle='Permission' name='Permission' />

    </div>
    {{-- table --}}
    <section class="py-1">
        <div class="container">
            <div class="row">

                <div class="col-md-12 mx-auto">
                    <div class="card border-1 border">
                        <div class='table-responsive'>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-5">
                                    <div class="">
                                        @can('hidden')
                                            <a href="{{ route('add.permission') }}" class="btn btn-custom-info"> <i
                                                    class="icon-xxs button-item-icon text-light me-2"
                                                    data-feather="edit"></i>Add
                                                Permission</a>
                                        @endcan
                                    </div>


                                </div>

                                <x-flash-message class="alert alert-success" />
                                <table class="table-centered w-100 table">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">SL</th>
                                            <th scope="col">permission Name</th>
                                            <th scope="col">Group Name</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($permissions as $key => $permission)
                                            <tr>
                                                <th>{{ $key + 1 }}</th>
                                                <td>{{ mb_convert_case($permission->name, MB_CASE_TITLE) }}</td>
                                                <th scope="row">
                                                    {{ mb_convert_case($permission->group_name, MB_CASE_TITLE) }}</th>
                                                <td class="table-action">
                                                    @can('hidden')
                                                        <div class="d-flex">

                                                            {{-- edit --}}
                                                            <a href="{{ route('permission.edit.permission', $permission->id) }}"
                                                                class="action-icon btn btn-primary me-2 text-white"
                                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                                <i class="fas fa-pen-square"></i></a>
                                                            {{-- delete -- Hide button --}}
                                                            <form
                                                                action="{{ route('permission.destroy.permission', $permission->id) }}"
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
