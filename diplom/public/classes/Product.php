<?php
require_once 'BaseModel.php';

class Product extends BaseModel {
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = ['subcategory_id', 'name', 'description', 'price', 'old_price', 'is_new'];
    
    private $subcategory = null;
    private $variants = null;
    private $images = null;
    
    public function getSubcategory() {
        if ($this->subcategory === null && isset($this->data['subcategory_id'])) {
            $this->subcategory = Subcategory::findById($this->data['subcategory_id']);
        }
        return $this->subcategory;
    }
    
    public function getVariants() {
        if ($this->variants === null) {
            $sql = "SELECT * FROM product_variants WHERE product_id = {$this->data['id']}";
            $result = $this->db->query($sql);
            $rows = $this->db->fetchAll($result);
            
            $this->variants = [];
            foreach ($rows as $row) {
                $variant = new ProductVariant();
                $variant->data = $row;
                $this->variants[] = $variant;
            }
        }
        return $this->variants;
    }
    
    public function getImages() {
        if ($this->images === null) {
            $sql = "SELECT * FROM product_images WHERE product_id = {$this->data['id']} ORDER BY is_main DESC";
            $result = $this->db->query($sql);
            $rows = $this->db->fetchAll($result);
            
            $this->images = [];
            foreach ($rows as $row) {
                $image = new ProductImage();
                $image->data = $row;
                $this->images[] = $image;
            }
        }
        return $this->images;
    }
    
    public function getMainImage() {
        $images = $this->getImages();
        foreach ($images as $image) {
            if ($image->is_main) {
                return $image;
            }
        }
        return $images[0] ?? null;
    }
    
    public function getFinalPrice() {
        return $this->data['old_price'] ?? $this->data['price'];
    }
    
    public function getDiscountPercent() {
        if (isset($this->data['old_price']) && $this->data['old_price'] > $this->data['price']) {
            return round((($this->data['old_price'] - $this->data['price']) / $this->data['old_price']) * 100);
        }
        return 0;
    }
    
    public function isOnSale() {
        return isset($this->data['old_price']) && $this->data['old_price'] > $this->data['price'];
    }
    
    public function saveWithVariants($variants = []) {
        $this->db->query("START TRANSACTION");
        
        try {
            if (!$this->save()) {
                throw new Exception("Не удалось сохранить товар");
            }
            
            if (!empty($variants)) {
                foreach ($variants as $variantData) {
                    $variant = new ProductVariant();
                    $variant->product_id = $this->data['id'];
                    $variant->color = $variantData['color'] ?? '';
                    $variant->size = $variantData['size'] ?? '';
                    $variant->price = !empty($variantData['price']) ? $variantData['price'] : $this->data['price'];
                    $variant->quantity = $variantData['quantity'] ?? 0;
                    
                    if (!$variant->save()) {
                        throw new Exception("Не удалось сохранить вариант товара");
                    }
                }
            }
            
            $this->db->query("COMMIT");
            return true;
            
        } catch (Exception $e) {
            $this->db->query("ROLLBACK");
            return false;
        }
    }
}
?>