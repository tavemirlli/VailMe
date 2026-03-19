<?php
require_once 'BaseModel.php';

class Subcategory extends BaseModel {
    protected $table = 'subcategories';
    protected $primaryKey = 'id';
    protected $fillable = ['category_id', 'name'];
    
    private $category = null;
    private $products = null;
    
    public function getCategory() {
        if ($this->category === null && isset($this->data['category_id'])) {
            $this->category = Category::findById($this->data['category_id']);
        }
        return $this->category;
    }
    
    public function getProducts() {
        if ($this->products === null) {
            $sql = "SELECT * FROM products WHERE subcategory_id = {$this->data['id']} ORDER BY id DESC";
            $result = $this->db->query($sql);
            $rows = $this->db->fetchAll($result);
            
            $this->products = [];
            foreach ($rows as $row) {
                $product = new Product();
                $product->data = $row;
                $this->products[] = $product;
            }
        }
        return $this->products;
    }
    
    public function hasProducts() {
        return count($this->getProducts()) > 0;
    }
    
    public function canDelete() {
        return !$this->hasProducts();
    }
    
    public static function getByCategory($categoryId) {
        $db = Database::getInstance();
        $categoryId = (int)$categoryId;
        $sql = "SELECT * FROM subcategories WHERE category_id = $categoryId ORDER BY name";
        $result = $db->query($sql);
        $rows = $db->fetchAll($result);
        
        $subcategories = [];
        foreach ($rows as $row) {
            $sub = new self();
            $sub->data = $row;
            $subcategories[] = $sub;
        }
        
        return $subcategories;
    }
}
?>