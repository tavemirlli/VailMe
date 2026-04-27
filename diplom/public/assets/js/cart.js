
function openCheckoutModal() {
    document.getElementById('checkoutModal').style.display = 'block';
}

function closeCheckoutModal() {
    document.getElementById('checkoutModal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('checkoutModal')) {
        closeCheckoutModal();
    }
}