@extends('layout.layout')

@section('title')
    {{ 'Branch Permission' }}
@endsection

@section('content')
    <x-breadcrumb title="Item" subtitle='Branch Permission' name='Item' href='item.index' />
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card border-1 border">
                        <div class="card-body">
                            <h3 class="py-2 text-center">Branch</h3>
                            <form action="{{ route('branch.branch-switch.update', $branchSwitch->id) }}" method="Post">
                                @csrf
                                @method('PUT')
                                <div class="row align-items-center">
                                    <div class="col-xxl-8">
                                        <div class="form-group">
                                            <select name="user_select" id="user_select" class="form-select element_select">
                                                <option value="" disabled selected>-- select User --</option>
                                                @unless (count($users) == 0)
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}">{{ Str::title($user->name) }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option value="0">No User</option>
                                                @endunless

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xxl-4">
                                        <div class="form-group">
                                            <button type="button" id="add_user" class="btn btn-secondary">Add
                                                User</button>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group mb-3">
                                    <input type="text" id="allowed-users" class="form-control" multiple readonly
                                        value="{{ $usersAllowed }}">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="user_allowed" class="form-label">User Allowed (JSON Array)</label>
                                    <textarea readonly name="user_allowed" id="user_allowed" class="form-control" rows="5" required>{{ old('user_allowed', json_encode($branchSwitch->user_allowed)) }}</textarea>
                                    <small class="form-text text-muted">Enter user IDs or roles in JSON format, e.g., [1, 2,
                                        3]
                                        or {"roles": ["admin", "manager"]}.</small>
                                    @error('user_allowed')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>


                                <div class="form-group mb-3">
                                    <button type="submit" name="submit"
                                        class="form-control btn btn-primary btn-block">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script>
        document.getElementById('add_user').addEventListener('click', function() {
            const select = document.getElementById('user_select');
            const textarea = document.getElementById('user_allowed');

            // Get the selected user's ID
            const userId = select.value;
            const userName = select.options[select.selectedIndex].text;
            // Add the user to the allowed users list


            if (!userId) {

                notyf.error('Please select a user.')
                return;
            }

            // Parse current JSON in the textarea
            let currentUsers = [];
            try {
                currentUsers = JSON.parse(textarea.value || '[]');
            } catch (e) {
                notyf.error('Invalid JSON format in the textarea.');
                return;
            }

            // Check if the user is already in the list
            const userIndex = currentUsers.indexOf(Number(userId));

            if (userIndex === -1) {
                // User not in the list, add them
                currentUsers.push(Number(userId));
                notyf.success(`User "${userName}" added.`);
            } else {
                // User already in the list, remove them
                currentUsers.splice(userIndex, 1);
                notyf.success(`User "${userName}" removed.`);
            }

            // Update the textarea with the new JSON
            textarea.value = JSON.stringify(currentUsers, null, 2);
        });

        // Validate JSON input on the fly
        document.getElementById('user_allowed').addEventListener('input', function() {
            const textarea = this;
            try {
                JSON.parse(textarea.value);
                textarea.classList.remove('border-danger');
            } catch (e) {
                textarea.classList.add('border-danger');
            }
        });
    </script>
@endsection
