@extends('layout.layout')

@section('title')
    {{ 'Add Role Permission' }}
@endsection

@section('content')
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card border-1 border">
                        <div class='table-responsive'>
                            <div class="card-body">
                                <span class="text-success font-title role-message"></span>
                                <!-- forms -->
                                <form method="post" action="{{ route('permission.role.permission.store') }}">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="role_id" class="text-muted">Roles Name</label>
                                        <select name="role_id" id="role_id" class="form-select">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger font-small group-error"></small>
                                    </div>

                                    {{-- select all --}}
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value=""
                                            id="selectAllPermission">
                                        <label class="form-check-label" for="selectAllPermission">
                                            Select all
                                        </label>
                                    </div>

                                    <hr>
                                    @foreach ($permission_groups as $group)
                                        <br>
                                        <div class="row">
                                            <div class="col-5">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="permission_group">
                                                    <label class="form-check-label" for="flexCheckIndeterminateDisabled">
                                                        {{ $group->group_name }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-7">

                                                @php
                                                    $permissions = App\Models\User::getPermissionByGroupName(
                                                        $group->group_name,
                                                    );
                                                @endphp
                                                @foreach ($permissions as $permission)
                                                    <div class="form-check">
                                                        <input class="form-check-input" name="permission[]" type="checkbox"
                                                            value="{{ $permission->id }}"
                                                            id="permission{{ $permission->id }}">
                                                        <label class="form-check-label"
                                                            for="permission{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- button --}}
                                    <div class="mb-3 mt-3">
                                        <button type="submit" name="submit"
                                            class="form-control btn btn-primary btn-block">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script type="text/javascript">
        const selectAll = document.querySelector('#selectAllPermission');

        selectAll?.addEventListener('click', selectAllCheck);

        function selectAllCheck() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');

            checkboxes.forEach(checkboxe => {
                if (selectAll.checked) {
                    checkboxe.checked = true;
                } else {
                    checkboxe.checked = false;
                }
            })

        }
    </script>
@endsection
