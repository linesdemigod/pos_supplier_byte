@extends('layout.layout')

@section('title', 'Supplier Item record')


@section('content')
    <section class="py-2">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p class="fw-bold fs-3">Supplier Item record</p>
                </div>
            </div>
        </div>
    </section>



    <section class="container">
        <div class="row">

            <div class="col-sm-12 col-xl-12 col-lg-12">

                <div class="card border">
                    <div class="card-body">
                        <div class="flex-grow-1 pe-2">
                            <p class="fw-bold fs-3">Supplier Item Record</p>
                        </div>
                        {{-- search item --}}
                        <div class="row mb-3">
                            <div class="col-xxl-12 mb-2">

                                <div class="row">
                                    <div class="col-xxl-7 mb-2">
                                        <div class="item-search-container">
                                            <label class="form-label">Search Item</label>
                                            <input type="text" name="item" class="form-control" id="item-search"
                                                autocomplete="off" placeholder="Start typing a item...">
                                            <ul id="suggestions" class="autocomplete-list">
                                            </ul>
                                        </div>

                                    </div>
                                    <div class="col-xxl-5 mb-2">
                                        <div class="col-auto mb-3">
                                            <div class="">
                                                <label class="form-label">Search Supplier</label>

                                                <input type="text" name="item" class="form-control"
                                                    id="supplier-search"
                                                    placeholder="Start typing supplier name or phone...">
                                                <ul id="supplier-suggestions" class="autocomplete-list">
                                                </ul>
                                            </div>
                                        </div>

                                        {{-- supplier details --}}
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-end align-items-center gap-2">
                                                <p class="fw-bold">Supplier:</p>
                                                <div class="d-flex align-items-center gap-2" id="supplier-container">

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        {{-- end search item --}}



                        {{-- cart table --}}
                        <div class="table-responsive" data-simplebar style="max-height: 400px">
                            <table class="table-striped table">
                                <thead class="table-primary">
                                    <tr>
                                        <th class="d-none">ID</th>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Unit type</th>
                                        <th>
                                            Conversion rate <br>
                                            <small class="text-muted">how many units per box</small>
                                        </th>
                                        <th>Cost price</th>
                                        <th>Subtotal</th>
                                        <th>Total unit added</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-table">

                                </tbody>
                            </table>
                        </div>
                        {{-- calculation --}}
                        <div class="m-3">
                            <div class="row justify-content-end mb-2 gap-3">
                                <div class="col-auto">
                                    <label for="inputPassword6" class="form-label">Subtotal</label>
                                </div>
                                <!-- gross -->
                                <div class="col-auto">
                                    <input type="text" name="gross" id="total-subtotal" class="form-control gross"
                                        readonly="">
                                </div>
                            </div>
                        </div>


                        {{-- button --}}
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-primary" id="save-purchase"><i class="fas fa-save"></i>
                                Save</button>
                            <button class="btn btn-danger" id="clear-cart"><i class="fas fa-trash-alt"></i>
                                Clear All</button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </section>


