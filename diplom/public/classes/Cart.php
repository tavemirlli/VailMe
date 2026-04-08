<?php
require_once 'BaseModel.php';

class Cart extends BaseModel {
    protected $table = 'carts';
    protected $primaryKey = 'id';
    protected $fillable = ['session_id', 'user_id'];
    
    private $items = null;
    
    public static function getCurrentCart() {
        $sessionId = session_id();
        $db = Database::getInstance();
        
        // Если пользователь авторизован, ищем корзину по user_id
        if (isset($_SESSION['user_id'])) {
            $userId = (int)$_SESSION['user_id'];
            $sql = "SELECT * FROM carts WHERE user_id = $userId";
            $result = $db->query($sql);
            $data = $db->fetchOne($result);
            
            if ($data) {
                $cart = new self();
                $cart->data = $data;
                return $cart;
            }
        }
        
        // Ищем корзину по session_id
        $sql = "SELECT * FROM carts WHERE session_id = '$sessionId'";
        $result = $db->query($sql);
        $data = $db->fetchOne($result);
        
        if ($data) {
            $cart = new self();
            $cart->data = $data;
            return $cart;
        }
        
        // Создаем новую корзину
        $cart = new self();
        $cart->session_id = $sessionId;
        if (isset($_SESSION['user_id'])) {
            $cart->user_id = (int)$_SESSION['user_id'];
        }
        $cart->save();
        return $cart;
    }
    
    public function getItems() {
        if ($this->items === null && isset($this->data['id'])) {
            $db = Database::getInstance();
            $sql = "SELECT ci.*, p.name as product_name, p.price as product_price,
                           pv.color, pv.size, pv.price as variant_price, pv.quantity as variant_quantity
                    FROM cart_items ci
                    JOIN products p ON ci.product_id = p.id
                    LEFT JOIN product_variants pv ON ci.variant_id = pv.id
                    WHERE ci.cart_id = {$this->data['id']}";
            $result = $db->query($sql);
            $rows = $db->fetchAll($result);
            
            $this->items = [];
            foreach ($rows as $row) {
                $itemPrice = $row['variant_price'] ?? $row['product_price'];
                $this->items[] = [
                    'id' => $row['id'],
                    'cart_id' => $row['cart_id'],
                    'product_id' => $row['product_id'],
                    'variant_id' => $row['variant_id'],
                    'product_name' => $row['product_name'],
                    'quantity' => $row['quantity'],
                    'price' => $itemPrice,
                    'total' => $itemPrice * $row['quantity'],
                    'color' => $row['color'] ?? '',
                    'size' => $row['size'] ?? '',
                    'max_quantity' => $row['variant_quantity'] ?? 99
                ];
            }
        }
        return $this->items ?? [];
    }
    
    public function addItem($productId, $quantity = 1, $color = '', $size = '') {
        $db = Database::getInstance();
        $productId = (int)$productId;
        $quantity = (int)$quantity;
        
        $sql = "SELECT p.*, pv.id as variant_id, pv.quantity as variant_quantity, pv.price as variant_price
                FROM products p
                LEFT JOIN product_variants pv ON p.id = pv.product_id AND pv.color = '$color' AND pv.size = '$size'
                WHERE p.id = $productId";
        $result = $db->query($sql);
        $product = $db->fetchOne($result);
        
        if (!$product) {
            return false;
        }
        
        $finalPrice = $product['variant_price'] ?? $product['price'];
        $maxQty = $product['variant_quantity'] ?? 99;
        $variantId = $product['variant_id'] ?? 0;
        
        $checkSql = "SELECT id, quantity FROM cart_items 
                     WHERE cart_id = {$this->data['id']} 
                     AND product_id = $productId 
                     AND " . ($variantId ? "variant_id = $variantId" : "variant_id IS NULL");
        $checkResult = $db->query($checkSql);
        $existing = $db->fetchOne($checkResult);
        
        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            if ($newQty > $maxQty) {
                $newQty = $maxQty;
            }
            $updateSql = "UPDATE cart_items SET quantity = $newQty WHERE id = {$existing['id']}";
            $db->query($updateSql);
        } else {
            $variantIdSql = $variantId ? $variantId : 'NULL';
            $insertSql = "INSERT INTO cart_items (cart_id, product_id, variant_id, quantity, price) 
                          VALUES ({$this->data['id']}, $productId, $variantIdSql, $quantity, $finalPrice)";
            $db->query($insertSql);
        }
        
        // Сохраняем корзину в БД
        $this->saveCartToDatabase();
        
