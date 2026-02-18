@extends('layout.layout')

@section('title')
    {{ 'User' }}
@endsection

@section('content')
    <x-breadcrumb title="Apps" subtitle='User' name='User' />
    <div class="py-3">
        <div class="card">

            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-5">
                    @can('systemUser.create')
                        <a href="{{ route('user.create') }}" class="btn btn-primary">
                            <i class="uil-plus"></i>
                            Create User</a>
                    </div>
                @endcan
                <div class="table-responsive">
                    <div id="msg"></div>

                    @unless (count($users) == 0)
                        <table class="table-centered w-100 table" id="table_id">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Branch Name</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Account Status</th>
                                    <th scope="col">Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>

                                        <td>{{ ucwords($user->store->name ?? ($user->warehouse->name ?? 'N/A')) }}</td>
                                        <td>{{ ucwords($user->role) ?? 'N/A' }} </td>
                                        <td>{{ ucwords($user->name) ?? 'N/A' }} </td>
                                        <td> {{ $user->username ?? 'N/A' }} </td>
                                        <td>
                                            <div>

                                                <select name="status" id="status-select" class="form-select">
                                                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>
                                                        Active</option>
                                                    <option value="inactive"
                                                        {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>

                                                </select>
                                                <input type="hidden" name="id" value="{{ $user->id }}"
                                                    id="account-id">

                                            </div>

                                            {{-- <span
                                                class="badge {{ $user->status == 'active' ? 'bg-primary' : 'bg-danger' }}">{{ $user->status ?? 'N/A' }}</span> --}}
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                @can('systemUser.edit')
                                                    <a href="{{ route('user.edit', $user->id) }}"
                                                        class="action-icon btn btn-primary me-2 text-white" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Edit">
                                                        <i class="fas fa-pen-square"></i>
                                                    </a>
                                                @endcan

                                                @can('systemUser.delete')
                                                    <form action="{{ route('user.destroy', $user->id) }}" method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger action-icon text-white"
                                                            value="Delete" id="table-delete" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete?');"
                                                            data-id={{ $user->id }}> <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    @else
                        <h3 class="text-center">No record available</h3>
                    @endunless


                </div>

            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        const statusSelect = document.querySelector('#status-select');
        const id = document.querySelector('#account-id');


        async function toggleUserStatus() {
            loader.classList.remove('invisible');
            const status = statusSelect.value;
            const acountId = id.value;

            const formData = new FormData();
            formData.append('status', status);
            formData.append('id', acountId);


            const config = {
                headers: {
                    "Content-Type": "application/json",
                    "X-Requessted-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document
                        .querySelector("meta[name='csrf-token']")
                        .getAttribute("content"),
                },
            }

            try {

                const res = await axios.post('{{ route('user.account_status') }}', formData, config);
                const data = res.data;
                if (res.status == 200) {

                    swal.fire(
                        'Success',
                        'Account status updated successfully',
                        'success'
                    )
                }

            } catch (error) {
                console.log(error)
            } finally {
                loader.classList.add('invisible');
            }


        }
        statusSelect.addEventListener('change', toggleUserStatus)
    </script>
@endsection
