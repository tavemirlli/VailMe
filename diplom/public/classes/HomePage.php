<?php
require_once 'BaseModel.php';
require_once 'Product.php';

class HomePage {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getHeroData() {
        return [
            'title' => 'Поверьте слова',
            'subtitle' => 'Откройте для себя новый стиль',
            'button_text' => 'Войти',
            'button_link' => '/login.php',
            'image_size' => '340 × 58'
        ];
    }
    
    public function getWeeklyProducts($limit = 4) {
        try {
            $sql = "SELECT * FROM products ORDER BY id DESC LIMIT " . (int)$limit;
            $result = $this->db->query($sql);
            $rows = $this->db->fetchAll($result);
            
            $products = [];
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $product = new Product();
                    $product->data = $row;
                    $products[] = $product;
                }
            }
            
            return $products;
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function getWomenProducts($limit = 4) {
        try {
            $limit = (int)$limit;
            // Получаем товары из категории "Женская одежда" (category_id = 1)
            $sql = "SELECT p.* FROM products p
                    JOIN subcategories s ON p.subcategory_id = s.id
                    WHERE s.category_id = 1
                    ORDER BY p.id DESC LIMIT $limit";
            $result = $this->db->query($sql);
            $rows = $this->db->fetchAll($result);
            
            $products = [];
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $product = new Product();
                    $product->data = $row;
                    $products[] = $product;
                }
            }
            
            return $products;
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function getOrderSteps() {
        return [
            ['number' => 1, 'text' => 'Соберите карман'],
            ['number' => 2, 'text' => 'Нажмите "Оформлять знаки"'],
            ['number' => 3, 'text' => 'Уложите данные для связи'],
            ['number' => 4, 'text' => 'Дождитесь сообщения командиров']
        ];
    }
}
?>