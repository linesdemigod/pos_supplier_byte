<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/index.css') }}">

        <title>Access Forbidden</title>
    </head>

    <body>
        <section>
            <div class="container">
                <div class="min-vh-100 d-flex justify-content-center align-items-center container">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-sm-12 col-md-12 mx-auto text-center">
                            <h1 class="fw-normal text-dark" style="font-size: 3rem;">Access Forbidden</h1>

                            <a href="{{ route('dashboard') }}" class="btn btn-dark mb-5">Return Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>

</html>
