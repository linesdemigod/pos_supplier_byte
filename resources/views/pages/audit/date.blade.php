@extends('layout.layout')

@section('title')
    {{ 'Audit By Date' }}
@endsection

@section('content')
    <x-breadcrumb title="Report" subtitle='Audit By Date' name='Audit By Date' href='report.index' />
    <div class="py-3">
        <div class="">
            @php
                use Carbon\Carbon;
                $date = Carbon::now()->toDateString();
            @endphp

            <div class="d-flex justify-content-end align-items-end gap-3">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="from" class="col-form-label">From</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" class="form-control form-control-light" name="from" id="date-from"
                            value="{{ $date }}">
                        <small class="text-danger from-date-error"></small>
                    </div>
                </div>
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="to" class="col-form-label">To</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" class="form-control form-control-light" name="to" id="date-to"
                            value="{{ $date }}">
                        <small class="text-danger to-date-error"></small>
                    </div>
                </div>
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <button type="button" name="submit" class="form-control btn btn-primary"
                            id="submit-btn">Search</button>
                    </div>
                </div>

            </div>

        </div>
    </div>
    {{-- table --}}
    <section>
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    {{-- table --}}
                    <div class="row">
                        <div class="col-md-6 col-lg-6 col-xl-6">
                            <div class="" data-simplebar style="max-height: 800px">

                                <div class='table-responsive'>
                                    <table class="table-centered table">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col">S/N</th>
                                                <th scope="col">Time</th>
                                                <th scope="col">Activity</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table-body">



                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6 col-xl-6">
                            <p class="log-msg text-center">Select a log to view details</p>

                            <div class="log-container py-3">

                            </div>
                        </div>
                    </div>

                    <section class="mb-3">
                        <div class="container">
                            <div class="text-center">
                                <button class="btn btn-primary loader-btn d-none" type="button" id="load-more-btn">
                                    <span class="spinner-border spinner-border-sm loader d-none" role="status"
                                        aria-hidden="true"></span>
                                    Load More
                                </button>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script>
        // const options = {
        //     year: 'numeric',
        //     month: 'numeric',
        //     day: 'numeric',
        //     hour: 'numeric',
        //     minute: 'numeric',
        //     second: 'numeric'
        // };
        // const formatter = new Intl.DateTimeFormat('en-UK', options);

        const dateFrom = document.querySelector('#date-from');
        const dateTo = document.querySelector('#date-to');
        const submitBtn = document.querySelector('#submit-btn');
        const logMsg = document.querySelector('.log-msg');
        const logContainer = document.querySelector('.log-container');

        let page = 1; // current page number
        const perPage = 10; // number of categories to fetch per page
        let count = 1;
        const loadMoreBtn = document.querySelector('#load-more-btn');

        loadMoreBtn.addEventListener('click', function() {
            searchSalesSummary();
        })

        submitBtn.addEventListener('click', function() {
            // Clear the table body and reset page to 1
            const tableBody = document.getElementById("table-body");
            tableBody.innerHTML = ""; // Clear all rows
            page = 1; // Reset the page to 1
            count = 1;

            searchSalesSummary(); // Call the search function
        });
        async function searchSalesSummary() {
            loader.classList.remove('d-none');
            loadMoreBtn.classList.remove('d-none');
            loadMoreBtn.disabled = true;

            //if submitBtn is clicked clear the tableBody first
            const config = {
                headers: {
                    Accept: "application/json",
                },
                params: {

                    date_from: dateFrom.value,
                    date_to: dateTo.value,
                    page: page,
                    perPage: perPage

                },
            }

            try {

                const res = await axios.get("{{ route('audit.get_date_audit') }}", config)


                const {
                    logs,
                    current_page,
                    last_page
                } = res.data;



                page >= last_page ? loadMoreBtn.classList.add('d-none') : ""


                const tableBody = document.getElementById("table-body");
                let option = "";




                logs.forEach(data => {

                    const createdAt = formatter.format(new Date(data.created_at));
                    // Calculate totalAmount

                    const user = data.user;
                    const visit = data.visit;

                    option = `
                    
                    <tr style="cursor:pointer; ">
                        <td >${count++}</td>
                        <td >${createdAt}</td>
                    <td >${user != null ? user.name : 'N/A'} - ${data.description}</td>
                    <td >
                        <button type="button" class="action-icon btn btn-secondary view-log text-white"
                        data-id="${data.id}">
                        <i class="fas fa-eye"></i>
                    </button>
                    </td>
                    
                    </tr>
                `;

                    tableBody.insertAdjacentHTML('beforeend', option);
                })

                page++;

            } catch (error) {
                console.log(error)
                if (error.response) {
                    document.querySelector(".from-date-error").innerText =
                        error.response.data.errors.date_from === undefined ?
                        "" :
                        error.response.data.errors.date_from[0];
                    document.querySelector(".to-date-error").innerText =
                        error.response.data.errors.date_to == undefined ?
                        "" : error.response.data.errors.date_to[0];



                } else {
                    console.log("Error", error.message);
                }
            } finally {
                loadMoreBtn.disabled = false;
            }

        }

        document.addEventListener('click', async e => {
            let targetElement = e.target;


            if (targetElement.classList.contains('view-log') || targetElement.closest('.view-log')) {
                // Find the closest parent element with the delete-teacher class
                const viewLogBtn = targetElement.closest('.view-log');
                const logId = viewLogBtn.dataset.id;

                try {

                    const config = {
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        params: {
                            id: logId
                        }
                    }

                    const res = await axios.get('{{ route('audit.view_date_audit') }}', config);
                    const userLog = res.data.log;
                    getUserLog(userLog)


                } catch (error) {
                    console.log(error);
                }

            }
        });

        function getUserLog(record) {

            //remove from the dom
            logMsg.remove();
            logContainer.innerHTML = '';
            const createdAt = formatter.format(new Date(record.created_at));

            let listItemsBefore = [];
            let listItemsAfter = [];

            //split record.description and join
            const description = record.description;

            const dataBeforeList = Object.entries(JSON.parse(record.data_before))
                .slice(0, -2)
                .map(([key, value]) => `<li><strong>${key}:</strong> ${value}</li>`)
                .join('');

            const dataAfterList = Object.entries(JSON.parse(record.data_after))
                .slice(0, -2)
                .map(([key, value]) => `<li><strong>${key}:</strong> ${value}</li>`)
                .join('');

            //append to the dom
            logContainer.innerHTML = `
           <div class="row">
                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <p class="log-label text-muted">User</p>
                            <p>${record.user.name}</p>
                        </div>
                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <p class="log-label text-muted">Username</p>
                            <p>${record.user.username}</p>
                        </div>

                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <p class="log-label text-muted">Role</p>
                            <p>${record.user.role}</p>
                        </div>
                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <p class="log-label text-muted">IP Address</p>
                            <p>${record.ip_address}</p>
                        </div>

                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <p class="log-label text-muted">Date</p>
                            <p>${createdAt}</p>
                        </div>
                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <p class="log-label text-muted">Description</p>
                            <p>${description}</p>
                        </div>

                       
                    </div>

                    <div class='row'>
                        
                         <div class="col-xs-12 col-md-12 col-lg-12">
                            <p class="log-label text-muted">Data Before</p>
                            <ul>${dataBeforeList}</ul>
                        </div>
                        <div class="col-xs-12 col-md-12 col-lg-12">
                            <p class="log-label text-muted">Data After</p>
                            <ul>
                                ${dataAfterList}
                            </ul>
                        </div>
                        
                        </div>
            `;




        }
    </script>
@endsection
