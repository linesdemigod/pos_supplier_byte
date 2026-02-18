@extends('layout.layout')

@section('title', 'Payment')


@section('content')
    <x-breadcrumb title="Apps" subtitle='Payment' name='Payment' />
    <div class="py-3">
        <div class="card">

            <div class="card-body">
                <div class="">
                    <div class="d-flex justify-content-end align-items-center gap-3">
                        <div class="card border-primary border">
                            <div class="card-body">
                                <h3 class="card-title">Outstanding Balance</h3>
                                <p class="card-text fs-4" id="remaining-balance">0</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="form-label">Search Supplier</label>

                        <input type="text" name="item" class="form-control" id="supplier-search"
                            placeholder="Start typing supplier name or phone...">
                        <ul id="supplier-suggestions" class="autocomplete-list">
                        </ul>
                    </div>

                    {{-- supplier select --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-start align-items-center gap-2">
                            <p class="fw-bold">Supplier:</p>
                            <div class="d-flex align-items-center gap-2" id="supplier-container">

                            </div>

                        </div>
                    </div>
                </div>

                <p class="fw-bold fs-4">Repayment form</p>
                <div class="d-flex justify-content-start align-items-center mb-4 flex-wrap gap-3">
                    <div class="">
                        <input type="hidden" name="" id="supplier-input-id">
                        <input type="text" name="amount" id="amount-paid" class="form-control"
                            placeholder="enter amount paid">
                    </div>
                    <div class="">
                        <button type="button" id="submit-repayment" class="btn btn-primary">Submit</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table-centered w-100 table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Payment date</th>
                                <th scope="col">Amount Paid</th>
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
@endsection
@push('scripts')
    <script>
        const prevBtn = document.getElementById("prevPageBtn");
        const nextBtn = document.getElementById("nextPageBtn");
        const tableBody = document.getElementById('tableBody')
        const submitRepayment = document.getElementById('submit-repayment')
        const supplierInputId = document.getElementById('supplier-input-id')
        const amountPaid = document.getElementById('amount-paid')
        const remainingBalance = document.getElementById('remaining-balance');
        let currentPage = 1;
        let perPage = 10;

        //repayment
        submitRepayment.addEventListener('click', async (e) => {
            if (amountPaid.value === "") {
                notyf.error("Enter the amount supplier paid");
                return;
            }

            if (supplierInputId.value === "") {
                notyf.error("Select a supplier");
                return;
            }

            const result = await Swal.fire({
                title: "Are you sure you want to proceed?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, proceed!",
            });

            try {
                if (result.isConfirmed) {
                    const res = await axios.post("/credit/pay-repayment", {
                        amount: amountPaid.value,
                        supplier: supplierInputId.value
                    });

                    const {
                        message,
                        orderOutstanding,
                        repayment
                    } = res.data;



                    if (res.status === 200) {
                        const repaymentPaid = Number(amountPaid.value)
                        const totalOutstanding = Number(orderOutstanding) - repaymentPaid

                        // const totalOutstanding = Number(orderOutstanding) - Number(amountPaid.value)

                        // Update remaining balance
                        remainingBalance.textContent = formatLocalCurrency(totalOutstanding);

                        // Clear input
                        amountPaid.value = "";

                        // Show success message
                        notyf.success(message);

                        // Format date
                        const createdAt = timeFormatter.format(new Date(repayment.created_at));

                        // Add to table
                        const row = `
                    <tr>
                        <td>${createdAt}</td>
                        <td>${formatLocalCurrency(repaymentPaid)}</td>
                    </tr>
                `;
                        tableBody.insertAdjacentHTML('beforeend', row); // Fixed typo
                    }
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
                        notyf.error(data?.errors?.supplier?.[0] ?? '');
                        notyf.error(data?.errors?.amount?.[0] ?? '');
                        break;
                    default:
                        notyf.error("An unexpected error occurred");
                }

                console.log(error);
            }
        });



        //search supplier
        const supplierSuggestionsList = document?.querySelector(
            "#supplier-suggestions"
        );
        const supplierInput = document.querySelector("#supplier-search");
        supplierInput.addEventListener("keyup", debounce(searchSupplier, 300));

        async function searchSupplier() {
            try {
                const response = await axios.get("/supplier/get-supplier", {
                    headers: {
                        "Content-Type": "application/json",
                    },
                    params: {
                        name: supplierInput.value,
                    },
                });

                const {
                    suppliers
                } = response.data;


                displaySupplierSuggestion(suppliers);
            } catch (error) {
                console.log(error);
            }
        }



        function displaySupplierSuggestion(suppliers) {
            // Clear previous suggestions
            supplierSuggestionsList.innerHTML = "";

            // Show suggestions if any item matches
            if (suppliers && suppliers.length > 0) {
                suppliers.forEach((supplier) => {
                    const listItem = document.createElement("li");
                    let itemInputWidth = supplierInput.offsetWidth;

                    const name = supplier.name;
                    const telephone = supplier.phone_number;

                    listItem.textContent = `${name} - ${telephone} `;
                    listItem.style.padding = "8px";
                    listItem.style.cursor = "pointer";
                    listItem.style.width = `${itemInputWidth}px`;
                    listItem.classList.add("list-dropdown");

                    // Add click event listener to select the item
                    listItem.addEventListener("click", () => {
                        // supplierInput.value = name;
                        selectSupplier(supplier);
                        //  itemCode.value = item.theCode;
                        supplierSuggestionsList.innerHTML = "";
                        supplierInput.value = "";

                        perPage = 10
                        currentPage = 1
                    });

                    supplierSuggestionsList.appendChild(listItem);
                });
            }
        }

        function selectSupplier(supplier) {
            const supplierContainer = document.querySelector("#supplier-container");

            const supplierTemplate = `
            <p>${supplier.name}</p>
            <p class="">- ${supplier.phone_number}</p>
            <p class="text-danger remove-supplier fw-bold" data-supplier-id="${supplier.id}" role="button" >X</p>
        `;

            supplierContainer.innerHTML = supplierTemplate;

            const supplierId = supplier.id;
            supplierInputId.value = supplierId
            getSupplierRepayment()
        }

        document.addEventListener("click", (e) => {
            const targetElement = e.target;
            if (targetElement.classList.contains("remove-supplier")) {
                const supplierContainer = document.querySelector(
                    "#supplier-container"
                );
                //remove supplierContainer children
                supplierContainer.innerHTML = "";
                supplierInputId.value = ""
                tableBody.innerHTML = ""
                remainingBalance.textContent = 0
                perPage = 10
                currentPage = 1
            }
        });



        async function getSupplierRepayment() {
            tableBody.innerHTML = "";
            try {
                const response = await axios.get("/credit/fetch-repayment", {
                    headers: {
                        "Content-Type": "application/json",
                    },
                    params: {
                        supplierId: supplierInputId.value,
                        page: currentPage,
                        per_page: perPage
                    },
                });

                const {
                    repayments,
                    supplier,
                    current_page,
                    last_page,
                    outstanding,
                } = response.data;


                remainingBalance.textContent = formatLocalCurrency(outstanding)
                // remainingBalance.textContent = formatLocalCurrency(outstanding)

                // Fill table
                repayments.forEach(repayment => {
                    const createdAt = timeFormatter.format(new Date(repayment.created_at));
                    const row = `
                    <tr>
                        <td>${createdAt}</td>
                        <td>${repayment.amount_paid}</td>
                    </tr>
                `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
                // Handle pagination buttons
                currentPage = current_page;
                prevBtn.disabled = currentPage <= 1;
                nextBtn.disabled = currentPage >= last_page;
            } catch (error) {
                const status = error?.response.status;
                const errors = error.response?.data?.errors ?? {};

                if (status === 422) {
                    // validation error
                    notyf.error(errors.supplier[0] ?? '');
                }
                console.log(error);
            }
        }

        // Event listeners for pagination buttons
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                getSupplierRepayment()
            }
        });

        nextBtn.addEventListener('click', () => {
            currentPage++;
            getSupplierRepayment()
        });

        //clear suggestions
        document.addEventListener("click", function(event) {
            // Check if the clicked element is not the element you want to hide
            if (!event.target.classList.contains("autocomplete-list")) {
                // Hide the dropdown
                const dropdownElementList =
                    document.querySelectorAll(".autocomplete-list");
                dropdownElementList.forEach((button) => {
                    //  button.nextElementSibling.classList.add("d-none");
                    button.innerHTML = "";
                });
            }
        });
    </script>
@endpush
