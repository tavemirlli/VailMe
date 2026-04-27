
function confirmStatusChange(orderId, newStatus) {
    if (newStatus === 'cancelled') {
        if (confirm('Вы уверены, что хотите отменить этот заказ?\nТовары будут возвращены на склад.')) {
            document.getElementById('status-form-' + orderId).submit();
        } else {
            location.reload();
        }
    } else {
        document.getElementById('status-form-' + orderId).submit();
    }
}