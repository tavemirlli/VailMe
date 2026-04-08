<?php
session_start();
require_once '../config/db.php';
require_once '../classes/Order.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: ../index.php');
    exit;
}

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = Order::getById($orderId);
$items = Order::getOrderItems($orderId);

if (!$order) {
    header('Location: orders.php');
    exit;
}

$pageTitle = 'Детали заказа №' . $order['order_number'] . ' - Админка';

include '../templates/admin-header.php';
?>

<h1>Детали заказа №<?php echo $order['order_number']; ?></h1>

<div class="order-info">
    <h3>Информация о заказе</h3>
    <p><strong>Дата:</strong> <?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></p>
    <p><strong>Статус:</strong> <?php echo Order::getStatusText($order['order_status']); ?></p>
    <p><strong>Счет отправлен:</strong> <?php echo $order['invoice_sent'] ? date('d.m.Y H:i', strtotime($order['invoice_sent_at'])) : 'Нет'; ?></p>
</div>

<div class="customer-info">
    <h3>Данные покупателя</h3>
    <p><strong>Имя:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
    <p><strong>Телефон:</strong> <?php echo $order['customer_phone']; ?></p>
    <p><strong>Email:</strong> <?php echo $order['customer_email']; ?></p>
    <p><strong>Комментарий:</strong> <?php echo nl2br(htmlspecialchars($order['admin_comment'] ?? '')); ?></p>
</div>

<div class="items-list">
    <h3>Товары в заказе</h3>
    
    <?php if (empty($items)): ?>
        <div class="empty-items">
            <p>Товары не найдены</p>
        </div>
    <?php else: ?>
        <table class="items-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Товар</th>
                    <th>Цвет/Размер</th>
                    <th>Кол-во</th>
                    <th>Цена</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td>
                        <?php 
                        // Получаем цвет и размер из варианта
                        if ($item['variant_id']) {
                            $variantSql = "SELECT color, size FROM product_variants WHERE id = {$item['variant_id']}";
                            $variantResult = mysqli_query($connect, $variantSql);
                            $variant = mysqli_fetch_assoc($variantResult);
                            if ($variant) {
                                echo htmlspecialchars($variant['color'] . ' / ' . $variant['size']);
                            } else {
                                echo '-';
                            }
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo number_format($item['product_price'], 0, '.', ' '); ?> ₽</td>
                    <td><?php echo number_format($item['total_price'], 0, '.', ' '); ?> ₽</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right;"><strong>Итого:</strong></td>
                    <td><strong><?php echo number_format($order['total_amount'], 0, '.', ' '); ?> ₽</strong></td>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>
</div>

<div class="actions">
    <a href="orders.php" class="btn-back">← Назад к списку</a>
    <form method="POST" action="orders.php" style="display: inline;">
        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
        <button type="submit" name="resend_invoice" class="btn-resend">📧 Отправить счет повторно</button>
    </form>
</div>

<style>
.order-info, .customer-info, .items-list {
    margin-bottom: 30px;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 12px;
}
.items-table {
    width: 100%;
    border-collapse: collapse;
}
.items-table th, .items-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
.items-table th {
    background: #f0f0f0;
    font-weight: 600;
}
.items-table tfoot td {
    border-top: 2px solid #ddd;
    padding-top: 15px;
    font-weight: bold;
}
.empty-items {
    text-align: center;
    padding: 40px;
    color: #999;
}
.actions {
    margin-top: 30px;
}
.btn-back {
    display: inline-block;
    padding: 10px 20px;
    background: #666;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    margin-right: 10px;
}
.btn-resend {
    padding: 10px 20px;
    background: #F0B1D3;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}
</style>

<?php include '../templates/admin-footer.php'; ?>