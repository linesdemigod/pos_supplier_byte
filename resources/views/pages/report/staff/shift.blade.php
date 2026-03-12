@extends('layout.layout')

@section('title', 'Shift Report')

@section('content')
    <x-breadcrumb title="Apps" subtitle='Shifts' name='Shifts' />

    <div class="py-3">
        <div>
            <section class="pt-4">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center gap-5">
                        {{-- create show perpage --}}
                        <div class="d-flex justify-content-between align-items-center gap-4">
                            <div class="d-flex align-items-center gap-2">
                                <label for="per_page">Show:</label>
                                <select name="perPage" class="form-select form-select-sm" id="perPageSelect">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>

                            {{-- <div class="d-flex align-items-center flex-grow-1 gap-2">
                                <label for="my-date">Period:</label>
                                <input type="date" name="dateRange" class="form-control" id="my-date" value="">
                                <button class="btn btn-primary" id="searchBtn">Submit</button>
                            </div> --}}

                            <div class="d-flex justify-content-start align-items-end mb-0 gap-3">

                                <!-- Date Filters -->
                                <div class="row g-3 align-items-center">
                                    <div class="col-auto">
                                        <label for="from" class="col-form-label">From</label>
                                    </div>
                                    <div class="col-auto">
                                        <input type="date" class="form-control form-control-light" id="startDate"
                                            value="{{ $dates['start'] }}">
                                    </div>
                                </div>
                                <div class="row g-3 align-items-center">
                                    <div class="col-auto">
                                        <label for="to" class="col-form-label">To</label>
                                    </div>
                                    <div class="col-auto">
                                        <input type="date" class="form-control form-control-light" id="endDate"
                                            value="{{ $dates['end'] }}">

                                    </div>
                                </div>
                                <div class="row g-3 align-items-center">
                                    <div class="col-auto">
                                        <button type="button" id="searchBtn"
                                            class="form-control btn btn-primary btn-block">
                                            Search
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>




                    </div>
                </div>
            </section>
            <div class="">
                <div class="card">
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table-striped table">
                                <thead>

                                    <tr>

                                        <th scope="col">Employee</th>
                                        <th scope="col">Opened At</th>
                                        <th scope="col">Closed At</th>
                                        <th scope="col">Starting Cash</th>
                                        <th scope="col">Closing Cash</th>
                                        <th scope="col">Expected Cash</th>
                                        <th scope="col">Cash Difference</th>
                                        {{-- <th>Action</th> --}}

                                    </tr>

                                </thead>
                                <tbody id="tableBody">

                                </tbody>
                            </table>
                        </div>



                        <div class="d-flex justify-content-end mt-3 gap-3">
                            <button class="btn btn-secondary" id="prevPageBtn" disabled>Previous</button>
                            <button class="btn btn-secondary" id="nextPageBtn" disabled>Next</button>
                        </div>


                    </div>
                </div>
            </div>

        </div>



    </div>
