<?php
require_once 'BaseModel.php';

class ProductVariant extends BaseModel {
    protected $table = 'product_variants';
    protected $primaryKey = 'id';
    protected $fillable = ['product_id', 'color', 'size', 'quantity', 'price'];
    
    private $product = null;
    
    public function getProduct() {
        if ($this->product === null && isset($this->data['product_id'])) {
            $this->product = Product::findById($this->data['product_id']);
        }
        return $this->product;
    }
    
    public function isInStock() {
        return $this->data['quantity'] > 0;
    }
    
    public function getDisplayName() {
        $parts = [];
        if (!empty($this->data['color'])) {
            $parts[] = $this->data['color'];
        }
        if (!empty($this->data['size'])) {
            $parts[] = $this->data['size'];
        }
        return implode(' / ', $parts);
    }
}
?>