document.addEventListener('DOMContentLoaded', function() {
    var quantityButtons = document.querySelectorAll('.quantity-button');

    for (var i = 0; i < quantityButtons.length; i++) {
        quantityButtons[i].addEventListener('click', function() {
            var itemId = this.getAttribute('data-item-id');
            var action = this.getAttribute('data-action');
            var quantityDisplay = document.querySelector('[data-item-id="' + itemId + '"] + .quantity-display');
            var hiddenInput = document.querySelector('[name="selected_items[' + itemId + ']"]');
            var quantity = parseInt(quantityDisplay.textContent);

            if (action === 'increase') {
                quantity++;
            } else if (action === 'decrease') {
                quantity = Math.max(0, quantity - 1);
            }

            quantityDisplay.textContent = quantity;
            hiddenInput.value = quantity; // Обновляем значение скрытого поля
        });
    }
});