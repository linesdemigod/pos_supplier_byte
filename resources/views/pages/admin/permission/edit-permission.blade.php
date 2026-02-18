@extends('layout.layout')

@section('title')
    {{ 'Edit Permission' }}
@endsection

@section('content')
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mx-auto">
                    <div class="card border-1 border">
                        <div class='table-responsive'>
                            <div class="card-body">
                                <span class="text-success font-title permission-message"></span>
                                <!-- forms -->
                                <form method="post" action="{{ route('update.permission', $permission->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label for="gus_name" class="form-label text-muted">Permission Name</label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ $permission->name }}" id="name">
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="group_name" class="text-muted">Group Name</label>
                                        <select name="group_name" id="group_name" class="form-select">
                                            <option value="0">Select group</option>
                                            <option value="user"
                                                {{ $permission->group_name === 'account' ? 'selected' : '' }}>User
                                            </option>
                                            <option value="class"
                                                {{ $permission->group_name === 'class' ? 'selected' : '' }}>Class
                                            </option>
                                            <option value="note"
                                                {{ $permission->group_name == 'note' ? 'selected' : '' }}>Note
                                            </option>
                                            <option value="members"
                                                {{ $permission->group_name == 'members' ? 'selected' : '' }}>Members
                                            </option>
                                            <option value="article"
                                                {{ $permission->group_name == 'article' ? 'selected' : '' }}>Blog Article
                                            </option>
                                            <option value="category"
                                                {{ $permission->group_name == 'category' ? 'selected' : '' }}>
                                                Blog Category
                                            </option>
                                            <option value="permission"
                                                {{ $permission->group_name == 'permission' ? 'selected' : '' }}>Permission
                                            </option>
                                            <option value="setting"
                                                {{ $permission->group_name == 'setting' ? 'selected' : '' }}>Setting
                                            </option>
                                        </select>
                                        @error('group_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" name="submit"
                                            class="form-control btn btn-custom-info">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
