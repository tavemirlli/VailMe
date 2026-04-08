<?php
require_once 'BaseModel.php';

class Order extends BaseModel {
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'order_number', 'customer_name', 'customer_phone', 'customer_email', 'total_amount', 'order_status', 'invoice_sent', 'admin_comment'];
    
    public static function create($userId, $name, $phone, $email, $cartItems, $total) {
    $db = Database::getInstance();
    
    // Проверяем наличие товаров на складе перед созданием заказа
    foreach ($cartItems as $item) {
        if (isset($item['variant_id']) && $item['variant_id']) {
            $sql = "SELECT quantity FROM product_variants WHERE id = {$item['variant_id']}";
            $result = $db->query($sql);
            $variant = $db->fetchOne($result);
            if (!$variant || $variant['quantity'] < $item['quantity']) {
                throw new Exception("Недостаточно товара '{$item['product_name']}' на складе");
            }
        } else {
            $sql = "SELECT quantity FROM products WHERE id = {$item['product_id']}";
            $result = $db->query($sql);
            $product = $db->fetchOne($result);
            if (!$product || $product['quantity'] < $item['quantity']) {
                throw new Exception("Недостаточно товара '{$item['product_name']}' на складе");
            }
        }
    }
    
    $orderNumber = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
    $userId = $userId ? (int)$userId : 'NULL';
    
    $sql = "INSERT INTO orders (user_id, order_number, customer_name, customer_phone, customer_email, total_amount, order_status) 
            VALUES ($userId, '$orderNumber', '$name', '$phone', '$email', $total, 'new')";
    $db->query($sql);
    $orderId = $db->lastInsertId();
    
    foreach ($cartItems as $item) {
        $variantId = isset($item['variant_id']) && $item['variant_id'] ? (int)$item['variant_id'] : 'NULL';
        $productName = $db->escape($item['product_name']);
        $productPrice = (float)$item['price'];
        $quantity = (int)$item['quantity'];
        $totalPrice = $productPrice * $quantity;
        
        $sql = "INSERT INTO order_items (order_id, product_id, variant_id, product_name, product_price, quantity, total_price) 
                VALUES ($orderId, {$item['product_id']}, $variantId, '$productName', $productPrice, $quantity, $totalPrice)";
        $db->query($sql);
    }
    
    // Списываем товары со склада
    self::decreaseStock($orderId);
    
    return self::getById($orderId);
}
    
    public static function getById($id) {
        $db = Database::getInstance();
        $id = (int)$id;
        $sql = "SELECT * FROM orders WHERE id = $id";
        $result = $db->query($sql);
        return $db->fetchOne($result);
    }
    
    public static function getUserOrders($userId) {
        $db = Database::getInstance();
        $userId = (int)$userId;
        $sql = "SELECT * FROM orders WHERE user_id = $userId ORDER BY id DESC";
        $result = $db->query($sql);
        return $db->fetchAll($result);
    }
    
    public static function getAllOrders() {
        $db = Database::getInstance();
        $sql = "SELECT * FROM orders ORDER BY id DESC";
        $result = $db->query($sql);
        return $db->fetchAll($result);
    }
    
    public static function updateStatus($orderId, $status) {
    $db = Database::getInstance();
    $orderId = (int)$orderId;
    $status = $db->escape($status);
    
    // Получаем текущий статус заказа
    $currentOrder = self::getById($orderId);
    $currentStatus = $currentOrder['order_status'];
    
    // Если статус меняется на "отменён" и ранее не был отменён - возвращаем товары
    if ($status == 'cancelled' && $currentStatus != 'cancelled') {
        self::restoreStock($orderId);
    }
    
    $sql = "UPDATE orders SET order_status = '$status' WHERE id = $orderId";
    return $db->query($sql);
}
    
    public static function markInvoiceSent($orderId) {
        $db = Database::getInstance();
        $orderId = (int)$orderId;
        $sql = "UPDATE orders SET invoice_sent = 1, invoice_sent_at = NOW() WHERE id = $orderId";
        return $db->query($sql);
    }
    
    public static function getStatusText($status) {
        $statuses = [
            'new' => 'Новый',
            'processing' => 'В обработке',
            'shipped' => 'Отправлен',
            'delivered' => 'Доставлен',
            'cancelled' => 'Отменён'
        ];
        return $statuses[$status] ?? $status;
    }
    
    public static function getOrderItems($orderId) {
        $db = Database::getInstance();
        $orderId = (int)$orderId;
        $sql = "SELECT * FROM order_items WHERE order_id = $orderId";
        $result = $db->query($sql);
        return $db->fetchAll($result);
    }
    public static function decreaseStock($orderId) {
    $db = Database::getInstance();
    $items = self::getOrderItems($orderId);
    
    foreach ($items as $item) {
        if ($item['variant_id'] && $item['variant_id'] != 'NULL') {
            // Списываем с конкретного варианта (цвет/размер)
            $sql = "UPDATE product_variants SET quantity = quantity - {$item['quantity']} 
                    WHERE id = {$item['variant_id']} AND quantity >= {$item['quantity']}";
            $db->query($sql);
        } else {
            // Списываем с общего остатка товара (если нет вариантов)
            $sql = "UPDATE products SET quantity = quantity - {$item['quantity']} 
                    WHERE id = {$item['product_id']} AND quantity >= {$item['quantity']}";
            $db->query($sql);
        }
    }
    
    return true;
}
public static function restoreStock($orderId) {
    $db = Database::getInstance();
    $items = self::getOrderItems($orderId);
    
    foreach ($items as $item) {
        if ($item['variant_id'] && $item['variant_id'] != 'NULL') {
            // Возвращаем количество для конкретного варианта
            $sql = "UPDATE product_variants SET quantity = quantity + {$item['quantity']} 
                    WHERE id = {$item['variant_id']}";
            $db->query($sql);
        } else {
            // Возвращаем количество для общего остатка товара
            $sql = "UPDATE products SET quantity = quantity + {$item['quantity']} 
                    WHERE id = {$item['product_id']}";
            $db->query($sql);
        }
    }
    
    return true;
}
    
    public static function sendInvoiceEmail($order) {
        // Временно отключаем отправку писем для тестирования
        return true;
        
        /* 
        $items = self::getOrderItems($order['id']);
        $to = $order['customer_email'];
        $subject = "Счет на оплату №{$order['order_number']}";
        
        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= "<tr>";
            $itemsHtml .= "<td>" . htmlspecialchars($item['product_name']) . "</td>";
            $itemsHtml .= "<td>" . $item['quantity'] . "</td>";
            $itemsHtml .= "<td>" . number_format($item['product_price'], 0, '.', ' ') . " ₽</td>";
            $itemsHtml .= "<td>" . number_format($item['total_price'], 0, '.', ' ') . " ₽</td>";
            $itemsHtml .= "</tr>";
        }
        
        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Счет на оплату №{$order['order_number']}</title>
            <style>
                body { font-family: Arial, sans-serif; }
                .invoice { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; }
                .header { text-align: center; border-bottom: 2px solid #F0B1D3; padding-bottom: 10px; }
                .total { font-size: 20px; font-weight: bold; color: #F0B1D3; text-align: right; margin-top: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
                .bank-details { background: #f9f9f9; padding: 15px; margin-top: 20px; border-radius: 8px; }
            </style>
        </head>
        <body>
            <div class='invoice'>
                <div class='header'>
                    <h2>Счет на оплату №{$order['order_number']}</h2>
                    <p>Дата: " . date('d.m.Y') . "</p>
                </div>
                
                <h3>Данные покупателя:</h3>
                <p><strong>{$order['customer_name']}</strong><br>
                Телефон: {$order['customer_phone']}<br>
                Email: {$order['customer_email']}</p>
                
                <h3>Товары:</h3>
                <table>
                    <thead><tr><th>Товар</th><th>Кол-во</th><th>Цена</th><th>Сумма</th></tr></thead>
                    <tbody>{$itemsHtml}</tbody>
                </table>
                
                <div class='total'>
                    Итого к оплате: " . number_format($order['total_amount'], 0, '.', ' ') . " ₽
                </div>
                
                <div class='bank-details'>
                    <h3>Реквизиты для оплаты:</h3>
                    <p><strong>ИП Фамилия Имя Отчество</strong><br>
                    ИНН: 1234567890<br>
                    Расчетный счет: 40802810XXXXXXXXXX<br>
                    Банк: Т-Банк<br>
                    БИК: 044525974</p>
                    <p>После оплаты, пожалуйста, сообщите нам об этом по телефону <strong>+7-988-888-88-88</strong></p>
                </div>
                
                <p style='text-align: center; margin-top: 30px; color: #999;'>
                    Спасибо за заказ!<br>
                    Администратор свяжется с вами для уточнения деталей доставки.
                </p>
            </div>
        </body>
        </html>";
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: VailMe <noreply@vailme.ru>\r\n";
        
        return mail($to, $subject, $message, $headers);
        */
    }
}
?>