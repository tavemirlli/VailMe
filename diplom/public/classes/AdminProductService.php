<?php
require_once 'Database.php';
require_once 'Product.php';
require_once 'Category.php';
require_once 'Subcategory.php';
require_once 'ProductImage.php';
require_once 'Order.php';

class AdminProductService {
    
    public static function getAllProducts() {
        return Product::findAll('id DESC');
    }

    public static function getProductsStats() {
        $db = Database::getInstance();
        $sql = "SELECT 
                    COUNT(*) as total_products,
                    SUM(CASE WHEN is_new = 1 THEN 1 ELSE 0 END) as new_products
                FROM products";
        $result = $db->query($sql);
        return $db->fetchOne($result);
    }

    public static function getProductById($id) {
        return Product::findById($id);
    }

    public static function getProductCategory($product) {
        $subcategory = $product->getSubcategory();
        return $subcategory ? $subcategory->getCategory() : null;
    }

    public static function getProductMainImage($product) {
        return $product->getMainImage();
    }
    
    public static function deleteProduct($id) {
        $id = (int)$id;
        
        if ($id <= 0) {
            return false;
        }
        
        $product = Product::findById($id);
        
        if (!$product) {
            return false;
        }

        $images = $product->getImages();
        foreach ($images as $image) {
            if (!empty($image->image_url)) {
                $filePath = $_SERVER['DOCUMENT_ROOT'] . $image->image_url;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }
    
        return $product->delete();
    }
}
?>