<?php
session_start();
require_once '../config/db.php';
require_once '../classes/Order.php';
require_once '../classes/User.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../index.php');
    exit;
}

$pageTitle = 'Управление заказами - Админка';

if (isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = $_POST['status'];
    
    // Если статус меняется на "отменён", показываем подтверждение
    if ($status == 'cancelled') {
        $order = Order::getById($orderId);
        echo "<script>
            if (confirm('Вы уверены, что хотите отменить заказ №{$order['order_number']}?\\nТовары будут возвращены на склад.')) {
                window.location.href = 'orders.php?confirm_cancel={$orderId}';
            } else {
                window.location.href = 'orders.php';
            }
        </script>";
        exit;
    }
    
    Order::updateStatus($orderId, $status);
}

// Обработка подтверждения отмены
if (isset($_GET['confirm_cancel'])) {
    $orderId = (int)$_GET['confirm_cancel'];
    Order::updateStatus($orderId, 'cancelled');
    header('Location: orders.php');
    exit;
}

if (isset($_POST['resend_invoice'])) {
    $orderId = (int)$_POST['order_id'];
    $order = Order::getById($orderId);
    if ($order) {
        Order::sendInvoiceEmail($order);
        Order::markInvoiceSent($orderId);
        $success = "Счет повторно отправлен на email {$order['customer_email']}";
    }
}

$orders = Order::getAllOrders();

include '../templates/admin-header.php';
?>
<link rel="stylesheet" href="assets/css/admin-orders.css">
<h1>Управление заказами</h1>

<?php if (isset($success)): ?>
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
        <?php foreach ($orders as $order): 
            $itemsCountSql = "SELECT SUM(quantity) as total FROM order_items WHERE order_id = {$order['id']}";
            $itemsCountResult = mysqli_query($connect, $itemsCountSql);
            $itemsCount = mysqli_fetch_assoc($itemsCountResult);
            $totalItems = $itemsCount['total'] ?? 0;
        ?>
        <tr>
            <td><?php echo $order['id']; ?></td>
            <td><?php echo $order['order_number']; ?></td>
            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
            <td><?php echo $order['customer_phone']; ?></td>
            <td><?php echo $order['customer_email']; ?></td>
            <td><?php echo number_format($order['total_amount'], 0, '.', ' '); ?> ₽</td>
            <td><?php echo $totalItems; ?> шт.</td>
            <td>
                <form method="POST" style="display: inline;" id="status-form-<?php echo $order['id']; ?>">
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
                <form method="POST" style="margin-top: 5px;">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <button type="submit" name="resend_invoice" class="btn-resend">📧 Отправить счет</button>
                </form>
            </td>
            <td>
                <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn-view">Детали</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
function confirmStatusChange(orderId, newStatus) {
    if (newStatus === 'cancelled') {
        if (confirm('Вы уверены, что хотите отменить этот заказ?\nТовары будут возвращены на склад.')) {
            document.getElementById('status-form-' + orderId).submit();
        } else {
            // Возвращаем предыдущее значение
            location.reload();
        }
    } else {
        document.getElementById('status-form-' + orderId).submit();
    }
}
</script>

<style>
.orders-table {
    width: 100%;
    border-collapse: collapse;
}
.orders-table th,
.orders-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}
.orders-table th {
    background: #f5f5f5;
}
.btn-resend {
    padding: 5px 10px;
    background: #F0B1D3;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.btn-view {
    display: inline-block;
    padding: 5px 10px;
    background: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}
.success-message {
    background: #e8f5e9;
    color: #4caf50;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 20px;
}
</style>

<?php include '../templates/admin-footer.php'; ?>