@endsection
@push('scripts')
    <script>
        const startOfMonth = document.querySelector('#startDate');
        const endOfMonth = document.querySelector('#endDate');
        const searchBtn = document.querySelector('#searchBtn');
        const perPageSelect = document.getElementById('perPageSelect');
        const tableBody = document.getElementById('tableBody')
        const prevBtn = document.getElementById("prevPageBtn");
        const nextBtn = document.getElementById("nextPageBtn");
        // dateRange.value = `${formatDate(startOfMonth)} to ${formatDate(endOfMonth)}`

        let currentPage = 1;
        let perPage = 10;


        async function fetchShifts() {
            tableBody.innerHTML = ""; // clear table before inserting new data
            const dateRange = `${startOfMonth.value} to ${endOfMonth.value}`

            const config = {
                headers: {
                    Accept: "application/json",
                },
                params: {
                    date_range: dateRange,
                    page: currentPage,
                    per_page: perPage
                },
            }

            try {
                const response = await axios.get("{{ route('report.saleShift') }}", config);
                const {
                    orders,
                    current_page,
                    last_page,
                    totalSale
                } = response.data;

                console.log(orders)

                // Fill table
                orders.forEach(order => {
                    const openingTime = timeFormatter.format(new Date(order.opened_at));
                    const closingTime = timeFormatter.format(new Date(order.closed_at));
                    const startingCash = formatLocalCurrency(order.starting_cash)
                    const closingCash = formatLocalCurrency(order.closing_cash)
                    const expectedCash = formatLocalCurrency(order.expected_cash)
                    const cashDifference = formatLocalCurrency(order.cash_difference)
                    const row = `
                    <tr>
                        <td>${order.user.name}</td>
                        <td>${openingTime}</td>
                        <td>${closingTime}</td>
                        <td>${startingCash}</td>
                        <td>${closingCash}</td>
                        <td>${expectedCash}</td>
                        <td>${cashDifference}</td>
                        
                    </tr>
                `;

                    // <td>
                    //             <button type='button' class="btn btn-primary view-shift"
                    //                 data-id='${order.id}'>
                    //                 <i class="fas fa-eye"></i> 
                    //             </button>
                    //         </td>
                    tableBody.insertAdjacentHTML('beforeend', row);
                });



                // Handle pagination buttons
                currentPage = current_page;
                prevBtn.disabled = currentPage <= 1;
                nextBtn.disabled = currentPage >= last_page;

            } catch (error) {
                console.error(error);
                // if (error.response) {
                //     if (error.response.data.errors.date_range[0]) {
                //         dateRange.classList.add("border", "border-danger")

                //     }



                // } else {
                //     console.log("Error", error.message);
                // }
            }
        }

        // Event listeners for pagination buttons
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                fetchShifts();
            }
        });

        nextBtn.addEventListener('click', () => {
            currentPage++;
            fetchShifts();
        });

        searchBtn.addEventListener('click', () => {
            currentPage = 1; // reset to page 1 when searching
            fetchShifts();
        });

        perPageSelect.addEventListener('change', () => {
            perPage = parseInt(perPageSelect.value);
            currentPage = 1;
            fetchShifts();
        });


        document.addEventListener('click', async (e) => {
            let targetElement = e.target;

            if (targetElement.classList.contains('view-shift') || targetElement.closest('.view-shift')) {
                // Find the closest parent element
                const viewShiftBtn = targetElement.closest('.view-shift');
                const requestId = viewShiftBtn.dataset.id;

                const config = {
                    headers: {
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": document
                            .querySelector("meta[name='csrf-token']")
                            .getAttribute("content"),
                    },
                    params: {
                        id: requestId
                    }
                };

                const url = "{{ route('report.saleShiftDetail') }}";

                try {
                    const res = await axios.get(url, config);
                    const {
                        shift
                    } = res.data;

                    // Create and display the custom modal
                    const modalHtml = `
                <div class="custom-modal" id="resultModal">
                    <div class="custom-modal-content">
                        <span class="custom-modal-close" id="closeModal">&times;</span>
                        <h2>Shift Report</h2>
                        <div id="modal-msg" class="text-success font-16 mb-3 text-center"></div>
                        <p class="fw-bold fs-4">${shift.name}</p>
                          <table class="table">
                                <tr>
                                    <th scope="col">Shift Open</th>
                                    <td>${shift.start_time}</td>
                                </tr>
                                <tr>
                                    <th scope="col">Shift Closed</th>
                                    <td>${shift.end_time}</td>
                                </tr>
                                <tr colspan="2">
                                    <th colspan="2"><span class="text-success">Sales Summary</span></th>
                                    
                                </tr>
                                <tr>
                                    <th scope="col">Gross Sales</th>
                                    <td>${shift.subtotal}</td>
                                </tr>
                                <tr>
                                    <th scope="col">Discount</th>
                                    <td>${shift.discount}</td>
                                </tr>
                                <tr>
                                    <th scope="col">Cash Collected</th>
                                    <td>${shift.cash_collected}</td>
                                </tr>
                                <tr>
                                    <th scope="col">Net Sales</th>
                                    <td>${shift.total}</td>
                                </tr>
                            </table>
                    </div>
                </div>`;

                    // Append the modal to the body
                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                    const modalMsg = document.getElementById('modal-msg');

                    // Show the modal by setting display to block
                    const resultModal = document.getElementById('resultModal');
                    resultModal.style.display = 'block';

                    // Close the modal when the close button is clicked
                    document.getElementById('closeModal').addEventListener('click', () => {
                        resultModal.style.display = 'none';
                        resultModal.remove(); // Remove the modal from the DOM
                    });

                    // Close the modal when clicking outside of the modal content
                    window.addEventListener('click', (event) => {
                        if (event.target === resultModal) {
                            resultModal.style.display = 'none';
                            resultModal.remove(); // Remove the modal from the DOM
                        }
                    });



                } catch (error) {
                    console.log(error);
                }
            }
        });


        // load when domcontent load
        document.addEventListener("DOMContentLoaded", async function() {
            await fetchShifts();
        });
    </script>
@endpush
