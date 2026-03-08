if (pageId === "request_item") {
    const itemInput = document?.querySelector("#item-search");
    const warehouseItemInput = document?.querySelector(
        "#warehouse-item-search"
    );
    const suggestionsList = document?.querySelector("#suggestions");
    const cartTable = document?.getElementById("cart-table");
    const warehouseSelect = document?.querySelector("#warehouse_id");

    itemInput?.addEventListener("keyup", debounce(searchItem, 300));
    warehouseItemInput?.addEventListener(
        "keyup",
        debounce(searchItemInWarehouse, 300)
    );

    async function searchItemInWarehouse() {
        //validate if warehouse is selected
        if (
            warehouseSelect.value === "" ||
            warehouseSelect.value === null ||
            warehouseSelect.value === "0"
        ) {
            document.querySelector(".warehouse-error").textContent =
                "Please select a warehouse first.";
            return;
        } else {
            document.querySelector(".warehouse-error").textContent = "";
        }

        try {
            if (warehouseItemInput.value === "") {
                return;
            }
            const response = await axios.get("/item/get-warehouse-item", {
                headers: {
                    "Content-Type": "application/json",
                },
                params: {
                    item: warehouseItemInput.value,
                    warehouse_id: warehouseSelect.value,
                },
            });
            const { items } = response.data;

            displayWarehouseSuggestions(items);
        } catch (error) {
            const errors = error?.response?.data.errors ?? {};
            document.querySelector(".warehouse-error").textContent =
                errors.warehouse_id ?? "";
            console.log(error);
        }
    }

    function displayWarehouseSuggestions(items) {
        // Clear previous suggestions
        suggestionsList.innerHTML = "";

        // Show suggestions if any item matches
        if (items && items.length > 0) {
            items.forEach((item) => {
                const listItem = document.createElement("li");
                let itemInputWidth = warehouseItemInput.offsetWidth;

                const itemName = item.name;
                const price = item.price;

                listItem.textContent = `${itemName} - ${price} `;
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
                    // itemInput.value = itemName;
                    addToTable(item);
                    //  itemCode.value = item.theCode;
                    suggestionsList.innerHTML = "";
                    warehouseItemInput.value = "";
                });

                suggestionsList.appendChild(listItem);
            });
        }
    }

    async function searchItem() {
        try {
            if (itemInput.value === "") {
                return;
            }
            const response = await axios.get("/item/get-item", {
                headers: {
                    "Content-Type": "application/json",
                },
                params: {
                    item: itemInput.value,
                },
            });
            const { items } = response.data;
            displaySuggestions(items);
        } catch (error) {
            console.log(error);
        }
    }

    function displaySuggestions(items) {
        // Clear previous suggestions
        suggestionsList.innerHTML = "";

        // Show suggestions if any item matches
        if (items && items.length > 0) {
            items.forEach((item) => {
                const listItem = document.createElement("li");
                let itemInputWidth = itemInput.offsetWidth;

                const itemName = item.name;
                const price = item.price;

                listItem.textContent = `${itemName} - ${price} `;
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
                    // itemInput.value = itemName;
                    addToTable(item);
                    //  itemCode.value = item.theCode;
                    suggestionsList.innerHTML = "";
                    itemInput.value = "";
                });

                suggestionsList.appendChild(listItem);
            });
        }
    }

    function addToTable(item) {
        let found = false;
        const id = item.id.toString();
        const name = item.name;
        const price = parseFloat(item.price);

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
                    <td>${price}</td>      
                    <td>
                        <input type="text" class="form-control quantity-cart" value="${quantity}" min="1"  maxlength="3" max="100" onkeypress="return event.charCode >= 48 && event.charCode <= 57"/>
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

    let totalSubtotal = 0;

    function updateTotalSubtotal() {
        totalSubtotal = 0;
        for (const row of cartTable.rows) {
            const subtotal = parseFloat(row.children[4].textContent);
            totalSubtotal += subtotal;
        }

        totalSubtotalElement.value = totalSubtotal.toFixed(2);
        totalGrandtotalElement.value = totalSubtotal.toFixed(2);
    }

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

    const placeOrderBtn = document?.querySelector("#place-order");
    placeOrderBtn?.addEventListener("click", placeOrder);

    async function placeOrder() {
        const orderItems = []; //define empty array to hold the order items

        // generate id for order
        const randomId = Math.floor(Math.random() * 999999999);
        const timestamp = Date.now();
        const combinedValue = `${randomId}${timestamp}`;

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

        if (orderItems.length === 0) {
            notyf.error("Cart is empty. Add items before placing an order.");
            return;
        }

        const result = await Swal.fire({
            title: "Are you sure you want to make the request?",
            icon: "success",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, order it!",
        });

        try {
            if (result.isConfirmed) {
                const res = await axios.post("/store-request/store", {
                    reference: randomId,
                    items: orderItems,
                    warehouse: warehouseSelect.value,
                });

                const { message, id } = res.data;

                if (res.status === 200) {
                    // clear the cart
                    document.querySelector("#cart-table").innerHTML = "";

                    updateTotalSubtotal(); //update the transaction part
                    notyf.success(message);
                    document.querySelector(".warehouse-error").textContent = "";
                }
            }
        } catch (error) {
            console.log(error);
            const errors = error?.response?.data?.errors ?? {};
            console.log(errors);
            document.querySelector(".warehouse-error").textContent =
                errors?.warehouse[0] ?? "";
        }
    }

    const updatePlaceOrderBtn = document?.querySelector("#update-place-order");
    updatePlaceOrderBtn?.addEventListener("click", updatePlaceOrder);
    const requestId = document?.querySelector("#request-item-id");

    async function updatePlaceOrder() {
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

        if (orderItems.length === 0) {
            notyf.error("Cart is empty. Add items before placing a request.");
            return;
        }

        const result = await Swal.fire({
            title: "Are you sure you want to update the request?",
            icon: "success",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, order it!",
        });

        try {
            if (result.isConfirmed) {
                const res = await axios.post("/store-request/update", {
                    items: orderItems,
                    warehouse: warehouseSelect.value,
                    requestId: requestId.value,
                });

                const { message } = res.data;

                if (res.status === 200) {
                    // clear the cart
                    document.querySelector("#cart-table").innerHTML = "";

                    updateTotalSubtotal(); //update the transaction part
                    notyf.success(message);
                    document.querySelector(".warehouse-error").textContent = "";
                }
            }
        } catch (error) {
            console.log(error);
            const errors = error?.response?.data?.errors ?? {};
            console.log(errors);
            document.querySelector(".warehouse-error").textContent =
                errors?.warehouse[0] ?? "";
        }
    }

    document.addEventListener("input", async (event) => {
        const targetElement = event.target;
        if (targetElement.classList.contains("quantity-cart")) {
            const quantity = targetElement; //get the input element
            const id =
                targetElement.parentElement.previousElementSibling
                    .previousElementSibling.previousElementSibling.textContent;

            let value = parseInt(quantity.value); //get the value
            value = isNaN(value) || value === "" ? 1 : value; //if it is NaN assign value to 0
            quantity.value = value;
            const price = parseFloat(
                quantity.parentNode.previousElementSibling.innerText
            );
            const subtotal = value * price;
            quantity.parentNode.nextElementSibling.innerText =
                subtotal.toFixed(2);
            updateTotalSubtotal();
        }
    });

    //approve request
    document.addEventListener("click", async (e) => {
        const loader = document.querySelector("#loader");
        const targetElement = e.target.closest(".approve");
        if (targetElement) {
            const requestId = targetElement.getAttribute(
                "data-requested-value"
            );
            loader.classList.remove("invisible");
            const config = {
                headers: {
                    Accept: "application/json",
                },
                params: {
                    id: requestId,
                },
            };
            const result = await Swal.fire({
                title: "Are you sure you want to approve this?",
                icon: "success",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, approve it!",
            });

            try {
                if (result.isConfirmed) {
                    const res = await axios.get("/transfer/approval", config);

                    const { message } = res.data;
                    if (res.status === 200) {
                        notyf.success(message);
                        //update ui
                        document.querySelector(".request-status").textContent =
                            "approved";
                        document.getElementById("button-container").remove();
                    }
                }
            } catch (error) {
                // console.log(error);
                const errors = error?.response?.data ?? {};
                console.log(errors);
                notyf.error(errors?.message);
                console.log(errors);
            } finally {
                loader.classList.add("invisible");
            }
        }
    });

    //cancel
    document.addEventListener("click", async (e) => {
        const loader = document.querySelector("#loader");
        const targetElement = e.target.closest(".cancel");
        if (targetElement) {
            const requestId = targetElement.getAttribute(
                "data-requested-value"
            );
            loader.classList.remove("invisible");
            const config = {
                headers: {
                    Accept: "application/json",
                },
                params: {
                    id: requestId,
                },
            };
            const result = await Swal.fire({
                title: "Are you sure you want to cancel this?",
                icon: "success",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, cancel it!",
            });

            try {
                if (result.isConfirmed) {
                    const res = await axios.get("/transfer/cancel", config);

                    const { message } = res.data;
                    if (res.status === 200) {
                        notyf.success(message);
                        //update ui
                        document.querySelector(".request-status").textContent =
                            "cancelled";
                        document.getElementById("button-container").remove();
                    }
                }
            } catch (error) {
                // console.log(error);
                const errors = error?.response?.data ?? {};
                console.log(errors);
                notyf.error(errors?.message);
                console.log(errors);
            } finally {
                loader.classList.add("invisible");
            }
        }
    });

    //dispatch good
    document.addEventListener("click", async (e) => {
        const loader = document.querySelector("#loader");
        const targetElement = e.target.closest(".dispatch");
        if (targetElement) {
            const targetId = targetElement.getAttribute("data-requested-value");

            const orderItems = []; //define empty array to hold the order items
            const cartTable = document.querySelector("#cart-table");
            loader.classList.remove("invisible");

            for (const row of cartTable.rows) {
                const id = row.children[0].textContent;
                const quantity = row.children[2].children[0].value;

                // Add the item to the orderItems array
                orderItems.push({
                    id: id,
                    quantity: quantity,
                });
            }

            const result = await Swal.fire({
                title: "Are you sure you want to dispatch this?",
                icon: "success",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, dispatch it!",
            });

            try {
                if (result.isConfirmed) {
                    const res = await axios.post("/transfer/dispatch", {
                        items: orderItems,
                        transferOrderId: targetId,
                    });

                    const { message } = res.data;
                    if (res.status === 200) {
                        notyf.success(message);
                        //update ui
                        document.querySelector(".request-status").textContent =
                            "dispatched";
                        document.getElementById("button-container").remove();
                    }
                }
            } catch (error) {
                // console.log(error);
                const errors = error?.response?.data ?? {};
                console.log(errors);
                notyf.error(errors?.message);
                console.log(errors);
            } finally {
                loader.classList.add("invisible");
            }
        }
    });

    //delivered
    document.addEventListener("click", async (e) => {
        const loader = document.querySelector("#loader");
        const targetElement = e.target.closest(".delivered");
        if (targetElement) {
            const targetId = targetElement.getAttribute("data-requested-value");

            const result = await Swal.fire({
                title: "Are you sure you want to proceed?",
                icon: "success",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, proceed!",
            });

            try {
                if (result.isConfirmed) {
                    const res = await axios.post("/transfer/delivered", {
                        id: targetId,
                    });

                    const { message } = res.data;
                    if (res.status === 200) {
                        notyf.success(message);
                        //update ui
                        document.querySelector(".request-status").textContent =
                            "delivered";
                        document.getElementById("button-container").remove();
                    }
                }
            } catch (error) {
                // console.log(error);
                const errors = error?.response?.data ?? {};
                console.log(errors);
                notyf.error(errors?.message);
                console.log(errors);
            } finally {
                loader.classList.add("invisible");
            }
        }
    });
}
