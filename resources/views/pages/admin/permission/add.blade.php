@extends('layout.layout')

@section('title')
    {{ 'Permission' }}
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
                                <form method="post" id="permission-form">
                                    @csrf
                                    <div class="mb-3">

                                        <label for="gus_name" class="form-label text-muted">Permission Name</label>
                                        <br>
                                        <small class="text-muted">Eg. class.trash or class</small>
                                        <input type="text" class="form-control" name="name" value=""
                                            id="name">
                                        <span class="text-danger font-small name-error"></span>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="group_name" class="text-muted">Group Name</label>
                                        <select name="group_name" id="group_name" class="form-select">
                                            <option value="0">Select group</option>
                                            <option value="acount">User</option>
                                            <option value="Class">Class</option>
                                            <option value="note">Note</option>
                                            <option value="Members">Members</option>
                                            <option value="article">Blog Article</option>
                                            <option value="category">Blog Category</option>
                                            <option value="permission">Permission</option>
                                            <option value="setting">Setting</option>
                                        </select>
                                        <small class="text-danger font-small group-error"></small>


                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" name="submit"
                                            class="form-control btn btn-custom-info btn-block">Add</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script>
        const permissionMsg = document.querySelector(".permission-message");
        const formPermission = document.querySelector("#permission-form");
        formPermission?.addEventListener("submit", submitPermission);

        async function submitPermission(e) {
            e.preventDefault();
            let data = new FormData(formPermission);
            await axios({
                    method: "post",

                    headers: {
                        "Content-Type": "application/json",
                        "X-Requessted-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": document
                            .querySelector("meta[name='csrf-token']")
                            .getAttribute("content"),
                    },
                    url: "{{ route('store.permission') }}",
                    data: data,
                })
                .then(res => {
                    if (res.data.success) {
                        permissionMsg.innerText = res.data.success;
                        formPermission.reset();
                        document.querySelector(".name-error").innerText = ""
                        document.querySelector(".group-error").innerText = ""


                    }

                })
                .catch(error => {
                    if (error.response) {
                        console.log(error.response.data.errors);
                        document.querySelector(".name-error").innerText =
                            error.response.data.errors.name === undefined ?
                            "" :
                            error.response.data.errors.name[0];
                        document.querySelector(".group-error").innerText =
                            error.response.data.errors.group_name == undefined ?
                            "" :
                            error.response.data.errors.group_name[0];



                    } else {
                        console.log("Error", error.message);
                    }



                });
        }
    </script>
@endsection
