<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="user" data-user="{{ auth()->id() }}">
        <link href="{{ asset('css/dataTables.min.css') }}" rel="stylesheet" type="text/css" />
        <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
        {{-- script --}}


        <!-- Theme Config Js -->
        <link rel="icon" href="{{ asset('img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

        <!-- Fonts and icons -->
        <script src="{{ asset('js/plugin/webfont/webfont.min.js') }}"></script>
        <script>
            WebFont.load({
                google: {
                    families: ["Public Sans:300,400,500,600,700"]
                },
                custom: {
                    families: [
                        "Font Awesome 5 Solid",
                        "Font Awesome 5 Regular",
                        "Font Awesome 5 Brands",
                        "simple-line-icons",
                    ],
                    urls: ["{{ asset('css/fonts.min.css') }}"],
                },
                active: function() {
                    sessionStorage.fonts = true;
                },
            });
        </script>


        <!-- CSS Files -->
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/plugins.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/kaiadmin.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/dselect.css') }}">
        {{-- sweet --}}
        <link rel="stylesheet" href="{{ asset('css/simplebar.min.css') }}">
        <link href="{{ asset('css/sweetalert2.css') }}" rel="stylesheet" type="text/css" />

        {{-- alertify --}}
        <link href="{{ asset('css/notyf.min.css') }}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="{{ asset('css/index.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>POS | @yield('title')</title>
    </head>

    <body id="@yield('page-id')">


        <div class="wrapper">
            <!-- sidebar here -->

            <x-back.sidebar />

            <div class="main-panel">
                <!-- navbar -->
                <x-back.navbar />

                <div class="container">
                    <div class="page-inner">
                        @yield('content')
                    </div>
                </div>
                <x-spinner />
                <!-- footer -->
                {{-- <x-back.footer /> --}}
            </div>
        </div>

        <!--   Core JS Files   -->
        <script src="{{ asset('js/axios.min.js') }}"></script>
        <script src="{{ asset('js/core/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('js/core/popper.min.js') }}"></script>
        <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>

        <!-- jQuery Scrollbar -->
        <script src="{{ asset('js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

        <!-- Sweet Alert -->
        <script src="{{ asset('js/plugin/sweetalert/sweetalert.min.js') }}"></script>
        <script src="{{ asset('js/dselect.js') }}"></script>

        <!-- Kaiadmin JS -->
        <script src="{{ asset('js/kaiadmin.min.js') }}"></script>


        <script src="{{ asset('js/simplebar.min.js') }}"></script>
        <script src="{{ asset('js/datatables.min.js') }}"></script>

        <!-- JavaScript -->
        <script src="{{ asset('js/notyf.min.js') }}"></script>
        <script src="{{ asset('js/sweetalert2.js') }}"></script>
        <script src="{{ asset('js/chartjs.js') }}"></script>
        <script src="{{ asset('js/chartjslabel.js') }}"></script>
        <script src="{{ asset('js/shop.js') }}"></script>
        <script src="{{ asset('js/store.js') }}"></script>
        <script src="{{ asset('js/request.js') }}"></script>


        <script>
            $(document).ready(function() {
                $('#table_id').DataTable();

            });

            //register data label
            Chart.register(ChartDataLabels);

            let select_box_element = document.querySelector('.select_box');
            let element_select = document.querySelector('.element_select');
            if (select_box_element != null) {

                dselect(select_box_element, {
                    search: true,
                    creatable: false, // Creatable selection. Default: false
                    clearable: false, // Clearable selection. Default: false
                    maxHeight: '200px', // Max height for showing scrollbar. Default: 360px
                    size: '', // Can be "sm" or "lg". Default ''

                });
            }
            if (element_select != null) {

                dselect(element_select, {
                    search: true,
                    creatable: false, // Creatable selection. Default: false
                    clearable: false, // Clearable selection. Default: false
                    maxHeight: '200px', // Max height for showing scrollbar. Default: 360px
                    size: '', // Can be "sm" or "lg". Default ''

                });
            }

            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                // hour: '2-digit',
                // minute: '2-digit',
                // second: '2-digit'
            };

            const formatter = new Intl.DateTimeFormat('en-GB', options);
            const timeFormatter = new Intl.DateTimeFormat('en-GB', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });

            // const now = new Date();

            // // Start of the current month
            // const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);

            // // End of the current month
            // const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);

            // const formatDate = date => date.toISOString().split('T')[0];
        </script>
        <x-js-flash />

        @yield('script')
        @stack('scripts')
    </body>

</html>
