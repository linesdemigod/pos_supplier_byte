@extends('layout.layout')

@section('title', 'Customer Credit Summary')


@section('content')
    <x-breadcrumb title="Credit" subtitle='{{ $customer->name }} credit' name='Credit Summary' href='credit.index' />
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-5">
                                <div class="col-md-4">
                                    <h2>{{ $customer->name }}, <small class="text-muted">Debtor</small></h2>
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item list-group-item-action">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="avtar avtar-s rounded-circle text-success bg-light-success">
                                                        <i class="ti ti-phone f-18"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="fw-bold mb-1">Phone</h6>
                                                    <p class="text-muted mb-0">{{ $customer->phone ?? 'N/A' }}</p>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="list-group-item list-group-item-action">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="avtar avtar-s rounded-circle text-success bg-light-success">
                                                        <i class="ti ti-home f-18"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="fw-bold mb-1">Address</h6>
                                                    <p class="text-muted mb-0">{{ $customer->location ?? 'N/A' }}</p>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="bg-primary mb-3 p-2 text-white">Account Summary</h5>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <th class="">Outstanding:</th>
                                                    <td class="text-muted text-end">{{ number_format($outstanding, 2) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="">Total Credit:</th>
                                                    <td class="text-muted text-end">
                                                        {{ number_format($creditSummary->total_credit_amount, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="">Total Repaid:</th>
                                                    <td class="text-muted text-end">
                                                        {{ number_format($creditSummary->total_repaid_amount, 2) }}</td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>


                            <div class="">
                                {{-- <div class="col-md-4 mb-3">

                                    <div class="d-flex align-items-center flex-grow-1 gap-2">
                                        <label for="my-date">Period:</label>
                                        <input type="date" name="dateRange" class="form-control" id="my-date"
                                            value="">
                                        <button class="btn btn-primary" id="searchBtn">Submit</button>
                                    </div>
                                </div> --}}
                                {{-- date filter --}}
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
                                <div class="mb-4">
                                    <span class="text-danger dates-error text-sm"></span>
                                </div>

                                <h6 class="fw-bold text-end" id="search-message"></h6>
                                <div class="table-responsive">
                                    <table class="table-centered table">
                                        <thead class="table-primary">
                                            <tr>

                                                <th>Date</th>
                                                <th>Reference No</th>
                                                <th>Amount Paid</th>
                                                <th>Payment Method</th>
                                            </tr>

                                        </thead>
                                        <tbody id="table-body">


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        const startOfMonth = document.querySelector('#startDate');
        const endOfMonth = document.querySelector('#endDate');
        const searchBtn = document.querySelector('#searchBtn');
        const tableBody = document.getElementById('table-body')
        const searchMessage = document.getElementById('search-message')

        // const dateRange = `${formatDate(startOfMonth.value)} to ${formatDate(endOfMonth.value)}`
        const dateRange = `${startOfMonth.value} to ${endOfMonth.value}`




        searchBtn.addEventListener('click', searchRepayment);

        async function searchRepayment() {

            tableBody.innerHTML = "";
            searchBtn.disabled = true;

            const customerId = "{{ $customer->id }}";
            try {
                const res = await axios.get("{{ route('credit.payment.detail') }}", {
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    params: {
                        date_range: dateRange,
                        customer_id: customerId
                    }
                })

                const {
                    repayments
                } = res.data;

                if (res.status === 200) {
                    let amountPaid = 0;
                    const fromDate = startOfMonth.value ? new Date(startOfMonth.value) : null;
                    const toDate = endOfMonth.value ? new Date(startOfMonth.value) : null;

                    if (repayments.length === 0) {
                        searchMessage.textContent = `No results found for ${dateRange}`;
                        tableBody.innerHTML = "";
                        return;
                    }
                    searchMessage.textContent = `Showing results for ${dateRange}`;
                    repayments.forEach(repayment => {

                        amountPaid = parseFloat(repayment.amount_paid)
                        const createdAt = timeFormatter.format(new Date(repayment.date_paid));
                        const row = `
                    <tr>
                        <td>${createdAt}</td>
                        <td>${repayment.reference ?? ''}</td>
                        <td>${amountPaid.toLocaleString()}</td>
                        <td>${repayment.payment_method ?? ''}</td>
                    </tr>
                `;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });
                }
            } catch (error) {
                console.log(error)
                if (error.response) {
                    if (error.response.data.errors.date_range[0]) {
                        // dateRange.classList.add("border", "border-danger")
                        document.querySelector(".dates-error").textContent = "error: " +
                            error.response.data.errors.date_range[0];

                    }



                } else {
                    console.log("Error", error.message);
                }
            } finally {
                searchBtn.disabled = false;
            }
        }

        document.addEventListener("DOMContentLoaded", async function() {
            await searchRepayment();
        });
    </script>
@endpush
