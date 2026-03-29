<?php
require_once 'BaseModel.php';
require_once 'ProductVariant.php';
require_once 'ProductImage.php';
require_once 'Subcategory.php';

class Product extends BaseModel {
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = ['subcategory_id', 'name', 'description', 'price', 'old_price', 'is_new'];
    
    private $subcategory = null;
    private $variants = null;
    private $images = null;
    
    public function getName() {
        return isset($this->data['name']) ? $this->data['name'] : '';
    }
    
    public function getPrice() {
        return isset($this->data['price']) ? (float)$this->data['price'] : 0;
    }
    
    public function getId() {
        return isset($this->data['id']) ? (int)$this->data['id'] : 0;
    }
    
    public function getOldPrice() {
        return isset($this->data['old_price']) ? (float)$this->data['old_price'] : null;
    }
    
    public function getIsNew() {
        return isset($this->data['is_new']) ? (int)$this->data['is_new'] : 0;
    }
    
    public function getDescription() {
        return isset($this->data['description']) ? $this->data['description'] : '';
    }
    
    public function getSubcategoryId() {
        return isset($this->data['subcategory_id']) ? (int)$this->data['subcategory_id'] : 0;
    }
    
    public function getSubcategory() {
        if ($this->subcategory === null && isset($this->data['subcategory_id'])) {
            $this->subcategory = Subcategory::findById($this->data['subcategory_id']);
        }
        return $this->subcategory;
    }
    
    public function getVariants() {
        if ($this->variants === null && isset($this->data['id'])) {
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
    if ($this->images === null && isset($this->data['id'])) {
        $sql = "SELECT * FROM product_images WHERE product_id = {$this->data['id']} ORDER BY is_main DESC";
        $result = $this->db->query($sql);
        $rows = $this->db->fetchAll($result);
        
        $this->images = [];
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $image = new ProductImage();
                $image->data = $row;
                $this->images[] = $image;
            }
        }
    }
    return $this->images ?? [];
}
    
    // ДОБАВЛЯЕМ ЭТОТ МЕТОД
    public function getMainImage() {
    // Получаем все изображения товара
    $images = $this->getImages();
    
    // Если изображения есть
    if (!empty($images)) {
        // Ищем главное изображение (is_main = 1)
        foreach ($images as $image) {
            if (isset($image->is_main) && $image->is_main == 1) {
                return $image;
            }
        }
        // Если главного нет, возвращаем первое
        return $images[0];
    }
    
    return null;
}
    
    public function getFinalPrice() {
        return $this->getOldPrice() ?? $this->getPrice();
    }
    
    public function getDiscountPercent() {
        $oldPrice = $this->getOldPrice();
        $price = $this->getPrice();
        if ($oldPrice && $oldPrice > $price) {
            return round((($oldPrice - $price) / $oldPrice) * 100);
        }
        return 0;
    }
    
    public function isOnSale() {
        $oldPrice = $this->getOldPrice();
        $price = $this->getPrice();
        return $oldPrice && $oldPrice > $price;
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