<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_logged'])) {
    header('Location: orders.php');
    exit;
}

$orderId = (int)$_GET['id'];
$orderSql = "SELECT * FROM orders WHERE id = $orderId";
$orderResult = mysqli_query($connect, $orderSql);
$order = mysqli_fetch_assoc($orderResult);

if (!$order) {
    header('Location: orders.php');
    exit;
}

$itemsSql = "SELECT * FROM order_items WHERE order_id = $orderId";
$itemsResult = mysqli_query($connect, $itemsSql);

$pageTitle = 'Детали заказа - Админка';
include '../templates/admin-header.php';
?>

<h1>Детали заказа №<?php echo $order['order_number']; ?></h1>

<div class="order-info">
    <h3>Информация о заказе</h3>
    <p><strong>Дата:</strong> <?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></p>
    <p><strong>Статус:</strong> <?php echo $order['order_status']; ?></p>
    <p><strong>Счет отправлен:</strong> <?php echo $order['invoice_sent'] ? date('d.m.Y', strtotime($order['invoice_sent_at'])) : 'Нет'; ?></p>
</div>

<div class="customer-info">
    <h3>Данные покупателя</h3>
    <p><strong>Имя:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
    <p><strong>Телефон:</strong> <?php echo $order['customer_phone']; ?></p>
    <p><strong>Email:</strong> <?php echo $order['customer_email']; ?></p>
    <p><strong>Комментарий:</strong> <?php echo nl2br(htmlspecialchars($order['admin_comment'] ?? '')); ?></p>
</div>

<div class="items-list">
    <h3>Товары</h3>
    <table>
        <thead><tr><th>Товар</th><th>Кол-во</th><th>Цена</th><th>Сумма</th></tr></thead>
        <tbody>
            <?php while ($item = mysqli_fetch_assoc($itemsResult)): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo number_format($item['product_price'], 0, '.', ' '); ?> ₽</td>
                <td><?php echo number_format($item['total_price'], 0, '.', ' '); ?> ₽</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr><td colspan="3" style="text-align: right;"><strong>Итого:</strong></td><td><strong><?php echo number_format($order['total_amount'], 0, '.', ' '); ?> ₽</strong></td></tr>
        </tfoot>
    </table>
</div>

<div class="actions">
    <a href="orders.php" class="btn-back">← Назад к списку</a>
    <form method="POST" action="orders.php" style="display: inline;">
        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
        <button type="submit" name="resend_invoice" class="btn-resend">📧 Отправить счет повторно</button>
    </form>
</div>

<style>
.order-info, .customer-info, .items-list { margin-bottom: 30px; padding: 20px; background: #f9f9f9; border-radius: 12px; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
.actions { margin-top: 30px; }
.btn-back { display: inline-block; padding: 10px 20px; background: #666; color: white; text-decoration: none; border-radius: 8px; margin-right: 10px; }
.btn-resend { padding: 10px 20px; background: #F0B1D3; border: none; border-radius: 8px; cursor: pointer; }
</style>

<?php include '../templates/admin-footer.php'; ?>