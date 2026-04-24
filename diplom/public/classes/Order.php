<?php
require_once 'BaseModel.php';
require_once 'Mailer.php';

class Order extends BaseModel {
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'order_number', 'customer_name', 'customer_phone', 'customer_email', 'total_amount', 'order_status', 'invoice_sent', 'admin_comment'];
    
    public static function create($userId, $name, $phone, $email, $cartItems, $total) {
    $db = Database::getInstance();
    
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
    
    $currentOrder = self::getById($orderId);
    $currentStatus = $currentOrder['order_status'];
    
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
    
    /**
 * Отправка счета на email через Mailer
 */
public static function sendInvoiceEmail($order) {
    require_once 'Mailer.php';
    return Mailer::sendInvoice($order);
}
    
}
?>