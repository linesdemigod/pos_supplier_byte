// let pageId = document.body.id.toLowerCase();

if (pageId === "inventory_create") {
    const itemInput = document.querySelector("#item-search");
    const suggestionsList = document.querySelector("#suggestions");
    const itemIdInput = document.querySelector("#item-value");
    const itemNameInput = document?.querySelector("#item-name");

    itemInput.addEventListener("keyup", debounce(searchItem, 300));

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
        const currentPrice = document?.querySelector("#current-price");

        // Show suggestions if any item matches
        if (items && items.length > 0) {
            items.forEach((item) => {
                const listItem = document.createElement("li");
                let itemInputWidth = itemInput.offsetWidth;

                const itemName = item.name;
                const price = item.price;
                const id = item.id;

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
                    if (currentPrice !== null) {
                        currentPrice.value = price;
                    }
                    itemIdInput.value = id;
                    itemNameInput.value = itemName;
                    //  itemCode.value = item.theCode;
                    suggestionsList.innerHTML = "";
                    itemInput.value = "";
                });

                suggestionsList.appendChild(listItem);
            });
        }
    }
}
