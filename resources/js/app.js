import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

document.addEventListener("DOMContentLoaded", function () {
    const addToCartButtons = document.querySelectorAll(".btn-primary");
    const cartBadge = document.getElementById("cart-badge");

    addToCartButtons.forEach((button) => {
        button.addEventListener("click", () => {
            let itemCount = parseInt(cartBadge.innerText);
            cartBadge.innerText = itemCount + 1;
        });
    });
});
