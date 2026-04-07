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
            'title' => 'VailMe',
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
        // Женские товары - подкатегория ID = 1
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

public function getMenProducts($limit = 4) {
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
    public function getOrderSteps() {
        return [
            ['number' => 1, 'text' => 'Соберите корзину'],
            ['number' => 2, 'text' => 'Нажмите "Оформить заказ"'],
            ['number' => 3, 'text' => 'Укажите данные для связи'],
            ['number' => 4, 'text' => 'Дождитесь сообщения администратора']
        ];
    }

}

?>