<?php
require_once 'BaseModel.php';

class Promocode extends BaseModel {
    protected $table = 'promocodes';
    protected $primaryKey = 'id';
    protected $fillable = ['code', 'discount_type', 'discount_value', 'min_order_amount', 'max_discount', 'usage_limit', 'used_count', 'is_active', 'user_id', 'expires_at'];
    
    public static function findByCode($code) {
        $db = Database::getInstance();
        $code = $db->escape($code);
        $sql = "SELECT * FROM promocodes WHERE code = '$code' AND is_active = 1";
        $result = $db->query($sql);
        $data = $db->fetchOne($result);
        
        if ($data) {
            $promocode = new self();
            $promocode->data = $data;
            return $promocode;
        }
        return null;
    }
    
    public static function findById($id) {
        $db = Database::getInstance();
        $id = (int)$id;
        $sql = "SELECT * FROM promocodes WHERE id = $id";
        $result = $db->query($sql);
        $data = $db->fetchOne($result);
        
        if ($data) {
            $promocode = new self();
            $promocode->data = $data;
            return $promocode;
        }
        return null;
    }
// промокод    
    public function isValid($totalAmount = 0) {
        if ($this->expires_at && strtotime($this->expires_at) < time()) {
            return ['valid' => false, 'message' => 'Срок действия промокода истек'];
        }
        
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'Промокод неактивен'];
        }
        
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Промокод уже использован'];
        }
        
        if ($this->min_order_amount && $totalAmount < $this->min_order_amount) {
            return ['valid' => false, 'message' => 'Минимальная сумма заказа для этого промокода: ' . number_format($this->min_order_amount, 0, '.', ' ') . ' ₽'];
        }
        
        return ['valid' => true, 'message' => ''];
    }
    
    public function calculateDiscount($totalAmount) {
        if ($this->discount_type == 'percent') {
            $discount = $totalAmount * ($this->discount_value / 100);
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
        } else {
            $discount = min($this->discount_value, $totalAmount);
        }
        return $discount;
    }
    
    public function applyToOrder($orderId, $totalAmount) {
        $validation = $this->isValid($totalAmount);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }
        
        $discount = $this->calculateDiscount($totalAmount);
        $newTotal = $totalAmount - $discount;
        if ($newTotal < 0) $newTotal = 0;
        
        $db = Database::getInstance();
        
        $sql = "UPDATE orders SET promocode_id = {$this->id}, promocode_discount = $discount, original_total = $totalAmount, total_amount = $newTotal 
                WHERE id = $orderId";
        $db->query($sql);
        
        $this->used_count++;
        $this->save();

        if ($this->usage_limit == 1 && $this->used_count >= $this->usage_limit) {
            $this->is_active = 0;
            $this->save();
        }
        
        return ['success' => true, 'discount' => $discount, 'new_total' => $newTotal];
    }
    
    public static function createForUser($userId) {
        $db = Database::getInstance();
        $code = 'WELCOME' . $userId . rand(100, 999);
        
        $sql = "INSERT INTO promocodes (code, discount_type, discount_value, min_order_amount, usage_limit, user_id, expires_at) 
                VALUES ('$code', 'percent', 10, 0, 1, $userId, DATE_ADD(NOW(), INTERVAL 30 DAY))";
        return $db->query($sql);
    }
    
    public static function getUserPromocode($userId) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM promocodes 
                WHERE user_id = $userId 
                AND is_active = 1 
                AND used_count < usage_limit
                AND (expires_at IS NULL OR expires_at > NOW())";
        $result = $db->query($sql);
        $data = $db->fetchOne($result);
        
        if ($data) {
            $promocode = new self();
            $promocode->data = $data;
            return $promocode;
        }
        return null;
    }
}
?>