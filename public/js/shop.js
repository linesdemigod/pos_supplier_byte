//get the items

let pageId = document.body.id.toLowerCase();
const notyf = new Notyf({
    duration: 4000,
    position: {
        x: "right",
        y: "top",
    },
});
const userId = document
    .querySelector("meta[name='user']")
    .getAttribute("data-user");

function debounce(func, delay = 300) {
    let debounceTimer;
    return function () {
        const context = this;
        const args = arguments;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => func.apply(context, args), delay);
    };
}

function timeAgo(datetime) {
    const now = new Date();
    const diff = Math.round((now - datetime) / 60000);
    if (diff < 1) {
        return "just now";
    } else if (diff === 1) {
        return "1 minute ago";
    } else if (diff < 60) {
        return diff + " minutes ago";
    } else if (diff < 120) {
        return "1 hour ago";
    } else if (diff < 1440) {
        return Math.floor(diff / 60) + " hours ago";
    } else if (diff < 2880) {
        return "1 day ago";
    } else {
        return Math.floor(diff / 1440) + " days ago";
    }
}

//clear suggestions
document.addEventListener("click", function (event) {
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

if (pageId === "shop") {
    let creditCustomer = false;
    const itemInput = document?.querySelector("#item-search");
    const suggestionsList = document.querySelector("#suggestions");
    const customerSuggestionsList = document.querySelector(
        "#customer-suggestions"
    );
    const cartTable = document.getElementById("cart-table");
    const categoryInput = document.querySelector("#category-search");
    const creditCustomerCheckbox = document.getElementById("credit-customer");
    // const customerSelect = document.querySelector("#customer_id");

    // Get the modal and button
    var modal = document?.getElementById("myModal");
    // var openModalBtn = document.getElementById('openModalBtn');
    var closeModalBtn = document?.getElementById("closeModal");

    // Close the modal
    if (closeModalBtn != null) {
        closeModalBtn.onclick = function () {
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

    itemInput?.addEventListener("keyup", debounce(searchItem, 300));

    async function searchItem() {
        try {
            if (itemInput.value === "") {
                return;
            }
            const response = await axios.get("/get-item", {
                headers: {
                    "Content-Type": "application/json",
                },
                params: {
                    item: itemInput.value,
                    category: categoryInput.value,
                },
            });

            const { items, allowedNegative, priceEdit } = response.data;

            displaySuggestions(items, allowedNegative, priceEdit);
        } catch (error) {
            console.log(error);
        }
    }

    //search customer
    const customerInput = document.querySelector("#customer-search");
    customerInput?.addEventListener("keyup", debounce(searchCustomer, 300));

    async function searchCustomer() {
        try {
            const response = await axios.get("/customer/get-customer", {
                headers: {
                    "Content-Type": "application/json",
                },
                params: {
                    name: customerInput.value,
                },
            });

            const { customers } = response.data;
            displayCustomerSuggestion(customers);
        } catch (error) {
            console.log(error);
        }
    }

    function displaySuggestions(items, allowedNegative, priceEdit) {
        // Clear previous suggestions
        suggestionsList.innerHTML = "";

        // Show suggestions if any item matches
        if (items && items.length > 0) {
            items.forEach((item) => {
                const listItem = document.createElement("li");
                let itemInputWidth = itemInput.offsetWidth;

                const itemName = item.name;
                const price = item.price;
                const quantity = item.store_inventories.quantity;

                listItem.textContent = `${itemName} - ${price} - (${quantity} qty) `;
                listItem.style.padding = "8px";
                listItem.style.cursor = "pointer";
                listItem.style.width = `${itemInputWidth}px`;
                listItem.classList.add(
                    "list-dropdown",
                    "border-2",
                    "border-bottom"
                );

                // Add click event listener to select the item
                listItem.addEventListener("click", () => {
                    // check if qantity is out of stock
                    if (quantity < 1 && allowedNegative === 0) {
                        notyf.error(
                            `Sorry, Item is out of stock. only ${quantity} left`
                        );

                        return;
                    }

                    // itemInput.value = itemName;
                    addToTable(item, priceEdit);
                    //  itemCode.value = item.theCode;
                    suggestionsList.innerHTML = "";
                    itemInput.value = "";
                });

                suggestionsList.appendChild(listItem);
            });
        }
    }

    function displayCustomerSuggestion(customers) {
        // Clear previous suggestions
        customerSuggestionsList.innerHTML = "";

        // Show suggestions if any item matches
        if (customers && customers.length > 0) {
            customers.forEach((customer) => {
                const listItem = document.createElement("li");
                let itemInputWidth = customerInput.offsetWidth;

                const name = customer.name;
                const telephone = customer.phone;

                listItem.textContent = `${name} - ${telephone} `;
                listItem.style.padding = "8px";
                listItem.style.cursor = "pointer";
                listItem.style.width = `${itemInputWidth}px`;
                listItem.classList.add("list-dropdown");

                // Add click event listener to select the item
                listItem.addEventListener("click", () => {
                    // customerInput.value = name;
                    selectCustomer(customer);
                    //  itemCode.value = item.theCode;
                    customerSuggestionsList.innerHTML = "";
                    customerInput.value = "";
                });

                customerSuggestionsList.appendChild(listItem);
            });
        }
    }

    function selectCustomer(customer) {
        const customerContainer = document.querySelector("#customer-container");

        const customerTemplate = `
            <p>${customer.name}</p>
            <p class="">- ${customer.phone}</p>
            <p class="text-danger remove-customer fw-bold" data-customer-id="${customer.id}" role="button">X</p>
        `;

        customerContainer.innerHTML = customerTemplate;
    }

    //check if it is null
    if (creditCustomerCheckbox != null) {
        creditCustomerCheckbox.addEventListener("change", () => {
            if (creditCustomerCheckbox.checked) {
                creditCustomer = true;
            } else {
                creditCustomer = false;
            }
        });
    }

    document.addEventListener("click", (e) => {
        const targetElement = e.target;
        if (targetElement.classList.contains("remove-customer")) {
            const customerContainer = document.querySelector(
                "#customer-container"
            );
            //remove customerContainer children
            customerContainer.innerHTML = "";
        }
    });

    function addToTable(item, priceEdit) {
        let found = false;
        const id = item.id.toString();
        const name = item.name;
        const price = parseFloat(item.price);

        const priceElement =
            priceEdit !== 0
                ? `<input type="text" class="form-control price-cart price-input-shop" value="${price}" min="1"   />`
                : price;

        for (const row of cartTable.rows) {
            if (row.children[0].textContent === id) {
                found = true;

                const quantity =
                    parseInt(row.children[3].children[0].value) + 1;

                const subtotal = quantity * price;
                row.children[3].children[0].value = quantity;
                row.children[4].textContent = subtotal;
            }
        }

        if (!found) {
            const quantity = 1;
            const subtotal = price;
            const newRow = ` <tr style="cursor:pointer">
                    <td class="d-none">${id}</td>
                    <td>${name}</td>
                    <td>
                         ${priceElement}
                    </td>      
                    <td>
                        <input type="text" class="form-control quantity-cart quantity-input-shop" value="${quantity}" min="1"  maxlength="3" max="100" onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
                    </td>
                    <td>${subtotal}</td>
                    <td><span class="text-danger fw-bold fs-4"><i class="fas fa-trash delete-btn" data-id="${id}"></i></span></td>
                </tr>
                    `;
            cartTable.insertAdjacentHTML("beforeend", newRow);
        }
        updateTotalSubtotal();
    }

    const totalSubtotalElement = document.getElementById("total-subtotal");
    const totalGrandtotalElement = document.getElementById("total-grandtotal");
    const discount = document.getElementById("discount");

    let totalSubtotal = 0;

    function updateTotalSubtotal() {
        totalSubtotal = 0;
        for (const row of cartTable.rows) {
            const subtotal = parseFloat(row.children[4].textContent);
            totalSubtotal += subtotal;
        }
        // get the value of the discount and substract from grandtotal
        const grandtotal = totalSubtotal - discount.value;

        totalSubtotalElement.value = totalSubtotal.toFixed(2);
        totalGrandtotalElement.value = grandtotal.toFixed(2);
    }

    //calc discount

    discount.addEventListener("input", () => {
        const result = totalSubtotalElement.value - discount.value;
        totalGrandtotalElement.value = result.toFixed(2);
    });

    //increase quantity by the input
    document.addEventListener(
        "input",
        debounce(async (event) => {
            const targetElement = event.target;
            if (targetElement.classList.contains("quantity-cart")) {
                const quantity = targetElement; //get the input element
                const id =
                    targetElement.parentElement.previousElementSibling
                        .previousElementSibling.previousElementSibling
                        .textContent;

                // should i use debounce here or not
                const { stockLeft, allowedNegative } = await quantityLeft(id);

                //check quantity left
                if (stockLeft <= quantity.value && allowedNegative === 0) {
                    notyf.error(
                        `Sorry, we are out of stock. only ${stockLeft} left`
                    );
                    quantity.value = stockLeft;
                }

                let value = parseInt(quantity.value); //get the value
                value = isNaN(value) || value === "" ? 1 : value; //if it is NaN assign value to 0
                quantity.value = value;

                let priceElement = quantity.parentNode.previousElementSibling;

                if (priceElement.firstElementChild) {
                    priceElement = priceElement.firstElementChild.value;
                } else {
                    priceElement = priceElement.textContent;
                }

                const price = parseFloat(priceElement);

                const subtotal = value * price;
                quantity.parentNode.nextElementSibling.innerText =
                    subtotal.toFixed(2);
                updateTotalSubtotal();
            }
        }),
        300
    );

    //increase price by the input
    document.addEventListener("input", async (event) => {
        const targetElement = event.target;
        if (targetElement.classList.contains("price-cart")) {
            const priceInput = targetElement; //get the input element
            const quantity =
                targetElement.parentElement.nextElementSibling.lastElementChild;
            const totalInput =
                targetElement.parentElement.nextElementSibling
                    .nextElementSibling;

            let priceValue = parseInt(priceInput.value); //get the value
            priceValue =
                isNaN(priceValue) || priceValue === "" || priceValue === 0
                    ? 0
                    : priceValue; //if it is NaN assign value to 0

            priceInput.value = priceValue;
            const qty = quantity.value;

            const subtotal = priceValue * qty;
            totalInput.textContent = subtotal.toFixed(2);
            updateTotalSubtotal();
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
    document?.querySelector("#clear-cart")?.addEventListener("click", () => {
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

    //place order
    const placeOrderBtn = document.querySelector("#place-order");
    placeOrderBtn?.addEventListener("click", placeOrder);

    async function placeOrder() {
        const orderItems = []; //define empty array to hold the order items

        // generate id for order
        const randomId = Math.floor(Math.random() * 999999999);
        const timestamp = Date.now();
        const combinedValue = `${randomId}${timestamp}`;
        //get the discount value
        const discount = document.querySelector("#discount");

        const cartCustomer = document.querySelector(".remove-customer");
        const customerId =
            cartCustomer?.getAttribute("data-customer-id") || null;

        // get the id and the quantity from the cart table
        for (const row of cartTable.rows) {
            const id = row.children[0].textContent.trim();
            const quantity = row.children[3].children[0].value.trim();
            const priceElement = row.children[2];
            const price = priceElement.children[0]
                ? priceElement.children[0].value?.trim()
                : priceElement.textContent?.trim();

            // Add the item to the orderItems array
            orderItems.push({
                id: id,
                quantity: quantity,
                price: price,
            });
        }

        if (orderItems.length === 0) {
            notyf.error("Cart is empty. Add items before placing an order.");
            return;
        }

        const result = await Swal.fire({
            title: "Are you sure you want to place order?",
            icon: "success",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, order it!",
        });

        try {
            if (result.isConfirmed) {
                const res = await axios.post("/place-order", {
                    reference: randomId,
                    discount: discount.value,
                    customer: customerId,
                    items: orderItems,
                    credit: creditCustomer,
                });

                const { message, id } = res.data;

                if (res.status === 200) {
                    // clear the cart
                    document.querySelector("#cart-table").innerHTML = "";

                    discount.value = 0;
                    updateTotalSubtotal(); //update the transaction part
                    notyf.success(message);

                    //clear the selected customer
                    document.querySelector("#customer-container").innerHTML =
                        "";

                    creditCustomer == true
                        ? (url = `/print-credit-receipt/${id}`)
                        : (url = `/print-receipt/${id}`);

                    // url = `/print-receipt/${id}`;

                    //print receipt
                    window.open(url, "_blank");

                    // getProducts();
                }
            }
        } catch (error) {
            const status = error?.response.status;
            const errors = error.response?.data?.errors ?? {};

            if (status === 422) {
                // validation error
                notyf.error(errors.customer[0] ?? "");
            }
            if (status === 400) {
                notyf.error(error?.response?.data.error ?? "");
                console.log(errors);
            }
            console.log(error);
        }
    }

    async function quantityLeft(id) {
        try {
            const res = await axios.get("/quantity-left", {
                headers: {
                    "Content-Type": "application/json",
                },
                params: {
                    id: id,
                },
            });
            const { quantity, allowed_negative } = res.data;

            return {
                stockLeft: quantity,
                allowedNegative: allowed_negative,
            };
        } catch (error) {
            console.log(error);
        }
    }

    async function fetchPriceEditStatus() {
        try {
            const res = await axios.get("/price-edit", {
                headers: {
                    "Content-Type": "application/json",
                },
            });
            const { priceEditStatus } = res.data;

            console.log(priceEditStatus);

            return {
                priceEditStatus: priceEditStatus,
            };
        } catch (error) {
            console.log(error);
        }
    }

    //create customer
    const setCustomer = document.querySelector("#customer-form");

    setCustomer?.addEventListener("submit", async (e) => {
        e.preventDefault();

        const data = new FormData(setCustomer);

        try {
            const response = await axios.post("/customer/store", data, {
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            const { message } = response.data;
            if (response.status == 200) {
                notyf.success(message);
                setCustomer.reset(); //clear the forms
                document.querySelector(".name-error").innerText = "";
                document.querySelector(".phone-error").innerText = "";
                document.querySelector(".address-error").innerText = "";
            }
        } catch (error) {
            const errors = error.response?.data?.errors ?? {};
            // console.log(errors);
            document.querySelector(".name-error").innerText = errors.name ?? "";
            document.querySelector(".phone-error").innerText =
                errors.phone ?? "";
            document.querySelector(".address-error").innerText =
                errors.location ?? "";
        }
    });

    //hold items
    const holdOrder = document.querySelector("#hold-order");

    holdOrder?.addEventListener("click", async function (e) {
        const orderItems = []; //define empty array to hold the order items

        // get the id and the quantity from the cart table
        for (const row of cartTable.rows) {
            const id = row.children[0].textContent;
            const quantity = row.children[3].children[0].value;

            // Add the item to the orderItems array
            orderItems.push({
                id: id,
                quantity: quantity,
            });
        }
        const result = await Swal.fire({
            title: "Are you sure you want to hold order?",
            icon: "success",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, hold it!",
        });

        try {
            if (result.isConfirmed) {
                const res = await axios.post("/hold-item", {
                    items: orderItems,
                });

                const { message } = res.data;

                if (res.status === 200) {
                    // clear the cart
                    document.querySelector("#cart-table").innerHTML = "";
                    discount.value = 0;
                    updateTotalSubtotal(); //update the transaction part
                    notyf.success(message);
                }
            }
        } catch (error) {
            console.log(error);
        }
    });

    //move hold item to cart
    document.addEventListener("click", async (event) => {
        const targetElement = event.target.closest(".add-to-cart-btn");
        if (targetElement) {
            const id = targetElement.getAttribute("data-id");
            const parentTr = targetElement.parentElement.parentElement;

            // get the tr
            const config = {
                headers: {
                    Accept: "application/json",
                },
                params: {
                    id: id,
                },
            };
            const result = await Swal.fire({
                title: "Are you sure you want to move it to the cart?",
                icon: "success",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, move it!",
            });

            try {
                if (result.isConfirmed) {
                    const res = await axios.get("/release-item", config);
                    const { items } = res.data;

                    const tableBody = document.getElementById("cart-table");
                    let option = "";

                    for (const holdItem of items) {
                        option += `
                                        <tr role="button">
                                    <td class="d-none">${holdItem.item_id}</td>
                                    <td>${holdItem.item.name}</td>
                                    <td>${holdItem.rate}</td>
                                    <td>
                        <input type="text" class="form-control quantity-cart" value="${holdItem.quantity}" min="1"  maxlength="3" max="100" onkeypress="return event.charCode >= 48 && event.charCode <= 57"/>
                    </td>
                    <td>${holdItem.subtotal}</td>
                    <td><span class="text-danger fw-bold fs-4"><i class="fas fa-trash delete-btn" data-id="${holdItem.item_id}"></i></span></td>   
                                   
                                </tr>
                    `;
                    }

                    tableBody.innerHTML = option;
                    updateTotalSubtotal();
                    parentTr.remove();
                }
            } catch (error) {
                console.log(error);
            }
        }
    });

    const getHoldItems = document.querySelector("#show-hold-item");
    getHoldItems?.addEventListener("click", async function () {
        const config = {
            headers: {
                Accept: "application/json",
            },
        };

        try {
            const res = await axios.get("/fetch-hold-items", config);
            const { items } = res.data;

            const tableBody = document.getElementById("hold-table");
            tableBody.innerHTML = "";
            let option = "";

            for (const item of items) {
                const datetime = new Date(item.created_at);
                const timeAgoString = timeAgo(datetime);
                option += `
                            <tr role="button" >
                            <td >${item.id}</td>
                            <td>${item.total}</td>
                            <td>${timeAgoString}</td>
                            <td><button class="btn btn-primary add-to-cart-btn"  data-id="${item.id}"><i class="fas fa-check add-to-cart"></i></button></td>
                            </tr>
                        `;
            }

            tableBody.innerHTML = option;
        } catch (err) {
            console.log(err);
        }
    });

    //open sales or close sales
    async function openOrCloseSale() {
        try {
            const response = await axios.get("/daily/set-daily", {
                headers: {
                    "COntent-Type": "application/json",
                },
            });

            const data = response.data;
        } catch (error) {
            console.log(error);
        }
    }

    // openOrCloseSale();
}
