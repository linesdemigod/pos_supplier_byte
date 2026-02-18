@extends('layout.layout')

@section('title', 'Supplier Payment')


@section('content')
    <x-breadcrumb title="Supplier" subtitle='{{ $supplier->name }} supplier' name='Supplier Summary' href='supplier.index' />
    <section class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-5">
                                <div class="col-md-4">
                                    <h2>{{ $supplier->name }}, <small class="text-muted">Supplier</small></h2>
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
                                                    <p class="text-muted mb-0">{{ $supplier->contact_info ?? '' }}</p>
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
                                                    <p class="text-muted mb-0">{{ $supplier->address ?? 'N/A' }}</p>
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
                                                    <td class="text-muted text-end" id="outstanding">
                                                        {{ number_format($outstanding, 2) ?? 0 }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="">Total Purchase:</th>
                                                    <td class="text-muted text-end" id="total-purchase">
                                                        {{ number_format($purchaseSummary->total_purchase_amount ?? 0, 2) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="">Total Repaid:</th>
                                                    <td class="text-muted text-end" id="total-repaid">
                                                        {{ number_format($purchaseSummary->total_repaid_amount ?? 0, 2) }}
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>

                                    <button type="button" class="btn btn-primary rounded-3" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal"><i class="fas fa-money-check-alt"></i>
                                        Payment</button>
                                </div>
                            </div>


                            <div class="">
                                <div class="col-md-4 mb-3">

                                    <div class="d-flex align-items-center flex-grow-1 gap-2">
                                        <div class="d-flex align-items-center flex-grow-1 gap-2">
                                            <label for="my-date">From:</label>
                                            <input type="date" name="date_from" class="form-control" id="date_from"
                                                value="{{ $dateRange }}">
                                        </div>

                                        <div class="d-flex align-items-center flex-grow-1 gap-2">
                                            <label for="my-date">To:</label>
                                            <input type="date" name="date_to" class="form-control" id="date_to"
                                                value="{{ $dateRange }}">
                                        </div>
                                        <button class="btn btn-primary" id="searchBtn">Submit</button>
                                    </div>
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
        {{-- modal --}}
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Payment</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="fw-bold text-success payment-success-msg"></p>
                        <form id="payment-form" method="Post">
                            @csrf
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="amount_paid" class="form-label">Amount</label>
                                    <input type="text" name="amount_paid" class="form-control">
                                    <span class="text-danger amount-error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <div class="">
                                        <div class="form-group mb-3">
                                            <label for="payment_method" class="form-label">Phone</label>
                                            <select name="payment_method" id="payment-method" class="form-select">
                                                <option value="cash">Cash</option>
                                                <option value="momo">Momo</option>
                                                <option value="card">Card</option>
                                                <option value="cheque">Cheque</option>
                                                <option value="bank_transfer">Bank Transfer</option>
                                            </select>
                                            <span class="text-danger payment-method-error"></span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group mb-3 mt-3">
                                <button type="submit" name="submit" class="btn btn-primary btn-block"
                                    id="customer_btn">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
@push('scripts')
    <script>
        const dateFrom = document.querySelector('#date_from');
        const dateTo = document.querySelector('#date_to');
        const searchBtn = document.querySelector('#searchBtn');
        const tableBody = document.getElementById('table-body')
        const searchMessage = document.getElementById('search-message')
        const outstandingEl = document.getElementById('outstanding');
        const totalPurchaseEl = document.getElementById('total-purchase');
        const totalRepaidEl = document.getElementById('total-repaid');



        searchBtn.addEventListener('click', searchPayment);

        async function searchPayment() {

            tableBody.innerHTML = "";
            searchBtn.disabled = true;

            const supplierId = "{{ $supplier->id }}";
            try {
                const res = await axios.get("{{ route('supplier.payment.detail') }}", {
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    params: {
                        date_from: dateFrom.value,
                        date_to: dateTo.value,
                        supplier_id: supplierId
                    }
                })

                const {
                    payments
                } = res.data;

                if (res.status === 200) {
                    let amountPaid = 0;


                    const fromDate = formatter.format(new Date(dateFrom.value));
                    const toDate = formatter.format(new Date(dateTo.value));

                    if (payments.length === 0) {
                        searchMessage.textContent = `No results found for ${dateFrom.value} - ${dateTo.value}`;
                        tableBody.innerHTML = "";
                        return;
                    }
                    searchMessage.textContent = `Showing results for ${dateFrom.value} - ${dateTo.value}`;
                    payments.forEach(payment => {

                        amountPaid = parseFloat(payment.amount_paid)
                        const createdAt = timeFormatter.format(new Date(payment.created_at));
                        const row = `
                    <tr>
                        <td>${createdAt}</td>
                        <td>${payment.reference ?? ''}</td>
                        <td>${amountPaid.toLocaleString()}</td>
                        <td>${payment.payment_method ?? ''}</td>
                    </tr>
                `;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });
                }
            } catch (error) {
                console.log(error)
                if (error.response) {
                    if (error.response.data.errors.date_range[0]) {
                        dateFrom.classList.add("border", "border-danger")
                        dateTo.classList.add("border", "border-danger")

                    }



                } else {
                    console.log("Error", error.message);
                }
            } finally {
                searchBtn.disabled = false;
            }
        }

        //payment
        const paymentForm = document.getElementById('payment-form');

        paymentForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(paymentForm);
            const supplierId = "{{ $supplier->id }}";
            formData.append('supplier_id', supplierId);

            //clear error message
            document.querySelector('.amount-error').textContent = '';
            document.querySelector('.payment-method-error').textContent = '';
            document.querySelector('.payment-success-msg').textContent = '';


            try {
                const res = await axios.post("{{ route('supplier.payment') }}", formData, {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const {
                    payment,
                    message,
                    amountPaid
                } = res.data;

                if (res.status === 200) {
                    let outstanding = 0;
                    let totalRepaid = 0;
                    let totalPurchase = 0;
                    outstanding = parseFloat(outstandingEl.textContent.replace(/,/g, '')) - parseFloat(
                        amountPaid);
                    totalRepaid = parseFloat(totalRepaidEl.textContent.replace(/,/g, '')) + parseFloat(
                        amountPaid);
                    outstandingEl.textContent = outstanding.toLocaleString();
                    totalRepaidEl.textContent = totalRepaid.toLocaleString();



                    notyf.success(message);
                    paymentForm.reset();

                    if (payment.length > 0) {
                        payments.forEach(payment => {

                            amountPaid = parseFloat(payment.amount_paid)
                            const createdAt = timeFormatter.format(new Date(payment.created_at));
                            const row = `
                    <tr>
                        <td>${createdAt}</td>
                        <td>${payment.reference ?? ''}</td>
                        <td>${amountPaid.toLocaleString()}</td>
                        <td>${payment.payment_method ?? ''}</td>
                    </tr>
                `;
                            tableBody.insertAdjacentHTML('beforeend', row);
                        });

                        return;
                    }

                    // amountPaid = parseFloat(payment.amount_paid)
                    const createdAt = timeFormatter.format(new Date(payment.created_at));
                    const row = `
                    <tr>
                        <td>${createdAt}</td>
                        <td>${payment.reference ?? ''}</td>
                        <td>${amountPaid.toLocaleString()}</td>
                        <td>${payment.payment_method ?? ''}</td>
                    </tr>
                `;
                    tableBody.insertAdjacentHTML('beforeend', row);

                }
            } catch (error) {

                const status = error?.response?.status;
                const data = error?.response?.data;

                switch (status) {
                    case 404:
                        notyf.error("Supplier not found");
                        break;
                    case 400:
                        notyf.error(data?.error ?? "Invalid request");
                        break;
                    case 422:
                        document.querySelector(".amount-error").innerText = errors.amount_paid ?? "";
                        document.querySelector(".payment-method-error").innerText =
                            errors.payment_method ?? "";
                        break;
                    default:
                        notyf.error("An unexpected error occurred");
                }



            }
        })

        document.addEventListener("DOMContentLoaded", async function() {
            await searchPayment();
        });
    </script>
@endpush
