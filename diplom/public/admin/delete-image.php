<?php
require_once '../config/db.php';
require_once '../classes/ProductImage.php';
require_once '../classes/Product.php';

if (!isset($_GET['id']) || !isset($_GET['product_id'])) {
    header('Location: index.php');
    exit;
}

$imageId = (int)$_GET['id'];
$productId = (int)$_GET['product_id'];

$image = ProductImage::findById($imageId);

if ($image) {
    // Получаем путь к файлу
    $filePath = $_SERVER['DOCUMENT_ROOT'] . $image->image_url;
    
    // Удаляем файл с диска
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    
    // Удаляем запись из БД
    $image->delete();
    
    // Проверяем, было ли удаленное изображение главным
    if ($image->is_main) {
        // Находим другое изображение и делаем его главным
        $product = Product::findById($productId);
        $remainingImages = $product->getImages();
        
        if (!empty($remainingImages)) {
            $remainingImages[0]->setAsMain();
        }
    }
}

header('Location: update.php?id=' . $productId . '&success=1');
exit;
?>