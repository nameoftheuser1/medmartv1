$(document).ready(function () {
    const productCards = $(".product-card");
    const addToSaleForm = $("#add-to-sale-form");
    const selectedProductIdInput = $("#selected-product-id");
    const searchInput = $("#search-input");
    const exchangeInput = $("#exchange-input");
    const exchangeHiddenInput = $("#exchange-hidden-input");
    const changeAmountElement = $("#change-amount");
    const finalPriceElement = $("#final-price");
    const amountCards = $(".amount-card");
    const discountCards = $(".discount-card"); // New line for discount selection cards
    const discountInput = $("#discount_percentage"); // Discount input element
    const resetExchangeButton = $("#reset-exchange-button");

    // Search functionality for products
    $("#search-input").on("input", function () {
        const searchTerm = $(this).val();

        $.ajax({
            url: "/pos",
            method: "GET",
            data: {
                search: searchTerm,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                // Update product list and pagination
                $("#product-list").html(response);
            },
            error: function (xhr, status, error) {
                console.error("Search error:", error);
            },
        });
    });

    // Product selection functionality
    $(document).ready(function () {
        const productCards = $(".product-card");
        const addToSaleForm = $("#add-to-sale-form");
        const selectedProductIdInput = $("#selected-product-id");
        const searchInput = $("#search-input");

        // Modal elements
        const quantityModal = $("#quantity-modal");
        const cancelModalButton = $("#cancel-modal");
        const quantityInput = $("#quantity");
        const modalProductName = $("#modal-product-name");

        // Search functionality for products
        searchInput.on("input", function () {
            const query = $(this).val().toLowerCase();
            productCards.each(function () {
                const productName = $(this).data("name").toLowerCase();
                $(this).toggleClass("hidden", !productName.includes(query));
            });
        });

        // Product selection functionality
        productCards.on("click", function () {
            const productId = $(this).data("id");
            const productName = $(this).data("name");
            const productQuantity = $(this).data("quantity");

            // Highlight selected product
            productCards.removeClass("ring ring-green-500 bg-green-50");
            $(this).addClass("ring ring-green-500 bg-green-50");

            // Set values in the form and modal
            selectedProductIdInput.val(productId);
            modalProductName.text(`Select Quantity for ${productName}`);
            quantityInput.val(1); // Default quantity is 1
            quantityInput.attr("max", productQuantity); // Limit max quantity to available stock

            // Show the modal
            quantityModal.removeClass("hidden");

            // Show the "Add to Cart" form
            addToSaleForm.removeClass("hidden");
        });

        // Close modal functionality
        cancelModalButton.on("click", function () {
            quantityModal.addClass("hidden");
        });

        // Handling form submission
        $("#quantity-form").on("submit", function (e) {
            e.preventDefault();

            // Optionally, hide the modal and reset the form
            quantityModal.addClass("hidden");
            quantityInput.val(1); // Reset the quantity input field
        });
    });

    // Exchange input and change calculation
    exchangeInput.on("input", function () {
        const finalPrice = parseFloat(
            finalPriceElement.text().replace(/[^0-9.-]+/g, "")
        );
        const exchangeAmount = parseFloat($(this).val()) || 0;
        const changeAmount = exchangeAmount - finalPrice;
        changeAmountElement.text(
            changeAmount >= 0 ? changeAmount.toFixed(2) : "0.00"
        );
        exchangeHiddenInput.val($(this).val());
    });

    // Amount selection cards functionality
    amountCards.on("click", function () {
        const amount = parseFloat($(this).data("amount"));

        // Add the selected amount to the current value in the exchange input
        exchangeInput.val((parseFloat(exchangeInput.val()) || 0) + amount);
        exchangeHiddenInput.val(exchangeInput.val());

        // Update the change calculation
        const finalPrice = parseFloat(
            finalPriceElement.text().replace(/[^0-9.-]+/g, "")
        );
        const exchangeAmount = parseFloat(exchangeInput.val());
        const changeAmount = exchangeAmount - finalPrice;
        changeAmountElement.text(
            changeAmount >= 0 ? changeAmount.toFixed(2) : "0.00"
        );
    });

    // Discount selection cards functionality
    discountCards.on("click", function () {
        const discount = parseFloat($(this).data("discount"));

        // Update the discount input with the selected value
        discountInput.val(discount);

        // Update the final price based on the selected discount
        const totalPrice = parseFloat(
            finalPriceElement.text().replace(/[^0-9.-]+/g, "")
        );
        finalPriceElement.text((totalPrice * (1 - discount / 100)).toFixed(2));
    });

    // Reset button functionality
    resetExchangeButton.on("click", function () {
        // Set the exchange input to 0
        exchangeInput.val(0);
        exchangeHiddenInput.val(0);

        // Recalculate the change amount
        const finalPrice = parseFloat(
            finalPriceElement.text().replace(/[^0-9.-]+/g, "")
        );
        const changeAmount = 0 - finalPrice;
        changeAmountElement.text(
            changeAmount >= 0 ? changeAmount.toFixed(2) : "0.00"
        );
    });
});
