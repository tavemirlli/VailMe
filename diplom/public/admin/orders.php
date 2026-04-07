<?php
session_start();
require_once '../config/db.php';

// Простая проверка админа (можно улучшить)
if (!isset($_SESSION['admin_logged'])) {
    // Временный вход для админа
    if ($_POST['login'] ?? '' === 'admin' && $_POST['password'] ?? '' === '123') {
        $_SESSION['admin_logged'] = true;
    } else {
        ?>
        <form method="POST">
            <input type="text" name="login" placeholder="Логин">
            <input type="password" name="password" placeholder="Пароль">
            <button type="submit">Войти</button>
        </form>
        <?php
        exit;
    }
}

// Обновление статуса
if (isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = mysqli_real_escape_string($connect, $_POST['status']);
    mysqli_query($connect, "UPDATE orders SET order_status = '$status' WHERE id = $orderId");
}

// Повторная отправка счета
if (isset($_POST['resend_invoice'])) {
    $orderId = (int)$_POST['order_id'];
    $orderSql = "SELECT * FROM orders WHERE id = $orderId";
    $orderResult = mysqli_query($connect, $orderSql);
    $order = mysqli_fetch_assoc($orderResult);
    
    if ($order) {
        // Получаем товары
        $itemsSql = "SELECT * FROM order_items WHERE order_id = $orderId";
        $itemsResult = mysqli_query($connect, $itemsSql);
        $itemsHtml = '';
        while ($item = mysqli_fetch_assoc($itemsResult)) {
            $itemsHtml .= "<tr>";
            $itemsHtml .= "<td>" . htmlspecialchars($item['product_name']) . "</td>";
            $itemsHtml .= "<td>" . $item['quantity'] . "</td>";
            $itemsHtml .= "<td>" . number_format($item['product_price'], 0, '.', ' ') . " ₽</td>";
            $itemsHtml .= "<td>" . number_format($item['total_price'], 0, '.', ' ') . " ₽</td>";
            $itemsHtml .= "</tr>";
        }
        
        $message = "Счет на оплату №{$order['order_number']}... (аналогично процессу)";
        mail($order['customer_email'], "Счет на оплату №{$order['order_number']}", $message, "Content-type:text/html;charset=UTF-8\r\n");
        mysqli_query($connect, "UPDATE orders SET invoice_sent = 1, invoice_sent_at = NOW() WHERE id = $orderId");
        $success = "Счет отправлен повторно";
    }
}

$ordersSql = "SELECT * FROM orders ORDER BY id DESC";
$ordersResult = mysqli_query($connect, $ordersSql);

$pageTitle = 'Управление заказами - Админка';
include '../templates/admin-header.php';
?>
<h1>Управление заказами</h1>

<?php if (isset($success)): ?>
    <div class="success"><?php echo $success; ?></div>
<?php endif; ?>

<table class="orders-table">
    <thead>
        <tr><th>ID</th><th>№ заказа</th><th>Клиент</th><th>Телефон</th><th>Email</th><th>Сумма</th><th>Статус</th><th>Счет</th><th>Действия</th></tr>
    </thead>
    <tbody>
        <?php while ($order = mysqli_fetch_assoc($ordersResult)): ?>
        <tr>
            <td><?php echo $order['id']; ?></td>
            <td><?php echo $order['order_number']; ?></td>
            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
            <td><?php echo $order['customer_phone']; ?></td>
            <td><?php echo $order['customer_email']; ?></td>
            <td><?php echo number_format($order['total_amount'], 0, '.', ' '); ?> ₽</td>
            <td>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <select name="status" onchange="this.form.submit()">
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
                    ✅ Отправлен <?php echo date('d.m.Y', strtotime($order['invoice_sent_at'])); ?>
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
        <?php endwhile; ?>
    </tbody>
</table>

<style>
.orders-table { width: 100%; border-collapse: collapse; }
.orders-table th, .orders-table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
.orders-table th { background: #f5f5f5; }
.btn-resend { padding: 5px 10px; background: #F0B1D3; border: none; border-radius: 5px; cursor: pointer; }
.btn-view { display: inline-block; padding: 5px 10px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; }
.success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
</style>

<?php include '../templates/admin-footer.php'; ?>