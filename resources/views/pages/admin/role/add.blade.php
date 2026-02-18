@extends('layout.layout')

@section('title')
    {{ 'Permission Name' }}
@endsection

@section('content')
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mx-auto">
                    <div class="card border-1 border">
                        <div class='table-responsive'>
                            <div class="card-body">
                                <span class="text-success font-title role-message"></span>
                                <!-- forms -->
                                <form method="post" id="role-form">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="gus_name" class="form-label text-muted">Permission Name</label>
                                        <input type="text" class="form-control" name="name" value=""
                                            id="name">
                                        <span class="text-danger font-small name-error"></span>
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" name="submit"
                                            class="form-control btn btn-dark btn-block">Add</button>
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
        const roleMsg = document.querySelector(".role-message");
        const formRole = document.querySelector("#role-form");
        formRole?.addEventListener("submit", submitPermission);

        async function submitPermission(e) {
            e.preventDefault();
            let data = new FormData(formRole);
            await axios({
                    method: "post",

                    headers: {
                        "Content-Type": "application/json",
                        "X-Requessted-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": document
                            .querySelector("meta[name='csrf-token']")
                            .getAttribute("content"),
                    },
                    url: "{{ route('permission.store.role') }}",
                    data: data,
                })
                .then(res => {
                    if (res.data.success) {
                        roleMsg.innerText = res.data.success;
                        formRole.reset();
                        document.querySelector(".name-error").innerText = ""


                    }

                })
                .catch(error => {
                    if (error.response) {
                        console.log(error.response.data.errors);
                        document.querySelector(".name-error").innerText =
                            error.response.data.errors.name === undefined ?
                            "" :
                            error.response.data.errors.name[0];



                    } else {
                        console.log("Error", error.message);
                    }



                });
        }
    </script>
@endsection
