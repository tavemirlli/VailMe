<?php
require_once 'BaseModel.php';

class ProductImage extends BaseModel {
    protected $table = 'product_images';
    protected $primaryKey = 'id';
    protected $fillable = ['product_id', 'image_url', 'is_main'];
    
    private $product = null;
    
    public function getProduct() {
        if ($this->product === null && isset($this->data['product_id'])) {
            $this->product = Product::findById($this->data['product_id']);
        }
        return $this->product;
    }
    
    public function setAsMain() {
        if (!isset($this->data['product_id'])) {
            return false;
        }
        
        $sql = "UPDATE product_images SET is_main = 0 WHERE product_id = {$this->data['product_id']}";
        $this->db->query($sql);
        
        $this->is_main = 1;
        return $this->save();
    }
    
    public function getImageUrl() {
        return $this->data['image_url'] ?? '';
    }
    
    public function getIsMain() {
        return isset($this->data['is_main']) ? (int)$this->data['is_main'] : 0;
    }
}
?>