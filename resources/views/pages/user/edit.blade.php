@extends('layout.layout')

@section('title')
    {{ 'User' }}
@endsection

@section('content')
    <x-breadcrumb title="User" subtitle='Update' name='User' href='user.index' />
    <div class="py-3">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title mb-3">Update User</h3>

                <form method="post" id="staff-form">
                    @csrf
                    <div class="row">
                        <div class="col-xxl-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Get Branch</label>
                                <select name="" id="select-branch" class="form-select">
                                    <option value="" disabled selected>Please select a branch type</option>
                                    <option value="0">Store</option>
                                    <option value="1">Warehouse</option>
                                </select>

                            </div>
                        </div>
                        <div class="col-xxl-6">
                            <label for="name" class="form-label">Select Branch</label>
                            <select name="" id="get-branch" class="form-select">
                                {{-- <option value="" disabled selected>Please select a branch</option> --}}
                                @if ($warehouseId)
                                    <option value="{{ $user->warehouse_id }}" selected>{{ $user->warehouse->name }}</option>
                                @endif
                                @if ($storeId)
                                    <option value="{{ $user->store_id }}" selected>{{ $user->store->name }}</option>
                                @endif
                            </select>
                            <span class="text-danger branch-error"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="mb-3">
                                <label for="contact" class="form-label">Role</label>
                                <select name="role" id="" class="form-select">
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $item)
                                        <option value="{{ $item->name }}"
                                            {{ $item->name == $user->role ? 'selected' : '' }}>{{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                            </div>
                        </div>
                    </div>



                    {{-- address --}}
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="hidden" value="{{ $user->id }}" id="user-id">
                                <input type="text" name="name" class="form-control" id="name" placeholder="Name"
                                    value="{{ $user->name }}">
                                <span class="text-danger name-error"></span>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="username" name="username" class="form-control" value="{{ $user->username }}"
                                    placeholder="Username">
                                <span class="text-danger username-error"></span>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-8 col-lg-8">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="d-flex justify-content-center gap-3">
                                    <input type="password" name="password" class="form-control" id="password-input">

                                    <button type="button" class="btn btn-secondary" id="toggle-password-field">
                                        Edit</button>
                                </div>

                                <span class="text-danger password-error"></span>
                            </div>
                        </div>

                    </div>

                    <div class="">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        const showPasswordField = document.querySelector('#toggle-password-field');
        const passwordInput = document.querySelector('#password-input');

        showPasswordField.addEventListener('click', () => {

            const getInputAttr = passwordInput.getAttribute('readonly');
            console.log(getInputAttr)

            //remove readonly if input is set to readonly
            if (getInputAttr === 'readonly') {
                passwordInput.removeAttribute('readonly');
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
                passwordInput.setAttribute('readonly', 'readonly');
            }

        })
        const selectbranch = document.querySelector('#select-branch');
        const branchesSelect = document.querySelector('#get-branch');
        const staffForm = document.querySelector('#staff-form')

        selectbranch.addEventListener('change', async () => {

            try {
                const response = await axios.get('/staff/branches', {
                    params: {
                        id: selectbranch.value
                    },
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const {
                    branches
                } = response.data;
                branchesSelect.innerHTML = "";

                if (response.status === 200) {
                    selectbranch.value === '0' ? branchesSelect.name = 'store_id' : branchesSelect.name =
                        'warehouse_id'
                    //create option element
                    branches.forEach(branch => {
                        const option = document.createElement('option');
                        option.value = branch.id;
                        option.text = branch.name;
                        branchesSelect.appendChild(option);

                    });

                }



            } catch (error) {
                console.error('Error fetching branches:', error);
                branchesSelect.innerHTML = `<option value="" disabled>No branches available</option>`;
            }
        })

        //register user
        staffForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(staffForm);

            //if branchesSelect is not select fire error message
            if (branchesSelect.length === 0 || branchesSelect.value === '') {
                document.querySelector('.branch-error').textContent = 'Please select a branch'
                return;
            }

            const id = document.querySelector('#user-id').value;
            const url = "/staff/edit/:user".replace(':user', id)

            try {
                const res = await axios.put(url, formData, {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                console.log(res)

                const {
                    message
                } = res.data;
                if (res.status === 200) {

                    notyf.success(message)
                    staffForm.reset()

                    document.querySelector('.name-error').textContent = '';
                    document.querySelector('.username-error').textContent = '';
                    document.querySelector('.password-error').textContent = '';
                    document.querySelector('.role-error').textContent = '';
                    document.querySelector('.branch-error').textContent = ''
                }
            } catch (error) {
                console.log(error)
                const errors = error.response.data.errors ?? {};

                document.querySelector('.name-error').textContent = errors.name ?? '';
                document.querySelector('.username-error').textContent = errors.username ?? '';
                document.querySelector('.password-error').textContent = errors.password ?? '';
                document.querySelector('.role-error').textContent = errors.role ?? '';
                document.querySelector('.branch-error').textContent = errors.branch ?? ''
            }
        })
    </script>
@endsection
