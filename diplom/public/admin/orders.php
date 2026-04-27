<?php
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once '../classes/OrderService.php';

$pageTitle = 'Управление заказами - Админка';

$success = '';

if (isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = $_POST['status'];

    if ($status == 'cancelled') {
        $order = OrderService::getOrderById($orderId);
        if ($order) {
            OrderService::updateOrderStatus($orderId, 'cancelled');
            $success = "Заказ №{$order['order_number']} отменен. Товары возвращены на склад.";
        }
    } else {
        OrderService::updateOrderStatus($orderId, $status);
        $success = "Статус заказа обновлен";
    }
}


$orders = OrderService::getAllOrders();

include '../templates/admin-header.php';
?>
<link rel="stylesheet" href="assets/css/order.css">
<script src="assets/js/orders.js"></script>

<h1>Управление заказами</h1>

<?php if ($success): ?>
    <div class="success-message"><?php echo $success; ?></div>
<?php endif; ?>

<table class="orders-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>№ заказа</th>
            <th>Клиент</th>
            <th>Телефон</th>
            <th>Email</th>
            <th>Сумма</th>
            <th>Кол-во товаров</th>
            <th>Статус</th>
            <th>Счет</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?php echo $order['id']; ?></td>
            <td><?php echo $order['order_number']; ?></td>
            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
            <td><?php echo $order['customer_phone']; ?></td>
            <td><?php echo $order['customer_email']; ?></td>
            <td><?php echo number_format($order['total_amount'], 0, '.', ' '); ?> ₽</td>
            <td><?php echo $order['total_items']; ?> шт.</td>
            <td>
                <form method="POST" id="status-form-<?php echo $order['id']; ?>">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <select name="status" onchange="confirmStatusChange(<?php echo $order['id']; ?>, this.value)">
                        <option value="new" <?php echo $order['order_status'] == 'new' ? 'selected' : ''; ?>>Новый</option>
                        <option value="processing" <?php echo $order['order_status'] == 'processing' ? 'selected' : ''; ?>>В обработке</option>
                        <option value="shipped" <?php echo $order['order_status'] == 'shipped' ? 'selected' : ''; ?>>Отправлен</option>
                        <option value="delivered" <?php echo $order['order_status'] == 'delivered' ? 'selected' : ''; ?>>Доставлен</option>
                        <option value="cancelled" <?php echo $order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Отменён</option>
                    </select>
                    <input type="hidden" name="update_status" value="1">
                </form>
            </td>
            <td>
                <?php if ($order['invoice_sent']): ?>
                    ✅ Отправлен<br>
                    <small><?php echo date('d.m.Y', strtotime($order['invoice_sent_at'])); ?></small>
                <?php else: ?>
                    ❌ Не отправлен
                <?php endif; ?>

            </td>
            <td>
                <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn-view">Детали</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../templates/admin-footer.php'; ?>