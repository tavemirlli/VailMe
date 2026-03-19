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
        $sql = "SELECT * FROM carts WHERE session_id = '" . $db->escape($sessionId) . "'";
        $result = $db->query($sql);
        $data = $db->fetchOne($result);
        
        if ($data) {
            $cart = new self();
            $cart->data = $data;
            return $cart;
        }
        
        $cart = new self();
        $cart->session_id = $sessionId;
        $cart->save();
        return $cart;
    }
    
    public function getItems() {
        if ($this->items === null) {
            $sql = "SELECT ci.*, p.name as product_name, p.price as product_price,
                           pv.color, pv.size, pv.price as variant_price
                    FROM cart_items ci
                    JOIN products p ON ci.product_id = p.id
                    LEFT JOIN product_variants pv ON ci.variant_id = pv.id
                    WHERE ci.cart_id = {$this->data['id']}";
            $result = $this->db->query($sql);
            $this->items = $this->db->fetchAll($result);
        }
        return $this->items;
    }
    
    public function addItem($productId, $variantId = null, $quantity = 1) {
        $productId = (int)$productId;
        $variantId = $variantId ? (int)$variantId : 'NULL';
        $quantity = (int)$quantity;
        
        if ($variantId && $variantId !== 'NULL') {
            $priceSql = "SELECT price FROM product_variants WHERE id = $variantId";
        } else {
            $priceSql = "SELECT price FROM products WHERE id = $productId";
        }
        $priceResult = $this->db->query($priceSql);
        $priceData = $this->db->fetchOne($priceResult);
        $price = $priceData['price'];
        
        $checkSql = "SELECT id, quantity FROM cart_items 
                     WHERE cart_id = {$this->data['id']} 
                     AND product_id = $productId 
                     AND " . ($variantId === 'NULL' ? "variant_id IS NULL" : "variant_id = $variantId");
        $checkResult = $this->db->query($checkSql);
        $existing = $this->db->fetchOne($checkResult);
        
        if ($existing) {
            $newQuantity = $existing['quantity'] + $quantity;
            $sql = "UPDATE cart_items SET quantity = $newQuantity 
                    WHERE id = {$existing['id']}";
        } else {
            $sql = "INSERT INTO cart_items (cart_id, product_id, variant_id, quantity, price) 
                    VALUES ({$this->data['id']}, $productId, $variantId, $quantity, $price)";
        }
        
        return $this->db->query($sql);
    }
    
    public function updateItemQuantity($itemId, $quantity) {
        $itemId = (int)$itemId;
        $quantity = (int)$quantity;
        
        if ($quantity <= 0) {
            return $this->removeItem($itemId);
        }
        
        $sql = "UPDATE cart_items SET quantity = $quantity WHERE id = $itemId AND cart_id = {$this->data['id']}";
        return $this->db->query($sql);
    }
    
    public function removeItem($itemId) {
        $itemId = (int)$itemId;
        $sql = "DELETE FROM cart_items WHERE id = $itemId AND cart_id = {$this->data['id']}";
        return $this->db->query($sql);
    }
    
    public function clear() {
        $sql = "DELETE FROM cart_items WHERE cart_id = {$this->data['id']}";
        return $this->db->query($sql);
    }
    
    public function getTotal() {
        $items = $this->getItems();
        $total = 0;
        foreach ($items as $item) {
            $itemPrice = $item['variant_price'] ?? $item['product_price'];
            $total += $itemPrice * $item['quantity'];
        }
        return $total;
    }
    
    public function getItemsCount() {
        $items = $this->getItems();
        return array_sum(array_column($items, 'quantity'));
    }
}
?>