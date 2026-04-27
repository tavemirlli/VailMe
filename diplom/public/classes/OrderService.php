<?php
require_once 'Database.php';
require_once 'Order.php';

class OrderService {

    public static function getAllOrders() {
        $db = Database::getInstance();
        $orders = Order::getAllOrders();
        
        foreach ($orders as &$order) {
            $sql = "SELECT SUM(quantity) as total FROM order_items WHERE order_id = {$order['id']}";
            $result = $db->query($sql);
            $data = $db->fetchOne($result);
            $order['total_items'] = $data['total'] ?? 0;
        }
        
        return $orders;
    }

    public static function getOrderById($id) {
        return Order::getById($id);
    }

    public static function updateOrderStatus($orderId, $status) {
        return Order::updateStatus($orderId, $status);
    }
    
}
?>