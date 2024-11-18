document.addEventListener("DOMContentLoaded", function () {
    const productCards = document.querySelectorAll(".product-card");
    const addToSaleForm = document.getElementById("add-to-sale-form");
    const selectedProductIdInput = document.getElementById(
        "selected-product-id"
    );
    const searchInput = document.getElementById("search-input");
    const exchangeInput = document.getElementById("exchange-input");
    const exchangeHiddenInput = document.getElementById(
        "exchange-hidden-input"
    );
    const changeAmountElement = document.getElementById("change-amount");
    const finalPriceElement = document.getElementById("final-price");
    const amountCards = document.querySelectorAll(".amount-card");
    const discountCards = document.querySelectorAll(".discount-card"); // New line for discount selection cards
    const discountInput = document.getElementById("discount_percentage"); // Discount input element
    const resetExchangeButton = document.getElementById(
        "reset-exchange-button"
    );

    // Search functionality for products
    searchInput.addEventListener("input", function () {
        const query = this.value.toLowerCase();
        productCards.forEach((card) => {
            const productName = card.getAttribute("data-name").toLowerCase();
            card.classList.toggle("hidden", !productName.includes(query));
        });
    });

    // Product selection functionality
    productCards.forEach((card) => {
        card.addEventListener("click", function () {
            productCards.forEach((card) =>
                card.classList.remove("ring", "ring-green-500", "bg-green-50")
            );
            this.classList.add("ring", "ring-green-500", "bg-green-50");

            selectedProductIdInput.value = this.getAttribute("data-id");
            addToSaleForm.classList.remove("hidden");
        });
    });

    // Exchange input and change calculation
    exchangeInput.addEventListener("input", function () {
        const finalPrice = parseFloat(
            finalPriceElement.textContent.replace(/[^0-9.-]+/g, "")
        );
        const exchangeAmount = parseFloat(this.value) || 0;
        const changeAmount = exchangeAmount - finalPrice;
        changeAmountElement.textContent =
            changeAmount >= 0 ? changeAmount.toFixed(2) : "0.00";
        exchangeHiddenInput.value = this.value;
    });

    // Amount selection cards functionality
    amountCards.forEach((card) => {
        card.addEventListener("click", function () {
            const amount = parseFloat(this.getAttribute("data-amount"));

            // Add the selected amount to the current value in the exchange input
            exchangeInput.value = parseFloat(exchangeInput.value || 0) + amount;
            exchangeHiddenInput.value = exchangeInput.value;

            // Update the change calculation
            const finalPrice = parseFloat(
                finalPriceElement.textContent.replace(/[^0-9.-]+/g, "")
            );
            const exchangeAmount = parseFloat(exchangeInput.value);
            const changeAmount = exchangeAmount - finalPrice;
            changeAmountElement.textContent =
                changeAmount >= 0 ? changeAmount.toFixed(2) : "0.00";
        });
    });

    // Discount selection cards functionality
    discountCards.forEach((card) => {
        card.addEventListener("click", function () {
            const discount = parseFloat(this.getAttribute("data-discount"));

            // Update the discount input with the selected value
            discountInput.value = discount;

            // Update the final price based on the selected discount
            const totalPrice = parseFloat(
                finalPriceElement.textContent.replace(/[^0-9.-]+/g, "")
            );
            finalPriceElement.textContent = (
                totalPrice *
                (1 - discount / 100)
            ).toFixed(2);
        });
    });

    // Reset button functionality
    resetExchangeButton.addEventListener("click", function () {
        // Set the exchange input to 0
        exchangeInput.value = 0;
        exchangeHiddenInput.value = 0;

        // Recalculate the change amount
        const finalPrice = parseFloat(
            finalPriceElement.textContent.replace(/[^0-9.-]+/g, "")
        );
        const changeAmount = 0 - finalPrice;
        changeAmountElement.textContent =
            changeAmount >= 0 ? changeAmount.toFixed(2) : "0.00";
    });
});
