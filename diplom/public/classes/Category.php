<?php
require_once 'BaseModel.php';

class Category extends BaseModel {
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'image_url'];
    
    private $subcategories = null;
    
    public function getSubcategories() {
        if ($this->subcategories === null) {
            $sql = "SELECT * FROM subcategories WHERE category_id = {$this->data['id']} ORDER BY name";
            $result = $this->db->query($sql);
            $rows = $this->db->fetchAll($result);
            
            $this->subcategories = [];
            foreach ($rows as $row) {
                $sub = new Subcategory();
                $sub->data = $row;
                $this->subcategories[] = $sub;
            }
        }
        return $this->subcategories;
    }
    
    public function hasSubcategories() {
        return count($this->getSubcategories()) > 0;
    }
    
    public function canDelete() {
        return !$this->hasSubcategories();
    }
    
    public function getImageUrl() {
        return $this->data['image_url'] ?? null;
    }
    
    public function hasImage() {
        return !empty($this->data['image_url']);
    }
    
    public static function getAllWithSubcategories() {
        $db = Database::getInstance();
        $sql = "SELECT c.id as cat_id, c.name as cat_name, c.image_url as cat_image,
                       s.id as sub_id, s.name as sub_name
                FROM categories c
                LEFT JOIN subcategories s ON c.id = s.category_id
                ORDER BY c.name, s.name";
        $result = $db->query($sql);
        $rows = $db->fetchAll($result);
        
        $categories = [];
        foreach ($rows as $row) {
            $catId = $row['cat_id'];
            if (!isset($categories[$catId])) {
                $categories[$catId] = [
                    'id' => $catId,
                    'name' => $row['cat_name'],
                    'image_url' => $row['cat_image'],
                    'subcategories' => []
                ];
            }
            if ($row['sub_id']) {
                $categories[$catId]['subcategories'][] = [
                    'id' => $row['sub_id'],
                    'name' => $row['sub_name']
                ];
            }
        }
        
        return $categories;
    }
    public function getAllProducts() {
    $db = Database::getInstance();
    $sql = "SELECT p.* FROM products p
            JOIN subcategories s ON p.subcategory_id = s.id
            WHERE s.category_id = {$this->id}
            ORDER BY p.id DESC";
    $result = $db->query($sql);
    $rows = $db->fetchAll($result);
    
    $products = [];
    if (!empty($rows)) {
        foreach ($rows as $row) {
            $product = new Product();
            $product->data = $row;
            $products[] = $product;
        }
    }
    return $products;
}
}
?>