@endsection
@push('scripts')
    <script>
        // Select all buttons and result display
        const buttons = document.querySelectorAll('.btn-dine-option');
        const resultDiv = document.querySelector('#result');



        // Add click event listeners to all buttons
        buttons.forEach(btn => {
            btn.addEventListener('click', handleButtonClick);
        });

        function debounce(func, delay = 300) {
            let debounceTimer;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => func.apply(context, args), delay);
            };
        }



        function limitString(str, maxLength, suffix = '...') {
            if (str.length <= maxLength) {
                return str;
            }
            return str.substr(0, maxLength - suffix.length) + suffix;
        }

        const supplierSuggestionsList = document?.querySelector(
            "#supplier-suggestions"
        );
        const cartTable = document.getElementById("cart-table");
        const categoryInput = document.querySelector("#category-search");
        // const supplierSelect = document.querySelector("#supplier_id");

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



        const itemInput = document?.querySelector("#item-search");
        const suggestionsList = document?.querySelector("#suggestions");




        itemInput.addEventListener("keyup", debounce(searchItem, 300));

        async function searchItem() {
            try {
                if (itemInput.value === "") {
                    return;
                }
                const response = await axios.get("{{ route('supplier.search.item') }}", {
                    headers: {
                        "Content-Type": "application/json",
                    },
                    params: {
                        item: itemInput.value,
                    },
                });

                const {
                    items
                } = response.data;



                displaySuggestions(items);
            } catch (error) {
                console.log(error);
            }
        }

        function displaySuggestions(records) {
            // Clear previous suggestions
            suggestionsList.innerHTML = "";
            const oldQuantity = document?.querySelector("#old-quantity");

            // Show suggestions if any item matches
            if (records && records.length > 0) {
                records.forEach((record) => {
                    const listItem = document.createElement("li");
                    let itemInputWidth = itemInput.offsetWidth;

                    const itemName = record.item.name;
                    const itemCode = record.item.item_code;
                    const price = record.item.price;
                    const id = record.item.id;
                    const quantity = record.quantity;

                    listItem.textContent = `${itemName} - ${itemCode} `;
                    listItem.style.padding = "8px";
                    listItem.style.cursor = "pointer";
                    listItem.style.width = `${itemInputWidth}px`;
                    listItem.classList.add(
                        "list-dropdown",
                        "border-2",
                        "border-bottom",

                    );

                    // Add click event listener to select the item
                    listItem.addEventListener("click", () => {
                        addToTable(record)

                        suggestionsList.innerHTML = "";
                        itemInput.value = "";
                    });

                    suggestionsList.appendChild(listItem);
                });
            }
        }


        // Get the modal and button
        var modal = document?.getElementById("myModal");
        // var openModalBtn = document.getElementById('openModalBtn');
        var closeModalBtn = document?.getElementById("closeModal");

        // Close the modal
        if (closeModalBtn != null) {
            closeModalBtn.onclick = function() {
                modal.style.display = "none";
            };
        }

        function getFormattedDate() {
            const today = new Date();
            const year = today.getFullYear();
            let month = today.getMonth() + 1;
            let day = today.getDate();

            // Adding leading zeros if month/day are single digits
            month = month < 10 ? `0${month}` : month;
            day = day < 10 ? `0${day}` : day;

            const formattedDate = `${year}-${month}-${day}`;
            return formattedDate;
        }



        function addToTable(record) {
            let found = false;
            const id = record.item.id.toString();
            const name = record.item.name;



            for (const row of cartTable.rows) {
                if (row.children[0].textContent === id) {
                    found = true;

                    const quantity =
                        parseInt(row.children[3].children[0].value) + 1;

                }
            }

            if (!found) {
                const quantity = 1;
                const newRow = ` <tr role="button" data-id="${id}" data-name="${name}" >
                    <td class="d-none">${id}</td>
                    <td>${name}</td>
                    <td>
                        <input type="number" class="form-control quantity" value="1" min="1"  maxlength="3" max="100" onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
                    </td>
                   
                    <td>
                        <select class="form-select unit-type-select">
                            <option value="unit" > Unit </option>
                            <option value="box" > Box </option>
                        </select> 
                    </td>      
                     <td>
                        <input type="number" class="form-control conversion-rate"  min="1"  maxlength="3" max="100" onkeypress="return event.charCode >= 48 && event.charCode <= 57" value="1" />
                    </td>
                     <td>
                        <input type="text" class="form-control cost-price" value="0" min="1"   />
                    </td> 
                    <td>
                        <span class="subtotal">0</span> 
                    </td>
                    <td>
                        <span class="total-unit-added">0</span>
                    </td>
                    <td><span class="text-danger fw-bold fs-4"><i class="fas fa-trash delete-btn" data-id="${id}"></i></span></td>
                </tr>
                    `;
                cartTable.insertAdjacentHTML("beforeend", newRow);
            }
            // updateTotalSubtotal();
        }

        const totalSubtotalElement = document.getElementById("total-subtotal");
        const totalGrandtotalElement = document.getElementById("total-grandtotal");


        let totalSubtotal = 0;

        function updateTotalSubtotal() {
            totalSubtotal = 0;
            for (const row of cartTable.rows) {
                const subtotal = parseFloat(row.children[6].textContent);
                totalSubtotal += subtotal;
            }


            totalSubtotalElement.value = totalSubtotal.toFixed(2);
        }

        // calculations for a given row
        function updateRowCalculations(row) {
            const quantityElement = row.querySelector('.quantity');
            const costPriceElement = row.querySelector('.cost-price');
            const subtotalElement = row.querySelector('.subtotal');
            const totalUnitsAddedElement = row.querySelector('.total-unit-added');
            const unitTypeSelectElement = row.querySelector('.unit-type-select');
            const conversionRateElement = row.querySelector('.conversion-rate');

            // Parse and sanitize quantity
            let quantityValue = parseInt(quantityElement.value);
            quantityValue = isNaN(quantityValue) || quantityValue === "" ? 1 : quantityValue;
            quantityElement.value = quantityValue;

            // Parse and sanitize price
            let priceValue = parseFloat(costPriceElement.value);
            priceValue = isNaN(priceValue) || priceValue === "" ? 0 : priceValue;
            costPriceElement.value = priceValue;

            // Parse conversion rate
            let conversionRateValue = parseFloat(conversionRateElement.value);
            conversionRateValue = isNaN(conversionRateValue) || conversionRateValue === "" ? 0 : conversionRateValue;
            conversionRateElement.value = conversionRateValue;

            // Update total units added (only for 'box')
            if (unitTypeSelectElement.value === 'box') {
                totalUnitsAddedElement.textContent = quantityValue * conversionRateValue;
            } else {
                totalUnitsAddedElement.textContent = quantityValue;
            }

            // Update subtotal
            const subtotal = quantityValue * priceValue;
            subtotalElement.textContent = subtotal;

            // Uncomment if you have a total updater
            updateTotalSubtotal();
        }


        document.addEventListener("input", debounce((event) => {
            const target = event.target;
            if (target.classList.contains("quantity") || target.classList.contains("cost-price") || target
                .classList.contains('conversion-rate')) {
                const row = target.closest('tr');
                if (row) updateRowCalculations(row);
            }
        }, 300));

        document.addEventListener("change", (event) => {
            const target = event.target;
            if (target.classList.contains("unit-type-select")) {
                const row = target.closest('tr');
                if (row) updateRowCalculations(row);
            }
        });



        //remove from cart
        document.addEventListener("click", (event) => {
            if (event.target.classList.contains("delete-btn")) {
                const id = event.target.getAttribute("data-id");
                // get the tr
                const row = event.target.parentNode.parentNode.parentNode;
                // remove the entire row
                row.parentNode.removeChild(row);
                updateTotalSubtotal();
            }
        });



        //clear cart
        document.querySelector("#clear-cart").addEventListener("click", () => {
            Swal.fire({
                title: "Are you sure you want to clear the cart?",
                icon: "success",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, clear it!",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    document.querySelector("#cart-table").innerHTML = "";
                    discount.value = 0;
                    updateTotalSubtotal(); //update the transaction part
                }
            });
        });

        //Supplier
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
                    const telephone = supplier.contact_info;

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
                    });

                    supplierSuggestionsList.appendChild(listItem);
                });
            }
        }

        function selectSupplier(supplier) {
            const supplierContainer = document.querySelector("#supplier-container");

            const supplierTemplate = `
            <p>${supplier.name}</p>
            <p class="">- ${supplier.contact_info}</p>
            <p class="text-danger remove-supplier fw-bold" data-supplier-id="${supplier.id}" role="button">X</p>
        `;

            supplierContainer.innerHTML = supplierTemplate;
        }

        document.addEventListener("click", (e) => {
            const targetElement = e.target;
            if (targetElement.classList.contains("remove-supplier")) {
                const supplierContainer = document.querySelector(
                    "#supplier-container"
                );
                //remove supplierContainer children
                supplierContainer.innerHTML = "";
            }
        });

        //place order
        const savePurchase = document.querySelector("#save-purchase");
        savePurchase.addEventListener("click", placeOrder);

        async function placeOrder() {
            const purchaseItems = [];

            savePurchase.disabled = true

            // generate id for order
            const randomId = Math.floor(Math.random() * 999999999);
            const timestamp = Date.now();
            const combinedValue = `${randomId}${timestamp}`;


            const supplierData = document.querySelector(".remove-supplier");
            const supplierId =
                supplierData?.getAttribute("data-supplier-id") || null;



            // get the id and the quantity from the cart table
            for (const row of cartTable.rows) {
                const id = row.children[0].textContent.trim();
                const quantity = row.children[2].children[0].value.trim();
                const unitType = row.children[3].children[0].value.trim();
                const conversionRate = row.children[4].children[0].value.trim();
                const costPrice = row.children[5].children[0].value.trim();
                const subtotal = row.children[6].textContent.trim();
                const totalUnitAdded = row.children[7].textContent.trim();


                // Add the item to the purchaseItems array
                purchaseItems.push({
                    id: id,
                    quantity: quantity,
                    costPrice: costPrice,
                    unitType: unitType,
                    conversionRate: conversionRate,
                    subtotal: subtotal,
                    totalUnitAdded: totalUnitAdded

                });
            }

            if (purchaseItems.length === 0) {
                notyf.error("Item is empty. Add items before saving.");
                savePurchase.disabled = false
                return;
            }

            if (supplierId === null) {
                notyf.error('Please select a supplier')
                savePurchase.disabled = false
                return;
            }

            const result = await Swal.fire({
                title: "Are you sure you want to save?",
                icon: "success",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, save it!",
            });



            try {
                if (result.isConfirmed) {
                    const res = await axios.post("{{ route('supplier.save.purchase') }}", {
                        reference: randomId,
                        items: purchaseItems,
                        supplier: supplierId
                    });

                    const {
                        message,
                        id
                    } = res.data;

                    if (res.status === 200) {
                        // clear the cart
                        document.querySelector("#cart-table").innerHTML = "";
                        updateTotalSubtotal(); //update the transaction part
                        notyf.success(message);

                        //clear the selected customer
                        document.querySelector("#supplier-container").innerHTML =
                            "";


                    }
                }
            } catch (error) {
                const status = error?.response.status;
                const errors = error.response?.data?.errors ?? {};

                if (status === 422) {
                    // validation error
                    notyf.error(errors.supplier[0] ?? '');
                }
                if (status === 400) {
                    notyf.error(error?.response?.data.error ?? '');
                    console.log(errors)
                }
                console.log(error);
            } finally {
                savePurchase.disabled = false
            }
        }
    </script>
@endpush
