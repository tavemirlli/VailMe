<?php
require_once 'Database.php';

class CartService {

    public static function checkItemLimit($itemId) {
        $db = Database::getInstance();
        $sql = "SELECT ci.*, pv.quantity as max_qty 
                FROM cart_items ci
                LEFT JOIN product_variants pv ON ci.variant_id = pv.id
                WHERE ci.id = $itemId";
        $result = $db->query($sql);
        $item = $db->fetchOne($result);
        
        if ($item && $item['quantity'] >= $item['max_qty']) {
            $_SESSION['cart_warning'] = 'Достигнуто максимальное количество товара';
            return false;
        }
        return true;
    }

    public static function getProductImage($productId) {
        $db = Database::getInstance();
        $sql = "SELECT image_url FROM product_images WHERE product_id = $productId AND is_main = 1 LIMIT 1";
        $result = $db->query($sql);
        $image = $db->fetchOne($result);
        return $image ? $image['image_url'] : null;
    }
}
?>