        return true;
    }
    
    public function removeItem($itemId) {
        $db = Database::getInstance();
        $itemId = (int)$itemId;
        
        $sql = "DELETE FROM cart_items WHERE id = $itemId AND cart_id = {$this->data['id']}";
        $db->query($sql);
        
        $this->saveCartToDatabase();
        
        return true;
    }
    
    public function increaseQuantity($itemId) {
        $db = Database::getInstance();
        $itemId = (int)$itemId;
        
        $sql = "SELECT ci.*, pv.quantity as max_quantity 
                FROM cart_items ci
                LEFT JOIN product_variants pv ON ci.variant_id = pv.id
                WHERE ci.id = $itemId AND ci.cart_id = {$this->data['id']}";
        $result = $db->query($sql);
        $item = $db->fetchOne($result);
        
        if ($item) {
            $maxQty = $item['max_quantity'] ?? 99;
            if ($item['quantity'] < $maxQty) {
                $newQty = $item['quantity'] + 1;
                $sql = "UPDATE cart_items SET quantity = $newQty WHERE id = $itemId";
                $db->query($sql);
                $this->saveCartToDatabase();
            }
        }
        
        return true;
    }
    
    public function decreaseQuantity($itemId) {
        $db = Database::getInstance();
        $itemId = (int)$itemId;
        
        $sql = "SELECT quantity FROM cart_items WHERE id = $itemId AND cart_id = {$this->data['id']}";
        $result = $db->query($sql);
        $item = $db->fetchOne($result);
        
        if ($item) {
            if ($item['quantity'] > 1) {
                $newQty = $item['quantity'] - 1;
                $sql = "UPDATE cart_items SET quantity = $newQty WHERE id = $itemId";
                $db->query($sql);
            } else {
                $sql = "DELETE FROM cart_items WHERE id = $itemId";
                $db->query($sql);
            }
            $this->saveCartToDatabase();
        }
        
        return true;
    }
    
    public function clear() {
        $db = Database::getInstance();
        $sql = "DELETE FROM cart_items WHERE cart_id = {$this->data['id']}";
        $db->query($sql);
        
        $this->saveCartToDatabase();
        
        return true;
    }
    
    public function getTotal() {
        $items = $this->getItems();
        $total = 0;
        foreach ($items as $item) {
            $total += $item['total'];
        }
        return $total;
    }
    
    public function getItemsCount() {
        $items = $this->getItems();
        $count = 0;
        foreach ($items as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }
    
    public function saveCartToDatabase() {
        $db = Database::getInstance();
        $cartId = $this->data['id'];
        
        // Сохраняем корзину в отдельную таблицу user_carts для авторизованных
        if (isset($_SESSION['user_id'])) {
            $userId = (int)$_SESSION['user_id'];
            $sql = "UPDATE carts SET user_id = $userId WHERE id = $cartId";
            $db->query($sql);
        }
        
        return true;
    }
    
    public static function syncCartForUser($userId) {
        $db = Database::getInstance();
        $userId = (int)$userId;
        $sessionId = session_id();
        
        // Ищем корзину пользователя
        $sql = "SELECT * FROM carts WHERE user_id = $userId";
        $result = $db->query($sql);
        $userCart = $db->fetchOne($result);
        
        // Ищем корзину сессии
        $sql = "SELECT * FROM carts WHERE session_id = '$sessionId'";
        $result = $db->query($sql);
        $sessionCart = $db->fetchOne($result);
        
        if ($userCart && $sessionCart) {
            // Объединяем корзины
            $sql = "UPDATE cart_items SET cart_id = {$userCart['id']} WHERE cart_id = {$sessionCart['id']}";
            $db->query($sql);
            $sql = "DELETE FROM carts WHERE id = {$sessionCart['id']}";
            $db->query($sql);
        } elseif ($sessionCart && !$userCart) {
            // Привязываем корзину сессии к пользователю
            $sql = "UPDATE carts SET user_id = $userId WHERE id = {$sessionCart['id']}";
            $db->query($sql);
        }
    }
    
    public static function saveCartForUser($userId, $cartData) {
        $db = Database::getInstance();
        $userId = (int)$userId;
        
        // Ищем корзину пользователя
        $sql = "SELECT id FROM carts WHERE user_id = $userId";
        $result = $db->query($sql);
        $cart = $db->fetchOne($result);
        
        if ($cart) {
            // Очищаем старые товары
            $sql = "DELETE FROM cart_items WHERE cart_id = {$cart['id']}";
            $db->query($sql);
            $cartId = $cart['id'];
        } else {
            // Создаем новую корзину
            $sql = "INSERT INTO carts (session_id, user_id) VALUES ('', $userId)";
            $db->query($sql);
            $cartId = $db->lastInsertId();
        }
        
        // Добавляем товары
        foreach ($cartData as $item) {
            $variantId = isset($item['variant_id']) && $item['variant_id'] ? $item['variant_id'] : 'NULL';
            $sql = "INSERT INTO cart_items (cart_id, product_id, variant_id, quantity, price) 
                    VALUES ($cartId, {$item['id']}, $variantId, {$item['quantity']}, {$item['price']})";
            $db->query($sql);
        }
    }
}
?>